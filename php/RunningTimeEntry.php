<?php
	/**
	 * Created by PhpStorm.
	 * User: imoffitt
	 * Date: 2019-03-15
	 * Time: 15:01
	 */

	require_once 'vendor/autoload.php';
	require_once 'functions.php';

	$config = (include __DIR__.'/config.php');

	$toggl = new \MorningTrain\TogglApi\TogglApi($config['togglkey']);

	$entry = $toggl->getRunningTimeEntry();
	$json_entry = json_encode($entry, JSON_FORCE_OBJECT);

	if (!empty($entry)) {

		if (!empty($entry->pid)) {

			$project_id = $entry->pid;

			$project = getProjectData($project_id);

			$client_id = $project->cid;

			$client = getClientData($client_id);

			dataResponse(array(
				'entry' => $entry,
				'project' => $project,
				'client' => $client,
				'clean' => array(
					'task' => (!empty($entry->description)) ? $entry->description : '',
					'project' => $project->name,
					'client' => $client->name,
					'start' => $entry->start
				)
			));

		} else {

			dataResponse($entry);

		}

	} else {

		error('Object empty');

	}

