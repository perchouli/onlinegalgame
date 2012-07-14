<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: func/core.php 2012-06-13 04:20:00 Beijing tengattack $
 */

if(!defined('IN_EI')) {
	exit('Access Denied');
}

define('EI_CORE_FUNCTION', true);

function system_error($message, $show = true, $save = true, $halt = true) {
	ei_error::system_error($message, $show, $save, $halt);
}

function setglobal($key , $value, $group = null) {
	global $_G;
	$key = explode('/', $group === null ? $key : $group.'/'.$key);
	$p = &$_G;
	foreach ($key as $k) {
		if(!isset($p[$k]) || !is_array($p[$k])) {
			$p[$k] = array();
		}
		$p = &$p[$k];
	}
	$p = $value;
	return true;
}

function libfile($libname, $folder = '') {
	$libpath = EI_ROOT.'/src/'.$folder;
	if(strstr($libname, '/')) {
		list($pre, $name) = explode('/', $libname);
		return realpath("{$libpath}/{$pre}/{$name}.php");
	} else {
		return realpath("{$libpath}/{$libname}.php");
	}
}

function dintval($int, $allowarray = false) {
	$ret = intval($int);
	if($int == $ret || !$allowarray && is_array($int)) return $ret;
	if($allowarray && is_array($int)) {
		foreach($int as &$v) {
			$v = dintval($v, true);
		}
		return $int;
	} elseif($int <= 0xffffffff) {
		$l = strlen($int);
		$m = substr($int, 0, 1) == '-' ? 1 : 0;
		if(($l - $m) === strspn($int,'0987654321', $m)) {
			return $int;
		}
	}
	return $ret;
}

function dimplode($array) {
	if(!empty($array)) {
		$array = array_map('addslashes', $array);
		return "'".implode("','", is_array($array) ? $array : array($array))."'";
	} else {
		return 0;
	}
}

function dheader($string, $replace = true, $http_response_code = 0) {
	$islocation = substr(strtolower(trim($string)), 0, 8) == 'location';
	if(defined('IN_MOBILE') && strpos($string, 'mobile') === false && $islocation) {
		if (strpos($string, '?') === false) {
			$string = $string.'?mobile=yes';
		} else {
			if(strpos($string, '#') === false) {
				$string = $string.'&mobile=yes';
			} else {
				$str_arr = explode('#', $string);
				$str_arr[0] = $str_arr[0].'&mobile=yes';
				$string = implode('#', $str_arr);
			}
		}
	}
	$string = str_replace(array("\r", "\n"), array('', ''), $string);
	if(empty($http_response_code) || PHP_VERSION < '4.3' ) {
		@header($string, $replace);
	} else {
		@header($string, $replace, $http_response_code);
	}
	if($islocation) {
		exit();
	}
}

function loadcache($cachenames, $force = false) {
	global $_G;
	static $loadedcache = array();
	$cachenames = is_array($cachenames) ? $cachenames : array($cachenames);
	$caches = array();
	foreach ($cachenames as $k) {
		if(!isset($loadedcache[$k]) || $force) {
			$caches[] = $k;
			$loadedcache[$k] = true;
		}
	}

	if(!empty($caches)) {
		$cachedata = C::t('common_syscache')->fetch_all($caches);
		foreach($cachedata as $cname => $data) {
			if($cname == 'setting') {
				$_G['setting'] = $data;
			} elseif($cname == 'usergroup_'.$_G['groupid']) {
				$_G['cache'][$cname] = $_G['group'] = $data;
			} elseif($cname == 'style_default') {
				$_G['cache'][$cname] = $_G['style'] = $data;
			} elseif($cname == 'grouplevels') {
				$_G['grouplevels'] = $data;
			} else {
				$_G['cache'][$cname] = $data;
			}
		}
	}
	return true;
}

function dgmdate($timestamp, $format = 'dt', $timeoffset = '9999', $uformat = '') {
	global $_G;
	$format == 'u' && !$_G['setting']['dateconvert'] && $format = 'dt';
	static $dformat, $tformat, $dtformat, $offset, $lang;
	if($dformat === null) {
		$dformat = getglobal('setting/dateformat');
		$tformat = getglobal('setting/timeformat');
		$dtformat = $dformat.' '.$tformat;
		$offset = getglobal('member/timeoffset');
		$lang = lang('core', 'date');
	}
	$timeoffset = $timeoffset == 9999 ? $offset : $timeoffset;
	$timestamp += $timeoffset * 3600;
	$format = empty($format) || $format == 'dt' ? $dtformat : ($format == 'd' ? $dformat : ($format == 't' ? $tformat : $format));
	if($format == 'u') {
		$todaytimestamp = TIMESTAMP - (TIMESTAMP + $timeoffset * 3600) % 86400 + $timeoffset * 3600;
		$s = gmdate(!$uformat ? $dtformat : $uformat, $timestamp);
		$time = TIMESTAMP + $timeoffset * 3600 - $timestamp;
		if($timestamp >= $todaytimestamp) {
			if($time > 3600) {
				return '<span title="'.$s.'">'.intval($time / 3600).'&nbsp;'.$lang['hour'].$lang['before'].'</span>';
			} elseif($time > 1800) {
				return '<span title="'.$s.'">'.$lang['half'].$lang['hour'].$lang['before'].'</span>';
			} elseif($time > 60) {
				return '<span title="'.$s.'">'.intval($time / 60).'&nbsp;'.$lang['min'].$lang['before'].'</span>';
			} elseif($time > 0) {
				return '<span title="'.$s.'">'.$time.'&nbsp;'.$lang['sec'].$lang['before'].'</span>';
			} elseif($time == 0) {
				return '<span title="'.$s.'">'.$lang['now'].'</span>';
			} else {
				return $s;
			}
		} elseif(($days = intval(($todaytimestamp - $timestamp) / 86400)) >= 0 && $days < 7) {
			if($days == 0) {
				return '<span title="'.$s.'">'.$lang['yday'].'&nbsp;'.gmdate($tformat, $timestamp).'</span>';
			} elseif($days == 1) {
				return '<span title="'.$s.'">'.$lang['byday'].'&nbsp;'.gmdate($tformat, $timestamp).'</span>';
			} else {
				return '<span title="'.$s.'">'.($days + 1).'&nbsp;'.$lang['day'].$lang['before'].'</span>';
			}
		} else {
			return $s;
		}
	} else {
		return gmdate($format, $timestamp);
	}
}

