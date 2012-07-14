<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: class/table/tag.php 2012-06-14 09:10:00 Beijing tengattack $
 */

if(!defined('IN_EI')) {
	exit('Access Denied');
}

class table_tag extends ei_table
{
	public function __construct() {

		$this->_table = 'tag';
		$this->_pk    = 'tid';

		parent::__construct();
	}
	
	public function check_tags($arrtags) {
		$sql = "SELECT *
				FROM ".DB::table('tag')."
				WHERE ( ";
		
		foreach ((array)$arrtags as $key => $tag) {
			if ($key != 0) $sql .= ' OR ';
			$sql .= DB::field('name_clean', $tag['name_clean']);
		}
		$sql .= ' )';
		
		$ctags = array();
		$query = DB::query($sql);
		while($tt = DB::fetch($query)) {
			$ctags[] = $tt;
		}
		
		return $ctags;
	}
}

?>