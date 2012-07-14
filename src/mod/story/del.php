<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: mod/story/del.php 2012-06-23 19:13:00 Beijing tengattack $
 */
 
if(!defined('IN_EI')) {
	exit('Access Denied');
}

$ctl_obj = new story_ctl();

$ctl_obj->setting = $_G['setting'];
$method = 'on_'.$mod;
$ctl_obj->$method();

?>