function dmktime($date) {
	if(strpos($date, '-')) {
		$time = explode('-', $date);
		return mktime(0, 0, 0, $time[1], $time[2], $time[0]);
	}
	return 0;
}

function dnumber($number) {
	return abs($number) > 10000 ? '<span title="'.$number.'">'.intval($number / 10000).lang('core', '10k').'</span>' : $number;
}

function savecache($cachename, $data) {
	C::t('common_syscache')->insert($cachename, $data);
}

function save_syscache($cachename, $data) {
	savecache($cachename, $data);
}

function dunserialize($data) {
	if(($ret = unserialize($data)) === false) {
		$ret = unserialize(stripslashes($data));
	}
	return $ret;
}

function dmkdir($dir, $mode = 0777, $makeindex = TRUE){
	if(!is_dir($dir)) {
		dmkdir(dirname($dir), $mode, $makeindex);
		@mkdir($dir, $mode);
		if(!empty($makeindex)) {
			@touch($dir.'/index.html'); @chmod($dir.'/index.html', 0777);
		}
	}
	return true;
}

function dreferer($default = '') {
	global $_G;

	$default = empty($default) ? $GLOBALS['_t_curapp'] : '';
	$_G['referer'] = !empty($_GET['referer']) ? $_GET['referer'] : $_SERVER['HTTP_REFERER'];
	$_G['referer'] = substr($_G['referer'], -1) == '?' ? substr($_G['referer'], 0, -1) : $_G['referer'];

	if(strpos($_G['referer'], 'member.php?mod=logging')) {
		$_G['referer'] = $default;
	}
	$_G['referer'] = dhtmlspecialchars($_G['referer'], ENT_QUOTES);
	$_G['referer'] = str_replace('&amp;', '&', $_G['referer']);
	$reurl = parse_url($_G['referer']);
	if(!empty($reurl['host']) && !in_array($reurl['host'], array($_SERVER['HTTP_HOST'], 'www.'.$_SERVER['HTTP_HOST'])) && !in_array($_SERVER['HTTP_HOST'], array($reurl['host'], 'www.'.$reurl['host']))) {
		if(!in_array($reurl['host'], $_G['setting']['domain']['app']) && !isset($_G['setting']['domain']['list'][$reurl['host']])) {
			$domainroot = substr($reurl['host'], strpos($reurl['host'], '.')+1);
			if(empty($_G['setting']['domain']['root']) || (is_array($_G['setting']['domain']['root']) && !in_array($domainroot, $_G['setting']['domain']['root']))) {
				$_G['referer'] = $_G['setting']['domain']['defaultindex'] ? $_G['setting']['domain']['defaultindex'] : 'index.php';
			}
		}
	} elseif(empty($reurl['host'])) {
		$_G['referer'] = $_G['siteurl'].'./'.$_G['referer'];
	}

	return strip_tags($_G['referer']);
}

function showmessage($message, $url_forward = '', $values = array(), $extraparam = array(), $custom = 0) {
	require_once libfile('func/message');
	return dshowmessage($message, $url_forward, $values, $extraparam, $custom);
}

function formhash($specialadd = '') {
	global $_G;
	$hashadd = defined('IN_ADMINCP') ? 'Only For EarlyImbrian Admin Control Panel' : '';
	return substr(md5(substr($_G['timestamp'], 0, -6).$_G['username'].$_G['uid'].$_G['authkey'].$hashadd.$specialadd), 8, 8);
}

function ipaccess($ip, $accesslist) {
	return preg_match("/^(".str_replace(array("\r\n", ' '), array('|', ''), preg_quote($accesslist, '/')).")/", $ip);
}

function periodscheck($periods, $showmessage = 1) {
	global $_G;
	if(($periods == 'postmodperiods' || $periods == 'postbanperiods') && ($_G['setting']['postignorearea'] || $_G['setting']['postignoreip'])) {
		if($_G['setting']['postignoreip']) {
			foreach(explode("\n", $_G['setting']['postignoreip']) as $ctrlip) {
				if(preg_match("/^(".preg_quote(($ctrlip = trim($ctrlip)), '/').")/", $_G['clientip'])) {
					return false;
					break;
				}
			}
		}
		if($_G['setting']['postignorearea']) {
			$location = $whitearea = '';
			require_once libfile('function/misc');
			$location = trim(convertip($_G['clientip'], "./"));
			if($location) {
				$whitearea = preg_quote(trim($_G['setting']['postignorearea']), '/');
				$whitearea = str_replace(array("\\*"), array('.*'), $whitearea);
				$whitearea = '.*'.$whitearea.'.*';
				$whitearea = '/^('.str_replace(array("\r\n", ' '), array('.*|.*', ''), $whitearea).')$/i';
				if(@preg_match($whitearea, $location)) {
					return false;
				}
			}
		}
	}
	if(!$_G['group']['disableperiodctrl'] && $_G['setting'][$periods]) {
		$now = dgmdate(TIMESTAMP, 'G.i', $_G['setting']['timeoffset']);
		foreach(explode("\r\n", str_replace(':', '.', $_G['setting'][$periods])) as $period) {
			list($periodbegin, $periodend) = explode('-', $period);
			if(($periodbegin > $periodend && ($now >= $periodbegin || $now < $periodend)) || ($periodbegin < $periodend && $now >= $periodbegin && $now < $periodend)) {
				$banperiods = str_replace("\r\n", ', ', $_G['setting'][$periods]);
				if($showmessage) {
					showmessage('period_nopermission', NULL, array('banperiods' => $banperiods), array('login' => 1));
				} else {
					return TRUE;
				}
			}
		}
	}
	return FALSE;
}

