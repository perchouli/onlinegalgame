<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: account.php 2012-06-13 22:59:00 Beijing tengattack $
 */

define('CURSCRIPT', 'account');

require_once './src/class/core.php';

$ei = C::app();
$ei->init_setting = false;
$ei->init_user = true;
$ei->init_session = false;
$ei->init_cron = false;
$ei->init();

require libfile('func/account');
require libfile('class/account');
runhooks();

$mod = getgpc('mod');
$do = getgpc('do');
$ref = '/';
if(!in_array($mod, array('signup', 'activate', 'signin', 'profile', 'password', 'antiactivate'))) {
	showmessage('undefined_action', $ref);
}

if ($mod == 'password') {
	if (!$do || !in_array($do, array('reset', 'change', 'submit'))) {
		showmessage('undefined_action', $ref);
	}
} elseif ($mod == 'signup') {
	if ($do && $do != 'submit') {
		showmessage('undefined_action');
	}
} elseif ($mod == 'signin') {
	if($do && !in_array($do, array('login', 'logout'))) {
		showmessage('undefined_action', $ref);
	}
}

define('CURMODULE', $mod);

$navtitle = lang('title', CURSCRIPT.'.'.CURMODULE);

require EI_ROOT.'./src/mod/account/'.$mod.'.php';

?>