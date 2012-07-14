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

class table_common_cache extends ei_table
{
	public function __construct() {

		$this->_table = 'common_cache';
		$this->_pk    = 'cachekey';

		parent::__construct();
	}

}

?>