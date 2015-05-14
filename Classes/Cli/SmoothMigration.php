<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Ingo Schmitt <is@marketing-factory.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

namespace Reelworx\RxSmoothmigration7\Cli;

use Reelworx\RxSmoothmigration7\Checks\AbstractCheckDefinition;
use Reelworx\RxSmoothmigration7\Checks\AbstractCheckProcessor;
use Reelworx\RxSmoothmigration7\Domain\Interfaces\Check;
use Reelworx\RxSmoothmigration7\Domain\Model\Issue;
use Reelworx\RxSmoothmigration7\Domain\Repository\IssueRepository;
use Reelworx\RxSmoothmigration7\Migrations\AbstractMigrationDefinition;
use Reelworx\RxSmoothmigration7\Migrations\AbstractMigrationProcessor;
use Reelworx\RxSmoothmigration7\Service;
use TYPO3\CMS\Core\Controller\CommandLineController;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class Reelworx\RxSmoothmigration7\Cli\SmoothMigration
 */
class SmoothMigration extends CommandLineController {

	/**
	 * The issue repository
	 *
	 * @var \Reelworx\RxSmoothmigration7\Domain\Repository\IssueRepository
	 */
	protected $issueRepository;

	/**
	 * @var Service\MessageService
	 */
	protected $messageBus;

	/**
	 * @var ObjectManager
	 */
	protected $objectManager;

	/**
	 * Constructor
	 */
	public function __construct() {
		// Loads the cli_args array with command line arguments
		$this->cli_setArguments($_SERVER['argv']);

		$this->objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		$this->issueRepository = $this->objectManager->get('Reelworx\\RxSmoothmigration7\\Domain\\Repository\\IssueRepository');
		$this->messageBus = $this->objectManager->get('Reelworx\\RxSmoothmigration7\\Service\\MessageService');

		// Adding options to help archive:
		$this->cli_options = array();
		$this->cli_options[] = array('check', 'Check your code for needed migrations');
		$this->cli_options[] = array('report', 'Detailed Report including extension, codeline and check');
		$this->cli_options[] = array('executeAllChecks', 'Execute all checks and show a short summary');
		$this->cli_options[] = array('migrate', 'Try to migrate your code');
		$this->cli_options[] = array('help', 'Display this message');

		// Setting help texts:
		$this->cli_help['name'] = 'CLI Smoothmigration Agent';
		$this->cli_help['synopsis'] = 'cli_dispatch.phpsh smoothmigration {task}';
		$this->cli_help['description'] = 'Executes the report of the smoothmigration extension on CLI Basis';
		$this->cli_help['examples'] = './typo3/cli_dispatch.phpsh smoothmigration report';
		$this->cli_help['author'] = 'Ingo Schmitt <is@marketing-factory.de>';
	}

	/**
	 * CLI engine
	 *
	 * @param array $argv command line arguments
	 * @return string
	 */
	public function cli_main($argv) {
		$task = ((string)$this->cli_args['_DEFAULT'][1]) ?: '';

		// Analysis type:
		switch ($task) {
			case 'check':
				$checkKey = ((string)$this->cli_args['_DEFAULT'][2]) ?: '';
				$extension = trim((string)$this->cli_args['--extension'][0]);
				$this->check($checkKey, $extension);
				break;
			case 'executeAllChecks':
				$this->executeAllChecks();
				break;
			case 'report':
				$this->report();
				break;
			case 'migrate':
				$migrationTask = ((string)$this->cli_args['_DEFAULT'][2]) ?: '';
				$experimental = in_array((string)$this->cli_args['--experimental'][0], array('y', 'yes', 'true', '1'));
				$extension = trim((string)$this->cli_args['--extension'][0]);
				$this->migrate($migrationTask, $extension, $experimental);
				break;
			default:
				$this->cli_validateArgs();
				$this->cli_help();
				exit;
		}
	}

	/**
	 * Check
	 *
	 * @param string $checkKey
	 * @param string $extensionKey
	 * @return void
	 */
	private function check($checkKey, $extensionKey = '') {
		$check = NULL;
		$registry = Service\Check\Registry::getInstance();

		if (!empty($checkKey)) {
			$check = $registry->getActiveCheckByIdentifier($checkKey);
		}
		if ($check === NULL) {
			$this->messageBus->message('Please choose a check to execute.' . LF . LF . 'Possible options are:' . LF);
			$this->messageBus->message($this->getChecks());
			return;
		}

		/** @var \Reelworx\RxSmoothmigration7\Checks\AbstractCheckProcessor $processor */
		$processor = $check->getProcessor();
		$processor->setExtensionKey($extensionKey);
		$processor->execute();
		foreach ($processor->getIssues() as $issue) {
			$this->issueRepository->add($issue);
		}
		$persistenceManger = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
		$persistenceManger->persistAll();
		$this->messageBus->infoMessage('Check: ' . $check->getTitle() . ' has ' . count($processor->getIssues()) . ' issues ');
	}

