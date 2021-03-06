<?php

########################################################################
# Extension Manager/Repository config file for ext: "pt_gmaps"
#
# Auto generated by Extension Builder 2013-11-22
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = [
	'title' => 'Sitemap Generator using Solr Index',
	'description' => 'Extension for creating sitemaps from solr index queues.',
	'category' => 'plugin',
	'author' => 'Michael Riedel',
	'author_email' => 'riedel@punkt.de',
	'author_company' => 'punkt.de',
	'shy' => '',
	'priority' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => '1',
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'version' => '1.0.1',
	'constraints' => [
		'depends' => [
			'typo3' => '7.6.0-7.6.99',
			'solr' => '5.0.0-5.1.99',
			'pt_extbase' => '2.3.0-2.3.99',
		],
		'conflicts' => [
		],
		'suggests' => [
		],
	],
];
