<?php
defined('TYPO3_MODE') || die('Access denied.');

// avoid that this block is loaded in the frontend or within the upgrade-wizards
if (TYPO3_MODE === 'BE' && !TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_INSTALL) {
	/**
	 * Registers a Backend Module
	 */
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		'rx_smoothmigration7',
		'tools',
		'smoothmigration',
		'after:reports',
		array(
			// An array holding the controller-action-combinations that are accessible
			'Report' => 'checks,show,extension,reportOverview',
			'Ajax' => 'runTest,getResults,clearTestResults'
		),
		array(
			'access' => 'user,group',
			'icon' => 'EXT:rx_smoothmigration7/Resources/Public/Images/ModuleIcon.png',
			'labels' => 'LLL:EXT:rx_smoothmigration7/Resources/Private/Language/locallang_mod.xml'
		)
	);

	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers']['smoothMigration'] = 'Reelworx\\RxSmoothmigration7\\Controller\\SmoothmigrationCommandController';
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_rxsmoothmigration7_issue');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_rxsmoothmigration7_deprecation');
