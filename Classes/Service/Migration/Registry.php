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

namespace Reelworx\RxSmoothmigration7\Service\Migration;
use Reelworx\RxSmoothmigration7\Domain\Interfaces\Migration;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class Registry
 *
 * @author Peter Beernink
 */
class Registry implements SingletonInterface {

	/**
	 * @var array
	 */
	protected $registeredMigrations = array();

	/**
	 * Array with active migrations
	 *
	 * @var array
	 */
	protected $activeMigrations = NULL;


	/**
	 * @param string $className
	 * @return void
	 */
	public function registerMigration($className) {
		if (class_exists($className) && in_array('Reelworx\\RxSmoothmigration7\\Domain\\Interfaces\\Migration', class_implements($className))) {
			$this->registeredMigrations[] = $className;
		}
	}


	/**
	 * @param array $classNames
	 * @return void
	 */
	public function registerMigrations(array $classNames) {
		foreach ($classNames as $className) {
			$this->registerMigration($className);
		}
	}

	/**
	 * Returns Instances of all registered migrations which apply to this instance.
	 *
	 * @return Migration[]
	 */
	public function getActiveMigrations() {
		if (!is_array($this->activeMigrations)) {
			$this->activeMigrations = array();
			$requirementsAnalyzer = GeneralUtility::makeInstance('Reelworx\\RxSmoothmigration7\\Service\\RequirementsAnalyzer');

			foreach ($this->registeredMigrations as $className) {
				/** @var Migration $check */
				$migration = GeneralUtility::makeInstance($className);
				if ($requirementsAnalyzer->isActive($migration)) {
					$this->activeMigrations[] = $migration;
				}
			}
		}
		return $this->activeMigrations;
	}

	/**
	 * @param $searchedIdentifier
	 *
	 * @return null|\Reelworx\RxSmoothmigration7\Domain\Interfaces\Migration
	 */
	public function getActiveMigrationByIdentifier($searchedIdentifier) {
		$migrations = $this->getActiveMigrations();
		foreach ($migrations as $migration) {
			if ($migration->getIdentifier() == $searchedIdentifier) {
				return $migration;
			}
		}
		return NULL;
	}

	/**
	 * @param $cliKey
	 *
	 * @return null|\Reelworx\RxSmoothmigration7\Domain\Interfaces\Migration
	 */
	public function getActiveMigrationByCliKey($cliKey) {
		$migrations = $this->getActiveMigrations();
		foreach ($migrations as $migration) {
			if ($migration->getCliKey() == $cliKey) {
				return $migration;
			}
		}
		return NULL;
	}

	/**
	 * @return Registry
	 */
	public static function getInstance() {
		return GeneralUtility::makeInstance(__CLASS__);
	}
}

