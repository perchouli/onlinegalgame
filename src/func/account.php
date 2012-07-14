<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: func/account.php 2012-06-14 09:10:00 Beijing tengattack $
 */

if(!defined('IN_EI')) {
	exit('Access Denied');
}

/* 
 * Hash the password by phpBB3
 * motify by tengattack
 */

/*
 * Return unique id
 * @param string $extra additional entropy
 */

function unique_id($extra = 'c')
{
	static $dss_seeded = false;
	global $_G;

	$val = $_G['config']['rand_seed'] . microtime();
	$val = md5($val);
	$nextval = md5($_G['config']['rand_seed'] . $val . $extra);

	if ($dss_seeded !== true && ($_G['config']['rand_seed_last_update'] < time() - rand(1,10)))
	{
		setglobal('config/rand_seed_last_update', time());
		setglobal('config/rand_seed', $nextval);
		$dss_seeded = true;
	}

	return substr($val, 4, 16);
}

function phpbb_hash($password)
{
	$itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

	$random_state = unique_id();
	$random = '';
	$count = 6;

	/*if (($fh = @fopen('/dev/urandom', 'rb')))
	{
		$random = fread($fh, $count);
		fclose($fh);
	}

	if (strlen($random) < $count)*/
	{
		$random = '';

		for ($i = 0; $i < $count; $i += 16)
		{
			$random_state = md5(unique_id() . $random_state);
			$random .= pack('H*', md5($random_state));
		}
		$random = substr($random, 0, $count);
	}

	$hash = _hash_crypt_private($password, _hash_gensalt_private($random, $itoa64), $itoa64);

	return $hash;
}

function phpbb_check_hash($password, $hash)
{
	$itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	return (_hash_crypt_private($password, $hash, $itoa64) === $hash) ? true : false;
}

/**
* Generate salt for hash generation
*/
function _hash_gensalt_private($input, &$itoa64, $iteration_count_log2 = 6)
{
	if ($iteration_count_log2 < 4 || $iteration_count_log2 > 31)
	{
		$iteration_count_log2 = 8;
	}

	$output = '$H$';
	$output .= $itoa64[min($iteration_count_log2 + ((PHP_VERSION >= 5) ? 5 : 3), 30)];
	$output .= _hash_encode64($input, 6, $itoa64);

	return $output;
}

/**
* Encode hash
*/
function _hash_encode64($input, $count, &$itoa64)
{
	$output = '';
	$i = 0;

	do
	{
		$value = ord($input[$i++]);
		$output .= $itoa64[$value & 0x3f];

		if ($i < $count)
		{
			$value |= ord($input[$i]) << 8;
		}

		$output .= $itoa64[($value >> 6) & 0x3f];

		if ($i++ >= $count)
		{
			break;
		}

		if ($i < $count)
		{
			$value |= ord($input[$i]) << 16;
		}

		$output .= $itoa64[($value >> 12) & 0x3f];

		if ($i++ >= $count)
		{
			break;
		}

		$output .= $itoa64[($value >> 18) & 0x3f];
	}
	while ($i < $count);

	return $output;
}

/**
* The crypt function/replacement
*/
function _hash_crypt_private($password, $setting, &$itoa64)
{
	$output = '*';

	// Check for correct hash
	if (substr($setting, 0, 3) != '$H$' && substr($setting, 0, 3) != '$P$')
	{
		return $output;
	}

	$count_log2 = strpos($itoa64, $setting[3]);

	if ($count_log2 < 7 || $count_log2 > 30)
	{
		return $output;
	}

	$count = 1 << $count_log2;
	$salt = substr($setting, 4, 8);

	if (strlen($salt) != 8)
	{
		return $output;
	}

	/**
	* We're kind of forced to use MD5 here since it's the only
	* cryptographic primitive available in all versions of PHP
	* currently in use.  To implement our own low-level crypto
	* in PHP would result in much worse performance and
	* consequently in lower iteration counts and hashes that are
	* quicker to crack (by non-PHP code).
	*/
	if (PHP_VERSION >= 5)
	{
		$hash = md5($salt . $password, true);
		do
		{
			$hash = md5($hash . $password, true);
		}
		while (--$count);
	}
	else
	{
		$hash = pack('H*', md5($salt . $password));
		do
		{
			$hash = pack('H*', md5($hash . $password));
		}
		while (--$count);
	}

	$output = substr($setting, 0, 12);
	$output .= _hash_encode64($hash, 16, $itoa64);

	return $output;
}


