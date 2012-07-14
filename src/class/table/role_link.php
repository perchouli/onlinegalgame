<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: class/table/role_link.php 2012-06-22 12:00:00 Beijing tengattack $
 */

if(!defined('IN_EI')) {
	exit('Access Denied');
}

class table_role_link extends ei_table
{
	public function __construct() {

		$this->_table = 'role_link';
		$this->_pk    = 'rid';

		parent::__construct();
	}

	public function fetch_all_by_tag($tag) {

	}

	public function fetch_all_by_uid($uids) {
		$roles = array();
		if(!empty($uids)) {
			$roles = DB::fetch_all('SELECT * FROM %t WHERE uid IN (%n)', array($this->_table, (array)$uids), 'uid');
		}
		return $roles;
	}
	
	public function get_role_by_uid($uid) {
		$roles = array();
		$query = DB::query("SELECT r.*
				FROM ". DB::table('role')." r
				INNER JOIN ".DB::table('role_link')." l USING(rid)
				WHERE r.rid=l.rid AND l.uid=".$uid);
		while($tr = DB::fetch($query)) {
			$roles[] = $tr;
		}
		return $roles;
	}
	
	public function get_all_by_uid($uid) {
		$roles = array();
		/*$query = DB::query("SELECT r.*, a.*
				FROM ". DB::table('role')." r
				INNER JOIN ".DB::table('attachment')." a USING(aid)
				WHERE r.aid=a.aid ORDER BY r.rid DESC ".DB::limit($start, $limit));*/
		$query = DB::query("SELECT * FROM ".DB::table($this->_table).' WHERE '.DB::field('uid', intval($uid)));
		while($tr = DB::fetch($query)) {
			$roles[] = $tr;
		}
		return $roles;
	}
	
	public function get_all_by_rid($rids) {
		$roles = array();
		$query = DB::query('SELECT * FROM %t WHERE rid IN (%n)', array($this->_table, (array)$rids), 'rid');
		while($tr = DB::fetch($query)) {
			$roles[] = $tr;
		}
		return $roles;
	}

	public function count_by_tag($tag) {
		//return $groupid ? DB::result_first('SELECT COUNT(*) FROM %t WHERE groupid=%d', array($this->_table, $groupid)) : 0;
	}

	public function delete($linkdata) {
		return DB::delete($this->_table, $linkdata);
	}

	public function range_by_uid($from, $limit) {
		return DB::fetch_all('SELECT * FROM %t WHERE uid >= %d ORDER BY uid LIMIT %d', array($this->_table, $from, $limit), $this->_pk);
	}
}

?>