<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: class/table/lastest_event.php 2012-06-24 21:14:00 Beijing tengattack $
 */

if(!defined('IN_EI')) {
	exit('Access Denied');
}

class table_lastest_event extends ei_table
{
	private $_tableids = array();

	public function __construct() {

		$this->_table = 'lastest_event';
		$this->_pk    = 'eid';
		
		parent::__construct();
	}

	public function count_by_uid($uid) {
		return $uid ? DB::result_first("SELECT COUNT(*) FROM %t WHERE uid=%d", array($this->_table, $uid)) : 0;
	}
	
	public function add_event($uid, $type, $id1, $id2 = 0) {
		return $this->insert(array('uid' => $uid, 'type' => $type, 'id1' => $id1, 'id2' => $id2, 'time' => TIMESTAMP));
	}

	public function get_event($count = 0, $start = 0, $limit = 0) {
		if($count) {
			return DB::result_first("SELECT COUNT(*)
				FROM ".DB::table($this->_table));
		}

		$events = array();
		$query = DB::query("SELECT m.username, m.avatarstatus, e.*
				FROM ". DB::table($this->_table)." e
				INNER JOIN ".DB::table('common_member')." m USING(uid)
				WHERE m.uid=e.uid ORDER BY e.time DESC".DB::limit($start, $limit));
		while($te = DB::fetch($query)) {
			$events[] = $te;
		}
		return $events;
	}
	
	public function get_event_by_uid($uid, $start = 0, $limit = 0) {
		$events = array();
		$query = DB::query("SELECT m.username, m.avatarstatus, e.*
				FROM ". DB::table($this->_table)." e
				INNER JOIN ".DB::table('common_member')." m USING(uid)
				WHERE m.uid=e.uid AND e.uid=".DB::quote($uid)."ORDER BY e.time DESC".DB::limit($start, $limit));
		while($te = DB::fetch($query)) {
			$events[] = $te;
		}
		return $events;
	}

}

?>