function memory($cmd, $key='', $value='', $ttl = 0, $prefix = '') {
	if($cmd == 'check') {
		return  C::memory()->enable ? C::memory()->type : '';
	} elseif(C::memory()->enable && in_array($cmd, array('set', 'get', 'rm', 'inc', 'dec'))) {
		if(defined('EI_DEBUG') && EI_DEBUG) {
			if(is_array($key)) {
				foreach($key as $k) {
					C::memory()->debug[$cmd][] = ($cmd == 'get' || $cmd == 'rm' ? $value : '').$prefix.$k;
				}
			} else {
				C::memory()->debug[$cmd][] = ($cmd == 'get' || $cmd == 'rm' ? $value : '').$prefix.$key;
			}
		}
		switch ($cmd) {
			case 'set': return C::memory()->set($key, $value, $ttl, $prefix); break;
			case 'get': return C::memory()->get($key, $value); break;
			case 'rm': return C::memory()->rm($key, $value); break;
			case 'inc': return C::memory()->inc($key, $value ? $value : 1); break;
			case 'dec': return C::memory()->dec($key, $value ? $value : -1); break;
		}
	}
	return null;
}

function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
	$ckey_length = 4;
	$key = md5($key != '' ? $key : getglobal('authkey'));
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}

}

function getuserbyuid($uid, $fetch_archive = 0) {
	static $users = array();
	if(empty($users[$uid])) {
		$users[$uid] = C::t('common_member'.($fetch_archive === 2 ? '_archive' : ''))->fetch($uid);
		if($fetch_archive === 1 && empty($users[$uid])) {
			$users[$uid] = C::t('common_member_archive')->fetch($uid);
		}
	}
	if(!isset($users[$uid]['self']) && $uid == getglobal('uid') && getglobal('uid')) {
		$users[$uid]['self'] = 1;
	}
	return $users[$uid];
}

function getuseravatar($uid, $user = array()) {
	if (!$user) {
		$user = getuserbyuid($uid);
	}
	if ($user) {
		if (!$user['avatarstatus']) {
			return '/static/img/noavatar.jpg';
		} else {
			return '/data/attach/avatar/'.$uid.'.jpg';
		}
	} else {
		return '/static/img/noavatar.jpg';
	}
}

function getgpc($k, $type='GP') {
	$type = strtoupper($type);
	switch($type) {
		case 'G': $var = &$_GET; break;
		case 'P': $var = &$_POST; break;
		case 'C': $var = &$_COOKIE; break;
		default:
			if(isset($_GET[$k])) {
				$var = &$_GET;
			} else {
				$var = &$_POST;
			}
			break;
	}

	return isset($var[$k]) ? $var[$k] : NULL;

}

function daddslashes($string, $force = 1) {
	if(is_array($string)) {
		$keys = array_keys($string);
		foreach($keys as $key) {
			$val = $string[$key];
			unset($string[$key]);
			$string[addslashes($key)] = daddslashes($val, $force);
		}
	} else {
		$string = addslashes($string);
	}
	return $string;
}

function dsetcookie($var, $value = '', $life = 0, $prefix = 1, $httponly = false) {

	global $_G;

	$config = $_G['config']['cookie'];

	$_G['cookie'][$var] = $value;
	$var = ($prefix ? $config['cookiepre'] : '').$var;
	$_COOKIE[$var] = $value;

	if($value == '' || $life < 0) {
		$value = '';
		$life = -1;
	}

	if(defined('IN_MOBILE')) {
		$httponly = false;
	}

	$life = $life > 0 ? getglobal('timestamp') + $life : ($life < 0 ? getglobal('timestamp') - 31536000 : 0);
	$path = $httponly && PHP_VERSION < '5.2.0' ? $config['cookiepath'].'; HttpOnly' : $config['cookiepath'];

	$secure = $_SERVER['SERVER_PORT'] == 443 ? 1 : 0;
	if(PHP_VERSION < '5.2.0') {
		setcookie($var, $value, $life, $path, $config['cookiedomain'], $secure);
	} else {
		setcookie($var, $value, $life, $path, $config['cookiedomain'], $secure, $httponly);
	}
}

function getcookie($key) {
	global $_G;
	return isset($_G['cookie'][$key]) ? $_G['cookie'][$key] : '';
}

function fileext($filename) {
	return addslashes(strtolower(substr(strrchr($filename, '.'), 1, 10)));
}

function checkrobot($useragent = '') {
	static $kw_spiders = array('bot', 'crawl', 'spider' ,'slurp', 'sohu-search', 'lycos', 'robozilla');
	static $kw_browsers = array('msie', 'netscape', 'opera', 'konqueror', 'mozilla');

	$useragent = strtolower(empty($useragent) ? $_SERVER['HTTP_USER_AGENT'] : $useragent);
	if(strpos($useragent, 'http://') === false && dstrpos($useragent, $kw_browsers)) return false;
	if(dstrpos($useragent, $kw_spiders)) return true;
	return false;
}
function checkmobile() {
	global $_G;
	$mobile = array();
	static $mobilebrowser_list =array('iphone', 'android', 'phone', 'mobile', 'wap', 'netfront', 'java', 'opera mobi', 'opera mini',
				'ucweb', 'windows ce', 'symbian', 'series', 'webos', 'sony', 'blackberry', 'dopod', 'nokia', 'samsung',
				'palmsource', 'xda', 'pieplus', 'meizu', 'midp', 'cldc', 'motorola', 'foma', 'docomo', 'up.browser',
				'up.link', 'blazer', 'helio', 'hosin', 'huawei', 'novarra', 'coolpad', 'webos', 'techfaith', 'palmsource',
				'alcatel', 'amoi', 'ktouch', 'nexian', 'ericsson', 'philips', 'sagem', 'wellcom', 'bunjalloo', 'maui', 'smartphone',
				'iemobile', 'spice', 'bird', 'zte-', 'longcos', 'pantech', 'gionee', 'portalmmm', 'jig browser', 'hiptop',
				'benq', 'haier', '^lct', '320x320', '240x320', '176x220');
	$pad_list = array('pad', 'gt-p1000');

	$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);

	if(dstrpos($useragent, $pad_list)) {
		return false;
	}
	if(($v = dstrpos($useragent, $mobilebrowser_list, true))) {
		$_G['mobile'] = $v;
		return true;
	}
	$brower = array('mozilla', 'chrome', 'safari', 'opera', 'm3gate', 'winwap', 'openwave', 'myop');
	if(dstrpos($useragent, $brower)) return false;

	$_G['mobile'] = 'unknown';
	if($_GET['mobile'] === 'yes') {
		return true;
	} else {
		return false;
	}
}

