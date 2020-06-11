<?php
	/**
	 * Created by PhpStorm.
	 * User: imoffitt
	 * Date: 2019-03-18
	 * Time: 08:19
	 */

	require_once 'vendor/autoload.php';

	use Phpfastcache\Helper\Psr16Adapter;

	$defaultDriver = 'Files';
	$cache = new Psr16Adapter($defaultDriver);

	$config = (include __DIR__.'/config.php');

	function error($msg)
	{

		print '{"error":"' . $msg . '"}';

	}

	function dataResponse($object)
	{

		print json_encode(array('data' => $object), JSON_FORCE_OBJECT);

	}

	function getProject($id)
	{

		$project = getProjectData($id);

		if (!empty($project)) {

			dataResponse($project);

		} else {

			error('Invalid project ID or project could not be fetched.');

		}

	}

	function getProjectData($id) {

		global $cache;
		global $config;

		$toggl = new \MorningTrain\TogglApi\TogglApi($config['togglkey']);

		$project = null;

		if (!$cache->has('project_' . $id)) {

			$project = $toggl->getProject($id);

			$cache->set('project_' . $id, $project, 15);

		} else {

			$project = $cache->get('project_' . $id);

		}

		return $project;

	}

	function getClientData($id) {

		global $cache;
		global $config;

		$toggl = new \MorningTrain\TogglApi\TogglApi($config['togglkey']);

		$client = null;

		if (!$cache->has('client_' . $id)) {

			$client = $toggl->getClientById($id);

			$cache->set('client_' . $id, $client, 15);

		} else {

			$client = $cache->get('client_' . $id);

		}

		return $client;

	}