	/**
	 * Renders a Report of Extensions as ASCII
	 *
	 * @return void
	 */
	private function report() {
		$registry = Service\Check\Registry::getInstance();
		$issuesWithInspections = $this->issueRepository->findAllGroupedByExtensionAndInspection();
		foreach ($issuesWithInspections as $extensionKey => $inspections) {
			$count = 0;
			foreach ($inspections as $issues) {
				/** @var Issue $singleIssue */
				foreach ($issues as $singleIssue) {
					if ($count == 0) {
						// Render Extension Key
						$this->messageBus->headerMessage('Extension : ' . $singleIssue->getExtension(), 'info');
					}
					$check = $registry->getActiveCheckByIdentifier($singleIssue->getInspection());
					if ($check) {
						$this->messageBus->message($check->getResultAnalyzer()->getSolution($singleIssue));
					}
					$count++;
				}
			}
			$this->messageBus->successMessage('Total: ' . $count . ' issues in ' . $extensionKey . LF);
		}
	}

	/**
	 * Execute all checks
	 *
	 * @return void
	 */
	private function executeAllChecks() {
		$issues = 0;
		$registry = Service\Check\Registry::getInstance();
		$checks = $registry->getActiveChecks();

		/** @var Check $singleCheck */
		foreach ($checks as $singleCheck) {
			$processor = $singleCheck->getProcessor();
			$this->messageBus->headerMessage('Check: ' . $singleCheck->getTitle(), 'info');
			$processor->execute();
			foreach ($processor->getIssues() as $issue) {
				$this->issueRepository->add($issue);
			}
			$issues = $issues + count($processor->getIssues());
			$this->messageBus->infoMessage(count($processor->getIssues()) . ' issues found');
		}
		$persistenceManger = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
		$persistenceManger->persistAll();
		$this->messageBus->infoMessage(LF . 'Total Issues : ' . $issues);
	}

	/**
	 * Migrate
	 *
	 * @param string $migrationTaskKey
	 * @param string $extensionKey
	 * @param boolean $experimental When TRUE, try to process experimental
	 *    migrations as well
	 * @return void
	 */
	private function migrate($migrationTaskKey, $extensionKey = '', $experimental) {
		$migrationTask = NULL;
		$registry = Service\Migration\Registry::getInstance();

		if (!empty($migrationTaskKey)) {
			$migrationTask = $registry->getActiveMigrationByCliKey($migrationTaskKey);
		}
		if ($migrationTask === NULL) {
			$this->messageBus->message('Please choose a migration to execute.' . LF . LF . 'Possible options are:' . LF);
			$this->messageBus->message($this->getMigrations());
			return;
		}

		/** @var \Reelworx\RxSmoothmigration7\Migrations\AbstractMigrationProcessor $processor */
		$processor = $migrationTask->getProcessor();
		$processor->setMessageService($this->messageBus);
		$processor->setExperimental($experimental);
		$processor->setExtensionKey($extensionKey);
		$processor->execute();
	}

	/**
	 * Get available checks
	 *
	 * @return string
	 */
	private function getChecks() {
		$output = '';
		$registry = Service\Check\Registry::getInstance();
		$checks = $registry->getActiveChecks();
		$maxLen = 0;
		/** @var AbstractCheckDefinition $check */
		foreach ($checks as $check) {
			if (strlen($check->getIdentifier()) > $maxLen) {
				$maxLen = strlen($check->getIdentifier());
			}
		}
		foreach ($checks as $check) {
			$output .= $check->getIdentifier() . substr($this->cli_indent(rtrim($check->getTitle()), $maxLen + 4), strlen($check->getIdentifier())) . LF;
		}

		return $output;
	}

	/**
	 * Get available migrations
	 *
	 * @return string
	 */
	private function getMigrations() {
		$output = '';
		$registry = Service\Migration\Registry::getInstance();
		$migrations = $registry->getActiveMigrations();
		$maxLen = 0;
		/** @var \Reelworx\RxSmoothmigration7\Migrations\AbstractMigrationDefinition $migration */
		foreach ($migrations as $migration) {
			if (strlen($migration->getCliKey()) > $maxLen) {
				$maxLen = strlen($migration->getCliKey());
			}
		}
		foreach ($migrations as $migration) {
			$output .= $migration->getCliKey() . substr($this->cli_indent(rtrim($migration->getTitle()), $maxLen + 4), strlen($migration->getCliKey())) . LF;
		}

		return $output;
	}
}

$cleanerObj = GeneralUtility::makeInstance('Reelworx\\RxSmoothmigration7\\Cli\\SmoothMigration');
$cleanerObj->cli_main($_SERVER['argv']);
