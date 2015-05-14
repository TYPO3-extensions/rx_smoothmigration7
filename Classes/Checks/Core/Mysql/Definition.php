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

/**
 * Class Tx_Smoothmigration_Checks_Core_Mysql_Definition
 *
 * @author Peter Beernink
 */
class Tx_Smoothmigration_Checks_Core_Mysql_Definition extends Tx_Smoothmigration_Checks_AbstractCheckDefinition {

	/**
	 * @return Tx_Smoothmigration_Domain_Interface_CheckProcessor
	 */
	public function getProcessor() {
		return $this->getObjectManager()->get('Tx_Smoothmigration_Checks_Core_Mysql_Processor', $this);
	}

	/**
	 * @return Tx_Smoothmigration_Domain_Interface_CheckResultAnalyzer
	 */
	public function getResultAnalyzer() {
		return $this->getObjectManager()->get('Tx_Smoothmigration_Checks_Core_Mysql_ResultAnalyzer', $this);
	}

	/**
	 * Returns an CheckIdentifier
	 * Has to be unique
	 *
	 * @return string
	 */
	public function getIdentifier() {
		return 'typo3-core-code-mysql';
	}

}