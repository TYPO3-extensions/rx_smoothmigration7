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

namespace Reelworx\RxSmoothmigration7\Utility;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

/**
 * Class Reelworx\RxSmoothmigration7\Utility\ExtensionUtility
 */
class ExtensionUtility implements SingletonInterface {

	/**
	 * @var null
	 */
	static $installedExtensions = NULL;

	/**
	 * @var null
	 */
	static $loadedExtensions = NULL;

	/**
	 * @var null
	 */
	static $loadedExtensionsFiltered = NULL;

	/**
	 * @var array
	 */
	static $packages = array();

	/**
	 * Current TYPO3 LTS version
	 */
	const CURRENT_LTS_VERSION = '7.2.0';

	/**
	 * Get extensions wich claim to be compatible in their ext_emconf.php
	 *
	 * Note that we are ignoring open ended comatibility here. These are the
	 * cases where the version requirements have a 0.0.0 at the end. This is
	 * because we assume that extension creators that care about compatibilty
	 * will specify the maximum supported version instead of providing a 0.0.0
	 * as upper limit.
	 *
	 * @param string $version The version to check against
	 * @param boolean $ignoreOpenEnd Should we ignore open ended requirements?
	 *
	 * @return array Array of compatible extension keys and their version ranges
	 */
	public static function getCompatibleExtensions($version = NULL, $ignoreOpenEnd = TRUE) {
		if ($version === NULL) {
			$version = self::CURRENT_LTS_VERSION;
		}
		$compatibleExtensions = array();
		$list = self::getInstalledExtensions(TRUE);
		foreach ($list as $extensionName => $extensionData) {
			if (isset($extensionData['EM_CONF']['constraints']['depends']['typo3'])) {
				$versionRange = self::splitVersionRange($extensionData);
				if ((bool)$ignoreOpenEnd) {
					$upperBound = $versionRange[1] !== '0.0.0' && version_compare($version, $versionRange[1], '<=');
				} else {
					$upperBound = $versionRange[1] === '0.0.0' || version_compare($version, $versionRange[1], '<=');
				}
				if (($versionRange[0] === '0.0.0' || version_compare($version, $versionRange[0], '>')) && $upperBound) {
					$compatibleExtensions[$extensionName] = $versionRange;
				}
			}
		}
		return $compatibleExtensions;
	}

	/**
	 * Get extensions wich do not claim to be compatible in their ext_emconf.php
	 *
	 * Note that we are ignoring open ended comatibility here. These are the
	 * cases where the version requirements have a 0.0.0 at the end. This is
	 * because we assume that extension creators that care about compatibilty
	 * will specify the maximum supported version instead of providing a 0.0.0
	 * as upper limit.
	 *
	 * @param string $version The version to check against
	 * @param boolean $ignoreOpenEnd Should we ignore open ended requirements?
	 *
	 * @return array Array of compatible extension keys and their version ranges
	 */
	public static function getIncompatibleExtensions($version = NULL, $ignoreOpenEnd = TRUE) {
		if ($version === NULL) {
			$version = self::CURRENT_LTS_VERSION;
		}
		$extensions = array();
		$list = self::getInstalledExtensions(TRUE);
		foreach ($list as $extensionName => $extensionData) {
//			list($extensionName, $extensionData) = self::getExtensionNameAndConfigurationData($extensionName, $extensionData);
			if (is_array($extensionData) && isset($extensionData['EM_CONF']['constraints']['depends']['typo3'])) {
				$versionRange = self::splitVersionRange($extensionData);
				if ((bool)$ignoreOpenEnd) {
					$upperBound = $versionRange[1] !== '0.0.0' && version_compare($version, $versionRange[1], '>');
				} else {
					$upperBound = $versionRange[1] === '0.0.0' || version_compare($version, $versionRange[1], '>');
				}
				if (($versionRange[0] !== '0.0.0' && version_compare($version, $versionRange[0], '<')) || $upperBound) {
					$extensions[$extensionName] = $versionRange;
				}
			}
		}
		return $extensions;
	}

	/**
	 * Get extensions that have category plugin or category fe
	 *
	 * @param bool $onlyKeys If true, only the extension keys are returned.
	 * @return array Array of frontend extension keys
	 */
	public static function getFrontendExtensions($onlyKeys = TRUE) {
		$extensions = array();
		foreach (self::getPackages() as $extensionName => $extensionData) {
			if (isset($extensionData['EM_CONF']['category'])) {
				if ((trim($extensionData['EM_CONF']['category']) === 'plugin') ||
					(trim($extensionData['EM_CONF']['category']) === 'fe')
				) {
					if ($onlyKeys) {
						array_push($extensions, $extensionName);
					} else {
						$extensions[$extensionName] = $extensionData;
					}
				}
			}
		}

		return $extensions;
	}

	/**
	 * Get a list of installed extensions
	 *
	 * @param bool $returnExtensionData
	 * @return array of installed extensions
	 */
	public static function getInstalledExtensions($returnExtensionData = FALSE) {
		if (self::$installedExtensions !== NULL) {
			return $returnExtensionData ? self::$installedExtensions : array_keys(self::$installedExtensions);
		}
		self::$installedExtensions = self::getPackages();
		return $returnExtensionData ? self::$installedExtensions : array_keys(self::$installedExtensions);
	}

