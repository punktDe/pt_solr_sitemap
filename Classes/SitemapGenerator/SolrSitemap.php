<?php

namespace PunktDe\PtSolrSitemap\SitemapGenerator;

/***************************************************************
 *  Copyright (C) 2017 punkt.de GmbH
 *  Authors: el_equipo <opiuqe_le@punkt.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use ApacheSolrForTypo3\Solr\Site;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Exception;

class SolrSitemap
{

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     * @inject
     */
    protected $objectManager;

    /**
     * @var \PunktDe\PtExtbase\Logger\Logger
     * @inject
     */
    protected $logger;

    /** @var Site $site */
    protected $site;

    /** @var array $settings */
    protected $settings = [];

    /** @var string $sitemapDirectory */
    protected $sitemapDirectory = 'fileadmin/tx_ptsolrsitemap/';

    /** @var string $absoluteSitemapPath */
    protected $absoluteSitemapPath = '';

    /** @var string $defaultLanguage */
    protected $defaultLanguage = '';

    /**
     * SolrSitemap constructor.
     * @param Site $site
     */
    public function __construct(Site $site)
    {
        $this->site = $site;
        $this->settings = \Tx_PtExtbase_Div::returnTyposcriptSetup($this->site->getRootPageId(), 'plugin.tx_ptsolrsitemap.settings.');

        $this->checkAndCreateSitemapFolder();
    }


    /**
     * @return boolean
     */
    public function run()
    {

        try {
            $this->doGenerateSitemap();
        } catch (Exception $e) {
            $this->logger->critical('Generate Sitemap aborted unexpected: ' . $e->getMessage(), 1489743417);
            return false;
        }
        return true;
    }


    /**
     * @throws \Exception
     */
    protected function doGenerateSitemap()
    {
        $sysLanguagesCores = $this->settings['sys_languages_cores.'];

        if (sizeof($sysLanguagesCores) == 0) {
            throw new \Exception('Missing configuration - no solrcores for sys_languages defined', 1489647267);
        }

        foreach ($sysLanguagesCores as $sysLanguage => $solrCore) {
            /** @var SolrDocumentProvider $solrDocumentProvider */
            $solrDocumentProvider = $this->objectManager->get(SolrDocumentProvider::class, $solrCore);
            if ($solrDocumentProvider instanceof SolrDocumentProvider) {
                $this->writeXmlFile($sysLanguage, $solrDocumentProvider);
            }
        }

    }

    /**
     * @param string $sysLanguage
     * @return null|object
     */
    protected function getXMLWriter($sysLanguage)
    {
        $rootLine = BackendUtility::BEgetRootLine($this->site->getRootPageId());
        $host = BackendUtility::firstDomainRecord($rootLine);
        $filename = $this->absoluteSitemapPath . 'sitemap-' . $host . '-' . $sysLanguage . '.xml';
        try {
            $solrSitemapXmlWriter = $this->objectManager->get(SolrSitemapXmlWriter::class, $filename);
        } catch (Exception $e) {
            $solrSitemapXmlWriter = null;
        }
        return $solrSitemapXmlWriter;
    }


    protected function checkAndCreateSitemapFolder()
    {
        $this->absoluteSitemapPath = PATH_site . $this->sitemapDirectory;
        if (!is_dir($this->absoluteSitemapPath)) {
            if (!mkdir($this->absoluteSitemapPath, 755)) {
                throw new \RuntimeException('Unable to create the directory "' . $this->absoluteSitemapPath . '" for site map.', 1490002938);
            }
        }
        if (!is_writable($this->absoluteSitemapPath)) {
            throw new \RuntimeException('Directory "' . $this->absoluteSitemapPath . '" for site map is unwritable.', 1490002950);
        }
    }

    /**
     * @param string $sysLanguage
     * @param SolrDocumentProvider $solrDocumentProvider
     */
    protected function writeXmlFile($sysLanguage, $solrDocumentProvider)
    {
        $solrSitemapXmlWriter = $this->getXMLWriter($sysLanguage);
        if ($solrSitemapXmlWriter instanceof SolrSitemapXmlWriter) {
            $solrSitemapXmlWriter->startWriting();
            while ($sitemapEntries = $solrDocumentProvider->getNextDocumentEntries()) {
                $solrSitemapXmlWriter->appendSitemapEntries($sitemapEntries);
            }
            $solrSitemapXmlWriter->finalizeWriting();
        }
    }

}
