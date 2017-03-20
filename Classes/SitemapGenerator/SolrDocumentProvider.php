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

class SolrDocumentProvider
{

    /** @var integer $pagerSize */
    private $pagerSize = 50;

    /** @var string $solrcore */
    private $solrcore;

    /** @var integer $resultOffset */
    private $resultOffset = 0;

    /** @var integer $totalResults */
    private $totalResults = 0;


    /**
     * SolrDocumentProvider constructor.
     * @param string $solrcore
     * @param integer $pagerSize
     */
    public function __construct($solrcore, $pagerSize = 50)
    {
        $this->setSolrcore($solrcore);
        $this->setPagerSize($pagerSize);
    }

    /**
     * @return array
     */
    public function getAllDocumentEntries()
    {
        $solrDocumentEntries = [];
        $this->setResultOffset(0);
        while($docs = $this->getNextDocumentEntries()) {
            $solrDocumentEntries = array_merge($solrDocumentEntries, $docs);
        }
        return $solrDocumentEntries;
    }

    /**
     * @return array|null
     */
    public function getNextDocumentEntries()
    {
        $solrDocumentEntries = $this->getNextResultContent($this->solrcore);
        if (sizeof($solrDocumentEntries) <= 0) {
            return null;
        } else {
            return $solrDocumentEntries;
        }
    }

    /**
     * @param string $solrCore
     * @return array
     */
    protected function getNextResultContent($solrCore)
    {
        $solrDocumentEntries = [];
        $this->totalResults = 0;
        $content = file_get_contents('http://localhost:8983/solr/' . $solrCore . '/select?q=*:*&wt=json&rows=' . $this->getPagerSize() . '&start=' . $this->getResultOffset());
        $json = json_decode($content, true);
        if ($json !== null) {
            $this->totalResults = intval($json['response']['numFound']);
            $docs = $json['response']['docs'];
            $this->setResultOffset($this->getResultOffset() + sizeof($docs));
            foreach ($docs as $key => $doc) {
                $solrDocumentEntry['loc'] = $doc['url'];
                $solrDocumentEntry['lastmod'] = $doc['changed'];
                $solrDocumentEntry['priority'] = '1.0';
                $solrDocumentEntries[] = $solrDocumentEntry;
            }
        }
        return $solrDocumentEntries;
    }

    /**
     * @return integer
     */
    public function getPagerSize()
    {
        return $this->pagerSize;
    }

    /**
     * @param integer $pagerSize
     */
    public function setPagerSize($pagerSize)
    {
        $this->pagerSize = $pagerSize;
    }

    /**
     * @return string
     */
    public function getSolrcore()
    {
        return $this->solrcore;
    }

    /**
     * @param string $solrcore
     */
    public function setSolrcore($solrcore)
    {
        $this->solrcore = $solrcore;
    }

    /**
     * @return integer
     */
    public function getResultOffset()
    {
        return $this->resultOffset;
    }

    /**
     * @param integer $resultOffset
     */
    public function setResultOffset($resultOffset)
    {
        $this->resultOffset = $resultOffset;
    }

    /**
     * @return integer
     */
    public function getTotalResults()
    {
        return $this->totalResults;
    }

}
