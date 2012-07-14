<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: class/table/story.php 2012-06-19 21:42:00 Beijing tengattack $
 */

if(!defined('IN_EI')) {
	exit('Access Denied');
}

class table_story extends ei_table
{
	public function __construct() {

		$this->_table = 'story';
		$this->_pk    = 'sid';
		$this->_pre_cache_key = 'story_';

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
	
	public function fetch_all_name_by_sid($sids) {
		$storys = array();
		if(($sids = dintval($sids, true))) {
			foreach($this->fetch_all($sids) as $sid => $value) {
				$storys[$sid] = $value['name'];
			}
		}
		return $storys;
	}
	
	public function get_all_by_uid($uid) {
		$storys = array();
		$query = DB::query("SELECT * FROM ".DB::table($this->_table).' WHERE '.DB::field('uid', intval($uid)));
		while($ts = DB::fetch($query)) {
			$storys[] = $ts;
		}
		return $storys;
	}

	public function insert($data, $return_insert_id = false) {
		$ret = parent::insert($data, $return_insert_id);
		$insert_id = 0;
		if ($return_insert_id) {
			$insert_id = $ret;
		} else {
			$insert_id = DB::insert_id();
		}
		C::t('lastest_event')->add_event($data['uid'], 'story', $insert_id);
		return $ret;
	}
	
	public function count_by_tag($tag) {
		//return $groupid ? DB::result_first('SELECT COUNT(*) FROM %t WHERE groupid=%d', array($this->_table, $groupid)) : 0;
	}

	public function delete($val, $unbuffered = false) {
		//[unfinish] delete attachment
		$ret = false;
		if(($val = dintval($val, true))) {
			if (is_array($val)) {
				foreach($val as &$v) {
					C::t('story_scene')->delete_all_by_sid($v);
				}
				$ret = parent::delete($val, $unbuffered);
			} else {
				if (C::t('story_scene')->delete_all_by_sid($val)) {
					$ret = parent::delete($val, $unbuffered);
				}
			}
		}
		return $ret;
	}

	public function range_by_uid($from, $limit) {
		return DB::fetch_all('SELECT * FROM %t WHERE uid >= %d ORDER BY uid LIMIT %d', array($this->_table, $from, $limit), $this->_pk);
	}
}

?>