function dstrpos($string, &$arr, $returnvalue = false) {
	if(empty($string)) return false;
	foreach((array)$arr as $v) {
		if(strpos($string, $v) !== false) {
			$return = $returnvalue ? $v : true;
			return $return;
		}
	}
	return false;
}

function isemail($email) {
	return strlen($email) >= 6 && strlen($email) <= 32 && preg_match("/^([A-Za-z0-9\-_.+]+)@([A-Za-z0-9\-]+[.][A-Za-z0-9\-.]+)$/", $email);
}

function random($length, $numeric = 0) {
	$seed = base_convert(md5(microtime().$_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
	$seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
	$hash = '';
	$max = strlen($seed) - 1;
	for($i = 0; $i < $length; $i++) {
		$hash .= $seed{mt_rand(0, $max)};
	}
	return $hash;
}

function strexists($string, $find) {
	return !(strpos($string, $find) === FALSE);
}

function dhtmlspecialchars($string, $flags = null) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = dhtmlspecialchars($val, $flags);
		}
	} else {
		if($flags === null) {
			$string = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string);
			if(strpos($string, '&amp;#') !== false) {
				$string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $string);
			}
		} else {
			if(PHP_VERSION < '5.4.0') {
				$string = htmlspecialchars($string, $flags);
			} else {
				if(strtolower(CHARSET) == 'utf-8') {
					$charset = 'UTF-8';
				} else {
					$charset = 'ISO-8859-1';
				}
				$string = htmlspecialchars($string, $flags, $charset);
			}
		}
	}
	return $string;
}

function dexit($message = '') {
	echo $message;
	output();
	exit();
}

function dstrlen($str) {
	if(strtolower(CHARSET) != 'utf-8') {
		return strlen($str);
	}
	$count = 0;
	for($i = 0; $i < strlen($str); $i++){
		$value = ord($str[$i]);
		if($value > 127) {
			$count++;
			if($value >= 192 && $value <= 223) $i++;
			elseif($value >= 224 && $value <= 239) $i = $i + 2;
			elseif($value >= 240 && $value <= 247) $i = $i + 3;
	    	}
    		$count++;
	}
	return $count;
}

function cutstr($string, $length, $dot = ' ...') {
	if(strlen($string) <= $length) {
		return $string;
	}

	$pre = chr(1);
	$end = chr(1);
	$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), $string);

	$strcut = '';
	if(strtolower(CHARSET) == 'utf-8') {

		$n = $tn = $noc = 0;
		while($n < strlen($string)) {

			$t = ord($string[$n]);
			if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1; $n++; $noc++;
			} elseif(194 <= $t && $t <= 223) {
				$tn = 2; $n += 2; $noc += 2;
			} elseif(224 <= $t && $t <= 239) {
				$tn = 3; $n += 3; $noc += 2;
			} elseif(240 <= $t && $t <= 247) {
				$tn = 4; $n += 4; $noc += 2;
			} elseif(248 <= $t && $t <= 251) {
				$tn = 5; $n += 5; $noc += 2;
			} elseif($t == 252 || $t == 253) {
				$tn = 6; $n += 6; $noc += 2;
			} else {
				$n++;
			}

			if($noc >= $length) {
				break;
			}

		}
		if($noc > $length) {
			$n -= $tn;
		}

		$strcut = substr($string, 0, $n);

	} else {
		for($i = 0; $i < $length; $i++) {
			$strcut .= ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
		}
	}

	$strcut = str_replace(array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

	$pos = strrpos($strcut, chr(1));
	if($pos !== false) {
		$strcut = substr($strcut,0,$pos);
	}
	return $strcut.$dot;
}

function dstripslashes($string) {
	if(empty($string)) return $string;
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = dstripslashes($val);
		}
	} else {
		$string = stripslashes($string);
	}
	return $string;
}

function runhooks($scriptextra = '') {
	if(!defined('HOOKTYPE')) {
		define('HOOKTYPE', !defined('IN_MOBILE') ? 'hookscript' : 'hookscriptmobile');
	}
	if(defined('CURMODULE')) {
		global $_G;
		if($_G['setting']['plugins']['func'][HOOKTYPE]['common']) {
			hookscript('common', 'global', 'funcs', array(), 'common');
		}
		hookscript(CURMODULE, $_G['basescript'], 'funcs', array(), '', $scriptextra);
	}
}

