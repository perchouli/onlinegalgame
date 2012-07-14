<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: func/story.php 2012-06-17 20:25:00 Beijing tengattack $
 */

if(!defined('IN_EI')) {
	exit('Access Denied');
}

function get_default_bgd($category = '') {
	//[unfinish]need category
	
	$list = array();
	return $list;
}

function get_upload_bgd() {
	global $_G;
	//[unfinish]need $_G['uid']
	
	$list = array();
	return $list;
}

function processspecialchars($process) {
	$process = str_replace("\\r", "", $process);
	$json_process = json_decode($process, true);
	foreach ($json_process['process'] as $key => $pori) {
		$p = array(
			'type' => $pori['type'],
			'value' => $pori['value'],
			'wait' => intval($pori['wait']),
		);
		if ($p['wait']) {
			$p['wait'] = 1;
		} else {
			$p['wait'] = 0;
		}
		if ($p['type'] == 'DLG') {
			$p['value'] = array('name' => $p['value']['name'], 'content' => $p['value']['content']);
		} else if ($p['type'] == 'BRANCH') {
			$json_branch = $p['value'];
			foreach ($json_branch as $key2 => $b) {
				$b = array('name' => $b['name'], 'ssid' => $b['ssid']);
				$json_branch[$key2] = dhtmlspecialchars($b);
			}
			$p['value'] = $json_branch;
		}
		$json_process['process'][$key] = dhtmlspecialchars($p);
	}
	return json_encode($json_process);
}

function processsoutput($process) {
	$process = str_replace("\\r", "", $process);
	return $process;
}

?>