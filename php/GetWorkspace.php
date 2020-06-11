<?php
	/**
	 * Created by PhpStorm.
	 * User: imoffitt
	 * Date: 2019-03-15
	 * Time: 14:57
	 */

	require_once 'vendor/autoload.php';

	$config = (include __DIR__.'/config.php');

	$toggl = new \MorningTrain\TogglApi\TogglApi($config['togglkey']);

	print json_encode($toggl->getWorkspaces());
