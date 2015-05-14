#
# Table structure for table 'tx_rxsmoothmigration7_issue'
#
CREATE TABLE tx_rxsmoothmigration7_issue (
	uid int(11) unsigned NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	inspection varchar(255) DEFAULT '' NOT NULL,
	identifier varchar(255) DEFAULT '' NOT NULL,
	extension varchar(255) DEFAULT '' NOT NULL,
	location_info text,
	additional_info text,
	migration_status int(1) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);