function hookscript($script, $hscript, $type = 'funcs', $param = array(), $func = '', $scriptextra = '') {
	global $_G;
	/*static $pluginclasses;
	if($hscript == 'home') {
		if($script == 'space') {
			$scriptextra = !$scriptextra ? $_GET['do'] : $scriptextra;
			$script = 'space'.(!empty($scriptextra) ? '_'.$scriptextra : '');
		} elseif($script == 'spacecp') {
			$scriptextra = !$scriptextra ? $_GET['ac'] : $scriptextra;
			$script .= !empty($scriptextra) ? '_'.$scriptextra : '';
		}
	}
	if(!isset($_G['setting'][HOOKTYPE][$hscript][$script][$type])) {
		return;
	}
	if(!isset($_G['cache']['plugin'])) {
		loadcache('plugin');
	}
	foreach((array)$_G['setting'][HOOKTYPE][$hscript][$script]['module'] as $identifier => $include) {
		$hooksadminid[$identifier] = !$_G['setting'][HOOKTYPE][$hscript][$script]['adminid'][$identifier] || ($_G['setting'][HOOKTYPE][$hscript][$script]['adminid'][$identifier] && $_G['adminid'] > 0 && $_G['setting']['hookscript'][$hscript][$script]['adminid'][$identifier] >= $_G['adminid']);
		if($hooksadminid[$identifier]) {
			@include_once EI_ROOT.'./source/plugin/'.$include.'.class.php';
		}
	}
	if(@is_array($_G['setting'][HOOKTYPE][$hscript][$script][$type])) {
		$_G['inhookscript'] = true;
		$funcs = !$func ? $_G['setting'][HOOKTYPE][$hscript][$script][$type] : array($func => $_G['setting'][HOOKTYPE][$hscript][$script][$type][$func]);
		foreach($funcs as $hookkey => $hookfuncs) {
			foreach($hookfuncs as $hookfunc) {
				if($hooksadminid[$hookfunc[0]]) {
					$classkey = (HOOKTYPE != 'hookscriptmobile' ? '' : 'mobile').'plugin_'.($hookfunc[0].($hscript != 'global' ? '_'.$hscript : ''));
					if(!class_exists($classkey)) {
						continue;
					}
					if(!isset($pluginclasses[$classkey])) {
						$pluginclasses[$classkey] = new $classkey;
					}
					if(!method_exists($pluginclasses[$classkey], $hookfunc[1])) {
						continue;
					}
					$return = $pluginclasses[$classkey]->$hookfunc[1]($param);

					if(is_array($return)) {
						if(!isset($_G['setting']['pluginhooks'][$hookkey]) || is_array($_G['setting']['pluginhooks'][$hookkey])) {
							foreach($return as $k => $v) {
								$_G['setting']['pluginhooks'][$hookkey][$k] .= $v;
							}
						}
					} else {
						if(!is_array($_G['setting']['pluginhooks'][$hookkey])) {
							$_G['setting']['pluginhooks'][$hookkey] .= $return;
						} else {
							foreach($_G['setting']['pluginhooks'][$hookkey] as $k => $v) {
								$_G['setting']['pluginhooks'][$hookkey][$k] .= $return;
							}
						}
					}
				}
			}
		}
	}*/
	$_G['inhookscript'] = false;
}

function hookscriptoutput($tplfile) {
	global $_G;
	if(!empty($_G['hookscriptoutput'])) {
		return;
	}
	hookscript('global', 'global');
	if(defined('CURMODULE')) {
		$param = array('template' => $tplfile, 'message' => $_G['hookscriptmessage'], 'values' => $_G['hookscriptvalues']);
		hookscript(CURMODULE, $_G['basescript'], 'outputfuncs', $param);
	}
	$_G['hookscriptoutput'] = true;
}


function rewriteoutput($type, $returntype, $host) {
	global $_G;
	$fextra = '';
	if($type == 'forum_forumdisplay') {
		list(,,, $fid, $page, $extra) = func_get_args();
		$r = array(
			'{fid}' => empty($_G['setting']['forumkeys'][$fid]) ? $fid : $_G['setting']['forumkeys'][$fid],
			'{page}' => $page ? $page : 1,
		);
	} elseif($type == 'forum_viewthread') {
		list(,,, $tid, $page, $prevpage, $extra) = func_get_args();
		$r = array(
			'{tid}' => $tid,
			'{page}' => $page ? $page : 1,
			'{prevpage}' => $prevpage && !IS_ROBOT ? $prevpage : 1,
		);
	} elseif($type == 'home_space') {
		list(,,, $uid, $username, $extra) = func_get_args();
		$_G['setting']['rewritecompatible'] && $username = rawurlencode($username);
		$r = array(
			'{user}' => $uid ? 'uid' : 'username',
			'{value}' => $uid ? $uid : $username,
		);
	} elseif($type == 'home_blog') {
		list(,,, $uid, $blogid, $extra) = func_get_args();
		$r = array(
			'{uid}' => $uid,
			'{blogid}' => $blogid,
		);
	} elseif($type == 'group_group') {
		list(,,, $fid, $page, $extra) = func_get_args();
		$r = array(
			'{fid}' => $fid,
			'{page}' => $page ? $page : 1,
		);
	} elseif($type == 'portal_topic') {
		list(,,, $name, $extra) = func_get_args();
		$r = array(
			'{name}' => $name,
		);
	} elseif($type == 'portal_article') {
		list(,,, $id, $page, $extra) = func_get_args();
		$r = array(
			'{id}' => $id,
			'{page}' => $page ? $page : 1,
		);
	} elseif($type == 'forum_archiver') {
		list(,, $action, $value, $page, $extra) = func_get_args();
		$host = '';
		$r = array(
			'{action}' => $action,
			'{value}' => $value,
		);
		if($page) {
			$fextra = '?page='.$page;
		}
	} elseif($type == 'plugin') {
		list(,, $pluginid, $module,, $param, $extra) = func_get_args();
		$host = '';
		$r = array(
			'{pluginid}' => $pluginid,
			'{module}' => $module,
		);
		if($param) {
			$fextra = '?'.$param;
		}
	}
	$href = str_replace(array_keys($r), $r, $_G['setting']['rewriterule'][$type]).$fextra;
	if(!$returntype) {
		return '<a href="'.$host.$href.'"'.(!empty($extra) ? stripslashes($extra) : '').'>';
	} else {
		return $host.$href;
	}
}

function mobilereplace($file, $replace) {
	return helper_mobile::mobilereplace($file, $replace);
}

function mobileoutput() {
	helper_mobile::mobileoutput();
}

