<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: class/table/role_tag.php 2012-06-14 09:10:00 Beijing tengattack $
 */

if(!defined('IN_EI')) {
	exit('Access Denied');
}

class table_role_tag extends ei_table_tag
{
	public function __construct() {

		$this->_table = 'role_tag';
		$this->_pk    = 'rid';

		parent::__construct();
	}
}

?>