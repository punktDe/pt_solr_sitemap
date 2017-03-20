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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Scheduler\AdditionalFieldProviderInterface;
use TYPO3\CMS\Scheduler\Controller\SchedulerModuleController;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

class SolrSitemapTaskAdditionalFieldProvider implements AdditionalFieldProviderInterface {

	/**
	 * Gets additional fields to render in the form to add/edit a task
	 *
	 * @param array $taskInfo Values of the fields from the add/edit task form
	 * @param SolrSitemapTask $task The task object being edited. Null when adding a task!
	 * @param SchedulerModuleController $schedulerModule Reference to the scheduler backend module
	 * @return array A two dimensional array, array('Identifier' => array('fieldId' => array('code' => '', 'label' => '', 'cshKey' => '', 'cshLabel' => ''))
	 */
	public function getAdditionalFields(array &$taskInfo, $task, SchedulerModuleController $schedulerModule) {
		if ($schedulerModule->CMD == 'add') {
			$taskInfo['site'] = null;
		}

		if ($schedulerModule->CMD == 'edit') {
			$taskInfo['site'] = $task->getSite();
		}

		$additionalFields = [
			'site' => [
				'code'     => Site::getAvailableSitesSelector('tx_scheduler[site]', $taskInfo['site']),
				'label'    => 'LLL:EXT:solr/lang/locallang.xml:scheduler_field_site',
				'cshKey'   => '',
				'cshLabel' => ''
			]
		];

		return $additionalFields;
	}

	/**
	 * Validates the additional fields' values
	 *
	 * @param array $submittedData An array containing the data submitted by the add/edit task form
	 * @param SchedulerModuleController $schedulerModule Reference to the scheduler backend module
	 * @return boolean true if validation was ok (or selected class is not relevant), false otherwise
	 */
	public function validateAdditionalFields(array &$submittedData, SchedulerModuleController $schedulerModule) {
		$result = false;

		// validate site
		$sites = Site::getAvailableSites();
		if (array_key_exists($submittedData['site'], $sites)) {
			$result = true;
		}

		return $result;
	}

	/**
	 * Takes care of saving the additional fields' values in the task's object
	 *
	 * @param array $submittedData An array containing the data submitted by the add/edit task form
	 * @param SolrSitemapTask $task Reference to the scheduler backend module
	 * @return void
	 */
	public function saveAdditionalFields(array $submittedData, AbstractTask $task) {
	    /** @var Site $site */
	    $site = GeneralUtility::makeInstance(Site::class, $submittedData['site']);
		$task->setSite($site);
	}

}