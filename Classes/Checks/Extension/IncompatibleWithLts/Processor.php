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
namespace Reelworx\RxSmoothmigration7\Checks\Extension\IncompatibleWithLts;

use Reelworx\RxSmoothmigration7\Domain\Model\IssueLocation\Extension;
use Reelworx\RxSmoothmigration7\Checks\AbstractCheckProcessor;
use Reelworx\RxSmoothmigration7\Domain\Model\Issue;
use Reelworx\RxSmoothmigration7\Utility\ExtensionUtility;

/**
 * Class Definition
 *
 * @author Michiel Roos
 */
class Processor extends AbstractCheckProcessor {

	/**
	 * Execute the check
	 *
	 * @return void
	 */
	public function execute() {
		$extensions = ExtensionUtility::getIncompatibleExtensions('7.2.0');
		foreach ($extensions as $extension => $versionRange) {
			$location = new Extension($extension, $versionRange[0], $versionRange[1]);
			$this->issues[] = new Issue($this->parentCheck->getIdentifier(), $location);
		}
	}
}
