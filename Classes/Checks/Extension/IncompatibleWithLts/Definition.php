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

use Reelworx\RxSmoothmigration7\Checks\AbstractCheckDefinition;
use Reelworx\RxSmoothmigration7\Domain\Interfaces\CheckProcessor;
use Reelworx\RxSmoothmigration7\Domain\Interfaces\CheckResultAnalyzer;

/**
 * Class Definition
 *
 * Checks for extensions that don't claim to be compatible with the current LTS
 * version in their ext_emconf.php files.
 *
 * @author Michiel Roos
 */
class Definition extends AbstractCheckDefinition {

	/**
	 * @return CheckProcessor
	 */
	public function getProcessor() {
		return $this->objectManager->get('Reelworx\\RxSmoothmigration7\\Checks\\Extension\\IncompatibleWithLts\\Processor', $this);
	}

	/**
	 * @return CheckResultAnalyzer
	 */
	public function getResultAnalyzer() {
		return $this->objectManager->get('Reelworx\\RxSmoothmigration7\\Checks\\Extension\\IncompatibleWithLts\\ResultAnalyzer', $this);
	}

	/**
	 * Returns an CheckIdentifier
	 * Has to be unique
	 *
	 * @return string
	 */
	public function getIdentifier() {
		return 'typo3-extension-code-incompatiblewithlts';
	}

}
