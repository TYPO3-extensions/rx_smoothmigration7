<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Michiel Roos <michiel@maxserv.nl>
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
namespace Reelworx\RxSmoothmigration7\Checks\Core\RemovedConstants;

use Reelworx\RxSmoothmigration7\Checks\AbstractCheckDefinition;
use Reelworx\RxSmoothmigration7\Domain\Interfaces\CheckProcessor;
use Reelworx\RxSmoothmigration7\Domain\Interfaces\CheckResultAnalyzer;

/**
 * Class Reelworx\RxSmoothmigration7\Checks\Core\RemovedConstants\Definition
 *
 * @author Michiel Roos
 */
class Definition extends AbstractCheckDefinition {

	/**
	 * @return CheckProcessor
	 */
	public function getProcessor() {
		return $this->objectManager->get('Reelworx\\RxSmoothmigration7\\Checks\\Core\\RemovedConstants\\Processor', $this);
	}

	/**
	 * @return CheckResultAnalyzer
	 */
	public function getResultAnalyzer() {
		return $this->objectManager->get('Reelworx\\RxSmoothmigration7\\Checks\\Core\\RemovedConstants\\ResultAnalyzer', $this);
	}

	/**
	 * Returns an CheckIdentifier
	 * Has to be unique
	 *
	 * @return string
	 */
	public function getIdentifier() {
		return 'typo3-core-code-removedConstants';
	}

}
