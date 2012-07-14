<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: class/helper/sysmessage.php 27823 2012-02-15 06:40:26Z zhangguosheng $
 */

if(!defined('IN_EI')) {
	exit('Access Denied');
}

class helper_sysmessage {

	public static function show($message, $title = '', $msgvar = array()) {
		if(function_exists('lang')) {
			$message = lang('message', $message, $msgvar);
			$title = $title ? lang('message', $title) : lang('error', 'System Message');
		} else {
			$title = $title ? $title : 'System Message';
		}
		$charset = CHARSET;
		echo <<<EOT
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=$charset" />
<title>$title</title>
<meta name="keywords" content="" />
<meta name="description" content="System Message - Discuz! Board" />
<meta name="generator" content="Discuz! " />
<meta name="author" content="Discuz! Team and Comsenz UI Team" />
<meta name="copyright" content="2001-2012 Comsenz Inc." />
<meta name="MSSmartTagsPreventParsing" content="True" />
<meta http-equiv="MSThemeCompatible" content="Yes" />
</head>
<body bgcolor="#FFFFFF">
<table cellpadding="0" cellspacing="0" border="0" width="850" align="center" height="85%">
<tr align="center" valign="middle">
	<td>
	<table cellpadding="20" cellspacing="0" border="0" width="80%" align="center" style="font-family: Verdana, Tahoma; color: #666666; font-size: 12px">
	<tr>
	<td valign="middle" align="center" bgcolor="#EBEBEB">
		<b style="font-size: 16px">$title</b>
		<br /><br /><p style="text-align:left;">$message</p>
		<br /><br />
	</td>
	</tr>
	</table>
	</td>
</tr>
</table>
</body>
</html>
EOT;
		die();
	}

}

?>