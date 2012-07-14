<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: class/helper/access.php 27823 2012-02-15 06:40:26Z zhangguosheng $
 */

if(!defined('IN_EI')) {
	exit('Access Denied');
}

class helper_access {

	public static function check_module($module) {
		$status = 0;
		$allowfuntype = array('portal', 'group', 'follow', 'collection', 'guide', 'feed', 'blog', 'doing', 'album', 'share', 'wall', 'homepage', 'ranklist');
		$module = in_array($module, $allowfuntype) ? trim($module) : '';
		if(!empty($module)) {
			$status = getglobal('setting/'.$module.'status');
		}
		return $status;
	}
}

?>