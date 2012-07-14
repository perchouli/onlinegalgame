<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: index.php 2012-06-13 04:12:00 Beijing tengattack $
 */

define('CURMODULE', 'index');
define('CURSCRIPT', 'ei');

require_once './src/class/core.php';

C::app()->init_setting = true;
//C::app()->init_user = false;
C::app()->init_session = false;
C::app()->init_cron = false;
C::app()->init_misc = false;
C::app()->init();

//echo 'Welcome to LUNA-PROJECT!!<br />(C)2009-2012 EarlyImbrian Committee.';
runhooks();

require libfile('func/event');
$events = getevents();

//$navtitle = lang('title', CURMODULE);
include template('index/'.CURSCRIPT);

/*global $_G;
exit($_G['username']);*/
?>