function output() {

	global $_G;


	if(defined('EI_OUTPUTED')) {
		return;
	} else {
		define('EI_OUTPUTED', 1);
	}

	if(!empty($_G['blockupdate'])) {
		block_updatecache($_G['blockupdate']['bid']);
	}

	if(defined('IN_MOBILE')) {
		mobileoutput();
	}
	/*if(!defined('IN_MOBILE') && !defined('IN_ARCHIVER')) {
		$tipsService = Cloud::loadClass('Service_EarlyImbrianTips');
		$tipsService->show();
	}*/
	$havedomain = implode('', $_G['setting']['domain']['app']);
	if($_G['setting']['rewritestatus'] || !empty($havedomain)) {
		$content = ob_get_contents();
		$content = output_replace($content);


		ob_end_clean();
		$_G['gzipcompress'] ? ob_start('ob_gzhandler') : ob_start();

		echo $content;
	}
	if($_G['setting']['ftp']['connid']) {
		@ftp_close($_G['setting']['ftp']['connid']);
	}
	$_G['setting']['ftp'] = array();

	if(defined('CACHE_FILE') && CACHE_FILE && !defined('CACHE_FORBIDDEN') && !defined('IN_MOBILE') && !checkmobile()) {
		if(diskfreespace(EI_ROOT.'./'.$_G['setting']['cachethreaddir']) > 1000000) {
			if($fp = @fopen(CACHE_FILE, 'w')) {
				flock($fp, LOCK_EX);
				fwrite($fp, empty($content) ? ob_get_contents() : $content);
			}
			@fclose($fp);
			chmod(CACHE_FILE, 0777);
		}
	}

	if(defined('EI_DEBUG') && EI_DEBUG && @include(libfile('function/debug'))) {
		function_exists('debugmessage') && debugmessage();
	}
}

function output_replace($content) {
	global $_G;
	if(defined('IN_MODCP') || defined('IN_ADMINCP')) return $content;
	if(!empty($_G['setting']['output']['str']['search'])) {
		if(empty($_G['setting']['domain']['app']['default'])) {
			$_G['setting']['output']['str']['replace'] = str_replace('{CURHOST}', $_G['siteurl'], $_G['setting']['output']['str']['replace']);
		}
		$content = str_replace($_G['setting']['output']['str']['search'], $_G['setting']['output']['str']['replace'], $content);
	}
	if(!empty($_G['setting']['output']['preg']['search'])) {
		if(empty($_G['setting']['domain']['app']['default'])) {
			$_G['setting']['output']['preg']['search'] = str_replace('\{CURHOST\}', preg_quote($_G['siteurl'], '/'), $_G['setting']['output']['preg']['search']);
			$_G['setting']['output']['preg']['replace'] = str_replace('{CURHOST}', $_G['siteurl'], $_G['setting']['output']['preg']['replace']);
		}

		$content = preg_replace($_G['setting']['output']['preg']['search'], $_G['setting']['output']['preg']['replace'], $content);
	}

	return $content;
}

function output_ajax() {
	global $_G;
	$s = ob_get_contents();
	ob_end_clean();
	$s = preg_replace("/([\\x01-\\x08\\x0b-\\x0c\\x0e-\\x1f])+/", ' ', $s);
	$s = str_replace(array(chr(0), ']]>'), array(' ', ']]&gt;'), $s);
	if(defined('EI_DEBUG') && EI_DEBUG && @include(libfile('function/debug'))) {
		function_exists('debugmessage') && $s .= debugmessage(1);
	}
	$havedomain = implode('', $_G['setting']['domain']['app']);
	if($_G['setting']['rewritestatus'] || !empty($havedomain)) {
        $s = output_replace($s);
	}
	return $s;
}

function lang($file, $langvar = null, $vars = array(), $default = null) {
	global $_G;
	$langname = $_G['langname'];
	list($path, $file) = explode('/', $file);
	if(!$file) {
		$file = $path;
		$path = '';
	}
	
	$file = $langname.'/'.$file;
	
	if($path != 'plugin') {
		$key = $path == '' ? $file : $path.'_'.$file;
		if(!isset($_G['lang'][$key])) {
			include EI_ROOT.'./src/lang/'.($path == '' ? '' : $path.'/').$file.'.php';
			$_G['lang'][$key] = $lang;
		}
		if(defined('IN_MOBILE') && !defined('TPL_DEFAULT')) {
			include EI_ROOT.'./src/lang/mobile/'.$langname.'/template.php';
			$_G['lang'][$key] = array_merge($_G['lang'][$key], $lang);
		}
		$returnvalue = &$_G['lang'];
	} else {
		if(empty($_G['config']['plugindeveloper'])) {
			loadcache('pluginlanguage_script');
		} elseif(!isset($_G['cache']['pluginlanguage_script'][$file]) && preg_match("/^[a-z]+[a-z0-9_]*$/i", $file)) {
			if(@include(EI_ROOT.'./data/plugindata/'.$file.'.lang.php')) {
				$_G['cache']['pluginlanguage_script'][$file] = $scriptlang[$file];
			} else {
				loadcache('pluginlanguage_script');
			}
		}
		$returnvalue = & $_G['cache']['pluginlanguage_script'];
		$key = &$file;
	}
	$return = $langvar !== null ? (isset($returnvalue[$key][$langvar]) ? $returnvalue[$key][$langvar] : null) : $returnvalue[$key];
	$return = $return === null ? ($default !== null ? $default : $langvar) : $return;
	$searchs = $replaces = array();
	if($vars && is_array($vars)) {
		foreach($vars as $k => $v) {
			$searchs[] = '{'.$k.'}';
			$replaces[] = $v;
		}
	}
	if(is_string($return) && strpos($return, '{_G/') !== false) {
		preg_match_all('/\{_G\/(.+?)\}/', $return, $gvar);
		foreach($gvar[0] as $k => $v) {
			$searchs[] = $v;
			$replaces[] = getglobal($gvar[1][$k]);
		}
	}
	$return = str_replace($searchs, $replaces, $return);
	return $return;
}


