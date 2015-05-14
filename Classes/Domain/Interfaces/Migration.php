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
namespace Reelworx\RxSmoothmigration7\Domain\Interfaces;

use TYPO3\CMS\Core\SingletonInterface;

/**
 * Interface Reelworx\RxSmoothmigration7\Domain\Interfaces\MigrationProcessor
 *
 * @author Michiel Roos
 */
interface Migration extends MigrationDescription, MigrationRequirements, SingletonInterface {

	const SUCCESS = 1;
	const ERROR_FILE_NOT_FOUND = 2;
	const ERROR_FILE_NOT_WRITABLE = 3;
	const ERROR_FILE_NOT_CHANGED = 4;

	/**
	 * @return MigrationProcessor
	 */
	public function getProcessor();

	/**
	 * @return MigrationResultAnalyzer
	 */
	public function getResultAnalyzer();
}
