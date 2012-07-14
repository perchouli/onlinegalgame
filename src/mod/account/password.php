<?php

/**
 *      [EarlyImbrian] (C)2001-2099 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: mod/account/password.php 2012-06-14 9:01:00 Beijing tengattack $
 */
 
if(!defined('IN_EI')) {
	exit('Access Denied');
}

define('NOROBOT', TRUE);
$ctl_obj = new password_ctl();

$_G['setting']['regctrl'] = true;
$_G['setting']['regfloodctrl'] = true;
$_G['setting']['sendregisterurl'] = true;
$_G['setting']['pwlength'] = 6;
	
$ctl_obj->setting = $_G['setting'];
$method = 'on_'.$do;
$ctl_obj->$method();

?>