<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: class/role.php 2012-06-17 20:04:00 Beijing tengattack $
 */

if(!defined('IN_EI')) {
	exit('Access Denied');
}

define('PAGE_COUNT_ROLE', 9);

class role_ctl {
	
	function role_ctl() {
		
	}
	
	function on_list() {
		global $_G;
		
		$tag = '';
		$page = $_G['p'];
		if (isset($_GET['p']) && $page != intval($_GET['p'])) {
			dheader('Location: /role.php?mod=list');
		}
		
		if (isset($_GET['tag']) && $_GET['tag']) {
			$tag = trim($_GET['tag']);
		}
		if ($tag) {
			$role_count = C::t('role')->count_by_tag($tag);
		} else {
			$role_count = C::t('role')->count();
		}
		if (($page - 1) * PAGE_COUNT_ROLE > $role_count) {
			dheader('Location: /role.php?mod=list');
		}

		if ($tag) {
			$roles = C::t('role')->range_by_tag($tag, ($page - 1) * PAGE_COUNT_ROLE, PAGE_COUNT_ROLE);
		} else {
			$roles = C::t('role')->range(($page - 1) * PAGE_COUNT_ROLE, PAGE_COUNT_ROLE);
		}
		
		$uids = array();
		foreach ($roles as $id => $role) {
			$roles[$id]['img'] = getattachmentpath($role['aid']);
			$uids[] = $role['uid'];
		}
		
		$uids = array_unique($uids);
		$users = C::t('common_member')->fetch_all_username_by_uid($uids);
		$links = array();
		$linkrids = array();
		if ($_G['uid']) $links = C::t('role_link')->get_all_by_uid($_G['uid']);
		
		foreach ($links as $id => $link) {
			$linkrids[] = $link['rid'];
		}
		
		foreach ($roles as $id => $role) {
			$roles[$id]['username'] = $users[$role['uid']];
			
			$rp = C::t('role_posture')->get_all_by_rid($role['rid']);
			if (!empty($rp)) {
				foreach ($rp as $rkey => $rtp) {
					$rp[$rkey]['img'] = getattachmentpath_by_attachment($rtp);
				}
			}
			$roles[$id]['posture'] = $rp;
			
			//is link?
			$roles[$id]['link'] = in_array($role['rid'], $linkrids);
			$roles[$id]['tag'] = C::t('role_tag')->get_tags($role['rid']);
			$roles[$id]['tag_text'] = implode(' ', $roles[$id]['tag']);
		}
		
		$pagination = array (
			'page' => $page,
			'page_count' => intval($role_count / PAGE_COUNT_ROLE),
			'has_previous' => ($page > 1),
			'has_next' => ($page * PAGE_COUNT_ROLE < $role_count),
			
			'page_previous' => $page - 1,
			'page_next' => $page + 1,
			'file' => 'role.php',
		);
		
		if ($pagination['page_count'] * PAGE_COUNT_ROLE != $role_count || $role_count == 0) {
			$pagination['page_count'] = $pagination['page_count'] + 1;
		}
		
		
		$navtitle = lang('title', CURSCRIPT.'.'.CURMODULE);
		include template($this->template);
	}
	
	function on_show() {
		global $_G;
		
		$role = array (
			'id' => intval($_GET['id']),
			'isshow' => true,
			'tags_errors' => '',
			'image_errors' => '',
		);
		
		if (!$role['id']) {
			showmessage('undefined_action', '/');
		}
		
		$r = C::t('role')->fetch($role['id']);
		if (!$r) {
			showmessage('role_error', '/');
		}
		
		$role = array_merge($role, $r);
		if ($role['aid']) $role['img'] = getattachmentpath($role['aid']);
		
		$role['posture'] = C::t('role_posture')->get_all_by_rid($role['id']);
		if (!empty($role['posture'])) {
			foreach ($role['posture'] as $rkey => $rtp) {
				$role['posture'][$rkey]['img'] = getattachmentpath_by_attachment($rtp);
			}
		}
		$role['posture_count'] = $role['posture'] ? count($role['posture']) : 0;
		$role['tag'] = C::t('role_tag')->get_tags($role['id']);
		foreach ((array)$role['tag'] as $key => $tag) {
			if ($key != 0) $role['tag_text'] .= ' ';
			$role['tag_text'] .= $tag['name_clean'];
		}

		$navtitle = $role['name'].' - '.lang('title', CURSCRIPT.'.'.CURMODULE);
		include template($this->template);
	}
	
