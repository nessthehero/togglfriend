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

	if (!empty($_GET['client'])) {

		$active = false;

		$client_id = (string) $_GET['client'];

		if (!empty($_GET['active'])) {
			$active = (bool) $_GET['active'];
		}

		if ($active) {

			if (!$cache->has('clients_' . $client_id . '_active' . $cb)) {

				$projects = $toggl->getActiveClientProjects($client_id);

				$cache->set('clients_' . $client_id . '_active' . $cb, $projects, 15);

			} else {

				$projects = $cache->get('clients_' . $client_id . '_active' . $cb);

			}

		} else {

			if (!$cache->has('clients_' . $client_id . $cb)) {

				$projects = $toggl->getClientProjects($client_id);

				$cache->set('clients_' . $client_id . $cb, $projects, 15);

			} else {

				$projects = $cache->get('clients_' . $client_id . $cb);

			}

		}

		print json_encode($projects);

	} else {

		error('No valid client ID provided.');

	}
