<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: class/table/common_statuser.php 2012-06-14 17:27:00 Beijing tengattack $
 */

if(!defined('IN_EI')) {
	exit('Access Denied');
}

class table_common_statuser extends ei_table
{
	public function __construct() {

		$this->_table = 'common_statuser';
		$this->_pk    = '';

		parent::__construct();
	}

	public function check_exists($uid, $daytime, $type) {

		$setarr = array(
			'uid' => intval($uid),
			'daytime' => intval($daytime),
			'type' => $type
		);
		if(DB::result_first('SELECT COUNT(*) FROM '.DB::table($this->_table).' WHERE '.DB::implode_field_value($setarr, ' AND '))) {
			return true;
		} else {
			return false;
		}
	}

	public function clear_by_daytime($daytime) {
		$daytime = intval($daytime);
		DB::delete('common_statuser', "`daytime` != '$daytime'");
	}
}

?>