function userlogin($username, $password, $questionid, $answer, $loginfield = 'username', $ip = '') {
	$return = array();

	if($loginfield == 'uid') {
		$isuid = 1;
	} elseif($loginfield == 'email') {
		$isuid = 2;
	} elseif($loginfield == 'auto') {
		$isuid = 3;
	} else {
		$isuid = 0;
	}

	/*if(!function_exists('uc_user_login')) {
		//loaducenter();
	}*/
	if($isuid == 3) {
		/*if(!strcmp(dintval($username), $username)) {
			$return['ucresult'] = uc_user_login($username, $password, 1, 1, $questionid, $answer, $ip);
		} else*/if(isemail($username)) {
			$return['ucresult'] = ei_user_login($username, $password, 2, 1, $questionid, $answer, $ip);
		} else
		/*if($return['ucresult'][0] <= 0 && $return['ucresult'][0] != -3)*/ {
			$return['ucresult'] = ei_user_login(addslashes($username), $password, 0, 1, $questionid, $answer, $ip);
		}
	} else {
		$return['ucresult'] = ei_user_login(addslashes($username), $password, $isuid, 1, $questionid, $answer, $ip);
	}
	$tmp = array();
	$duplicate = '';
	list($tmp['uid'], $tmp['username'], $tmp['password'], $tmp['email'], $duplicate) = $return['ucresult'];
	$return['ucresult'] = $tmp;
	if($duplicate && $return['ucresult']['uid'] > 0 || $return['ucresult']['uid'] <= 0) {
		$return['status'] = 0;
		return $return;
	}

	$member = getuserbyuid($return['ucresult']['uid'], 1);
	if(!$member || empty($member['uid'])) {
		$return['status'] = -1;
		return $return;
	}
	$return['member'] = $member;
	$return['status'] = 1;
	if($member['_inarchive']) {
		C::t('common_member_archive')->move_to_master($member['uid']);
	}
	/*if($member['email'] != $return['ucresult']['email']) {
		C::t('common_member')->update($return['ucresult']['uid'], array('email' => $return['ucresult']['email']));
	}*/

	return $return;
}

function setloginstatus($member, $cookietime) {
	global $_G;
	$_G['uid'] = intval($member['uid']);
	$_G['username'] = $member['username'];
	$_G['adminid'] = $member['adminid'];
	$_G['groupid'] = $member['groupid'];
	$_G['formhash'] = formhash();
	$_G['session']['invisible'] = getuserprofile('invisible');
	$_G['member'] = $member;
	loadcache('usergroup_'.$_G['groupid']);
	C::app()->session->isnew = true;
	C::app()->session->updatesession();

	dsetcookie('auth', authcode("{$member['password']}\t{$member['uid']}", 'ENCODE'), $cookietime, 1, true);
	dsetcookie('loginuser');
	dsetcookie('activationauth');
	dsetcookie('pmnum');

	include_once libfile('func/stat');
	updatestat('login', 1);
	if(defined('IN_MOBILE')) {
		updatestat('mobilelogin', 1);
	}
	if($_G['setting']['connect']['allow'] && $_G['member']['conisbind']) {
		updatestat('connectlogin', 1);
	}
	//Ã¿ÈÕµÇÂ¼
	/*$rule = updatecreditbyaction('daylogin', $_G['uid']);
	if(!$rule['updatecredit']) {
		checkusergroup($_G['uid']);
	}*/
}

function logincheck($username) {
	global $_G;

	$return = 0;
	$username = trim($username);
	//loaducenter();
	/*if(function_exists('uc_user_logincheck')) {
		$return = uc_user_logincheck(addslashes($username), $_G['clientip']);
	} else*/ {
		$login = C::t('common_failedlogin')->fetch_ip($_G['clientip']);
		$return = (!$login || (TIMESTAMP - $login['lastupdate'] > 900)) ? 5 : max(0, 5 - $login['count']);

		if(!$login) {
			C::t('common_failedlogin')->insert(array(
				'ip' => $_G['clientip'],
				'count' => 0,
				'lastupdate' => TIMESTAMP
			), false, true);
		} elseif(TIMESTAMP - $login['lastupdate'] > 900) {
			C::t('common_failedlogin')->insert(array(
				'ip' => $_G['clientip'],
				'count' => 0,
				'lastupdate' => TIMESTAMP
			), false, true);
			C::t('common_failedlogin')->delete_old(901);
		}
	}
	return $return;
}

