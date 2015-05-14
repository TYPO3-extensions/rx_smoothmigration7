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
namespace Reelworx\RxSmoothmigration7\Checks\Core\CallToDeprecatedMethods;

use Reelworx\RxSmoothmigration7\Checks\AbstractCheckResultAnalyzer;
use Reelworx\RxSmoothmigration7\Domain\Model\Issue;

/**
 * Class Reelworx\RxSmoothmigration7\Checks\Core\CallToDeprecatedMethods\ResultAnalyzer
 *
 * @author Peter Beernink
 */
class ResultAnalyzer extends AbstractCheckResultAnalyzer {

	/**
	 * @param Issue $issue
	 *
	 * @return string
	 */
	public function getExplanation(Issue $issue) {
		return $this->ll('result.typo3-core-code-callToDeprecatedStaticMethods.explanation');
	}

	/**
	 * @param Issue $issue
	 *
	 * @return string
	 */
	public function getSolution(Issue $issue) {
		return $this->ll(
			'result.typo3-core-code-callToDeprecatedStaticMethods.solution',
			array(
				$issue->getLocation()->getMatchedString(),
				$issue->getLocation()->getFilePath(),
				$issue->getLocation()->getLineNumber()
			)
		);
	}
}

