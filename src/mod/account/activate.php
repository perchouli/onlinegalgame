<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: mod/account/activate.php 2012-06-14 9:01:00 Beijing tengattack $
 */
 
if(!defined('IN_EI')) {
	exit('Access Denied');
}

$hash = $_GET['hash'];
$uid = intval($_GET['uid']);
$hash = explode("\t", authcode($_GET['hash'], 'DECODE', $_G['config']['security']['authkey']));

if(is_array($hash) && isemail($hash[0]) && TIMESTAMP - $hash[1] < 259200) {
	$u = getuserbyuid($uid);
	if (!empty($u)) {
		if ($u['emailstatus'] == 0 && $u['email'] == $hash[0]) {
			if (C::t('common_member')->update($uid, array('emailstatus' => 1))) {
				C::t('lastest_event')->add_event($uid, 'signup', $uid);
				showmessage('activate_succeed', '/', array('username' => $u['username']));
			}
		} /*else {
			showmessage('activate_succeed', 'index.php', array('username' => $u['username']));
		}*/
	}
}

showmessage('register_activation_invalid', 'account.php?mod=signin');

?>