function loginfailed($username) {
	global $_G;

	//loaducenter();
	/*if(function_exists('uc_user_logincheck')) {
		return;
	}*/
	C::t('common_failedlogin')->update_failed($_G['clientip']);
}

function getinvite() {
	global $_G;

	if($_G['setting']['regstatus'] == 1) return array();
	$result = array();
	$cookies = empty($_G['cookie']['invite_auth']) ? array() : explode(',', $_G['cookie']['invite_auth']);
	$cookiecount = count($cookies);
	$_GET['invitecode'] = trim($_GET['invitecode']);
	if($cookiecount == 2 || $_GET['invitecode']) {
		$id = intval($cookies[0]);
		$code = trim($cookies[1]);
		if($_GET['invitecode']) {
			$invite = C::t('common_invite')->fetch_by_code($_GET['invitecode']);
			$code = trim($_GET['invitecode']);
		} else {
			$invite = C::t('common_invite')->fetch($id);
		}
		if(!empty($invite)) {
			if($invite['code'] == $code && empty($invite['fuid']) && (empty($invite['endtime']) || $_G['timestamp'] < $invite['endtime'])) {
				$result['uid'] = $invite['uid'];
				$result['id'] = $invite['id'];
				$result['appid'] = $invite['appid'];
			}
		}
	} elseif($cookiecount == 3) {
		$uid = intval($cookies[0]);
		$code = trim($cookies[1]);
		$appid = intval($cookies[2]);

		$invite_code = space_key($uid, $appid);
		if($code == $invite_code) {
			$inviteprice = 0;
			$member = getuserbyuid($uid);
			if($member) {
				$usergroup = C::t('common_usergroup')->fetch($member['groupid']);
				$inviteprice = $usergroup['inviteprice'];
			}
			if($inviteprice > 0) return array();
			$result['uid'] = $uid;
			$result['appid'] = $appid;
		}
	}

	if($result['uid']) {
		$member = getuserbyuid($result['uid']);
		$result['username'] = $member['username'];
	} else {
		dsetcookie('invite_auth', '');
	}

	return $result;
}

function replacesitevar($string, $replaces = array()) {
	global $_G;
	$sitevars = array(
		'{sitename}' => $_G['setting']['sitename'],
		'{bbname}' => $_G['setting']['bbname'],
		'{time}' => dgmdate(TIMESTAMP, 'Y-n-j H:i'),
		'{adminemail}' => $_G['setting']['adminemail'],
		'{username}' => $_G['member']['username'],
		'{myname}' => $_G['member']['username']
	);
	$replaces = array_merge($sitevars, $replaces);
	return str_replace(array_keys($replaces), array_values($replaces), $string);
}

function clearcookies() {
	global $_G;
	foreach($_G['cookie'] as $k => $v) {
		if($k != 'widthauto') {
			dsetcookie($k);
		}
	}
	$_G['uid'] = $_G['adminid'] = 0;
	$_G['username'] = $_G['member']['password'] = '';
}