	function on_add() {
		global $_G;
		
		if (!$_G['uid']) {
			showmessage('permission_error', 'role.php?mod=list');
		}
		checkactivate();
		
		$role = array (
			'id' => 0,
			'gender' => 0,
			'tags_errors' => '',
			'image_errors' => '',
		);

		if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['formhash'] == $_G['formhash']) {
			
			if($_GET['name'] && $_FILES['image']) {
				$upload = new ei_upload();
				if($upload->init($_FILES['image'], 'image') && $upload->save(1)) {
					
					$gender = intval($_GET['gender']);
					if ($gender < 0 || $gender > 2) $gender = 0;
					if (!isset($_GET['description'])) $_GET['description'] = '';
					
					$roledata = array (
						'uid' => $_G['uid'],
						'aid' => $upload->attid,
						'updatetime' => TIMESTAMP,
						'private' => 0,	//is private?
						'gender' => $gender,
						'name' => dhtmlspecialchars($_GET['name']),
						'description' => dhtmlspecialchars($_GET['description']),
					);
					
					if (!($roleid = C::t('role')->insert($roledata, true))) {
						showmessage('role_add_error', '/role.php?mod=add');
					}
					
					if ($_GET['tags']) C::t('role_tag')->update_tags($roleid, $_GET['tags']);
					
					dheader('Location: role.php?mod=edit&id='.$roleid);
				}
			} else {
				$role['image_errors'] = 'Error!';
			}
		}
		
