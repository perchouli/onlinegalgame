<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: func/event.php 2012-06-24 21:31:00 Beijing tengattack $
 */

if(!defined('IN_EI')) {
	exit('Access Denied');
}

function getevents($uid = 0) {
	$events = array();
	$e = array();
	if ($uid) {
		$e = C::t('lastest_event')->get_event_by_uid($uid, 0, 3);
	} else {
		$e = C::t('lastest_event')->get_event(0, 0, 9);
	}
	if (!empty($e)) {
		
		$ids = array (
			'story' => array(),
			'scene' => array(),
			'role' => array(),
		);
		
		$info = array (
			'story' => array(),
			'scene' => array(),
			'role' => array(),
		);
		
		foreach ($e as $item) {
			switch ($item['type']) {
			case 'story':
				$ids['story'][] = $item['id1'];
				break;
			case 'scene':
				$ids['story'][] = $item['id1'];
				$ids['scene'][] = $item['id2'];
				break;
			case 'role':
				$ids['role'][] = $item['id1'];
				break;
			}
		}
		
		foreach ($ids as $key => $id) {
			if (!empty($id)) {
				$id = array_unique($id);
				switch ($key) {
				case 'story':
					$info[$key] = C::t('story')->fetch_all_name_by_sid($id);
				break;
				case 'scene':
					$info[$key] = C::t('story_scene')->fetch_all_name_by_ssid($id);
				break;
				case 'role':
					$info[$key] = C::t('role')->fetch_all_name_by_rid($id);
				break;
				}
			}
		}
		
		foreach ($e as $item) {
			$t = '';
			if (!$uid) {
				$t = '<div class="gravatar"><img src="';
				$t .= getuseravatar($item['uid'], $item);
				$t .= '" width="40"></div><br><a href="account.php?mod=profile&id='.$item['uid'].'">@'.$item['username'].'</a> ';
			}
			$add = true;
			switch ($item['type']) {
			case 'signup':
				$t .= lang('message', 'event_signup');
				break;
			case 'story':
				if (!$info['story'][$item['id1']]) $add = false;
				if ($add) $t .= lang('message', 'event_story', array('id'  => $item['id1'], 'name' => $info['story'][$item['id1']]));
				break;
			case 'scene':
				if (!$info['story'][$item['id1']] || !$info['scene'][$item['id2']]) $add = false;
				if ($add) $t .= lang('message', 'event_scene', array('id'  => $item['id1'], 'name' => $info['story'][$item['id1']], 
									'ssid'  => $item['id2'], 'scenename' => $info['scene'][$item['id2']]));
				break;
			case 'role':
				$t .= lang('message', 'event_role', array('id'  => $item['id1'], 'name' => $info['role'][$item['id1']]));
				break;
			}
			if ($add) $events[] = $t;
		}
	}
	return $events;
}

?>