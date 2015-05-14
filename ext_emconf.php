<?php

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Smooth migration report for upgrading TYPO3 CMS 6.2 to 7',
	'description' => 'The module analyses your current setup, extensions and configuration in regard to features, functions and configuration, which have been removed or changed since the release of TYPO3 CMS 6.2 LTS.',
	'category' => 'be',
	'author' => 'Markus Klein',
	'author_email' => 'markus.klein@typo3.org',
	'state' => 'alpha',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 1,
	'author_company' => 'Reelworx GmbH',
	'version' => '1.0.0',
	'constraints' => array(
		'depends' => array(
			'typo3' => '6.2.12-7.2.99',
			'php' => '5.3.0-5.6.99'
		),
		'conflicts' => array(),
		'suggests' => array(),
	),
);
