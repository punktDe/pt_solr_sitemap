<?php

namespace PunktDe\PtSolrSitemap\Scheduler;

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
use PunktDe\PtSolrSitemap\SitemapGenerator\SolrSitemap;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Core\Bootstrap;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

class SolrSitemapTask extends AbstractTask
{

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \PunktDe\PtSolrSitemap\SitemapGenerator\SolrSitemap
     */
    protected $solrSitemap;


    /** @var  \ApacheSolrForTypo3\Solr\Site */
    protected $site;

    /**
     * This is the main method that is called when a task is executed
     *
     * @return bool Returns true on successful execution, false on error
     * @throws \Exception If now site is set in task
     */
    public function execute()
    {
        if (is_null($this->site)) {
            throw new \Exception('No site is selected for this task! Check task settings!', 1489588837);
        }

        $this->initializeExtbase();
        $this->initializeObject();

        if ($this->solrSitemap->run()) {
            return true;
        }
        return false;
    }

    /**
     * @return Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param Site $site
     */
    public function setSite(Site $site)
    {
        $this->site = $site;
    }

    /**
     * Additional information to be shown for the task in the scheduler module
     *
     * @return string
     */
    public function getAdditionalInformation()
    {
        return 'Site: ' . ($this->site != null ? $this->site->getLabel() : '');
    }

    /**
     * Initialize Extbase
     *
     * This is necessary to resolve the TypoScript interface definitions
     */
    protected function initializeExtbase() {
        $configuration['extensionName'] = 'PtSolrSitemap';
        $configuration['pluginName'] = 'dummy';
        /** @var \TYPO3\CMS\Extbase\Core\Bootstrap $extbaseBootstrap  */
        $extbaseBootstrap = GeneralUtility::makeInstance(Bootstrap::class);
        $extbaseBootstrap->initialize($configuration);
    }

    /**
     * @return void
     */
    public function initializeObject() {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->solrSitemap = $this->objectManager->get(SolrSitemap::class, $this->site);
    }

}
