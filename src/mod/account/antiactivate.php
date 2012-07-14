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

if(is_array($hash) && $hash[0] == 'antiactivate' && isemail($hash[1]) && TIMESTAMP - $hash[2] < 259200) {
	$u = getuserbyuid($uid);
	if (!empty($u)) {
		if ($u['emailstatus'] == 1 && $u['email'] == $hash[1]) {
			if (C::t('common_member')->update($uid, array('emailstatus' => 0))) {
				showmessage('antiactivate_succeed', '/', array('username' => $u['username']));
			}
		}
	}
}

showmessage('antiactivate_invalid', 'account.php?mod=signin');

?>