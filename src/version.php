<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: version.php 2012-06-13 03:55:00 Beijing tengattack $
 */

if(!defined('IN_EI')) {
	exit('Access Denied');
}

if(!defined('EI_VERSION')) {
	define('EI_VERSION', '0.01 alpha');
	define('EI_RELEASE', '20120608');
	define('EI_FIXBUG', '00000001');
}

define('VERHASH', EI_RELEASE);

?>