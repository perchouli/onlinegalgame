<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: class/table/common_cron.php 2012-06-13 12:34:00 Beijing tengattack $
 */

if(!defined('IN_EI')) {
	exit('Access Denied');
}

class table_common_cron extends ei_table
{
	public function __construct() {

		$this->_table = 'common_cron';
		$this->_pk    = 'cronid';

		parent::__construct();
	}

	public function fetch_nextrun($timestamp) {
		$timestamp = intval($timestamp);
		return DB::fetch_first('SELECT * FROM '.DB::table($this->_table)."	WHERE available>'0' AND nextrun<='$timestamp' ORDER BY nextrun LIMIT 1");
	}

	public function fetch_nextcron() {
		return DB::fetch_first('SELECT * FROM '.DB::table($this->_table)."	WHERE available>'0' ORDER BY nextrun LIMIT 1");
	}


}

?>