<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: class/story.php 2012-06-17 20:25:00 Beijing tengattack $
 */

if(!defined('IN_EI')) {
	exit('Access Denied');
}

define('PAGE_COUNT_STORY', 6);

class story_ctl {
	
	function story_ctl() {
		
	}
	
	function on_list() {
		global $_G;
		
		$page = $_G['p'];
		if (isset($_GET['p']) && $page != intval($_GET['p'])) {
			dheader('Location: /story.php?mod=list');
		}
		
		$story_count = C::t('story')->count();
		if (($page - 1) * PAGE_COUNT_STORY > $story_count) {
			dheader('Location: /story.php?mod=list');
		}

		$stories = C::t('story')->range(($page - 1) * PAGE_COUNT_STORY, PAGE_COUNT_STORY);
		$uids = array();
		foreach ($stories as $id => $story) {
			$stories[$id]['img'] = getattachmentpath($story['aid']);
			$stories[$id]['createdate'] = date('Y-m-d H:i', $story['createtime']);
			$stories[$id]['updatedate'] = date('Y-m-d H:i', $story['updatetime']);
			
			$uids[] = $story['uid'];
		}
		
		$uids = array_unique($uids);
		$users = C::t('common_member')->fetch_all_username_by_uid($uids);
		
		foreach ($stories as $id => $story) {
			$stories[$id]['username'] = $users[$story['uid']];
			
			/*$rp = C::t('stroy_scene')->get_scenes($story['sid']);
			if (!empty($rp)) {
				foreach ($rp as $rkey => $rtp) {
					$rp[$rkey]['img'] = getattachmentpath($rtp['aid']);
				}
			}
			$stories[$id]['posture'] = $rp;*/
		}
		
		$pagination = array (
			'page' => $page,
			'page_count' => intval($story_count / PAGE_COUNT_STORY),
			'has_previous' => ($page > 1),
			'has_next' => ($page * PAGE_COUNT_STORY < $story_count),
			
			'page_previous' => $page - 1,
			'page_next' => $page + 1,
			'file' => 'story.php',
		);
		
		if ($pagination['page_count'] * PAGE_COUNT_STORY != $story_count || $story_count == 0) {
			$pagination['page_count'] = $pagination['page_count'] + 1;
		}
		
		
		$navtitle = lang('title', CURSCRIPT.'.'.CURMODULE);
		include template($this->template);
	}
	
	function on_add() {
		global $_G;
		
		if (!$_G['uid']) {
			showmessage('permission_error', 'story.php?mod=list');
		}
		checkactivate();
		
		$story = array();
		if (isset($_GET['id'])) {
			$story['id'] = intval($_GET['id']);
			
			$s = C::t('story')->fetch($story['id']);
			if (!$s) {
				showmessage('story_info_error', '/');
			}
			
			$story = array_merge($story, $s);
			
			/*if ($_G['uid'] != $s['uid']) {...}
			 * all users can add sence
			 */
			
			setglobal('sid', $story['id']);
		}
			
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			//$_POST['formhash'] == $_G['formhash']
			
			$storydata = array();
			
			$scenedata = array (
				'uid' => $_G['uid'],
				//'aid' => 0,
				'sid' => 0,
				'sortid' => intval($_GET['sort']),
				'createtime' => TIMESTAMP,
				'updatetime' => TIMESTAMP,
				'name' => dhtmlspecialchars($_GET['scene_title']),
				'script' => processspecialchars($_GET['process']),
			);
			
			if ($story['id']) {
				$scenedata['sid'] = $story['id'];
				$scenedata['description'] = dhtmlspecialchars($_GET['description']);
			} else {
				$storydata = array (
					'uid' => $_G['uid'],
					//'aid' => 0,
					'createtime' => TIMESTAMP,
					'updatetime' => TIMESTAMP,
					'private' => 0,	//is private?
					'name' => dhtmlspecialchars($_GET['story_title']),
					'description' => dhtmlspecialchars($_GET['description']),
				);
				
				if($_FILES['story_image']) {
					$upload = new ei_upload();
					if($upload->init($_FILES['story_image'], 'image') && $upload->save(1)) {
						$storydata['aid'] = $upload->attid;
					}
				}
				
				$scenedata['sid'] = C::t('story')->insert($storydata, true);
			}
			
			if($_FILES['scene_image']) {
				$upload2 = new ei_upload();
				if($upload2->init($_FILES['scene_image'], 'image') && $upload2->save(1)) {
					$scenedata['aid'] = $upload2->attid;
				}
			}
			
			if ($scenedata['sid']) {
				if ($ssid = C::t('story_scene')->insert($scenedata, true)) {
					$referer = "story.php?mod=edit&id=".$scenedata['sid'];
					if ($story['id']) {
						$referer .= '&ssid='.$ssid;
					}
					dheader("Location: ".$referer);
				} else {
					showmessage('scene_add_error', 'story.php?mod=add&id='.$scenedata['sid']);
				}
			} else {
				showmessage('story_add_error', 'story.php?mod=add');
			}
		}
		
