<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: class/table/story_scene.php 2012-06-21 21:38:00 Beijing tengattack $
 */

if(!defined('IN_EI')) {
	exit('Access Denied');
}

class table_story_scene extends ei_table
{
	public function __construct() {

		$this->_table = 'story_scene';
		$this->_pk    = 'ssid';
		$this->_pre_cache_key = 'story_scene_';

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
	
	public function fetch_all_name_by_ssid($ssids) {
		$scenes = array();
		if(($ssids = dintval($ssids, true))) {
			foreach($this->fetch_all($ssids) as $ssid => $value) {
				$scenes[$ssid] = $value['name'];
			}
		}
		return $scenes;
	}
	
	public function insert($data, $return_insert_id = false) {
		$ret = parent::insert($data, $return_insert_id);
		$insert_id = 0;
		if ($return_insert_id) {
			$insert_id = $ret;
		} else {
			$insert_id = DB::insert_id();
		}
		C::t('lastest_event')->add_event($data['uid'], 'scene', $data['sid'], $insert_id);
		return $ret;
	}
	
	public function delete_all_by_sid($sid, $unbuffered = false) {
		//[unfinish] delete attachment
		return DB::delete($this->_table, DB::field('sid', $sid), null, $unbuffered);
	}
	
	public function get_all_by_sid($sid) {
		$scenes = array();
		$query = DB::query("SELECT * FROM ".DB::table($this->_table).' WHERE '.DB::field('sid', intval($sid)).' ORDER BY '.DB::order('sortid'));
		while($ts = DB::fetch($query)) {
			$scenes[] = $ts;
		}
		return $scenes;
	}

	public function count_by_tag($tag) {
		//return $groupid ? DB::result_first('SELECT COUNT(*) FROM %t WHERE groupid=%d', array($this->_table, $groupid)) : 0;
	}

	public function range_by_uid($from, $limit) {
		return DB::fetch_all('SELECT * FROM %t WHERE uid >= %d ORDER BY uid LIMIT %d', array($this->_table, $from, $limit), $this->_pk);
	}
}

?>