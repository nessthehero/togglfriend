<?php
	/**
	 * Created by PhpStorm.
	 * User: imoffitt
	 * Date: 2019-03-15
	 * Time: 15:01
	 */

	require_once 'vendor/autoload.php';
	require_once 'functions.php';

	use Phpfastcache\Helper\Psr16Adapter;

	$config = (include __DIR__.'/config.php');

	$toggl = new \MorningTrain\TogglApi\TogglApi($config['togglkey']);

	$workspace = $toggl->getWorkspaces();
	$client = null;
	$name = '';

	if (!empty($workspace[0])) {

		$work = $workspace[0]->id;

		if (!empty($_GET['pid']) && !empty($_GET['description'])) {

			$pid = $_GET['pid'];
			$description = $_GET['description'];

			$data = array(
				'description'  => $description,
				'pid'          => $pid,
				'created_with' => 'togglfriend'
			);

//			print_r($data);

			$return = $toggl->startTimeEntry($data);

			print dataResponse($return);

		} else {

			error('Invalid project ID or description');

		}

	} else {

		error('No workspace found');

	}

