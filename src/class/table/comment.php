<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: class/table/comment.php 2012-06-23 20:55:00 Beijing tengattack $
 */

if(!defined('IN_EI')) {
	exit('Access Denied');
}

class table_comment extends ei_table
{
	private $_tableids = array();

	public function __construct() {

		$this->_table = 'comment';
		$this->_pk    = 'cid';

		parent::__construct();
	}
	
	public function get_comments($type, $id1, $id2 = 0, $start = 0, $limit = 0, $sort = '') {
		$comments = array();
		$whereadd = '';
		if ($id2) $whereadd .= " AND c.id2= ".DB::quote($id2);
		if ($sort) $whereadd .= " ORDER BY c.time ".$sort;	//DESC
		
		$query = DB::query("SELECT c.*, m.username
				FROM ". DB::table($this->_table)." c
				INNER JOIN ".DB::table('common_member')." m USING(uid)
				WHERE c.uid=m.uid AND c.type=".DB::quote($type)." AND c.id1=".DB::quote($id1).$whereadd.DB::limit($start, $limit));
		while($tc = DB::fetch($query)) {
			$comments[] = $tc;
		}
		return $comments;
	}
	
	public function delete_comments($type, $id1, $id2 = 0) {
		$condition = array('type' => $type, 'id1' => $id1);
		if ($id2) $condition['id2'] = $id2;
		return DB::delete($this->_table, $condition);
	}
}

?>