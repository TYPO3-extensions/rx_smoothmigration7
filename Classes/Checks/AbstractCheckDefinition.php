<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Steffen Ritter, rs websystems (steffen.ritter@typo3.org)
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

namespace Reelworx\RxSmoothmigration7\Checks;

use Reelworx\RxSmoothmigration7\Domain\Interfaces\Check;
use Reelworx\RxSmoothmigration7\Domain\Interfaces\CheckDescription;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Lang\LanguageService;

/**
 * Class Reelworx\RxSmoothmigration7\Checks\AbstractCheckDefinition
 *
 * @author Steffen Ritter
 */
abstract class AbstractCheckDefinition implements Check {

	/**
	 *
	 * @var ObjectManager
	 */
	protected $objectManager;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
	}

	/**
	 * Return the minimum TYPO3 Version the checks needs to be executed on.
	 *
	 * @return string
	 */
	public function getMinimalTypo3Version() {
		return '';
	}

	/**
	 * Return the maximal TYPO3 Version the checks needs to be executed on.
	 *
	 * @return string
	 */
	public function getMaximalTypo3Version() {
		return '';
	}

	/**
	 * Return the minimum PHP Version the checks needs to be executed on.
	 *
	 * @return string
	 */
	public function getMinimalPhpVersion() {
		return '';
	}

	/**
	 * Return the maximal PHP Version the checks needs to be executed on.
	 *
	 * @return string
	 */
	public function getMaximalPhpVersion() {
		return '';
	}

	/**
	 * Returns an array of php-modules, which need to be available
	 * to activate this check;
	 *
	 * Empty array is returned if no special modules are needed
	 *
	 * @return string[]
	 */
	public function getRequiredAvailablePhpModules() {
		return array();
	}

	/**
	 * Returns an array of php-modules, which need to be absent
	 * to activate this check
	 *
	 * Empty array is returned if there are no missing php-modules
	 *
	 * @return string[]
	 */
	public function getRequiredAbsentPhpModules() {
		return array();
	}

	/**
	 * Returns an array of Extensions which are required by this check.
	 *
	 * The array may either contain the extension-key as array key while a
	 * a version-range (x.x.x-y.y.y)is provided as value or only the extension-key
	 * as value which then will just check for presence of the given extension.
	 *
	 * @return array
	 */
	public function getRequiredExtensions() {
		return array();
	}

	/**
	 * Returns an array of Extensions which make this check either obsolete,
	 * or this check is incompatible to.
	 *
	 * @return array
	 */
	public function getConflictingExtensions() {
		return array();
	}

	/**
	 * Returns the name of the check
	 *
	 * @return string
	 */
	public function getTitle() {
		return $this->getLanguageLabelForCheck('title');
	}

	/**
	 * Returns the Type of the Check
	 * One of the Constants Defined in that interface
	 *
	 * @return int
	 */
	public function getType() {
		return CheckDescription::TYPE_PHP_CODE;
	}

	/**
	 * Returns a string which describes the check in one sentence.
	 *
	 * @return string
	 */
	public function getShortDescription() {
		return $this->getLanguageLabelForCheck('shortDescription');
	}

	/**
	 * Returns a string which contains an elaborate description
	 * of what the check does.
	 *
	 * @return string
	 */
	public function getDescription() {
		return $this->getLanguageLabelForCheck('description');
	}

	/**
	 * Get language label for check
	 *
	 * @param $field
	 *
	 * @return mixed
	 */
	protected function getLanguageLabelForCheck($field) {
		$classParts = explode('_', __CLASS__);
		$extensionName = strtolower($classParts[1]);
		/** @var LanguageService $lang */
		$lang = $GLOBALS['LANG'];
		return $lang->sL(
			'LLL:EXT:' . $extensionName . '/Resources/Private/Language/locallang.xml:check.' . $this->getIdentifier() . '.' . $field
		);
	}

	/**
	 * Get objectManager
	 *
	 * @return ObjectManager
	 */
	public function getObjectManager() {
		return $this->objectManager;
	}

}
