<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: class/table/common_mailcron.php 2012-06-13 00:07:00 Beijing tengattack $
 */

if(!defined('IN_EI')) {
	exit('Access Denied');
}

class table_common_mailqueue extends ei_table
{
	public function __construct() {

		$this->_table = 'common_mailqueue';
		$this->_pk    = 'qid';

		parent::__construct();
	}

	public function fetch_all_by_cid($cids) {
		if(empty($cids)) {
			return array();
		}
		return DB::fetch_all('SELECT * FROM %t WHERE '.DB::field('cid', $cids), array($this->_table));
	}

	public function delete_by_cid($cids) {
		if(empty($cids)) {
			return false;
		}
		return DB::query('DELETE FROM %t WHERE '.DB::field('cid', $cids), array($this->_table));
	}
}

?>