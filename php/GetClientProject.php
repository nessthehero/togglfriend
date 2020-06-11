<?php
	/**
	 * Created by PhpStorm.
	 * User: imoffitt
	 * Date: 2019-03-14
	 * Time: 14:39
	 */

	require_once 'vendor/autoload.php';

	use Phpfastcache\Helper\Psr16Adapter;

	$defaultDriver = 'Files';
	$cache = new Psr16Adapter($defaultDriver);

	$config = (include __DIR__.'/config.php');

	$toggl = new \MorningTrain\TogglApi\TogglApi($config['togglkey']);

	$cb = '';
	if (!empty($_GET['cb'])) {
		$cb = '-' . $_GET['cb'];
	}

	if (!empty($_GET['pid'])) {

		$project_id = (string) $_GET['pid'];

		if (!$cache->has('project_' . $project_id . $cb)) {

			$projects = $toggl->getProject($project_id);

			$cache->set('project_' . $project_id . $cb, $projects, 15);

		} else {

			$projects = $cache->get('project_' . $project_id . $cb);

		}

		print json_encode($projects);

	} else {

		error('No valid project ID provided.');

	}