function crime($fun) {
	if(!$fun) {
		return false;
	}
	include_once libfile('class/member');
	$crimerecord = & crime_action_ctl::instance();
	$arg_list = func_get_args();
	if($fun == 'recordaction') {
		list(, $uid, $action, $reason) = $arg_list;
		return $crimerecord->$fun($uid, $action, $reason);
	} elseif($fun == 'getactionlist') {
		list(, $uid) = $arg_list;
		return $crimerecord->$fun($uid);
	} elseif($fun == 'getcount') {
		list(, $uid, $action) = $arg_list;
		return $crimerecord->$fun($uid, $action);
	} elseif($fun == 'search') {
		list(, $action, $username, $operator, $startime, $endtime, $reason, $start, $limit) = $arg_list;
		return $crimerecord->$fun($action, $username, $operator, $startime, $endtime, $reason, $start, $limit);
	} elseif($fun == 'actions') {
		return $crimerecord->$fun;
	}
	return false;
}
function checkfollowfeed() {
	global $_G;

	if($_G['uid']) {
		$lastcheckfeed = 0;
		if(!empty($_G['cookie']['lastcheckfeed'])) {
			$time = explode('|', $_G['cookie']['lastcheckfeed']);
			if($time[0] == $_G['uid']) {
				$lastcheckfeed = $time[1];
			}
		}
		if(!$lastcheckfeed) {
			$lastcheckfeed = getuserprofile('lastactivity');
		}
		dsetcookie('lastcheckfeed', $_G['uid'].'|'.TIMESTAMP, 31536000);
		$followuser = C::t('home_follow')->fetch_all_following_by_uid($_G['uid']);
		$uids = array_keys($followuser);
		if(!empty($uids)) {
			$count = C::t('home_follow_feed')->count_by_uid_dateline($uids, $lastcheckfeed);
			if($count) {
				notification_add($_G['uid'], 'follow', 'member_follow', array('count' => $count, 'from_id'=>$_G['uid'], 'from_idtype' => 'follow'), 1);
			}
		}
	}
	dsetcookie('checkfollow', 1, 30);
}
function checkemail($email) {

	$email = strtolower(trim($email));
	if(strlen($email) > 32) {
		showmessage('profile_email_illegal', '', array(), array('handle' => false));
	}
	//loaducenter();
	$ucresult = ei_user_checkemail($email);

	if($ucresult == -4) {
		showmessage('profile_email_illegal', '', array(), array('handle' => false));
	} elseif($ucresult == -5) {
		showmessage('profile_email_domain_illegal', '', array(), array('handle' => false));
	} elseif($ucresult == -6) {
		showmessage('profile_email_duplicate', '', array(), array('handle' => false));
	}
}

function ei_user_checkemail($email) {
	if (!ei_check_emailformat($email)) {
		return -4;
	} elseif (ei_check_emailexists($email)) {
		return -6;
	}
	return 0;
}

function ei_check_emailformat($email) {
	return strlen($email) >= 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
}

function ei_check_emailexists($email, $username = '') {
	$sqladd = $username !== '' ? "AND username<>'$username'" : '';
	$email = DB::result_first("SELECT email FROM ".DB::table("common_member")." WHERE email='$email' $sqladd");
	return $email;
}

function ei_user_register($username, $password, $email, $questionid = '', $answer = '', $regip = '') {
	include_once EI_ROOT.'src/inc/utf/utf_tools.php';
	$data = array (
		'username' => $username,
		'username_clean' => utf8_clean_string($username),
		'password' => phpbb_hash($password)
	);
	DB::insert('account', $data);
	return DB::insert_id();
}

function ei_user_delete($uid) {
	return 1;
}

function ei_get_user($username) {
	include_once EI_ROOT.'src/inc/utf/utf_tools.php';
	$username_clean = utf8_clean_string($username);
	return DB::fetch_first("SELECT * FROM ".DB::table("account")." WHERE username_clean='$username_clean'");
}

function ei_user_is_active($username) {
	$u = ei_get_user($username);
	if (!$u) return false;
	if (DB::result_first("SELECT emailstatus FROM ".DB::table("common_member")." WHERE uid='$u[uid]'") == 0) {
		return false;
	} else {
		return true;
	}
}

function ei_user_login($username, $password, $isuid = 0, $checkques = 0, $questionid = '', $answer = '') {
	$isuid = intval($isuid);
	$u = array();
	$m = array();
	$status = -1;
	switch ($isuid) {
		case 1:
			$username = intval($username);
			$u = DB::fetch_first("SELECT * FROM ".DB::table("account")." WHERE uid='$username'");
			break;
		case 2:
			$m = C::t('common_member')->fetch_by_email(strtolower($username));
			if (!empty($m)) {
				$u = DB::fetch_first("SELECT * FROM ".DB::table("account")." WHERE uid='$m[uid]'");
			}
			break;
		default:
			$u = ei_get_user($username);
			break;
	}
	if (!empty($u)) {
		if (empty($m)) {
			$m = getuserbyuid($u['uid']);
		}
		if (!empty($m)) {
			if (phpbb_check_hash($password, $u['password'])) {
				$status = $m['uid'];
			} else {
				$status = -2;
			}
		}	
	}
	
	return array($status, $u['username'], $password, $m['email'], 0);
}

?>