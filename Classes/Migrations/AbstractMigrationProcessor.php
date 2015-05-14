<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Peter Beernink <p.beernink@drecomm.nl>
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
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

namespace Reelworx\RxSmoothmigration7\Migrations;

use Reelworx\RxSmoothmigration7\Domain\Interfaces\Migration;
use Reelworx\RxSmoothmigration7\Domain\Interfaces\MigrationProcessor;
use Reelworx\RxSmoothmigration7\Domain\Repository\IssueRepository;
use Reelworx\RxSmoothmigration7\Service\MessageService;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class Reelworx\RxSmoothmigration7\Migrations\AbstractMigrationProcessor
 *
 * @author Peter Beernink
 */
abstract class AbstractMigrationProcessor implements MigrationProcessor {

	/**
	 * @var MessageService
	 */
	protected $messageService;

	/**
	 * @var boolean
	 */
	protected $encounteredExperimentalIssues = FALSE;

	/**
	 * @var boolean
	 */
	protected $experimental;

	/**
	 * @var string
	 */
	protected $extensionKey;

	/**
	 * @var IssueRepository
	 */
	protected $issueRepository;

	/**
	 * The issues found
	 *
	 * @var array
	 */
	protected $issues;

	/**
	 *
	 * @var ObjectManager
	 */
	protected $objectManager;

	/**
	 * @var \Reelworx\RxSmoothmigration7\Migrations\AbstractMigrationDefinition
	 */
	protected $parentMigration;

	/**
	 * Inject the issue repository
	 *
	 * @param IssueRepository $issueRepository
	 * @return void
	 */
	public function injectIssueRepository(IssueRepository $issueRepository) {
		$this->issueRepository = $issueRepository;
	}

	/**
	 * Inject the object manager
	 *
	 * @param ObjectManager $objectManager
	 * @return void
	 */
	public function injectObjectManager(ObjectManager $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * When set, try to process experimental migrations as well if any
	 *
	 * @param boolean $experimental
	 * @return void
	 */
	public function setExperimental($experimental) {
		$this->experimental = $experimental;
	}

	/**
	 * @param string $extensionKey
	 *
	 * @return $this to allow for chaining
	 */
	public function setExtensionKey($extensionKey) {
		$this->extensionKey = $extensionKey;

		return $this;
	}

	/**
	 * Set the Message Service
	 *
	 * @param MessageService $messageService
	 * @return void
	 */
	public function setMessageService(MessageService $messageService) {
		$this->messageService = $messageService;
	}

	/**
	 * @param Migration $migration
	 */
	public function __construct(Migration $migration) {
		$this->parentMigration = $migration;
	}

	/**
	 * BUG
	 * This causes a fatal error with PHP 5.3 since the method is already defined in the interface and not further specified here.
	 */
	//abstract public function execute();

	/**
	 * Any issues?
	 *
	 * @return boolean
	 */
	public function hasIssues() {
		if ($this->issues === NULL) {
			$this->getIssues();
		}
		return (count($this->issues) > 0);
	}

	/**
	 * Get all issues
	 *
	 * @return array
	 */
	public function getIssues() {
		if ($this->issues === NULL) {
			if ($this->extensionKey) {
				$this->issues = $this->issueRepository->findByInspectionAndExtensionKey($this->parentMigration->getIdentifier(), $this->extensionKey)->toArray();
			} else {
				$this->issues = $this->issueRepository->findByInspection($this->parentMigration->getIdentifier())->toArray();
			}
		}
		return $this->issues;
	}

	/**
	 * Get pending issues
	 *
	 * @return array
	 */
	public function getPendingIssues() {
		if ($this->issues === NULL) {
			if ($this->extensionKey) {
				$this->issues = $this->issueRepository->findPendingByInspectionAndExtensionKey($this->parentMigration->getIdentifier(), $this->extensionKey)->toArray();
			} else {
				$this->issues = $this->issueRepository->findByInspection($this->parentMigration->getIdentifier())->toArray();
			}
		}
		return $this->issues;
	}

	/**
	 * Shortcut function for fetching language labels
	 *
	 * @param $key
	 * @param $arguments
	 * @return string
	 */
	public function ll($key, $arguments = NULL) {
		return LocalizationUtility::translate($key, 'rx_smoothmigration7', $arguments);
	}
}
