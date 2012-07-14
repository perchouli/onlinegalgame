<?php

/**
 *      [EarlyImbrian] (C)2001-2099 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: mod/account/profile.php 2012-07-09 21:00:00 Beijing tengattack $
 */
 
if(!defined('IN_EI')) {
	exit('Access Denied');
}

$user = array();

if (isset($_GET['id']) && $_GET['id']) {
	$user['uid'] = intval($_GET['id']);
} else if($_G['uid']) {
	$user['uid'] = $_G['uid'];
}

if ($do) {
	if(!in_array($do, array('edit', 'change_email', 'resend_email', 'antiactivate_email'))) {
		showmessage('undefined_action', '/');
	}
	
	$ctl_obj = new profile_ctl();
	
	$ctl_obj->setting = $_G['setting'];
	$method = 'on_'.$do;
	$ctl_obj->user = & $user;
	$ctl_obj->template = 'account/profile_'.$do;
	$ctl_obj->$method();
	
} else {
	if (!$user['uid']) {
		if ($_G['uid']) {
			showmessage('message_bad_touid', '/');
		} else {
			showmessage('permission_error', '/');
		}
	} else {
		$u = getuserbyuid($user['uid']);
		if (!$u) showmessage('message_bad_touid', '/');
		$user = array_merge($user, $u);
		
		require libfile('func/event');
		
		$events = getevents($user['uid']);
		$roles = C::t('role')->get_all_by_uid($user['uid']);
		$stories = C::t('story')->get_all_by_uid($user['uid']);
		
		$profile = C::t('common_member_profile')->fetch($user['uid']);
		
		$user['avatar_url'] = getuseravatar($user['uid'], $user);
	}
	
	$navtitle = $user['username'].' - '.$navtitle;
	include template('account/profile');
}

?>