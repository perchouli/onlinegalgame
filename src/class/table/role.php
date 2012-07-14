<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: class/table/role.php 2012-06-18 15:10:00 Beijing tengattack $
 */

if(!defined('IN_EI')) {
	exit('Access Denied');
}

class table_role extends ei_table_archive
{
	public function __construct() {

		$this->_table = 'role';
		$this->_pk    = 'rid';
		$this->_pre_cache_key = 'role_';

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
	
	public function fetch_all_name_by_rid($rids) {
		$roles = array();
		if(($rids = dintval($rids, true))) {
			foreach($this->fetch_all($rids) as $rid => $value) {
				$roles[$rid] = $value['name'];
			}
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
	
	public function insert($data, $return_insert_id = false) {
		$ret = parent::insert($data, $return_insert_id);
		$insert_id = 0;
		if ($return_insert_id) {
			$insert_id = $ret;
		} else {
			$insert_id = DB::insert_id();
		}
		C::t('lastest_event')->add_event($data['uid'], 'role', $insert_id);
		return $ret;
	}

	public function count_by_tag($tag) {
		$sql = "SELECT count(*)
				FROM ". DB::table($this->_table)." r
				INNER JOIN ".DB::table('role_tag')." s USING(rid)
				INNER JOIN ".DB::table('tag')." t USING(tid)
				WHERE r.".$this->_pk."=s.".$this->_pk." AND s.tid=t.tid AND t.name=".DB::quote($tag);
		$count = (int) DB::result_first($sql);
		return $count;
	}

	public function delete($val, $unbuffered = false) {
		//[unfinish] delete attachment & comment
		$ret = false;
		if(($val = dintval($val, true))) {
			if (is_array($val)) {
				foreach($val as &$v) {
					C::t('role_posture')->delete_all_by_rid($v);
				}
				$ret = parent::delete($val, $unbuffered);
			} else {
				if (C::t('role_posture')->delete_all_by_rid($val)) {
					$ret = parent::delete($val, $unbuffered);
				}
			}
		}
		return $ret;
	}
	
	public function range_by_uid($from, $limit) {
		return DB::fetch_all('SELECT * FROM %t WHERE uid >= %d ORDER BY uid LIMIT %d', array($this->_table, $from, $limit), $this->_pk);
	}

	public function range_by_tag($tag, $start = 0, $limit = 0, $sort = '') {
		$sql = "SELECT r.*
				FROM ". DB::table($this->_table)." r
				INNER JOIN ".DB::table('role_tag')." s USING(rid)
				INNER JOIN ".DB::table('tag')." t USING(tid)
				WHERE r.".$this->_pk."=s.".$this->_pk." AND s.tid=t.tid AND t.name=".DB::quote($tag)." ORDER BY r.".DB::order($this->_pk, $sort).DB::limit($start, $limit);
		return DB::fetch_all($sql, null, $this->_pk ? $this->_pk : '');
	}
}

?>