	/**
	 * @param string $type
	 * @return array
	 * @throws \TYPO3\Flow\Package\Exception\InvalidPackageStateException
	 */
	public function getPackages($type = 'available') {
		$type = strtolower($type);
		$objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		$packageManager = $objectManager->get('TYPO3\\CMS\\Core\\Package\\PackageManager');
		$packages = $packageManager->getFilteredPackages($type);
		//get $EM_CONF array, the EM_CONF data is to encapsulated to get (\TYPO3\CMS\Core\Package\Package::getExtensionEmconf)
		/**@var $package \TYPO3\CMS\Core\Package\Package */
		foreach ($packages as $packageName => $package) {
			$_EXTKEY = $packageName;
			if ($_EXTKEY) {
				include($package->getPackagePath() . '/ext_emconf.php');
				$GLOBALS['TYPO3_LOADED_EXT'][$packageName]['type'] = strpos($package->getPackagePath(), 'typo3/sysext/')
					? 'S' : 'L';
				self::$packages[$type][$packageName] = array('EM_CONF' => $EM_CONF[$_EXTKEY]);
			}
		}
		return self::$packages[$type];
	}

	/**
	 * Get a list of loaded / active extensions
	 *
	 * @param bool $returnExtensionData
	 * @return array Array of installed
	 */
	public static function getLoadedExtensions($returnExtensionData = FALSE) {
		if (self::$loadedExtensions !== NULL) {
			return $returnExtensionData ? self::$loadedExtensions : array_keys(self::$loadedExtensions);
		}
		self::$loadedExtensions = self::getPackages('active');
		return $returnExtensionData ? self::$loadedExtensions : array_keys(self::$loadedExtensions);
	}

	/**
	 * Get a filtered list of loaded / active extensions
	 *
	 * Compatible extensions can be filtered out.
	 * Ignored extensions can be filtered out.
	 * System extensions can be filtered out.
	 * Smoothmigration is filtered out.
	 *
	 * @param bool $removeCompatible
	 * @param bool $removeIgnored
	 * @param bool $removeSystem
	 *
	 * @return array Array of installed
	 */
	public static function getLoadedExtensionsFiltered(
		$removeCompatible = TRUE,
		$removeIgnored = TRUE,
		$removeSystem = TRUE
	) {
		if (self::$loadedExtensionsFiltered !== NULL) {
			return self::$loadedExtensionsFiltered;
		}

		// get extension configuration
		$configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['rx_smoothmigration7']);

		if (isset($configuration['includeInactiveExtensions']) &&
			intval($configuration['includeInactiveExtensions']) > 0
		) {
			$loadedExtensionsFiltered = self::getInstalledExtensions();
		} else {
			$loadedExtensionsFiltered = self::getLoadedExtensions();
		}
		$loadedExtensionsFiltered = array_flip($loadedExtensionsFiltered);
		unset($loadedExtensionsFiltered['rx_smoothmigration7']);

		if ($removeCompatible) {
			if (isset($configuration['excludeCompatibleExtensions']) &&
				intval($configuration['excludeCompatibleExtensions']) > 0
			) {
				$compatibleExtensions = ExtensionUtility::getCompatibleExtensions('7.2.0');
				foreach ($compatibleExtensions as $key => $_) {
					unset($loadedExtensionsFiltered[$key]);
				}
			}
		}
		if ($removeIgnored) {
			if (isset($configuration['excludedExtensions']) &&
				trim($configuration['excludedExtensions']) !== ''
			) {
				$ingoreExtensions = explode(',', str_replace(' ', '', $configuration['excludedExtensions']));
				foreach ($ingoreExtensions as $key) {
					unset($loadedExtensionsFiltered[$key]);
				}
			}
		}
		if ($removeSystem) {
			foreach ($loadedExtensionsFiltered as $key => $_) {
				if ($GLOBALS['TYPO3_LOADED_EXT'][$key]['type'] === 'S') {
					unset($loadedExtensionsFiltered[$key]);
				}
			}
		}
		$loadedExtensionsFiltered = array_flip($loadedExtensionsFiltered);
		self::$loadedExtensionsFiltered = $loadedExtensionsFiltered;

		return self::$loadedExtensionsFiltered;
	}

	/**
	 * Get extensions that are marked as obsolete in their ext_emconf.php
	 *
	 * @param bool $onlyKeys If true, only the extension keys are returned.
	 *
	 * @return array Array of obsolete extension keys
	 */
	public static function getObsoleteExtensions($onlyKeys = TRUE) {
		$extensions = array();
		$list = self::getInstalledExtensions(TRUE);
		foreach ($list as $extensionName => $extensionData) {
			if (isset($extensionData['EM_CONF']['state'])) {
				if (trim($extensionData['EM_CONF']['state']) === 'obsolete') {
					if ($onlyKeys) {
						array_push($extensions, $extensionName);
					} else {
						$extensions[$extensionName] = $extensionData;
					}
				}
			}
		}

		return $extensions;
	}

	/**
	 * If $extensionDataOrKey array, then return. Else get the $EM_CONF array from ext_emconf.php from the key.
	 *
	 * @param $extensionName
	 * @param mixed $extensionDataOrKey
	 * @return array
	 */
	public static function getExtensionNameAndConfigurationData($extensionName, $extensionDataOrKey) {
		$_EXTKEY = $extensionDataOrKey;
		$extensionConfigurationData = '';
		if ($_EXTKEY) {
			$extensionAbsolutePath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY);
			include($extensionAbsolutePath . '/ext_emconf.php');
			$extensionConfigurationData = array('EM_CONF' => $EM_CONF[$_EXTKEY]);
		}
		return array($_EXTKEY, $extensionConfigurationData);
	}

	/**
	 * @param $extensionData
	 * @return array
	 */
	public function splitVersionRange($extensionData) {
		return VersionNumberUtility::splitVersionRange($extensionData['EM_CONF']['constraints']['depends']['typo3']);
	}

}
