<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: class/table/story_item.php 2012-07-08 22:21:00 Beijing tengattack $
 */

if(!defined('IN_EI')) {
	exit('Access Denied');
}

class table_story_item extends ei_table
{
	public function __construct() {

		$this->_table = 'story_item';
		$this->_pk    = 'siid';

		parent::__construct();
	}

	public function add_item($uid, $aid, $type, $name, $description = '') {
		return $this->insert(array('uid' => $uid, 'aid' => $aid, 'type' => $type, 'time' => TIMESTAMP,
							'name' => $name, 'description' => $description));
	}

	public function get_item($uid, $type, $count = 0, $start = 0, $limit = 0) {
		if($count) {
			return DB::result_first("SELECT COUNT(*)
				FROM ".DB::table($this->_table));
		}

		$items = array();
		$query = DB::query("SELECT i.*, a.*
				FROM ". DB::table($this->_table)." i
				INNER JOIN ".DB::table('attachment')." a USING(aid)
				WHERE i.aid=a.aid AND i.uid=".DB::quote($uid)." AND i.type=".DB::quote($type).DB::limit($start, $limit));
		while($te = DB::fetch($query)) {
			$items[] = $te;
		}
		return $items;
	}
}

?>