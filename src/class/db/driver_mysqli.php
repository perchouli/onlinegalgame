<?php

/**
 *      [EarlyImbrian] (C)2001-2099 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: class/db/mysqli.php 2012-06-13 12:12:00 Beijing tengattack $
 */

if(!defined('IN_EI')) {
	exit('Access Denied');
}

class db_driver_mysqli extends db_mysqli
{
	function db_mysql($config = array()) {
		$this->db_mysqli($config);
	}
}

class db_mysqli
{
	var $tablepre;
	var $version = '';
	var $querynum = 0;
	var $slaveid = 0;
	var $curlink;
	var $link = array();
	var $config = array();
	var $sqldebug = array();
	var $map = array();
 
	function db_mysqli($config = array()) {
		if(!function_exists('mysqli_connect')) exit('mysqli extension not found!');
		if(!empty($config)) {
			$this->set_config($config);
		}
	}
 
	function set_config($config) {
		$this->config = &$config;
		$this->tablepre = $config['1']['tablepre'];
		if(!empty($this->config['map'])) {
			$this->map = $this->config['map'];
			for($i = 1; $i <= 100; $i++) {
				if(isset($this->map['forum_thread'])) {
					$this->map['forum_thread_'.$i] = $this->map['forum_thread'];
				}
				if(isset($this->map['forum_post'])) {
					$this->map['forum_post_'.$i] = $this->map['forum_post'];
				}
				if(isset($this->map['forum_attachment']) && $i <= 10) {
					$this->map['forum_attachment_'.($i-1)] = $this->map['forum_attachment'];
				}
			}
			if(isset($this->map['common_member'])) {
				$this->map['common_member_archive'] =
				$this->map['common_member_count'] = $this->map['common_member_count_archive'] =
				$this->map['common_member_status'] = $this->map['common_member_status_archive'] =
				$this->map['common_member_profile'] = $this->map['common_member_profile_archive'] =
				$this->map['common_member_field_forum'] = $this->map['common_member_field_forum_archive'] =
				$this->map['common_member_field_home'] = $this->map['common_member_field_home_archive'] =
				$this->map['common_member_validate'] = $this->map['common_member_verify'] =
				$this->map['common_member_verify_info'] = $this->map['common_member'];
			}
		}
	}
 
	function connect($serverid = 1) {
 
		if(empty($this->config) || empty($this->config[$serverid])) {
			$this->halt('config_db_not_found');
		}
 
		$this->link[$serverid] = $this->_dbconnect(
			$this->config[$serverid]['dbhost'],
			$this->config[$serverid]['dbuser'],
			$this->config[$serverid]['dbpw'],
			$this->config[$serverid]['dbcharset'],
			$this->config[$serverid]['dbname'],
			$this->config[$serverid]['pconnect']
			);
		$this->curlink = $this->link[$serverid];
 
	}
 
	function _dbconnect($dbhost, $dbuser, $dbpw, $dbcharset, $dbname, $pconnect) {
		$link = null;
		if(!$link = new mysqli($dbhost, $dbuser, $dbpw, $dbname)) {
			$this->halt('notconnect');
		} else {
			$this->curlink = $link;
			if($this->version() > '4.1') {
				$dbcharset = $dbcharset ? $dbcharset : $this->config[1]['dbcharset'];
				$serverset = $dbcharset ? 'character_set_connection='.$dbcharset.', character_set_results='.$dbcharset.', character_set_client=binary' : '';
				$serverset .= $this->version() > '5.0.1' ? ((empty($serverset) ? '' : ',').'sql_mode=\'\'') : '';
				$serverset && $link->query("SET $serverset");
			}
		}
		return $link;
	}
 
	function table_name($tablename) {
		if(!empty($this->map) && !empty($this->map[$tablename])) {
			$id = $this->map[$tablename];
			if(!$this->link[$id]) {
				$this->connect($id);
			}
			$this->curlink = $this->link[$id];
		} else {
			$this->curlink = $this->link[1];
		}
		return $this->tablepre.$tablename;
	}
 
	function select_db($dbname) {
		return $this->curlink->select_db($dbname);
	}
 
	function fetch_array($query, $result_type = MYSQLI_ASSOC) {
		return $query->fetch_array($result_type);
	}
 
	function fetch_first($sql) {
		return $this->fetch_array($this->query($sql));
	}
 
	function result_first($sql) {
		return $this->result($this->query($sql), 0);
	}
 
	function query($sql, $type = '') {
 
		if(defined('EI_DEBUG') && EI_DEBUG) {
			$starttime = dmicrotime();
		}
		//$func = $type == 'UNBUFFERED' && @function_exists('mysql_unbuffered_query') ? 'mysql_unbuffered_query' : 'mysql_query';
		if(!($query = $this->curlink->query($sql))) {
			if(in_array($this->errno(), array(2006, 2013)) && substr($type, 0, 5) != 'RETRY') {
				$this->connect();
				return $this->query($sql, 'RETRY'.$type);
			}
			if($type != 'SILENT' && substr($type, 5) != 'SILENT') {
				$this->halt('query_error', $sql);
			}
		}
 
		if(defined('EI_DEBUG') && EI_DEBUG) {
			$this->sqldebug[] = array($sql, number_format((dmicrotime() - $starttime), 6), debug_backtrace());
		}
 
		$this->querynum++;
		return $query;
	}
 
	function affected_rows() {
		return $this->curlink->affected_rows;
	}
 
	function error() {
		return $this->curlink->error;
	}
 
	function errno() {
		return intval($this->curlink->errno);
	}
 
	function result($query, $row = 0) {
		$query->data_seek($row);
		list($query) = $query->fetch_row();
		return $query;
	}
 
	function num_rows($query) {
		$query = $query->num_rows;
		return $query;
	}
 
	function num_fields($query) {
		return $query->field_count;
	}
 
	function free_result($query) {
		return $query->free_result();
	}
 
	function insert_id() {
		return ($id = $this->curlink->insert_id) >= 0 ? $id : $this->result($this->query("SELECT last_insert_id()"), 0);
	}
 
	function fetch_row($query) {
		$query = $query->fetch_row();
		return $query;
	}
 
	function fetch_fields($query) {
		return $query->fetch_field();
	}
 
	function version() {
		if(empty($this->version)) {
			$this->version = $this->curlink->client_version;
		}
		return $this->version;
	}
 
	function close() {
		return $this->curlink->close();
	}
 
	function halt($message = '', $sql = '') {
		throw new DbException($message, $code, $sql);
	}
	
}

?>