		$navtitle = lang('title', CURSCRIPT.'.'.CURMODULE);
		include template($this->template);
	}
	
	function on_addposture() {
		global $_G;
		
		if (!$_G['uid']) {
			showmessage('permission_error', 'role.php?mod=list');
		}
		checkactivate();
		
		$role = array (
			'id' => intval($_GET['id']),
		);
			
		if (!$role['id']) {
			showmessage('undefined_action', '/');
		}
			
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {

			$r = C::t('role')->fetch($role['id']);
			if (!$r) {
				showmessage('role_error', '/');
			}

			$rposture = C::t('role_posture')->get_postures($role['id']);
			
			$pid_max = 0;
			if (!empty($rposture)) {
				foreach ($rposture as $rkey => $rtp) {
					$pid_max = max($pid_max, $rtp['pid']);
				}
			}
			 
			$role = array_merge($role, $r);
			
			$new_posture = array();
			
			foreach ($_POST as $key => $tp) {
				$arr = explode('_', $key);
				if ($arr[0] === 'id' && $arr[1] === 'posture') {
					$pid = intval($arr[3]);

					$new_posture[$pid][$arr[2]] = $tp;
				}
			}
			
			if (!empty($new_posture)) {
				foreach ($new_posture as $key => $tp) {
					if ($tp['pid']) {
						if (!$tp['name']) {
							$tp['name'] = 'name'.$tp['pid'];
						}
						//change
						$np = array (
							'name' => dhtmlspecialchars($tp['name']),
							//description
						);
						
						$upload = new ei_upload();
						if($upload->init($_FILES['id_posture_image_'.$key], 'image') && $upload->save(1)) {
							$np['aid'] = $upload->attid;
						}
						
						$need_update = false;
						// check need delete
						foreach ($rposture as $rkey => $rtp) {
							if ($rtp['pid'] == $tp['pid']) {
								$need_update = ($np['name'] != $rtp['name']);
								$rposture[$rkey]['exist'] = true;
								break;
							}
						}
						if (!$need_update) $need_update = $np['aid'] ? true : false;

						if ($need_update) DB::update('role_posture', $np, 
									array('rid'=>$role['id'], 'pid' => $tp['pid'], 'uid' => $_G['uid']));
					} else {
						
						$pid_max++;
						
						if (!$tp['name']) {
							$tp['name'] = 'name'.$pid_max;
						}
						$np = array (
							'pid' => $pid_max,
							'rid' => $role['id'],
							'uid' => $_G['uid'],
							'name' => dhtmlspecialchars($tp['name']),
							//[unfinish]description
						);
						
						if ($_FILES['id_posture_image_'.$key]) {
							$upload = new ei_upload();
							if($upload->init($_FILES['id_posture_image_'.$key], 'image') && $upload->save(1)) {
								$np['aid'] = $upload->attid;
								//[unfinish]delete old attach
							}
						}
						
						C::t('role_posture')->insert($np);
					}
				}
			}
			
			foreach ($rposture as $rkey => $rtp) {
				if (!$rposture[$rkey]['exist']) {
					DB::delete('role_posture', array('rid'=>$role['id'], 'pid' => $rtp['pid'], 'uid' => $_G['uid']));
				}
			}
		}
		
		dheader("Location: role.php?mod=edit&id=".$role['id']);
	}
	
	function on_edit() {
		global $_G;
		
		if (!$_G['uid']) {
			showmessage('permission_error', 'role.php?mod=list');
		}
		checkactivate();
		
		$role = array (
			'id' => intval($_GET['id']),
			'tags_errors' => '',
			'image_errors' => '',
		);
		
		if (!$role['id']) {
			showmessage('undefined_action', '/');
		}
		
		$r = C::t('role')->fetch($role['id']);
		if (!$r) {
			showmessage('role_error', '/');
		}
		
		if ($_G['uid'] != $r['uid']) {
			showmessage('permission_error', 'role.php?mod=list');
		}
		
		$role = array_merge($role, $r);
		
		if ($role['aid']) $role['img'] = getattachmentpath($role['aid']);
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['formhash'] == $_G['formhash']) {
			
			if (!$_GET['name']) {
				$_GET['name'] = $role['name'];
			}
			
			$gender = intval($_GET['gender']);
			if ($gender < 0 || $gender > 2) $gender = 0;
			if (!isset($_GET['description'])) $_GET['description'] = '';
			
			$roledata = array (
				//'aid' => $upload->attid,
				'updatetime' => TIMESTAMP,
				'private' => 0,	//is private?
				'gender' => $gender,
				'name' => dhtmlspecialchars($_GET['name']),
				'description' => dhtmlspecialchars($_GET['description']),
			);
			
			if ($_FILES['image']) {
				$upload = new ei_upload();
				if($upload->init($_FILES['image'], 'image') && $upload->save(1)) {
					//[unfinish]delete old attach
					$roledata['aid'] = $upload->attid;
				}
			}
			
			if (!($roleid = C::t('role')->update($role['id'], $roledata, true))) {
				showmessage('role_edit_error', '/role.php?mod=edit&id='.$role['id']);
			}
			
			C::t('role_tag')->update_tags($role['id'], $_GET['tags']);
			
			$role = array_merge($role, $roledata);
		}
		
		$role['posture'] = C::t('role_posture')->get_all_by_rid($role['id']);
		if (!empty($role['posture'])) {
			foreach ($role['posture'] as $rkey => $rtp) {
				$role['posture'][$rkey]['img'] = getattachmentpath_by_attachment($rtp);
			}
		}
		$role['posture_count'] = $role['posture'] ? count($role['posture']) : 0;
		$role['tag'] = C::t('role_tag')->get_tags($role['id']);
		foreach ((array)$role['tag'] as $key => $tag) {
			if ($key != 0) $role['tag_text'] .= ' ';
			$role['tag_text'] .= $tag['name_clean'];
		}
		
		$navtitle = lang('title', CURSCRIPT.'.'.CURMODULE);
		include template($this->template);
	}
	
	function on_link() {
		global $_G;
		
		if (!$_G['uid']) {
			exit('1');
		}
		if (!isactivate()) {
			exit('1');
		}
		
		$role = array (
			'id' => intval($_GET['id']),
		);
		
		if (!$role['id']) {
			exit('1');
		}
		
		$r = C::t('role')->fetch($role['id']);
		if (!$r) {
			exit('1');
		}
		//the same author
		if ($_G['uid'] == $r['uid']) {
			exit('1');
		}
		
		$role = array_merge($role, $r);
		
		$qcount = DB::result_first("SELECT COUNT(*) FROM ".DB::table('role_link')." WHERE ".DB::field('rid', $role['id'])." AND ".DB::field('uid', $_G['uid']));
		$qcount = intval($qcount);
		
		$linkdata = array ('rid' => $role['id'], 'uid' => $_G['uid']);
		if ($_GET['do'] == 'del') {
			if ($qcount > 0) {
				C::t('role_link')->delete($linkdata);
			} else {
				exit('1');
			}
		} else {
			if ($qcount == 0) {
				C::t('role_link')->insert($linkdata);
			} else {
				exit('1');
			}
		}
		exit('0');
	}
	
	function on_del() {
		global $_G;
		
		$inajax = $_GET['ajax'];
		if (!$_G['uid']) {
			if ($inajax) exit('1');
			showmessage('permission_error', 'role.php?mod=list');
		}
		
		$role = array (
			'id' => intval($_GET['id']),
		);
		
		if (!$role['id']) {
			if ($inajax) exit('1');
			showmessage('undefined_action', 'role.php?mod=list');
		}
		
		$r = C::t('role')->fetch($role['id']);
		if (!$r) {
			if ($inajax) exit('1');
			showmessage('role_info_error', 'role.php?mod=list');
		}

		if ($_G['uid'] != $r['uid']) {
			if ($inajax) exit('1');
			showmessage('permission_error', 'role.php?mod=list');
		}
		
		if (C::t('role')->delete($role['id'])) {
			if ($inajax) exit('0');
			showmessage('role_delete_success', 'role.php?mod=list');
		} else {
			if ($inajax) exit('1');
			showmessage('role_delete_error', 'role.php?mod=list');
		}
	}
}


?>