<?php
	/**
	 * Created by PhpStorm.
	 * User: imoffitt
	 * Date: 2019-03-14
	 * Time: 14:55
	 */

	require_once 'vendor/autoload.php';

	$config = (include __DIR__.'/config.php');

	$toggl = new \MorningTrain\TogglApi\TogglApi($config['togglkey']);

	$clients = $toggl->getClients();

	print json_encode($clients);
