<?php
defined('TYPO3_MODE') || die('Access denied.');

$checkArray = array(
	'Reelworx\\RxSmoothmigration7\\Checks\\Core\\CallToDeprecatedMethods\\Definition',
	'Reelworx\\RxSmoothmigration7\\Checks\\Core\\CallToDeprecatedViewHelpers\\Definition',
	'Reelworx\\RxSmoothmigration7\\Checks\\Core\\MySql\\Definition',
	'Reelworx\\RxSmoothmigration7\\Checks\\Core\\Namespaces\\Definition',
	'Reelworx\\RxSmoothmigration7\\Checks\\Core\\RemovedConstants\\Definition',
	'Reelworx\\RxSmoothmigration7\\Checks\\Extension\\IncompatibleWithLts\\Definition',
	'Reelworx\\RxSmoothmigration7\\Checks\\Extension\\Obsolete\\Definition',
);
\Reelworx\RxSmoothmigration7\Service\Check\Registry::getInstance()->registerChecks($checkArray);

$migrationArray = array(
	'Reelworx\\RxSmoothmigration7\\Migrations\\CallToDeprecatedStaticMethods\\Definition',
	'Reelworx\\RxSmoothmigration7\\Migrations\\Namespaces\\Definition',
	'Reelworx\\RxSmoothmigration7\\Migrations\\MissingAddPluginParameter\\Definition',
);

\Reelworx\RxSmoothmigration7\Service\Migration\Registry::getInstance()->registerMigrations($migrationArray);

$TYPO3_CONF_VARS['SC_OPTIONS']['GLOBAL']['cliKeys']['smoothmigration'] = array(
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('rx_smoothmigration7', 'Classes/Cli/SmoothMigration.php'),
	'_CLI_smoothmigration'
);
