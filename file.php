<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: file.php 2012-07-07 13:50:00 Beijing tengattack $
 */

define('CURSCRIPT', 'show');
define('CURMODULE', 'file');

require_once './src/class/core.php';

C::app()->init_setting = false;
C::app()->init_user = false;
C::app()->init_session = false;
C::app()->init_cron = false;
C::app()->init_misc = false;
C::app()->init();

$attid = intval($_GET['id']);
$attach_src = '';
if ($attid) $attach_src = getattachmentpath($attid);
if ($attach_src) {
	dheader("Location: ".getattachmentpath($attid));
} else {
	showmessage('portal_attachment_noexist', '/');
}

?>