function checktplrefresh($maintpl, $subtpl, $timecompare, $templateid, $cachefile, $tpldir, $file) {
	static $tplrefresh, $timestamp, $targettplname;
	if($tplrefresh === null) {
		$tplrefresh = getglobal('config/output/tplrefresh');
		$timestamp = getglobal('timestamp');
	}

	if(empty($timecompare) || $tplrefresh == 1 || ($tplrefresh > 1 && !($timestamp % $tplrefresh))) {
		if(empty($timecompare) || @filemtime(EI_ROOT.$subtpl) > $timecompare) {
			require_once EI_ROOT.'/src/class/template.php';
			$template = new template();
			$template->parse_template($maintpl, $templateid, $tpldir, $file, $cachefile);
			if($targettplname === null) {
				$targettplname = getglobal('style/tplfile');
				if(!empty($targettplname)) {
					include_once libfile('func/block');
					$targettplname = strtr($targettplname, ':', '_');
					update_template_block($targettplname, getglobal('style/tpldirectory'), $template->blocks);
				}
				$targettplname = true;
			}
			return TRUE;
		}
	}
	return FALSE;
}

function template($file, $templateid = 0, $tpldir = '', $gettplfile = 0, $primaltpl='') {
	global $_G;

	static $_init_style = false;
	if($_init_style === false) {
		C::app()->_init_style();
		$_init_style = true;
	}
	$oldfile = $file;
	if(strpos($file, ':') !== false) {
		$clonefile = '';
		list($templateid, $file, $clonefile) = explode(':', $file);
		$oldfile = $file;
		$file = empty($clonefile) ? $file : $file.'_'.$clonefile;
		if($templateid == 'diy') {
			$indiy = false;
			$_G['style']['tpldirectory'] = $tpldir ? $tpldir : (defined('TPLDIR') ? TPLDIR : '');
			$_G['style']['prefile'] = '';
			$diypath = EI_ROOT.'./data/diy/'.$_G['style']['tpldirectory'].'/'; //DIY模板文件目录
			$preend = '_diy_preview';
			$_GET['preview'] = !empty($_GET['preview']) ? $_GET['preview'] : '';
			$curtplname = $oldfile;
			$basescript = $_G['mod'] == 'viewthread' && !empty($_G['thread']) ? 'forum' : $_G['basescript'];
			if(isset($_G['cache']['diytemplatename'.$basescript])) {
				$diytemplatename = &$_G['cache']['diytemplatename'.$basescript];
			} else {
				if(!isset($_G['cache']['diytemplatename'])) {
					loadcache('diytemplatename');
				}
				$diytemplatename = &$_G['cache']['diytemplatename'];
			}
			$tplsavemod = 0;
			if(isset($diytemplatename[$file]) && file_exists($diypath.$file.'.htm') && ($tplsavemod = 1) || empty($_G['forum']['styleid']) && ($file = $primaltpl ? $primaltpl : $oldfile) && isset($diytemplatename[$file]) && file_exists($diypath.$file.'.htm')) {
				$tpldir = 'data/diy/'.$_G['style']['tpldirectory'].'/';
				!$gettplfile && $_G['style']['tplsavemod'] = $tplsavemod;
				$curtplname = $file;
				if(isset($_GET['diy']) && $_GET['diy'] == 'yes' || isset($_GET['diy']) && $_GET['preview'] == 'yes') { //DIY模式或预览模式下做以下判断
					$flag = file_exists($diypath.$file.$preend.'.htm');
					if($_GET['preview'] == 'yes') {
						$file .= $flag ? $preend : '';
					} else {
						$_G['style']['prefile'] = $flag ? 1 : '';
					}
				}
				$indiy = true;
			} else {
				$file = $primaltpl ? $primaltpl : $oldfile;
			}
			$tplrefresh = $_G['config']['output']['tplrefresh'];
			if($indiy && ($tplrefresh ==1 || ($tplrefresh > 1 && !($_G['timestamp'] % $tplrefresh))) && filemtime($diypath.$file.'.htm') < filemtime(EI_ROOT.$_G['style']['tpldirectory'].'/'.($primaltpl ? $primaltpl : $oldfile).'.htm')) {
				if (!updatediytemplate($file, $_G['style']['tpldirectory'])) {
					unlink($diypath.$file.'.htm');
					$tpldir = '';
				}
			}

			if (!$gettplfile && empty($_G['style']['tplfile'])) {
				$_G['style']['tplfile'] = empty($clonefile) ? $curtplname : $oldfile.':'.$clonefile;
			}

			$_G['style']['prefile'] = !empty($_GET['preview']) && $_GET['preview'] == 'yes' ? '' : $_G['style']['prefile'];

		} else {
			$tpldir = './src/plugin/'.$templateid.'/template';
		}
	}

	$file .= !empty($_G['inajax']) && ($file == 'common/header' || $file == 'common/footer') ? '_ajax' : '';
	$tpldir = $tpldir ? $tpldir : (defined('TPLDIR') ? TPLDIR : '');
	$templateid = $templateid ? $templateid : (defined('TEMPLATEID') ? TEMPLATEID : '');
	$filebak = $file;

	if(defined('IN_MOBILE') && !defined('TPL_DEFAULT') && strpos($file, 'mobile/') === false || (isset($_G['forcemobilemessage']) && $_G['forcemobilemessage'])) {
		$file = 'mobile/'.$oldfile;
	}

	if(!$tpldir) {
		$tpldir = './template/default';
	}
	$tplfile = $tpldir.'/'.$file.'.htm';

	$file == 'common/header' && defined('CURMODULE') && CURMODULE && $file = 'common/header_'.$_G['basescript'].'_'.CURMODULE;

	if(defined('IN_MOBILE') && !defined('TPL_DEFAULT')) {
		if(strpos($tpldir, 'plugin')) {
			if(!file_exists(EI_ROOT.$tpldir.'/'.$file.'.htm') && !file_exists(EI_ROOT.$tpldir.'/'.$file.'.php')) {
				ei_error::template_error('template_notfound', $tpldir.'/'.$file.'.htm');
			} else {
				$mobiletplfile = $tpldir.'/'.$file.'.htm';
			}
		}
		!$mobiletplfile && $mobiletplfile = $file.'.htm';
		if(strpos($tpldir, 'plugin') && (file_exists(EI_ROOT.$mobiletplfile) || file_exists(substr(EI_ROOT.$mobiletplfile, 0, -4).'.php'))) {
			$tplfile = $mobiletplfile;
		} elseif(!file_exists(EI_ROOT.TPLDIR.'/'.$mobiletplfile) && !file_exists(substr(EI_ROOT.TPLDIR.'/'.$mobiletplfile, 0, -4).'.php')) {
			$mobiletplfile = './template/default/'.$mobiletplfile;
			if(!file_exists(EI_ROOT.$mobiletplfile) && !$_G['forcemobilemessage']) {
				$tplfile = str_replace('mobile/', '', $tplfile);
				$file = str_replace('mobile/', '', $file);
				define('TPL_DEFAULT', true);
			} else {
				$tplfile = $mobiletplfile;
			}
		} else {
			$tplfile = TPLDIR.'/'.$mobiletplfile;
		}
	}

	$cachefile = './data/template/'.(defined('STYLEID') ? STYLEID.'_' : '_').$templateid.'_'.str_replace('/', '_', $file).'.tpl.php';
	if($templateid != 1 && !file_exists(EI_ROOT.$tplfile) && !file_exists(substr(EI_ROOT.$tplfile, 0, -4).'.php')
			&& !file_exists(EI_ROOT.($tplfile = $tpldir.$filebak.'.htm'))) {
		$tplfile = './template/default/'.$filebak.'.htm';
	}

	if($gettplfile) {
		return $tplfile;
	}
	checktplrefresh($tplfile, $tplfile, @filemtime(EI_ROOT.$cachefile), $templateid, $cachefile, $tpldir, $file);
	return EI_ROOT.$cachefile;
}

