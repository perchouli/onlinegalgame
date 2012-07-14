<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: class/table/account.php 2012-06-24 00:43:00 Beijing tengattack $
 */

if(!defined('IN_EI')) {
	exit('Access Denied');
}

class table_account extends ei_table
{
	public function __construct() {

		$this->_table = 'account';
		$this->_pk    = 'uid';
		$this->_pre_cache_key = 'account_';

		parent::__construct();
	}

	public function fetch_by_username($username) {
		include_once EI_ROOT.'./src/inc/utf/utf_tools.php';
		$username_clean = utf8_clean_string($username);
		
		$user = array();
		if($username_clean) {
			$user = DB::fetch_first('SELECT * FROM %t WHERE username_clean=%s', array($this->_table, $username_clean));
		}
		return $user;
	}

	/*public function fetch_all_by_username($usernames) {
		$users = array();
		if(!empty($usernames)) {
			$users = DB::fetch_all('SELECT * FROM %t WHERE username IN (%n)', array($this->_table, (array)$usernames), 'username');
		}
		return $users;
	}

	public function fetch_uid_by_username($username) {
		$uid = 0;
		if($username) {
			$uid = DB::result_first('SELECT uid FROM %t WHERE username=%s', array($this->_table, $username));
		}
		return $uid;
	}

	public function fetch_all_uid_by_username($usernames) {
		$uids = array();
		if($usernames) {
			foreach($this->fetch_all_by_username($usernames) as $username => $value) {
				$uids[$username] = $value['uid'];
			}
		}
		return $uids;
	}*/

	public function fetch_all_username_by_uid($uids) {
		$users = array();
		if(($uids = dintval($uids, true))) {
			foreach($this->fetch_all($uids) as $uid => $value) {
				$users[$uid] = $value['username'];
			}
		}
		return $users;
	}

	public function count() {
		$count = DB::result_first('SELECT COUNT(*) FROM %t', array($this->_table));
		return $count;
	}

	public function max_uid() {
		return DB::result_first('SELECT MAX(uid) FROM %t', array($this->_table));
	}

	public function range_by_uid($from, $limit) {
		return DB::fetch_all('SELECT * FROM %t WHERE uid >= %d ORDER BY uid LIMIT %d', array($this->_table, $from, $limit), $this->_pk);
	}
}

?>