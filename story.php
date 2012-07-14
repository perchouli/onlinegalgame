<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: story.php 2012-06-17 20:25:00 Beijing tengattack $
 */

define('CURSCRIPT', 'story');

require_once './src/class/core.php';

$ei = C::app();
$ei->init_setting = false;
$ei->init_user = true;
$ei->init_session = false;
$ei->init_cron = false;
$ei->init();

require libfile('func/story');
require libfile('class/story');
runhooks();

$mod = getgpc('mod');
//$do = getgpc('do');
$ref = 'index.php';
if(!in_array($mod, array('list', 'add', 'edit', 'show', 'del', 'background', 'music'))) {
	showmessage('undefined_action', $ref);
}

define('CURMODULE', $mod);

$navtitle = lang('title', CURSCRIPT.'.'.CURMODULE);

require EI_ROOT.'./src/mod/story/'.$mod.'.php';

?>