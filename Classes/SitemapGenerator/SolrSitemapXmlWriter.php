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

use TYPO3\CMS\Core\Utility\GeneralUtility;

class SolrSitemapXmlWriter
{

    /** @var string $xmlFilename */
    private $xmlFilename;


    /**
     * XmlWriter constructor.
     */
    public function __construct($xmlFilename)
    {
        $this->xmlFilename = $xmlFilename;
    }


    public function startWriting()
    {
        $output = '<?xml version = "1.0" encoding = "UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        if (file_exists($this->xmlFilename)) {
            unlink($this->xmlFilename);
        }
        if (file_put_contents($this->xmlFilename, $output) === false) {
            throw new \Exception('File "' . $this->xmlFilename . '" could not be written.', 1489654848);
        }

    }

    public function finalizeWriting()
    {
        $output = '</urlset>';
        if (file_put_contents($this->xmlFilename, $output, FILE_APPEND) === false) {
            throw new \Exception('Error finalizing file "' . $this->xmlFilename . '".', 1489947126);
        }

    }

    /**
     * @param string $output
     * @throws \Exception
     */
    public function appendData($output)
    {
        if (file_put_contents($this->xmlFilename, $output, FILE_APPEND) === false) {
            throw new \Exception('Error appending data to file "' . $this->xmlFilename . '".', 1489947208);
        }

    }

    /**
     * @param array $sitemapEntry
     * @return string
     */
    public function getSitemapEntryXml(array $sitemapEntry)
    {
        $output = '<url>' . PHP_EOL;
        $output .= '<loc>' . PHP_EOL . $this->getFullUrl($sitemapEntry['loc']) . PHP_EOL . '</loc>' . PHP_EOL;
        $output .= '<lastmod>' . $sitemapEntry['lastmod'] . '</lastmod>' . PHP_EOL;
        $output .= '<priority>' . $sitemapEntry['priority'] . '</priority>' . PHP_EOL;
        $output .= '</url>' . PHP_EOL;

        return $output;
    }

    /**
     * @param array $sitemapEntry
     */
    public function appendSitemapEntries(array $sitemapEntries)
    {
        $output = '';
        foreach ($sitemapEntries as $sitemapEntry) {
            $output .= $this->getSitemapEntryXml($sitemapEntry);
        }
        $this->appendData($output);
    }

    /**
     * @param string $sitemapEntryUrl
     * @return string
     */
    protected function getFullUrl($sitemapEntryUrl)
    {
        return GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . ltrim($sitemapEntryUrl , '/');
    }

}