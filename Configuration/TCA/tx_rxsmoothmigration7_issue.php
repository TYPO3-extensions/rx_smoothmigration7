<?php

return array(
	'ctrl' => array(
		'title' => 'Recognized upgrade issues',
		'default_sortby' => 'ORDER BY extension, inspection',
		'label_userFunc' => 'Reelworx\\RxSmoothmigration7\\ExtbaseTeam\BlogExample\Domain\Model\IssueLocation\Database\\DatabaseLabels->issueTitle',
		'iconfile' => 'EXT:rx_smoothmigration7/ext_icon.png'
	),
	'interface' => array(
		'showRecordFieldList' => 'inspection, identifier, extension, location_info, additional_info, migration_status',
	),
	'types' => array(
		1 => array('showitem' => 'inspection, identifier, extension, location_info, additional_info, migration_status')
	),
	'columns' => array(
		'inspection' => array(
			'label' => 'inspection',
			'config' => array(
				'type' => 'input',
				'size' => '40',
				'max' => '256',
			)
		),
		'identifier' => array(
			'label' => 'issue_identifier',
			'config' => array(
				'type' => 'input',
				'size' => '40',
				'max' => '256',
			)
		),
		'extension' => array(
			'label' => 'extension',
			'config' => array(
				'type' => 'input',
				'size' => '40',
				'max' => '256',
			)
		),
		'location_info' => array(
			'label' => 'location',
			'config' => array(
				'type' => 'text',
				'cols' => '40',
				'rows' => '3'
			)
		),
		'additional_info' => array(
			'label' => ' additional information',
			'config' => array(
				'type' => 'text',
				'cols' => '40',
				'rows' => '3'
			)
		),
		'migration_status' => array(
			'label' => 'Reelworx\RxSmoothmigration7\Domain\Interfaces\Migration status',
			'config' => array(
				'type' => 'input',
				'cols' => '40',
				'max' => '1',
			),
		),
	),
);
