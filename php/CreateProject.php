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

	use Phpfastcache\Helper\Psr16Adapter;

	$toggl = new \MorningTrain\TogglApi\TogglApi($config['togglkey']);

	$workspace = $toggl->getWorkspaces();
	$client = null;
	$name = '';

	if (!empty($workspace[0])) {

		$work = $workspace[0]->id;

		if (!empty($_GET['client']) && !empty($_GET['name'])) {

			$client = $_GET['client'];
			$name = $_GET['name'];

			$data = array(
				'name' => $name,
				'wid'  => $work,
				'cid'  => $client
			);

			$return = $toggl->createProject($data);

			print dataResponse($return);

		} else {

			error('Invalid client ID or Project Name');

		}

	} else {

		error('No workspace found');

	}

