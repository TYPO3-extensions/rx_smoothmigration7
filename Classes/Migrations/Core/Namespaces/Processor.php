<?php
/**
 *  Copyright notice
 *
 *  ⓒ 2014 Michiel Roos <michiel@maxserv.nl>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is free
 *  software; you can redistribute it and/or modify it under the terms of the
 *  GNU General Public License as published by the Free Software Foundation;
 *  either version 2 of the License, or (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful, but
 *  WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 *  or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for
 *  more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 */
namespace Reelworx\RxSmoothmigration7\Migrations\Namespaces;

use Reelworx\RxSmoothmigration7\Domain\Interfaces\Migration;
use Reelworx\RxSmoothmigration7\Domain\Model\Issue;
use Reelworx\RxSmoothmigration7\Migrations\AbstractMigrationProcessor;
use Reelworx\RxSmoothmigration7\Service\ClassAliasProvider;

/**
 * Class Processor
 */
class Processor extends AbstractMigrationProcessor {

	/**
	 * Class Alias Map
	 *
	 * @var array
	 */
	protected $classAliasMap;

	/**
	 * Legacy Classes
	 *
	 * @var array
	 */
	protected $legacyClasses;

	/**
	 * Class Alias Provider
	 *
	 * @var ClassAliasProvider
	 */
	private $classAliasProvider;

	/**
	 * @return void
	 */
	public function execute() {
		$this->classAliasProvider = $this->objectManager->get('Reelworx\\RxSmoothmigration7\\Service\\ClassAliasProvider');
		$this->legacyClasses = $this->classAliasProvider->getLegacyClasses();
		$this->legacyClasses = array_change_key_case($this->legacyClasses, CASE_LOWER);
		$this->classAliasMap = $this->classAliasProvider->getClassAliasMap();
		$this->classAliasMap = array_change_key_case($this->classAliasMap, CASE_LOWER);

		$this->getPendingIssues($this->parentMigration->getIdentifier());
		if (count($this->issues)) {
			foreach ($this->issues as $issue) {
				$this->handleIssue($issue);
				$this->issueRepository->update($issue);
			}
		} else {
			$this->messageService->successMessage('No issues found', TRUE);
		}

		$persistenceManger = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
		$persistenceManger->persistAll();
	}

	/**
	 * Get replacement class
	 *
	 * @param $class
	 *
	 * @return string
	 */
	protected function getReplacementClass($class) {
		$replacement = '';
		$class = strtolower($class);
		if (array_key_exists($class, $this->legacyClasses)) {
			$replacement = $this->legacyClasses[$class];
		} elseif (array_key_exists($class, $this->classAliasMap)) {
			$replacement = $this->classAliasMap[$class];
		}

		return $replacement;
	}

	/**
	 * Handle issue
	 *
	 * @param Issue $issue
	 *
	 * @return void
	 */
	protected function handleIssue(Issue $issue) {
		if (is_string($issue->getLocationInfo())) {
			$locationInfo = unserialize($issue->getLocationInfo());
		} else {
			$locationInfo = $issue->getLocationInfo();
		}
		$this->performReplacement($issue, $locationInfo);
	}

	/**
	 * Perform the actual replacement
	 *
	 * @param Issue $issue
	 * @param object $locationInfo
	 *
	 * @return boolean
	 */
	protected function performReplacement(Issue $issue, $locationInfo) {

		$search = trim($locationInfo->getMatchedString());
		$replacement = $this->getReplacementClass($search);

		$this->messageService->message($locationInfo->getFilePath() . ' line: ' . $locationInfo->getLineNumber() . LF
			. 'Replacing [' . $search . '] =>' . ' [' . $replacement . ']');

		if ($issue->getMigrationStatus() != 0) {
			$this->messageService->successMessage('already migrated', TRUE);

			return;
		}

		if (!file_exists($locationInfo->getFilePath())) {
			$issue->setMigrationStatus(Migration::ERROR_FILE_NOT_FOUND);
			$this->messageService->errorMessage('Error, file not found', TRUE);

			return;
		}
		if (!is_writable($locationInfo->getFilePath())) {
			$issue->setMigrationStatus(Migration::ERROR_FILE_NOT_WRITABLE);
			$this->messageService->errorMessage('Error, file not writable', TRUE);

			return;
		}

		$fileObject = new \SplFileObject($locationInfo->getFilePath());
		$newFileContent = '';

		foreach ($fileObject as $lineNumber => $lineContent) {

			if ($lineNumber + 1 != $locationInfo->getLineNumber()) {
				$newFileContent .= $lineContent;
			} else {
				$newLineContent = preg_replace(
					'/(^|\s+|[^\/\.a-zA-Z0-9_]+)(' . $search . ')([^\/\.a-zA-Z0-9_]+)/',
					'$1' . $replacement . '$3',
					$lineContent
				);
				if ($newLineContent == $lineContent) {
					$issue->setMigrationStatus(Migration::ERROR_FILE_NOT_CHANGED);
					$this->messageService->errorMessage($this->ll('migrationsstatus.4'), TRUE);

					return;
				}
				$newFileContent .= $newLineContent;
			}
		}

		file_put_contents($locationInfo->getFilePath(), $newFileContent);
		$issue->setMigrationStatus(Migration::SUCCESS);
		$this->messageService->successMessage('Success' . LF, TRUE);
	}

}
