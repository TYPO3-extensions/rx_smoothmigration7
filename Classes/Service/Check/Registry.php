<?php

namespace Reelworx\RxSmoothmigration7\Service\Check;

use Reelworx\RxSmoothmigration7\Service\RequirementsAnalyzer;
use Reelworx\RxSmoothmigration7\Domain\Interfaces\Check;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;


/**
 * Class CheckRegistry
 */
class Registry implements SingletonInterface {

	/**
	 * @var array
	 */
	protected $registeredChecks = array();


	/**
	 * @param string $className
	 *
	 * @return void
	 */
	public function registerCheck($className) {
		if (class_exists($className) && in_array('Reelworx\\RxSmoothmigration7\\Domain\\Interfaces\\Check', class_implements($className))) {
			$this->registeredChecks[] = $className;
		}
	}

	/**
	 * @param array $classNames
	 *
	 * @return void
	 */
	public function registerChecks(array $classNames) {
		foreach ($classNames as $className) {
			$this->registerCheck($className);
		}
	}

	/**
	 * Returns Instances of all registered checks which apply to this instance.
	 *
	 * @return \Reelworx\RxSmoothmigration7\Domain\Interfaces\Check[]
	 */
	public function getActiveChecks() {
		$activeChecks = array();
		/** @var RequirementsAnalyzer $requirementsAnalyzer */
		$requirementsAnalyzer = GeneralUtility::makeInstance('Reelworx\\RxSmoothmigration7\\Service\\RequirementsAnalyzer');

		foreach ($this->registeredChecks as $className) {
			/** @var \Reelworx\RxSmoothmigration7\Domain\Interfaces\Check $check */
			$check = GeneralUtility::makeInstance($className);
			if ($requirementsAnalyzer->isActive($check)) {
				$activeChecks[] = $check;
			}
		}

		return $activeChecks;
	}

	/**
	 * @param $searchedIdentifier
	 *
	 * @return NULLl|\Reelworx\RxSmoothmigration7\Domain\Interfaces\Check
	 */
	public function getActiveCheckByIdentifier($searchedIdentifier) {
		$checks = $this->getActiveChecks();
		foreach ($checks as $check) {
			if ($check->getIdentifier() == $searchedIdentifier) {
				return $check;
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
