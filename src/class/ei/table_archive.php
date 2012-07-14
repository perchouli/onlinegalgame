<?php

/**
 *      [EarlyImbrian] (C)2001-2099 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: class/ei/table_archive.php 2012-06-14 09:43:00 Beijing tengattack $
 */

if(!defined('IN_EI')) {
	exit('Access Denied');
}


class ei_table_archive extends ei_table
{

	public $membersplit = null;
	public function __construct($para = array()) {
		$this->membersplit = getglobal('setting/membersplit');
		parent::__construct($para);
	}

	public function fetch($id, $force_from_db = false, $fetch_archive = 0){
		$data = array();
		if(!empty($id)) {
			$data = parent::fetch($id, $force_from_db);
			if(isset($this->membersplit) && $fetch_archive && empty($data)) {
				$data = C::t($this->_table.'_archive')->fetch($id);
			}
		}
		return $data;
	}


	public function fetch_all($ids, $force_from_db = false, $fetch_archive = 1) {
		$data = array();
		if(!empty($ids)) {
			$data = parent::fetch_all($ids, $force_from_db);
			if(isset($this->membersplit) && $fetch_archive && count($data) != count($ids)) {
				$data = $data + C::t($this->_table.'_archive')->fetch_all(array_diff($ids, array_keys($data)));
			}
		}
		return $data;
	}


	public function delete($val, $unbuffered = false, $fetch_archive = 0) {
		$ret = false;
		if($val) {
			$ret = parent::delete($val, $unbuffered);
			if(isset($this->membersplit) && $fetch_archive) {
				$_ret = C::t($this->_table.'_archive')->delete($val, $unbuffered);
				if(!$unbuffered) {
					$ret = $ret + $_ret;
				}
			}
		}
		return $ret;
	}

}

?>