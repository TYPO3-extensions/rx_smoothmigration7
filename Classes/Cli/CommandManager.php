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

namespace Reelworx\RxSmoothmigration7\Cli;

use Reelworx\RxSmoothmigration7\Service;
use TYPO3\CMS\Extbase\Mvc\Cli\Command;
use TYPO3\CMS\Extbase\Mvc\Exception\AmbiguousCommandIdentifierException;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchCommandException;

/**
 * A helper for CLI commands
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public
 *    License, version 3 or later
 */
class CommandManager extends \TYPO3\CMS\Extbase\Mvc\Cli\CommandManager {

	/**
	 * Returns an array of all commands
	 *
	 * @return Command[][]
	 * @api
	 */
	public function getAvailableCommands() {
		if ($this->availableCommands === NULL) {
			$this->availableCommands = array();

			$checkRegistry = $this->objectManager->get('Reelworx\\RxSmoothmigration7\\Service\\Check\\Registry');
			$checks = $checkRegistry->getActiveChecks();
			foreach ($checks as $command) {
				$commandIdentifier = $command->getIdentifier();
				$this->availableCommands['check'][$commandIdentifier] = $command;
			}

			$migrationRegistry = $this->objectManager->get('Reelworx\\RxSmoothmigration7\\Service\\Migration\\Registry');
			$migrations = $migrationRegistry->getActiveMigrations();
			foreach ($migrations as $command) {
				$commandIdentifier = $command->getIdentifier();
				$this->availableCommands['migration'][$commandIdentifier] = $command;
			}
		}
		return $this->availableCommands;
	}

	/**
	 * Returns a Command that matches the given identifier.
	 * If no Command could be found a CommandNotFoundException is thrown
	 * If more than one Command matches an AmbiguousCommandIdentifierException
	 * is thrown that contains the matched Commands
	 *
	 * @param string $type The type of command
	 * @param string $commandIdentifier command identifier in the format
	 *    foo:bar:baz
	 * @return Command
	 * @throws AmbiguousCommandIdentifierException
	 * @throws NoSuchCommandException
	 * @api
	 */
	public function getCommandByTypeAndIdentifier($type, $commandIdentifier) {
		$commandIdentifier = strtolower(trim($commandIdentifier));
		$type = strtolower(trim($type));

		if ($commandIdentifier === 'help') {
			$commandIdentifier = 'extbase:help:help';
		}
		$matchedCommands = array();
		$availableCommands = $this->getAvailableCommands();
		foreach ($availableCommands as $commandType => $commands) {
			if ($type === $commandType) {
				foreach ($commands as $identifier => $command) {
					if (strtolower($identifier) === $commandIdentifier) {
						$matchedCommands[] = $command;
					}
				}
			}
		}
		if (count($matchedCommands) === 0) {
			throw new NoSuchCommandException('No command could be found that matches the command identifier "' . $commandIdentifier . '".', 1310556663);
		}
		if (count($matchedCommands) > 1) {
			throw new AmbiguousCommandIdentifierException('More than one command matches the command identifier "' . $commandIdentifier . '"', 1310557169, NULL, $matchedCommands);
		}

		return current($matchedCommands);
	}

	/**
	 * Returns TRUE if the specified command identifier matches the identifier
	 * of the specified command. This is the case, if the identifiers are the
	 * same or if at least the last two command parts match (case sensitive).
	 *
	 * @param Command $command
	 * @param string $commandIdentifier command identifier in the format
	 *    foo:bar:baz (all lower case)
	 *
	 * @return boolean TRUE if the specified command identifier matches this
	 *    commands identifier
	 * @author Bastian Waidelich <bastian@typo3.org>
	 */
	protected function commandMatchesIdentifier(Command $command, $commandIdentifier) {
		$commandIdentifierParts = explode(':', $command->getCommandIdentifier());
		$searchedCommandIdentifierParts = explode(':', $commandIdentifier);
		$extensionKey = array_shift($commandIdentifierParts);
		if (count($searchedCommandIdentifierParts) === 3) {
			$searchedExtensionKey = array_shift($searchedCommandIdentifierParts);
			if ($searchedExtensionKey !== $extensionKey) {
				return FALSE;
			}
		}
		if (count($searchedCommandIdentifierParts) !== 2) {
			return FALSE;
		}

		return $searchedCommandIdentifierParts === $commandIdentifierParts;
	}
}