		$bgd_list = get_default_bgd();

		$roles = C::t('role')->get_all_by_uid($_G['uid']);
		$rt = C::t('role_link')->get_role_by_uid($_G['uid']);
		$roles = array_merge($roles, $rt);
		
		foreach ($roles as $id => $role) {
			$roles[$id]['postures'] = C::t('role_posture')->get_all_by_rid($role[rid]);
			//$roles[$id]['postures_count'] = count($roles[$id]['postures']);
			foreach ($roles[$id]['postures'] as $idp => $rp) {
				if ($rp['aid']) {
					$roles[$id]['postures'][$idp]['img'] = getattachmentpath_by_attachment($rp);
				}
			}
		}
		
		//add scene
		if ($story['id']) {
			$scenes = C::t('story_scene')->get_all_by_sid($story['id']);
			$navtitle = lang('title', CURSCRIPT.'.'.CURMODULE.'_scene');
		} else {
			$navtitle = lang('title', CURSCRIPT.'.'.CURMODULE);
		}
		
		include template($this->template);
	}
	
	function do_item($type) {
		global $_G;
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if ($_GET['do'] == 'upload') {
				//no cookies here
				$uid = intval($_GET['uid']);
				if (!$uid) {
					exit();
				}
				if ($_FILES['Filedata']) {
					$upload = new ei_upload();
					$name = dhtmlspecialchars(trim($_FILES['Filedata']['name']));
					$utype = '';
					switch ($type) {
						case 'bg':
							$utype = 'image';
							break;
						case 'music':
							$utype = 'music';
							break;
						default:
							exit('1');
					}
					if($upload->init($_FILES['Filedata'], $utype) && $upload->save(1)) {
						C::t('story_item')->add_item($uid, $upload->attid, $type, $name);
					}
					
				}
				echo '0';
			}
		} else {
			if (!$_G['uid']) {
				exit();
			}
			if ($_GET['do'] == 'update') {
				$items = C::t('story_item')->get_item($_G['uid'], $type);
				$json_items = array();
				foreach ($items as $id => $item) {
					if ($subpath = getattachmentpath_by_attachment($item)) {
						//echo $subpath.'|';
						$json_items[] = array ('id' => $item['aid'], 'name' => $item['name'], 'url' => $subpath);
					}
				}
				echo json_encode($json_items);
			}
		}
	}
	
	function on_background() {
		$this->do_item('bg');
	}
	
	function on_music() {
		$this->do_item('music');
	}
	
	function on_edit() {
		global $_G;
		
		if (!$_G['uid']) {
			showmessage('permission_error', 'story.php?mod=list');
		}
		checkactivate();
		
		$story = array (
			'id' => intval($_GET['id']),
		);
		$scene = array (
			'id' => intval($_GET['ssid']),
		);
		
		if (!$story['id']) {
			showmessage('undefined_action', '/');
		}
		
		$s = C::t('story')->fetch($story['id']);
		
		if (!$s) {
			showmessage('story_info_error', '/');
		}
		if ($scene['id']) {
			$ss = C::t('story_scene')->fetch($scene['id']);
			if (!$ss) {
				showmessage('story_info_error', '/');
			}
			
			if ($_G['uid'] != $ss['uid'] && $_G['uid'] != $s['uid']) {
				showmessage('permission_error', 'story.php?mod=list');
			}
			
			setglobal('sid', $story['id']);
			setglobal('ssid', $scene['id']);
			
			$scene = array_merge($scene, $ss);
		} else {
			if ($_G['uid'] != $s['uid']) {
				showmessage('permission_error', 'story.php?mod=list');
			}
		}
		
		$story = array_merge($story, $s);
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (isset($_GET['ajax']) && $_GET['ajax']) {
				if (isset($_GET['sort']) && isset($_GET['title']) && isset($_GET['ssid'])) {
					$sdata = array('sortid' => intval($_GET['sort']), 'name' => dhtmlspecialchars($_GET['title']));
					$condition = array('ssid' => intval($_GET['ssid']), 'sid' => $story['id']);
					if (DB::update('story_scene', $sdata, $condition)) {
						//update story
						C::t('story')->update($story['id'], array('updatetime' => TIMESTAMP));
						exit('0');
					}
				}
				exit('1');
			}
			if ($_POST['formhash'] == $_G['formhash']) {
				if (isset($_GET['story_title'])) {
					
					$storydata = array (
						'updatetime' => TIMESTAMP,
						//'private' => 0,	//is private?
						'name' => dhtmlspecialchars($_GET['story_title']),
						'description' => dhtmlspecialchars($_GET['description']),
					);
					
					if($_FILES['story_image']) {
						$upload = new ei_upload();
						if($upload->init($_FILES['story_image'], 'image') && $upload->save(1)) {
							//[unfinish] need delete old
							$storydata['aid'] = $upload->attid;
						}
					}
					
					if (C::t('story')->update($story['id'], $storydata)) {
						$story = array_merge($story, $storydata);
					}
				} else if (isset($_GET['scene_title']) && $scene['id']) {
					$scenedata = array (
						//'private' => 0,	//is private?
						'sortid' => intval($_GET['sort']),
						'updatetime' => TIMESTAMP,
						'name' => dhtmlspecialchars($_GET['scene_title']),
						'description' => dhtmlspecialchars($_GET['description']),
						'script' => processspecialchars($_GET['process']),
					);
					
					if($_FILES['scene_image']) {
						$upload = new ei_upload();
						if($upload->init($_FILES['scene_image'], 'image') && $upload->save(1)) {
							//[unfinish] need delete old
							$scenedata['aid'] = $upload->attid;
						}
					}
					
					if (C::t('story_scene')->update($scene['id'], $scenedata)) {
						$scene = array_merge($scene, $scenedata);
						
						//update story
						C::t('story')->update($story['id'], array('updatetime' => TIMESTAMP));
					}
				}
			}
		}
		
		$scenes = C::t('story_scene')->get_all_by_sid($story['id']);
		
		if ($scene['id']) {
			//get role & bgd
			$bgd_list = get_default_bgd();
	
			$crole = json_decode($scene['script'], true);
			$rids = array();

			foreach ($crole['process'] as $id => $process) {
				if ($process['type'] == 'ROLE') {
					foreach ($process['value'] as $id2 => $value) {
						list($rid, ) = explode('_', $value);
						$rids[] = $rid;
					}
				}
			}
		
			$rids = array_unique($rids);
			$crids = array();
		
			$roles = C::t('role')->get_all_by_uid($_G['uid']);
			$rt = C::t('role_link')->get_role_by_uid($_G['uid']);
			foreach ($rids as $r) {
				$bexist = false;
				foreach ($roles as $role) {
					if ($role['rid'] == $r) {
						$bexist = true;
						break;
					}
				}
				if (!$bexist) {
					foreach ($rt as $role) {
						if ($role['rid'] == $r) {
							$bexist = true;
							break;
						}
					}
				}
				if (!$bexist) {
					$crids[] = $r;
				}
			}
			
			if (!empty($crids)) {
				$rt2 = C::t('role')->get_all_by_rid($crids);
				$roles = array_merge($roles, $rt, $rt2);
			} else {
				$roles = array_merge($roles, $rt);
			}
			
			foreach ($roles as $id => $role) {
				$roles[$id]['postures'] = C::t('role_posture')->get_all_by_rid($role[rid]);
				//$roles[$id]['postures_count'] = count($roles[$id]['postures']);
				foreach ($roles[$id]['postures'] as $idp => $rp) {
					if ($rp['aid']) {
						$roles[$id]['postures'][$idp]['img'] = getattachmentpath_by_attachment($rp);
					}
				}
			}
			
			$scene['script'] = processsoutput($scene['script']);
			$scene['command'] = json_decode($scene['script'], true);
			
			$navtitle = lang('title', CURSCRIPT.'.'.CURMODULE.'_scene');
			include template('story/add');
		} else {	
			
			$navtitle = lang('title', CURSCRIPT.'.'.CURMODULE);
			include template($this->template);
		}
	}
	
	function on_show() {
		global $_G;
		
		$story = array (
			'id' => intval($_GET['id']),
		);
		
		if (!$story['id']) {
			showmessage('undefined_action', '/');
		}
		
		$s = C::t('story')->fetch($story['id']);
		if (!$s) {
			showmessage('story_info_error', '/');
		}
		
		$story = array_merge($story, $s);
		if ($story['aid']) $story['img'] = getattachmentpath($story['aid']);
		
		$scenes = C::t('story_scene')->get_all_by_sid($story['id']);
		if (!$scenes) {
			showmessage('story_no_scene', 'story.php?mod=list');
		}
		$command = '';
		
		if (isset($_GET['ssid'])) {
			$mssid = intval($_GET['ssid']);
			$bfind = false;
			foreach ($scenes as $id => $scene) {
				if ($scene['ssid'] == $mssid) {
					$bfind = true;
					$command = $scene['script'];
					break;
				}
			}
			if (!$bfind) {
				showmessage('scene_not_found', 'story.php?mod=show&id='.$story['id']);
			}
		} else {
			$mssid = $scenes[0]['ssid'];
			$command = $scenes[0]['script'];
		}
		
		setglobal('ssid', $mssid);
		
		$crole = json_decode($command, true);
		$rids = array();
		foreach ($crole['process'] as $id => $process) {
			if ($process['type'] == 'ROLE') {
				foreach ($process['value'] as $id2 => $value) {
					list($rid, ) = explode('_', $value);
					$rids[] = $rid;
				}
			}
		}
		
		$rids = array_unique($rids);
		
		$roles = C::t('role')->get_all_by_rid($rids);
		foreach ($roles as $id => $role) {
			$roles[$id]['postures'] = C::t('role_posture')->get_all_by_rid($role[rid]);
			foreach ($roles[$id]['postures'] as $idp => $rp) {
				if ($rp['aid']) {
					$roles[$id]['postures'][$idp]['img'] = getattachmentpath_by_attachment($rp);
				}
			}
		}
		
		//author
		$author = getuserbyuid($story['uid']);
		$author['profile'] = C::t('common_member_profile')->fetch($story['uid']);
		$author['avatar_url'] = getuseravatar($story['uid'], $author);
		
		//comment
		$comments = C::t('comment')->get_comments('scene', $story['id'], $_G['ssid'], 0, 15, 'DESC');
		$command = processsoutput($command);
		
		$navtitle = lang('title', CURSCRIPT.'.'.CURMODULE);
		if ($story['name']) $navtitle = $story['name'].' - '.$navtitle;
		include template($this->template);
	}
	
	function on_del() {
		global $_G;
		
		$inajax = $_GET['ajax'];
		if (!$_G['uid']) {
			if ($inajax) exit('1');
			showmessage('permission_error', 'story.php?mod=list');
		}
		
		$story = array (
			'id' => intval($_GET['id']),
		);
		$scene = array (
			'id' => intval($_GET['ssid']),
		);
		
		
		if (!$story['id']) {
			if ($inajax) exit('1');
			showmessage('undefined_action', '/');
		}
		
		if ($scene['id']) {
			$ss = C::t('story_scene')->fetch($scene['id']);
			if (!$ss) {
				if ($inajax) exit('1');
				showmessage('story_info_error', '/');
			}
			
			if ($_G['uid'] != $ss['uid']) {
				if ($inajax) exit('1');
				showmessage('permission_error', 'story.php?mod=list');
			}
			
			if (C::t('story_scene')->delete($scene['id'])) {
				C::t('comment')->delete_comments('scene', $story['id'], $scene['id']);
				if ($inajax) exit('0');
				showmessage('scene_delete_success', 'story.php?mod=list');
			} else {
				if ($inajax) exit('1');
				showmessage('scene_delete_error', 'story.php?mod=list');
			}
		} else {
			$s = C::t('story')->fetch($story['id']);
			if (!$s) {
				if ($inajax) exit('1');
				showmessage('story_info_error', '/');
			}
			
			if ($_G['uid'] != $s['uid']) {
				if ($inajax) exit('1');
				showmessage('permission_error', 'story.php?mod=list');
			}
			
			if (C::t('story')->delete($story['id'])) {
				C::t('comment')->delete_comments('scene', $story['id']);
				if ($inajax) exit('0');
				showmessage('story_delete_success', 'story.php?mod=list');
			} else {
				if ($inajax) exit('1');
				showmessage('story_delete_error', 'story.php?mod=list');
			}
		}
	}
}

?>