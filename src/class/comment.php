<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: class/comment.php 2012-06-23 20:34:00 Beijing tengattack $
 */

if(!defined('IN_EI')) {
	exit('Access Denied');
}

class comment_ctl {

	function comment_ctl() {
	
	}
	
	function on_add() {
		global $_G;
		
		if (!$_G['uid']) {
			showmessage('permission_error', 'role.php?mod=list');
		}
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['formhash'] == $_G['formhash'] && $_GET['comment']) {
			
			$commentdata = array (
				'uid' => $_G['uid'],
				'time' => TIMESTAMP,
				'type' => trim($_GET['type']),
				'content' => dhtmlspecialchars($_GET['comment']),
			);
			
			switch ($commentdata['type']) {
			case 'story':
				$commentdata['id1'] = intval($_GET['sid']);
				break;
			case 'scene':
				$commentdata['id1'] = intval($_GET['sid']);
				$commentdata['id2'] = intval($_GET['ssid']);
				break;
			default:
				showmessage('comment_add_error', dreferer());
				exit();
			}
			
			if (C::t('comment')->insert($commentdata)) {
				dheader('Location: '.dreferer());
			}
			
		}
		showmessage('comment_add_error', dreferer());
	}
}
?>