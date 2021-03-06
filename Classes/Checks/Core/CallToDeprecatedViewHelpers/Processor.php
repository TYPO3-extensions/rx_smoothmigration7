<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Peter Beernink, Drecomm (p.beernink@drecomm.nl)
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
namespace Reelworx\RxSmoothmigration7\Checks\Core\CallToDeprecatedViewHelpers;

use Reelworx\RxSmoothmigration7\Checks\AbstractCheckProcessor;
use Reelworx\RxSmoothmigration7\Domain\Model\Issue;
use Reelworx\RxSmoothmigration7\Utility\FileLocatorUtility;

/**
 * Class Reelworx\RxSmoothmigration7\Checks\Core\CallToDeprecatedViewHelpers\Definition
 *
 * @author Peter Beernink
 */
class Processor extends AbstractCheckProcessor {

	/**
	 * Array of all deprecated viewHelpers
	 *
	 * @var array
	 */
	protected $viewHelpers = array(
		'escape',
		'form.errors',
		'form.textbox',
		'renderflashmessages',
	);

	/**
	 * Execute the check
	 *
	 * @return void
	 */
	public function execute() {
		if ($this->getExtensionKey()) {
			$locations = FileLocatorUtility::searchInExtension(
				$this->getExtensionKey(),
				'.*\.(html)$',
				$this->generateRegularExpression()
			);
		} else {
			$locations = FileLocatorUtility::searchInExtensions(
				'.*\.(html)$',
				$this->generateRegularExpression()
			);
		}
		foreach ($locations as $location) {
			$this->issues[] = new Issue($this->parentCheck->getIdentifier(), $location);
		}
	}

	/**
	 * Generate a regular expression to search for all deprecated static calls
	 */
	protected function generateRegularExpression() {
		$regularExpression = array();
		foreach ($this->viewHelpers as $viewHelper) {
			$regularExpression[] = '<f\:' . $viewHelper;
		}
		return '(' . implode('|', $regularExpression) . ')';
	}

}

