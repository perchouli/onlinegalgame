<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: mod/account/signin.php 2012-06-14 9:01:00 Beijing tengattack $
 */
 
if(!defined('IN_EI')) {
	exit('Access Denied');
}

if ($do) {
	define('NOROBOT', TRUE);
	
	$ctl_obj = new logging_ctl();
	
	// 自动选择是用户名还是email
	$_G['setting']['autoidselect'] = true;
	
	$ctl_obj->setting = $_G['setting'];
	$method = 'on_'.$do;
	$ctl_obj->template = 'account/signin';
	$ctl_obj->$method();
} else {
	if($_G['uid']) {
		showmessage('login_succeed', '/', array('username' => $_G['member']['username'], 'usergroup' => $_G['group']['grouptitle'], 'uid' => $_G['uid']));
	}
	include template('account/signin');
}

?>