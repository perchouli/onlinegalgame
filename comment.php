<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: comment.php 2012-06-17 20:01:00 Beijing tengattack $
 */

define('CURSCRIPT', 'comment');

require_once './src/class/core.php';

$ei = C::app();
$ei->init_setting = false;
$ei->init_user = true;
$ei->init_session = false;
$ei->init_cron = false;
$ei->init();

require libfile('func/comment');
require libfile('class/comment');
runhooks();

$mod = getgpc('mod');
//$do = getgpc('do');
$ref = '/';
if(!in_array($mod, array('add'))) {
	showmessage('undefined_action', $ref);
}

define('CURMODULE', $mod);

//$navtitle = lang('title', CURSCRIPT.'.'.CURMODULE);

require EI_ROOT.'./src/mod/comment/'.$mod.'.php';

?>