<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Steffen Ritter, rs websystems <steffen.ritter@typo3.org>
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

namespace Reelworx\RxSmoothmigration7\Utility;

use Reelworx\RxSmoothmigration7\Domain\Interfaces\IssueLocation;
use Reelworx\RxSmoothmigration7\Domain\Model\IssueLocation\File;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class Reelworx\RxSmoothmigration7\Utility\FileLocatorUtility
 */
class FileLocatorUtility implements SingletonInterface {

	/**
	 * Current TYPO3 LTS version
	 */
	const CURRENT_LTS_VERSION = '7.2.0';

	/**
	 * @param string $searchPattern
	 * @param string $haystackFilePath
	 *
	 * @return array
	 */
	public static function findLineNumbersOfStringInPhpFile($searchPattern, $haystackFilePath) {
		$positions = array();
		foreach (new \SplFileObject($haystackFilePath) as $lineNumber => $lineContent) {
			$matches = array();
			if (preg_match('/' . trim($searchPattern, '/') . '/i', $lineContent, $matches)) {
				$positions[] = array(
					'line' => $lineNumber + 1,
					'match' => $matches[1]
				);
			}
		}

		return $positions;
	}

	/**
	 * @param string $fileNamePattern
	 * @param string $searchPattern
	 * @param array $excludedExtensions
	 *
	 * @return IssueLocation[]
	 */
	public static function searchInExtensions($fileNamePattern, $searchPattern, $excludedExtensions = array()) {
		$locations = array();

		$loadedExtensions = ExtensionUtility::getLoadedExtensionsFiltered();

		foreach ($loadedExtensions as $extensionKey) {
			if (in_array($extensionKey, $excludedExtensions)) {
				continue;
			}
			$locations = array_merge(self::searchInExtension($extensionKey, $fileNamePattern, $searchPattern), $locations);
		}

		return $locations;
	}

	/**
	 * @param string $extensionKey
	 * @param string $fileNamePattern
	 * @param string $searchPattern
	 *
	 * @return IssueLocation[]
	 *
	 */
	public static function searchInExtension($extensionKey, $fileNamePattern, $searchPattern) {
		$objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		$packageManager = $objectManager->get('TYPO3\\CMS\\Core\\Package\\PackageManager');
		$pathToExtensionFolder = $packageManager->getPackage($extensionKey)->getPackagePath();

		$extensionIterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($pathToExtensionFolder));
		$regularExpressionIterator = new \RegexIterator($extensionIterator, '/' . trim($fileNamePattern, '/') . '/');

		$positions = array();
		foreach ($regularExpressionIterator as $fileInfo) {
			$locations = self::findLineNumbersOfStringInPhpFile($searchPattern, $fileInfo->getPathname());

			foreach ($locations as $location) {
				$positions[] = new File($extensionKey, str_replace(PATH_site, '', $fileInfo->getPathname()), $location['line'], $location['match']);
			}
		}

		return $positions;
	}
}
