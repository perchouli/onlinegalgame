<?php

/**
 *      [EarlyImbrian] (C)2001-2099 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: mod/account/signup.php 2012-06-14 9:01:00 Beijing tengattack $
 */
 
if(!defined('IN_EI')) {
	exit('Access Denied');
}

if ($do == 'submit') {
	define('NOROBOT', TRUE);
	$ctl_obj = new register_ctl();
	
	$_G['setting']['regctrl'] = true;
	$_G['setting']['regfloodctrl'] = true;
	$_G['setting']['sendregisterurl'] = true;
	$_G['setting']['pwlength'] = 6;
	//$_G['setting']['regclosed'] = true;
	
	$ctl_obj->setting = $_G['setting'];
	
	$ctl_obj->template = 'account/signup';
	$ctl_obj->on_register();
} else {
	if($_G['uid']) {
		showmessage('login_succeed', '/', array('username' => $_G['member']['username'], 'usergroup' => $_G['group']['grouptitle'], 'uid' => $_G['uid']));
	}
	include template('account/signup');
}

?>