<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: class/table/role_posture.php 2012-06-14 09:10:00 Beijing tengattack $
 */

if(!defined('IN_EI')) {
	exit('Access Denied');
}

class table_role_posture extends ei_table
{
	public function __construct() {

		$this->_table = 'role_posture';
		$this->_pk    = 'rid';
		$this->_pre_cache_key = 'role_posture_';

		parent::__construct();
	}
	
	public function get_postures($rid) {
		$postures = array();
		$query = DB::query('SELECT * FROM '.DB::table($this->_table).' WHERE '.DB::field('rid', intval($rid)));
		while($tp = DB::fetch($query)) {
			$postures[] = $tp;
		}
		return $postures;
	}

	public function get_all_by_rid($rid) {
		$postures = array();
		$query = DB::query("SELECT p.*, a.uploadtime, a.type, a.ext
				FROM ". DB::table($this->_table)." p
				INNER JOIN ".DB::table('attachment')." a USING(aid)
				WHERE p.aid=a.aid AND p.rid=".$rid);
		while($tp = DB::fetch($query)) {
			$postures[] = $tp;
		}
		return $postures;
	}
	
	public function delete_all_by_rid($rid, $unbuffered = false) {
		//[unfinish] delete attachment & comment
		return DB::delete($this->_table, DB::field('rid', $rid), null, $unbuffered);
	}
	
	public function fetch_all_by_tag($tag) {

	}
	
	public function fetch_all_by_rid($rids) {
		$roles = array();
		if(!empty($rids)) {
			$roles = DB::fetch_all('SELECT * FROM %t WHERE rid IN (%n)', array($this->_table, (array)$rids), 'rid');
		}
		return $roles;
	}

	public function fetch_all_by_uid($uids) {
		$roles = array();
		if(!empty($uids)) {
			$roles = DB::fetch_all('SELECT * FROM %t WHERE uid IN (%n)', array($this->_table, (array)$uids), 'uid');
		}
		return $roles;
	}
}

?>