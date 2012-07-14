<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: mod/comment/add.php 2012-06-23 20:34:00 Beijing tengattack $
 */
 
if(!defined('IN_EI')) {
	exit('Access Denied');
}

$ctl_obj = new comment_ctl();

$ctl_obj->setting = $_G['setting'];
$method = 'on_'.$mod;
$ctl_obj->$method();

?>