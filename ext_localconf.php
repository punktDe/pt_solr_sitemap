<?php
defined('TYPO3_MODE') || die ('Access denied.');

// Sitemap scheduler task
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['PunktDe\\PtSolrSitemap\\Scheduler\\SolrSitemapTask'] = [
    'extension'   => $_EXTKEY,
    'title'       => 'Sitemap Builder using Solr Index',
    'description' => 'Build XML sitemap as static files (in fileadmin/tx_ptsolrsitemap/)',
    'additionalFields' => 'PunktDe\\PtSolrSitemap\\Scheduler\\SolrSitemapTaskAdditionalFieldProvider'
];

