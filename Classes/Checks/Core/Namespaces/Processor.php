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
namespace Reelworx\RxSmoothmigration7\Checks\Core\Namespaces;

use Reelworx\RxSmoothmigration7\Checks\AbstractCheckProcessor;
use Reelworx\RxSmoothmigration7\Domain\Model\Issue;
use Reelworx\RxSmoothmigration7\Utility\FileLocatorUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class Reelworx\RxSmoothmigration7\Checks\Core\Namespaces\Definition
 *
 * @author Michiel Roos
 */
class Processor extends AbstractCheckProcessor {

	/**
	 * Execute the check
	 *
	 * The regex can become quite large. We try to reduce the size by making the
	 * keys unique and splitting the array in several parts.
	 *
	 * @return void
	 */
	public function execute() {
		$classAliasProvider = GeneralUtility::makeInstance('Reelworx\\RxSmoothmigration7\\Service\\ClassAliasProvider');

		$legacyClasses = array_keys($classAliasProvider->getLegacyClasses());
		$classAaliases = array_keys($classAliasProvider->getClassAliasMap());

		$classes = array_unique(array_merge($legacyClasses, $classAaliases), SORT_REGULAR);

		$count = count($classes);

		$classChunks = array_chunk($classes, $count / 4);

		if ($this->getExtensionKey()) {
			foreach ($classChunks as $chunk) {
				$locations = FileLocatorUtility::searchInExtension(
					$this->getExtensionKey(),
					'.*\.(php|inc)$',
					'(?:^|\s+|[^\/\.a-zA-Z0-9_]+)(' . implode('|', $chunk) . ')(?:[^\/\.a-zA-Z0-9_]+)'
				);
				foreach ($locations as $location) {
					$this->issues[] = new Issue($this->parentCheck->getIdentifier(), $location);
				}
			}
		} else {
			foreach ($classChunks as $chunk) {
				$locations = FileLocatorUtility::searchInExtensions(
					'.*\.(php|inc)$',
					'(?:^|\s+|[^\/\.a-zA-Z0-9_]+)(' . implode('|', $chunk) . ')(?:[^\/\.a-zA-Z0-9_]+)'
				);
				foreach ($locations as $location) {
					$this->issues[] = new Issue($this->parentCheck->getIdentifier(), $location);
				}
			}
		}
	}
}