function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val{strlen($val)-1});
    switch($last) {
        case 'g': $val *= 1024;
        case 'm': $val *= 1024;
        case 'k': $val *= 1024;
    }
    return $val;
}

function getattachmentpath($attid) {
	global $_G;
	
	$path = '';
	if ($attid != 0) {
		$att = array();
		if ($att = C::t('attachment')->fetch($attid)) {
			$path = getattachmentpath_by_attachment($att);
		}
	}
	
	return $path;
}

function getattachmentpath_by_attachment($att) {
	$path = '';
	if ($att['aid'] != 0) {
		$subdir = $subdir1 = $subdir2 = '';
		$subdir1 = date('Ym', $att['uploadtime']);
		$subdir2 = date('d', $att['uploadtime']);
		$subdir = $subdir1.'/'.$subdir2.'/';
		$path = '/data/attach/'.$att['type'].'/'.$subdir.$att['aid'].'.'.$att['ext'];
	}
	
	return $path;
}

function getuserprofile($field) {
	global $_G;
	if(isset($_G['member'][$field])) {
		return $_G['member'][$field];
	}
	static $tablefields = array(
		//'count'		=> array('extcredits1','extcredits2','extcredits3','extcredits4','extcredits5','extcredits6','extcredits7','extcredits8','friends','posts','threads','digestposts','doings','blogs','albums','sharings','attachsize','views','oltime','todayattachs','todayattachsize', 'follower', 'following', 'newfollower'),
		'status'	=> array('regip','lastip','lastvisit','lastactivity','lastpost','lastsendmail','invisible','buyercredit','sellercredit','favtimes','sharetimes','profileprogress'),
		//'field_forum'	=> array('publishfeed','customshow','customstatus','medals','sightml','groupterms','authstr','groups','attentiongroup'),
		//'field_home'	=> array('videophoto','spacename','spacedescription','domain','addsize','addfriend','menunum','theme','spacecss','blockposition','recentnote','spacenote','privacy','feedfriend','acceptemail','magicgift','stickblogs'),
		'profile'	=> array('realname','gender','birthyear','birthmonth','birthday','constellation','zodiac','telephone','mobile','idcardtype','idcard','address','zipcode','nationality','birthprovince','birthcity','resideprovince','residecity','residedist','residecommunity','residesuite','graduateschool','company','education','occupation','position','revenue','affectivestatus','lookingfor','bloodtype','height','weight','alipay','icq','qq','yahoo','msn','taobao','site','bio','interest','field1','field2','field3','field4','field5','field6','field7','field8'),
		//'verify'	=> array('verify1', 'verify2', 'verify3', 'verify4', 'verify5', 'verify6', 'verify7'),
	);
	$profiletable = '';
	foreach($tablefields as $table => $fields) {
		if(in_array($field, $fields)) {
			$profiletable = $table;
			break;
		}
	}
	if($profiletable) {

		if(is_array($_G['member']) && $_G['member']['uid']) {
			//space_merge($_G['member'], $profiletable);
		} else {
			foreach($tablefields[$profiletable] as $k) {
				$_G['member'][$k] = '';
			}
		}
		return $_G['member'][$field];
	}
	return null;
}

function isactivate() {
	global $_G;
	return $_G['member']['emailstatus'];
}

function checkactivate() {
	global $_G;
	if (!isactivate()) {
		showmessage('need_activate', 'account.php?mod=profile&id='.$_G['uid']);
	}
}

function getglobal($key, $group = null) {
	global $_G;
	$key = explode('/', $group === null ? $key : $group.'/'.$key);
	$v = &$_G;
	foreach ($key as $k) {
		if (!isset($v[$k])) {
			return null;
		}
		$v = &$v[$k];
	}
	return $v;
}

function sysmessage($message) {
	helper_sysmessage::show($message);
}

function runlog($file, $message, $halt=0) {
	helper_log::runlog($file, $message, $halt);
}

function writelog($file, $log) {
	helper_log::writelog($file, $log);
}

function debuginfo() {
	global $_G;
	//if(getglobal('setting/debug')) {
		$db = & DB::object();
		$_G['debuginfo'] = array(
		    'time' => number_format((microtime(true) - $_G['starttime']), 6),
		    'queries' => $db->querynum,
		    //'memory' => ucwords(C::memory()->type)
		    );
		if($db->slaveid) {
			$_G['debuginfo']['queries'] = 'Total '.$db->querynum.', Slave '.$db->slavequery;
		}
		return TRUE;
	/*} else {
		return FALSE;
	}*/
}

?>