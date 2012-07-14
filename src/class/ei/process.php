<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: class/ei/cron.php 2012-06-13 12:36:00 Beijing tengattack $
 */

if(!defined('IN_EI')) {
	exit('Access Denied');
}

class ei_process
{
	public static function islocked($process, $ttl = 0) {
		$ttl = $ttl < 1 ? 600 : intval($ttl);
		return ei_process::_status('get', $process) || ei_process::_find($process, $ttl);
	}

	public static function unlock($process) {
		ei_process::_status('rm', $process);
		ei_process::_cmd('rm', $process);
	}

	private static function _status($action, $process) {
		static $plist = array();
		switch ($action) {
			case 'set' : $plist[$process] = true; break;
			case 'get' : return !empty($plist[$process]); break;
			case 'rm' : $plist[$process] = null; break;
			case 'clear' : $plist = array(); break;
		}
		return true;
	}

	private static function _find($name, $ttl) {

		if(!ei_process::_cmd('get', $name)) {
			ei_process::_cmd('set', $name, $ttl);
			$ret = false;
		} else {
			$ret = true;
		}
		ei_process::_status('set', $name);
		return $ret;
	}

	private static function _cmd($cmd, $name, $ttl = 0) {
		static $allowmem;
		if($allowmem === null) {
			$mc = memory('check');
			$allowmem = $mc == 'memcache' || $mc == 'redis';
		}
		if($allowmem) {
			return ei_process::_process_cmd_memory($cmd, $name, $ttl);
		} else {
			return ei_process::_process_cmd_db($cmd, $name, $ttl);
		}
	}

	private static function _process_cmd_memory($cmd, $name, $ttl = 0) {
		$ret = '';
		switch ($cmd) {
			case 'set' :
				$ret = memory('set', 'process_lock_'.$name, time(), $ttl);
				break;
			case 'get' :
				$ret = memory('get', 'process_lock_'.$name);
				break;
			case 'rm' :
				$ret = memory('rm', 'process_lock_'.$name);
		}
		return $ret;
	}

	private static function _process_cmd_db($cmd, $name, $ttl = 0) {
		$ret = '';
		switch ($cmd) {
			case 'set':
				$ret = C::t('common_process')->insert(array('processid' => $name, 'expiry' => time() + $ttl), FALSE, true);
				break;
			case 'get':
				$ret = C::t('common_process')->fetch($name);
				if(empty($ret) || $ret['expiry'] < time()) {
					$ret = false;
				} else {
					$ret = true;
				}
				break;
			case 'rm':
				$ret = C::t('common_process')->delete_process($name, time());
				break;
		}
		return $ret;
	}
}

?>