<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: install/index.php 2012-06-13 17:23:00 Beijing tengattack $
 */

error_reporting(E_ERROR | E_WARNING | E_PARSE);
@set_time_limit(1000);
@set_magic_quotes_runtime(0);

define('IN_EI', TRUE);
define('ROOT_PATH', dirname(__FILE__).'/../');
define('CHARSET', 'utf-8');
define('DBCHARSET', 'utf8');

define('ORIG_TABLEPRE', 'pre_');

$default_config = $_config = array();
$default_configfile = './src/conf/global.php';

function createtable($sql) {
	$type = strtoupper(preg_replace("/^\s*CREATE TABLE\s+.+\s+\(.+?\).*(ENGINE|TYPE)\s*=\s*([a-z]+?).*$/isU", "\\2", $sql));
	$type = in_array($type, array('INNODB', 'MYISAM', 'HEAP', 'MEMORY')) ? $type : 'INNODB';
	return preg_replace("/^\s*(CREATE TABLE\s+.+\s+\(.+?\)).*$/isU", "\\1", $sql)." ENGINE=$type DEFAULT CHARSET=".DBCHARSET;
}

function runquery($sql) {
	global $lang, $tablepre, $db;

	if(!isset($sql) || empty($sql)) return;

	$sql = str_replace("\r", "\n", str_replace(' '.ORIG_TABLEPRE, ' '.$tablepre, $sql));
	$sql = str_replace("\r", "\n", str_replace(' `'.ORIG_TABLEPRE, ' `'.$tablepre, $sql));
	$ret = array();
	$num = 0;
	foreach(explode(";\n", trim($sql)) as $query) {
		$ret[$num] = '';
		$queries = explode("\n", trim($query));
		foreach($queries as $query) {
			$ret[$num] .= (isset($query[0]) && $query[0] == '#') || (isset($query[1]) && isset($query[1]) && $query[0].$query[1] == '--') ? '' : $query;
		}
		$num++;
	}
	unset($sql);

	foreach($ret as $query) {
		$query = trim($query);
		if($query) {

			if(substr($query, 0, 12) == 'CREATE TABLE') {
				$name = preg_replace("/CREATE TABLE ([a-z0-9_]+) .*/is", "\\1", $query);
				//showjsmessage(lang('create_table').' '.$name.' ... '.lang('succeed'));
				$db->query(createtable($query));
			} else {
				$db->query($query);
			}

		}
	}

}

if(!file_exists(ROOT_PATH.$default_configfile)) {
	exit('conf/global.php was lost, please reupload this file.');
} else {
	include ROOT_PATH.$default_configfile;
	$default_config = $_config;
}
$tablepre = $_config['db'][1]['tablepre'];

include ROOT_PATH.'./src/class/dbexception.php';
include ROOT_PATH.'./src/class/db/driver_mysqli.php';

$db = new db_mysqli($_config['db']);

if (isset($_GET['f'])) {
	$sql = file_get_contents(ROOT_PATH.'./install/data/' . $_GET['f']);
} else {
	$sql = file_get_contents(ROOT_PATH.'./install/data/install.sql');
	$sql .= file_get_contents(ROOT_PATH.'./install/data/role.sql');
	$sql .= file_get_contents(ROOT_PATH.'./install/data/story.sql');
	$sql .= file_get_contents(ROOT_PATH.'./install/data/comment.sql');
}
$sql = str_replace("\r\n", "\n", $sql);

$db->connect();
runquery($sql);
		
echo 'install';

?>