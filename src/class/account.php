<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: class/account.php 2012-06-14 09:10:00 Beijing tengattack $
 */

if(!defined('IN_EI')) {
	exit('Access Denied');
}

class logging_ctl {

	function logging_ctl() {
		require_once libfile('func/misc');
	}

	function logging_more($questionexist) {
		global $_G;
		if(empty($_GET['lssubmit'])) {
			return;
		}
		$auth = authcode($_GET['username']."\t".$_GET['password']."\t".($questionexist ? 1 : 0), 'ENCODE');
		$js = '<script type="text/javascript">showWindow(\'login\', \'member.php?mod=logging&action=login&auth='.rawurlencode($auth).'&referer='.rawurlencode(dreferer()).(!empty($_GET['remember_me']) ? '&remember_me=1' : '').'\')</script>';
		showmessage('location_login', '', array('type' => 1), array('extrajs' => $js));
	}

	function on_login() {
		global $_G;
		/*if($_G['uid']) {
			$referer = '/';
			//$ucsynlogin = $this->setting['allowsynlogin'] ? uc_user_synlogin($_G['uid']) : '';
			$param = array('username' => $_G['member']['username'], 'usergroup' => $_G['group']['grouptitle'], 'uid' => $_G['member']['uid']);
			showmessage('login_succeed', $referer, $param, array('showdialog' => 1, 'locationtime' => true, 'extrajs' => $ucsynlogin));
		}*/

		$from_connect = $this->setting['connect']['allow'] && !empty($_GET['from']) ? 1 : 0;
		$seccodecheck = $from_connect ? false : $this->setting['seccodestatus'] & 2;
		$seccodestatus = !empty($_GET['lssubmit']) ? false : $seccodecheck;
		//$invite = getinvite();
		
		if($_GET['formhash'] != $_G['formhash']) {
			include template($this->template);
		} /*if(!submitcheck('loginsubmit', 1, $seccodestatus)) {

			$auth = '';
			$username = !empty($_G['cookie']['loginuser']) ? dhtmlspecialchars($_G['cookie']['loginuser']) : '';

			if(!empty($_GET['auth'])) {
				list($username, $password, $questionexist) = explode("\t", authcode($_GET['auth'], 'DECODE'));
				$username = dhtmlspecialchars($username);
				if($username && $password) {
					$auth = dhtmlspecialchars($_GET['auth']);
				} else {
					$auth = '';
				}
			}

			$cookietimecheck = !empty($_G['cookie']['cookietime']) || !empty($_GET['remember_me']) ? 'checked="checked"' : '';

			if($seccodecheck) {
				$seccode = random(6, 1) + $seccode{0} * 1000000;
			}

			if($this->extrafile && file_exists($this->extrafile)) {
				require_once $this->extrafile;
			}

			$navtitle = lang('core', 'title_login');
			include template($this->template);

		}*/ else {

			/*if(!empty($_GET['auth'])) {
				list($_GET['username'], $_GET['password']) = daddslashes(explode("\t", authcode($_GET['auth'], 'DECODE')));
			}*/
		
			$_GET['username'] = $_GET['identification'];

			if(!($_G['member_loginperm'] = logincheck($_GET['username']))) {
				showmessage('login_strike');
			}
			
			$_G['uid'] = $_G['member']['uid'] = 0;
			$_G['username'] = $_G['member']['username'] = $_G['member']['password'] = '';
			if(!$_GET['username'] || $_GET['username'] != addslashes($_GET['username'])) {
				showmessage('profile_illegal');
			}
			if(!$_GET['password'] || $_GET['password'] != addslashes($_GET['password'])) {
				showmessage('profile_passwd_illegal');
			}
			
			$result = userlogin($_GET['username'], $_GET['password'], $_GET['questionid'], $_GET['answer'], $this->setting['autoidselect'] ? 'auto' : $_GET['loginfield'], $_G['clientip']);
			$uid = $result['ucresult']['uid'];

			/* 验证码
			 * if(!empty($_GET['lssubmit']) && ($result['ucresult']['uid'] == -3 || $seccodecheck && $result['status'] > 0)) {
				$_GET['username'] = $result['ucresult']['username'];
				$this->logging_more($result['ucresult']['uid'] == -3);
			}*/

			/* 提示激活
			if($result['status'] == -1) {
				if(!$this->setting['fastactivation']) {
					$auth = authcode($result['ucresult']['username']."\t".FORMHASH, 'ENCODE');
					showmessage('location_activation', 'member.php?mod='.$this->setting['regname'].'&action=activation&auth='.rawurlencode($auth).'&referer='.rawurlencode(dreferer()), array(), array('location' => true));
				} else {
					$init_arr = explode(',', $this->setting['initcredits']);
					$groupid = $this->setting['regverify'] ? 8 : $this->setting['newusergroupid'];

					C::t('common_member')->insert($uid, $result['ucresult']['username'], md5(random(10)), $result['ucresult']['email'], $_G['clientip'], $groupid, $init_arr);
					$result['member'] = getuserbyuid($uid);
					$result['status'] = 1;
				}
			}*/

			if($result['status'] > 0) {

				if($this->extrafile && file_exists($this->extrafile)) {
					require_once $this->extrafile;
				}
				
				setloginstatus($result['member'], $_GET['remember_me'] ? 2592000 : 0);
				//checkfollowfeed();

				C::t('common_member_status')->update($_G['uid'], array('lastip' => $_G['clientip'], 'lastvisit' =>TIMESTAMP, 'lastactivity' => TIMESTAMP));
				
				/*if ($result['member']['emailstatus'] == 0) {
					showmessage('location_activation', 'account.php?mod=profile');
				}*/
				
				$ucsynlogin = /*$this->setting['allowsynlogin'] ? uc_user_synlogin($_G['uid']) :*/ '';

				/*if($invite['id']) {
					$result = C::t('common_invite')->count_by_uid_fuid($invite['uid'], $uid);
					if(!$result) {
						C::t('common_invite')->update($invite['id'], array('fuid'=>$uid, 'fusername'=>$_G['username']));
						updatestat('invite');
					} else {
						$invite = array();
					}
				}
				if($invite['uid']) {
					require_once libfile('func/friend');
					friend_make($invite['uid'], $invite['username'], false);
					dsetcookie('invite_auth', '');
					if($invite['appid']) {
						updatestat('appinvite');
					}
				}*/

				$param = array(
					'username' => $result['ucresult']['username'],
					'usergroup' => $_G['group']['grouptitle'],
					'uid' => $_G['member']['uid'],
					'groupid' => $_G['groupid'],
					'syn' => $ucsynlogin ? 1 : 0
				);

				$extra = array(
					'showdialog' => true,
					'locationtime' => true,
					'extrajs' => $ucsynlogin
				);

				$loginmessage = $result['member']['emailstatus'] == 0 /*$_G['groupid'] == 8*/ ? 'login_succeed_inactive_member' : 'login_succeed';
				$location = $result['member']['emailstatus'] == 0 ? 'account.php?mod=profile' : '/';
				
				if(empty($_GET['handlekey']) || !empty($_GET['lssubmit'])) {
					if(defined('IN_MOBILE')) {
						showmessage('location_login_succeed_mobile', $location, array('username' => $result['ucresult']['username']), array('location' => true));
					} else {
						if(!empty($_GET['lssubmit'])) {
							if(!$ucsynlogin) {
								$extra['location'] = true;
							}
							showmessage($loginmessage, $location, $param, $extra);
						} else {
							$href = str_replace("'", "\'", $location);
							//'$(\'succeedlocation\').innerHTML = \''.lang('message', $loginmessage, $param).'\';</script>'
							showmessage('location_login_succeed', $location, array(),
								array(
									'showid' => 'succeedmessage',
									'extrajs' => '<script type="text/javascript">'.
										'setTimeout("window.location.href =\''.$href.'\';", 3000);'.
										'$(\'succeedmessage_href\').href = \''.$href.'\';'.
										'$(\'main_message\').style.display = \'none\';'.
										'$(\'main_succeed\').style.display = \'\';'.
										'</script>'.lang('message', $loginmessage, $param).'<br />'.
										$ucsynlogin,
									'striptags' => false,
									'showdialog' => true
								)
							);
						}
					}
				} else {
					showmessage($loginmessage, $location, $param, $extra);
				}
			} else {
				$password = preg_replace("/^(.{".round(strlen($_GET['password']) / 4)."})(.+?)(.{".round(strlen($_GET['password']) / 6)."})$/s", "\\1***\\3", $_GET['password']);
				$errorlog = dhtmlspecialchars(
					TIMESTAMP."\t".
					($result['ucresult']['username'] ? $result['ucresult']['username'] : $_GET['username'])."\t".
					$password."\t".
					"Ques #".intval($_GET['questionid'])."\t".
					$_G['clientip']);
				writelog('illegallog', $errorlog);
				loginfailed($_GET['username']);
				$fmsg = $result['ucresult']['uid'] == '-3' ? (empty($_GET['questionid']) || $answer == '' ? 'login_question_empty' : 'login_question_invalid') : 'login_invalid';
				if($_G['member_loginperm'] > 1) {
					showmessage($fmsg, '', array('loginperm' => $_G['member_loginperm'] - 1));
				} elseif($_G['member_loginperm'] == -1) {
					showmessage('login_password_invalid');
				} else {
					showmessage('login_strike');
				}
			}

		}

	}

	function on_logout() {
		global $_G;

		$ucsynlogout = /*$this->setting['allowsynlogin'] ? uc_user_synlogout() :*/ '';

		/*if($_GET['formhash'] != $_G['formhash']) {
			showmessage('logout_succeed', dreferer(), array('formhash' => FORMHASH, 'ucsynlogout' => $ucsynlogout));
		}*/
		
		//reset temp password (must be activated)
		if ($_G['uid'] && $_G['member']['emailstatus']) {
			$password = md5(random(10));
			C::t('common_member')->update($_G['uid'], array('password' => $password));
		}
		
		/*if(is_array($_COOKIE) && (empty($_G['uid']) || ($_G['uid'] && $formhash == formhash()))) {
			foreach($_G['cookie'] as $key => $val) {
				dsetcookie($key, '', -1, 0);
			}
			foreach($_COOKIE as $key => $val) {
				setcookie($key, '', -1, $_G['config']['cookie']['cookiepath'], '');
			}
		}*/
		clearcookies();
		
		$_G['groupid'] = $_G['member']['groupid'] = 7;
		$_G['uid'] = $_G['member']['uid'] = 0;
		$_G['username'] = $_G['member']['username'] = $_G['member']['password'] = '';
		$_G['setting']['styleid'] = $this->setting['styleid'];

		showmessage('logout_succeed', dreferer(), array('formhash' => FORMHASH, 'ucsynlogout' => $ucsynlogout));
	}

}

class register_ctl {

	var $showregisterform = 1;

	function register_ctl() {
		global $_G;
		if($_G['setting']['bbclosed']) {
			if(($_GET['action'] != 'activation' && !$_GET['activationauth']) || !$_G['setting']['closedallowactivation'] ) {
				showmessage('register_disable', NULL, array(), array('login' => 1));
			}
		}

		loadcache(array('modreasons', 'stamptypeid', 'fields_required', 'fields_optional', 'fields_register', 'ipctrl'));
		/*require_once libfile('func/misc');
		require_once libfile('func/profile');*/
		if(!function_exists('sendmail')) {
			include libfile('func/mail');
		}
		//loaducenter();
	}

	function on_register() {
		global $_G;

		$_GET['password'] = $_GET['password1'];

		if ($this->setting['regclosed']) {
			showmessage(!$this->setting['regclosemessage'] ? 'register_disable' : str_replace(array("\r", "\n"), '', $this->setting['regclosemessage']));
		}
		/* elseif(!$this->setting['regclosed'] && (!$this->setting['regstatus'] || !$this->setting['ucactivation'])) {
			if($_GET['action'] == 'activation' || $_GET['activationauth']) {
				if(!$this->setting['ucactivation'] && !$this->setting['closedallowactivation']) {
					showmessage('register_disable_activation');
				}
			} elseif(!$this->setting['regstatus']) {
				showmessage(!$this->setting['regclosemessage'] ? 'register_disable' : str_replace(array("\r", "\n"), '', $this->setting['regclosemessage']));
			}
		}*/
		
		/*$bbrules = & $this->setting['bbrules'];
		$bbrulesforce = & $this->setting['bbrulesforce'];
		$bbrulestxt = & $this->setting['bbrulestxt'];
		$welcomemsg = & $this->setting['welcomemsg'];
		$welcomemsgtitle = & $this->setting['welcomemsgtitle'];
		$welcomemsgtxt = & $this->setting['welcomemsgtxt'];
		$regname = $this->setting['regname'];*/

		if($this->setting['regverify']) {
			if($this->setting['areaverifywhite']) {
				$location = $whitearea = '';
				$location = trim(convertip($_G['clientip'], "./"));
				if($location) {
					$whitearea = preg_quote(trim($this->setting['areaverifywhite']), '/');
					$whitearea = str_replace(array("\\*"), array('.*'), $whitearea);
					$whitearea = '.*'.$whitearea.'.*';
					$whitearea = '/^('.str_replace(array("\r\n", ' '), array('.*|.*', ''), $whitearea).')$/i';
					if(@preg_match($whitearea, $location)) {
						$this->setting['regverify'] = 0;
					}
				}
			}

			if($_G['cache']['ipctrl']['ipverifywhite']) {
				foreach(explode("\n", $_G['cache']['ipctrl']['ipverifywhite']) as $ctrlip) {
					if(preg_match("/^(".preg_quote(($ctrlip = trim($ctrlip)), '/').")/", $_G['clientip'])) {
						$this->setting['regverify'] = 0;
						break;
					}
				}
			}
		}
		
		$invitestatus = false;
		if($this->setting['regstatus'] == 2) {
			if($this->setting['inviteconfig']['inviteareawhite']) {
				$location = $whitearea = '';
				$location = trim(convertip($_G['clientip'], "./"));
				if($location) {
					$whitearea = preg_quote(trim($this->setting['inviteconfig']['inviteareawhite']), '/');
					$whitearea = str_replace(array("\\*"), array('.*'), $whitearea);
					$whitearea = '.*'.$whitearea.'.*';
					$whitearea = '/^('.str_replace(array("\r\n", ' '), array('.*|.*', ''), $whitearea).')$/i';
					if(@preg_match($whitearea, $location)) {
						$invitestatus = true;
					}
				}
			}

			if($this->setting['inviteconfig']['inviteipwhite']) {
				foreach(explode("\n", $this->setting['inviteconfig']['inviteipwhite']) as $ctrlip) {
					if(preg_match("/^(".preg_quote(($ctrlip = trim($ctrlip)), '/').")/", $_G['clientip'])) {
						$invitestatus = true;
						break;
					}
				}
			}
		}

		$groupinfo = array();
		if($this->setting['regverify']) {
			$groupinfo['groupid'] = 8;
		} else {
			$groupinfo['groupid'] = $this->setting['newusergroupid'];
		}

		$seccodecheck = $this->setting['seccodestatus'] & 1;
		$secqaacheck = $this->setting['secqaa']['status'] & 1;
		$fromuid = !empty($_G['cookie']['promotion']) && $this->setting['creditspolicy']['promotion_register'] ? intval($_G['cookie']['promotion']) : 0;
		$username = isset($_GET['username']) ? $_GET['username'] : '';
		$bbrulehash = $bbrules ? substr(md5(FORMHASH), 0, 8) : '';
		$auth = $_GET['auth'];

		if(!$invitestatus) {
			$invite = getinvite();
		}
		$sendurl = $this->setting['sendregisterurl'] ? true : false;
		if($sendurl) {
			if(!empty($_GET['hash'])) {
				$hash = explode("\t", authcode($_GET['hash'], 'DECODE', $_G['config']['security']['authkey']));
				if(is_array($hash) && isemail($hash[0]) && TIMESTAMP - $hash[1] < 259200) {
					$sendurl = false;
				}
			}
		}
		
		if($_GET['formhash'] != $_G['formhash']) {
			include template($this->template);
		}
		/*if(!submitcheck('regsubmit', 0, $seccodecheck, $secqaacheck)) {

			if(!$sendurl) {
				if($_GET['action'] == 'activation') {
					$auth = explode("\t", authcode($auth, 'DECODE'));
					if(FORMHASH != $auth[1]) {
						showmessage('register_activation_invalid', 'member.php?mod=logging&action=login');
					}
					$username = $auth[0];
					$activationauth = authcode("$auth[0]\t".FORMHASH, 'ENCODE');
				}

				if($fromuid) {
					$member = getuserbyuid($fromuid);
					if(!empty($member)) {
						$fromuser = dhtmlspecialchars($member['username']);
					} else {
						dsetcookie('promotion');
					}
				}

				$bbrulestxt = nl2br("\n$bbrulestxt\n\n");
				if($_GET['action'] == 'activation') {
					$auth = dhtmlspecialchars($auth);
				}

				if($seccodecheck) {
					$seccode = random(6, 1);
				}

				$username = dhtmlspecialchars($username);

				$htmls = $settings = array();
				foreach($_G['cache']['fields_register'] as $field) {
					$fieldid = $field['fieldid'];
					$html = profile_setting($fieldid, array(), false, false, true);
					if($html) {
						$settings[$fieldid] = $_G['cache']['profilesetting'][$fieldid];
						$htmls[$fieldid] = $html;
					}
				}

				$navtitle = $this->setting['reglinkname'];

				if($this->extrafile && file_exists($this->extrafile)) {
					require_once $this->extrafile;
				}
			}
			$dreferer = dreferer();

			include template($this->template);

		}*/ else {
		
			$_GET['email'] = strtolower(trim($_GET['email']));
			
			if($sendurl) {
				checkemail($_GET['email']);
			}
			$emailstatus = 0;
			/*if($this->setting['sendregisterurl'] && !$sendurl) {
				$_GET['email'] = strtolower($hash[0]);
				$this->setting['regverify'] = $this->setting['regverify'] == 1 ? 0 : $this->setting['regverify'];
				if(!$this->setting['regverify']) {
					$groupinfo['groupid'] = $this->setting['newusergroupid'];
				}
				$emailstatus = 1;
			}*/

			if($this->setting['regstatus'] == 2 && empty($invite) && !$invitestatus) {
				showmessage('not_open_registration_invite');
			}

			if($bbrules && $bbrulehash != $_POST['agreebbrule']) {
				showmessage('register_rules_agree');
			}

			$activation = array();
			/*if(isset($_GET['activationauth'])) {
				$activationauth = explode("\t", authcode($_GET['activationauth'], 'DECODE'));
				if($activationauth[1] == FORMHASH && !($activation = uc_get_user($activationauth[0]))) {
					showmessage('register_activation_invalid', 'member.php?mod=logging&action=login');
				}
			}

			//未激活
			if(!$activation)*/ {
				$usernamelen = dstrlen($username);
				if($usernamelen < 2) {
					showmessage('profile_username_tooshort');
				} elseif($usernamelen > 15) {
					showmessage('profile_username_toolong');
				}
				if (ei_get_user(addslashes($username))) {
					if (ei_user_is_active($username)) {
						showmessage('profile_username_duplicate');
					} else {
						showmessage('register_activation_message', 'account.php?mod=signin', array('username' => $username));
					}
				}
				
				if($this->setting['pwlength']) {
					if(strlen($_GET['password']) < $this->setting['pwlength']) {
						showmessage('profile_password_tooshort', '', array('pwlength' => $this->setting['pwlength']));
					}
				}
				if($this->setting['strongpw']) {
					$strongpw_str = array();
					if(in_array(1, $this->setting['strongpw']) && !preg_match("/\d+/", $_GET['password'])) {
						$strongpw_str[] = lang('member/template', 'strongpw_1');
					}
					if(in_array(2, $this->setting['strongpw']) && !preg_match("/[a-z]+/", $_GET['password'])) {
						$strongpw_str[] = lang('member/template', 'strongpw_2');
					}
					if(in_array(3, $this->setting['strongpw']) && !preg_match("/[A-Z]+/", $_GET['password'])) {
						$strongpw_str[] = lang('member/template', 'strongpw_3');
					}
					if(in_array(4, $this->setting['strongpw']) && !preg_match("/[^a-zA-z0-9]+/", $_GET['password'])) {
						$strongpw_str[] = lang('member/template', 'strongpw_4');
					}
					if($strongpw_str) {
						showmessage(lang('member/template', 'password_weak').implode(',', $strongpw_str));
					}
				}
				$email = strtolower(trim($_GET['email']));
				if(empty($this->setting['ignorepassword'])) {
					if($_GET['password'] !== $_GET['password2']) {
						showmessage('profile_passwd_notmatch');
					}

					if(!$_GET['password'] || $_GET['password'] != addslashes($_GET['password'])) {
						showmessage('profile_passwd_illegal');
					}
					$password = $_GET['password'];
				} else {
					$password = md5(random(10));
				}
			}

			$censorexp = '/^('.str_replace(array('\\*', "\r\n", ' '), array('.*', '|', ''), preg_quote(($this->setting['censoruser'] = trim($this->setting['censoruser'])), '/')).')$/i';

			if($this->setting['censoruser'] && @preg_match($censorexp, $username)) {
				showmessage('profile_username_protect');
			}

			if($this->setting['regverify'] == 2 && !trim($_GET['regmessage'])) {
				showmessage('profile_required_info_invalid');
			}

			if($_G['cache']['ipctrl']['ipregctrl']) {
				foreach(explode("\n", $_G['cache']['ipctrl']['ipregctrl']) as $ctrlip) {
					if(preg_match("/^(".preg_quote(($ctrlip = trim($ctrlip)), '/').")/", $_G['clientip'])) {
						$ctrlip = $ctrlip.'%';
						$this->setting['regctrl'] = $this->setting['ipregctrltime'];
						break;
					} else {
						$ctrlip = $_G['clientip'];
					}
				}
			} else {
				$ctrlip = $_G['clientip'];
			}
			
			if($this->setting['regctrl']) {
				if(C::t('common_regip')->count_by_ip_dateline($ctrlip, $_G['timestamp']-$this->setting['regctrl']*3600)) {
					showmessage('register_ctrl', NULL, array('regctrl' => $this->setting['regctrl']));
				}
			}
			
			$setregip = null;
			if($this->setting['regfloodctrl']) {
				$regip = C::t('common_regip')->fetch_by_ip_dateline($_G['clientip'], $_G['timestamp']-86400);
				if($regip) {
					if($regip['count'] >= $this->setting['regfloodctrl']) {
						showmessage('register_flood_ctrl', NULL, array('regfloodctrl' => $this->setting['regfloodctrl']));
					} else {
						$setregip = 1;
					}
				} else {
					$setregip = 2;
				}
			}

			$profile = $verifyarr = array();
			foreach($_G['cache']['fields_register'] as $field) {
				if(defined('IN_MOBILE')) {
					break;
				}
				$field_key = $field['fieldid'];
				$field_val = $_GET[''.$field_key];
				if($field['formtype'] == 'file' && !empty($_FILES[$field_key]) && $_FILES[$field_key]['error'] == 0) {
					$field_val = true;
				}

				if(!profile_check($field_key, $field_val)) {
					$showid = !in_array($field['fieldid'], array('birthyear', 'birthmonth')) ? $field['fieldid'] : 'birthday';
					showmessage($field['title'].lang('message', 'profile_illegal'), '', array(), array(
						'showid' => 'chk_'.$showid,
						'extrajs' => $field['title'].lang('message', 'profile_illegal').($field['formtype'] == 'text' ? '<script type="text/javascript">'.
							'$(\'registerform\').'.$field['fieldid'].'.className = \'px er\';'.
							'$(\'registerform\').'.$field['fieldid'].'.onblur = function () { if(this.value != \'\') {this.className = \'px\';$(\'chk_'.$showid.'\').innerHTML = \'\';}}'.
							'</script>' : '')
					));
				}
				if($field['needverify']) {
					$verifyarr[$field_key] = $field_val;
				} else {
					$profile[$field_key] = $field_val;
				}
			}

			if(!$activation) {
				$uid = ei_user_register(addslashes($username), $password, $email, $questionid, $answer, $_G['clientip']);

				if($uid <= 0) {
					if($uid == -1) {
						showmessage('profile_username_illegal');
					} elseif($uid == -2) {
						showmessage('profile_username_protect');
					} elseif($uid == -3) {
						showmessage('profile_username_duplicate');
					} elseif($uid == -4) {
						showmessage('profile_email_illegal');
					} elseif($uid == -5) {
						showmessage('profile_email_domain_illegal');
					} elseif($uid == -6) {
						showmessage('profile_email_duplicate');
					} else {
						showmessage('undefined_action');
					}
				}
			} else {
				list($uid, $username, $email) = $activation;
			}
			$_G['username'] = $username;
			if(getuserbyuid($uid, 1)) {
				if(!$activation) {
					ei_user_delete($uid);
				}
				showmessage('profile_uid_duplicate', '', array('uid' => $uid));
			}

			$password = md5(random(10));
			$secques = $questionid > 0 ? random(8) : '';

			if(isset($_POST['birthmonth']) && isset($_POST['birthday'])) {
				$profile['constellation'] = get_constellation($_POST['birthmonth'], $_POST['birthday']);
			}
			if(isset($_POST['birthyear'])) {
				$profile['zodiac'] = get_zodiac($_POST['birthyear']);
			}

			if($setregip !== null) {
				if($setregip == 1) {
					C::t('common_regip')->update_count_by_ip($_G['clientip']);
				} else {
					C::t('common_regip')->insert(array('ip' => $_G['clientip'], 'count' => 1, 'dateline' => $_G['timestamp']));
				}
			}

			if($invite && $this->setting['inviteconfig']['invitegroupid']) {
				$groupinfo['groupid'] = $this->setting['inviteconfig']['invitegroupid'];
			}

			$init_arr = array('credits' => explode(',', $this->setting['initcredits']), 'profile'=>$profile, 'emailstatus' => $emailstatus);

			C::t('common_member')->insert($uid, $username, $password, $email, $_G['clientip'], $groupinfo['groupid'], $init_arr);
			if($emailstatus) {
				updatecreditbyaction('realemail', $uid);
			}
			if($verifyarr) {
				$setverify = array(
					'uid' => $uid,
					'username' => $username,
					'verifytype' => '0',
					'field' => serialize($verifyarr),
					'dateline' => TIMESTAMP,
				);
				C::t('common_member_verify_info')->insert($setverify);
				C::t('common_member_verify')->insert(array('uid' => $uid));
			}

			/*require_once libfile('cache/userstats', 'function');
			build_cache_userstats();

			if($this->extrafile && file_exists($this->extrafile)) {
				require_once $this->extrafile;
			}*/

			if($this->setting['regctrl'] || $this->setting['regfloodctrl']) {
				C::t('common_regip')->delete_by_dateline($_G['timestamp']-($this->setting['regctrl'] > 72 ? $this->setting['regctrl'] : 72)*3600);
				if($this->setting['regctrl']) {
					C::t('common_regip')->insert(array('ip' => $_G['clientip'], 'count' => -1, 'dateline' => $_G['timestamp']));
				}
			}

			$regmessage = dhtmlspecialchars($_GET['regmessage']);
			if($this->setting['regverify'] == 2) {
				C::t('common_member_validate')->insert(array(
					'uid' => $uid,
					'submitdate' => $_G['timestamp'],
					'moddate' => 0,
					'admin' => '',
					'submittimes' => 1,
					'status' => 0,
					'message' => $regmessage,
					'remark' => '',
				), false, true);
				//manage_addnotify('verifyuser');
			}
			
			if($sendurl) {
				$hashstr = urlencode(authcode("$_GET[email]\t$_G[timestamp]", 'ENCODE', $_G['config']['security']['authkey']));
				$registerurl = "{$_G[siteurl]}account.php?mod=activate&amp;hash={$hashstr}&amp;uid={$uid}";
				$email_register_message = lang('email', 'email_register_message', array(
					'bbname' => $this->setting['bbname'],
					'siteurl' => $_G['siteurl'],
					'url' => $registerurl
				));

				if(!sendmail("$_GET[email] <$_GET[email]>", lang('email', 'email_register_subject'), $email_register_message)) {
					runlog('sendmail', "$_GET[email] sendmail failed.");
				}
				//dreferer()
				showmessage('register_email_send_succeed', 'account.php?mod=signin', array('bbname' => $this->setting['bbname']), array('showdialog' => true, 'msgtype' => 3, 'closetime' => 10));
			}

			setloginstatus(array(
				'uid' => $uid,
				'username' => $_G['username'],
				'password' => $password,
				'groupid' => $groupinfo['groupid'],
			), 0);
			/*include_once libfile('func/stat');
			updatestat('register');

			if($invite['id']) {
				$result = C::t('common_invite')->count_by_uid_fuid($invite['uid'], $uid);
				if(!$result) {
					C::t('common_invite')->update($invite['id'], array('fuid'=>$uid, 'fusername'=>$_G['username'], 'regdateline' => $_G['timestamp'], 'status' => 2));
					updatestat('invite');
				} else {
					$invite = array();
				}
			}
			if($invite['uid']) {
				if($this->setting['inviteconfig']['inviteaddcredit']) {
					updatemembercount($uid, array($this->setting['inviteconfig']['inviterewardcredit'] => $this->setting['inviteconfig']['inviteaddcredit']));
				}
				if($this->setting['inviteconfig']['invitedaddcredit']) {
					updatemembercount($invite['uid'], array($this->setting['inviteconfig']['inviterewardcredit'] => $this->setting['inviteconfig']['invitedaddcredit']));
				}
				require_once libfile('func/friend');
				friend_make($invite['uid'], $invite['username'], false);
				notification_add($invite['uid'], 'friend', 'invite_friend', array('actor' => '<a href="home.php?mod=space&uid='.$invite['uid'].'" target="_blank">'.$invite['username'].'</a>'), 1);

				space_merge($invite, 'field_home');
				if(!empty($invite['privacy']['feed']['invite'])) {
					require_once libfile('func/feed');
					$tite_data = array('username' => '<a href="home.php?mod=space&uid='.$_G['uid'].'">'.$_G['username'].'</a>');
					feed_add('friend', 'feed_invite', $tite_data, '', array(), '', array(), array(), '', '', '', 0, 0, '', $invite['uid'], $invite['username']);
				}
				if($invite['appid']) {
					updatestat('appinvite');
				}
			}

			if($welcomemsg && !empty($welcomemsgtxt)) {
				$welcomemsgtitle = replacesitevar($welcomemsgtitle);
				$welcomemsgtxt = replacesitevar($welcomemsgtxt);
				if($welcomemsg == 1) {
					$welcomemsgtxt = nl2br(str_replace(':', '&#58;', $welcomemsgtxt));
					notification_add($uid, 'system', $welcomemsgtxt, array('from_id' => 0, 'from_idtype' => 'welcomemsg'), 1);
				} elseif($welcomemsg == 2) {
					sendmail_cron($email, $welcomemsgtitle, $welcomemsgtxt);
				} elseif($welcomemsg == 3) {
					sendmail_cron($email, $welcomemsgtitle, $welcomemsgtxt);
					$welcomemsgtxt = nl2br(str_replace(':', '&#58;', $welcomemsgtxt));
					notification_add($uid, 'system', $welcomemsgtxt, array('from_id' => 0, 'from_idtype' => 'welcomemsg'), 1);
				}
			}

			if($fromuid) {
				updatecreditbyaction('promotion_register', $fromuid);
				dsetcookie('promotion', '');
			}*/
			dsetcookie('loginuser', '');
			dsetcookie('activationauth', '');
			dsetcookie('invite_auth', '');

			$url_forward = dreferer();
			$refreshtime = 3000;
			switch($this->setting['regverify']) {
				case 1:
					$idstring = random(6);
					$authstr = $this->setting['regverify'] == 1 ? "$_G[timestamp]\t2\t$idstring" : '';
					C::t('common_member_field_forum')->update($_G['uid'], array('authstr' => $authstr));
					$verifyurl = "{$_G[siteurl]}member.php?mod=activate&amp;uid={$_G[uid]}&amp;id=$idstring";
					$email_verify_message = lang('email', 'email_verify_message', array(
						'username' => $_G['member']['username'],
						'bbname' => $this->setting['bbname'],
						'siteurl' => $_G['siteurl'],
						'url' => $verifyurl
					));
					if(!sendmail("$username <$email>", lang('email', 'email_verify_subject'), $email_verify_message)) {
						runlog('sendmail', "$email sendmail failed.");
					}
					$message = 'register_email_verify';
					$locationmessage = 'register_email_verify_location';
					$refreshtime = 10000;
					break;
				case 2:
					$message = 'register_manual_verify';
					$locationmessage = 'register_manual_verify_location';
					break;
				default:
					$message = 'register_succeed';
					$locationmessage = 'register_succeed_location';
					break;
			}
			$param = array('bbname' => $this->setting['bbname'], 'username' => $_G['username'], 'usergroup' => $_G['group']['grouptitle'], 'uid' => $_G['uid']);
			if(strpos($url_forward, $this->setting['regname']) !== false || strpos($url_forward, 'buyinvitecode') !== false) {
				$url_forward = 'forum.php';
			}
			$href = str_replace("'", "\'", $url_forward);
			$extra = array(
				'showid' => 'succeedmessage',
				'extrajs' => '<script type="text/javascript">'.
					'setTimeout("window.location.href =\''.$href.'\';", '.$refreshtime.');'.
					'$(\'succeedmessage_href\').href = \''.$href.'\';'.
					'$(\'main_message\').style.display = \'none\';'.
					'$(\'main_succeed\').style.display = \'\';'.
					'$(\'succeedlocation\').innerHTML = \''.lang('message', $locationmessage).'\';'.
				'</script>',
				'striptags' => false,
			);
			showmessage($message, $url_forward, $param, $extra);
		}
	}

}

class crime_action_ctl {

	var $actions = array('all', 'crime_delpost', 'crime_warnpost', 'crime_banpost', 'crime_banspeak', 'crime_banvisit', 'crime_banstatus', 'crime_avatar', 'crime_sightml', 'crime_customstatus');

	function crime_action_ctl() {}

	function &instance() {
		static $object;
		if(empty($object)) {
			$object = new crime_action_ctl();
		}
		return $object;
	}

	function recordaction($uid, $action, $reason) {
		global $_G;

		$uid = intval($uid);
		$key = array_search($action, $this->actions);
		if($key === FALSE) {
			return false;
		}
		$insert = array(
			'uid' => $uid,
			'operatorid' => $_G['uid'],
			'operator' => $_G['username'],
			'action' => $key,
			'reason' => $reason,
			'dateline' => $_G['timestamp']
		);
		C::t('common_member_crime')->insert($insert);
		return true;
	}

	function getactionlist($uid) {
		$uid = intval($uid);
		$clist = array();
		foreach(C::t('common_member_crime')->fetch_all_by_uid($uid) as $c) {
			$c['action'] = $this->actions[$c['action']];
			$clist[] = $c;
		}
		return $clist;
	}

	function getcount($uid, $action) {
		$uid = intval($uid);
		$key = array_search($action, $this->actions);
		if($key === FALSE) {
			return 0;
		}
		return C::t('common_member_crime')->count_by_uid_action($uid, $key);
	}

	function search($action, $username, $operator, $startime, $endtime, $reason, $start, $limit) {
		$action = intval($action);
		$operator = daddslashes(trim($operator));
		$starttime = $starttime ? strtotime($starttime) : 0;
		$endtime = $endtime ? (strtotime($endtime) + 3600 * 24) : 0;
		$reason = daddslashes(trim($reason));
		$start = intval($start);
		$limit = intval($limit);

		if(!empty($username)) {
			$uid = C::t('common_member')->fetch_uid_by_username($username);
			$wheresql[] = "uid='$uid'";
		}

		if($action) {
			$wheresql[] = "action='$action'";
		}
		if($operator) {
			$wheresql[] = "operator='$operator'";
		}
		if($starttime) {
			$wheresql[] = "dateline>='$starttime'";
		}
		if($endtime) {
			$wheresql[] = "dateline<='$endtime'";
		}
		if($reason) {
			$wheresql[] = "reason LIKE '%$reason%'";
		}

		if($wheresql) {
			$wheresql = 'WHERE '.implode(' AND ', $wheresql);
		} else {
			$wheresql = '';
		}
		$clist = array();
		$count = C::t('common_member_crime')->count_by_where($wheresql);

		if($count) {
			$uids = array();
			foreach(C::t('common_member_crime')->fetch_all_by_where($wheresql, $start, $limit) as $crime) {
				$crime['action'] = $this->actions[$crime['action']];
				$clist[] = $crime;
				$uids[$crime['uid']] = $crime['uid'];
			}
			$members = C::t('common_member')->fetch_all($uids, false, 0);
			foreach($clist as $key => $crime) {
				$crime['username'] = $members[$crime['uid']]['username'];
				$clist[$key] = $crime;
			}
		}
		return array($count, $clist);
	}
}

class password_ctl {
	
	function password_ctl() {
		
		loadcache(array('modreasons', 'stamptypeid', 'fields_required', 'fields_optional', 'fields_register', 'ipctrl'));
		
		if(!function_exists('sendmail')) {
			include libfile('func/mail');
		}
		
		//[unfinish] ip filter / ip need ctrl!
	}
	
	function on_reset() {
		global $_G;
		
		if ($_GET['hash'] && $_GET['uid']) {
			$uid = intval($_GET['uid']);
			$hash = explode("\t", authcode($_GET['hash'], 'DECODE', $_G['config']['security']['authkey']));
			
			if (intval($hash[0]) != $uid || TIMESTAMP - $hash[2] >= 259200) {
				showmessage('user_info_error', 'account.php?mod=password&do=reset');
			}
			
			$u = C::t('account')->fetch($uid);
			if ($hash[1] !== $u['password']) {
				showmessage('user_info_error', 'account.php?mod=password&do=reset');
			}
					
			$user = array(
				'uid' => $uid,
				'username' => $u['username'],
				'hash' => dhtmlspecialchars($_GET['hash']),
			);
			
			$navtitle = lang('title', CURSCRIPT.'.password_input');
			include template('account/password_input');
		} else {
			
			$navtitle = lang('title', CURSCRIPT.'.password_reset');
			include template('account/password_reset');
		}
	}
	
	function on_change() {
		global $_G;
		if (!$_G['uid']) {
			showmessage('permission_error', 'account.php?mod=signin');
		}
		
		$navtitle = lang('title', CURSCRIPT.'.password_change');
		include template('account/password_change');
	}
	
	function on_submit() {
		global $_G;

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if ($_POST['formhash'] == $_G['formhash']) {
				if ($_GET['type'] == 'reset') {
					$_GET['email'] = trim($_GET['email']);
					if ($_GET['username'] && $_GET['email']) {
						$user = C::t('account')->fetch_by_username($_GET['username']);
						if ($user) {
							$common_user = getuserbyuid($user['uid']);
							$email = strtolower(trim($_GET['email']));
							if ($common_user && $common_user['email'] == $email) {
								$hashstr = urlencode(authcode("$user[uid]\t$user[password]\t$_G[timestamp]", 'ENCODE', $_G['config']['security']['authkey']));
								$registerurl = "{$_G[siteurl]}account.php?mod=password&amp;do=reset&amp;hash={$hashstr}&amp;uid={$user[uid]}";
								
								$email_pr_message = lang('email', 'email_password_reset_message', array(
									'bbname' => $this->setting['bbname'],
									'siteurl' => $_G['siteurl'],
									'url' => $registerurl
								));
								
								if(!sendmail("$_GET[email] <$_GET[email]>", lang('email', 'email_password_reset_subject'), $email_pr_message)) {
									runlog('sendmail', "$_GET[email] sendmail failed.");
								}

								showmessage('password_reset_email_send_succeed', 'account.php?mod=signin');
							}
						}
						showmessage('user_info_error', 'account.php?mod=password&do=reset');
					}
				} elseif ($_GET['type'] == 'input') {
					if ($_GET['password1'] && $_GET['password2'] && $_GET['uid'] && $_GET['hash']) {
						
						$_GET['password'] = $_GET['password1'];
						if($_GET['password'] !== $_GET['password2']) {
							showmessage('profile_passwd_notmatch');
						}
	
						if(!$_GET['password'] || $_GET['password'] != addslashes($_GET['password'])) {
							showmessage('profile_passwd_illegal');
						}
						if($this->setting['pwlength']) {
							if(strlen($_GET['password']) < $this->setting['pwlength']) {
								showmessage('profile_password_tooshort', '', array('pwlength' => $this->setting['pwlength']));
							}
						}
						$password = $_GET['password'];
						
						$uid = intval($_GET['uid']);
						$hash = explode("\t", authcode($_GET['hash'], 'DECODE', $_G['config']['security']['authkey']));
						
						if (intval($hash[0]) != $uid || TIMESTAMP - $hash[2] >= 259200) {
							showmessage('user_info_error', 'account.php?mod=password&do=reset');
						}
						
						$u = C::t('account')->fetch($uid);
						if ($hash[1] !== $u['password']) {
							showmessage('user_info_error', 'account.php?mod=password&do=reset');
						}
						
						$password = phpbb_hash($password);
						C::t('account')->update($uid, array('password' => $password));
						
						//fix login status
						$password = md5(random(10));
						C::t('common_member')->update($uid, array('password' => $password));
						
						showmessage('reset_password_success', 'account.php?mod=signin');
					}
					showmessage('reset_password_error', 'account.php?mod=password&do=reset');
				} elseif ($_GET['type'] == 'change') {
					if (!$_G['uid']) {
						showmessage('permission_error', 'account.php?mod=signin');
					}
					
					if ($_GET['password1'] && $_GET['password2'] && $_GET['oldpassword']) {
						
						$uid = $_G['uid'];
						$_GET['password'] = $_GET['password1'];
						
						if($_GET['password'] !== $_GET['password2']) {
							showmessage('profile_passwd_notmatch');
						}
	
						if(!$_GET['password'] || $_GET['password'] != addslashes($_GET['password'])) {
							showmessage('profile_passwd_illegal');
						}
						if($this->setting['pwlength']) {
							if(strlen($_GET['password']) < $this->setting['pwlength']) {
								showmessage('profile_password_tooshort', '', array('pwlength' => $this->setting['pwlength']));
							}
						}
						
						$account = C::t('account')->fetch($uid);
						if (!phpbb_check_hash($_GET['oldpassword'], $account['password'])) {
							showmessage('profile_passwd_wrong', 'account.php?mod=password&do=change');
						}
						
						$password = $_GET['password'];
						$password = phpbb_hash($password);
						C::t('account')->update($uid, array('password' => $password));
						
						//fix login status
						$password = md5(random(10));
						C::t('common_member')->update($uid, array('password' => $password));
						
						clearcookies();
						showmessage('change_password_success', 'account.php?mod=signin');
					}
					
					
				}
			}
		}
		
		if (in_array($_GET['type'], array('reset', 'change'))) {
			dheader('Location: account.php?mod=password&do='.$_GET['type']);
		} else {
			showmessage('undefined_action');
		}
	}
	
}

class profile_ctl {
	
	function profile_ctl() {
		
	}
	
	function do_saveavatar() {
		$user = & $this->user;
		if (!$user['uid']) {
			return false;
		}
		
		if (!$_FILES['avatar']) {
			return false;
		}
		
		$upload = new ei_upload();
		if($upload->init($_FILES['avatar'], 'avatar') && $upload->save(1, $user['uid'])) {
			C::t('common_member')->update($user['uid'], array('avatarstatus' => 1));
			return true;
		}
		
		return false;
	}
	
	function on_edit() {
		global $_G;
		
		$user = & $this->user;
		if ($user['uid'] != $_G['uid'] || !$_G['uid']) {
			showmessage('permission_error', '/');
		}

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if ($_POST['formhash'] == $_G['formhash']) {
				
				$up = array (
					'realname' => trim($_GET['realname']),
					'mobile' => trim($_GET['mobile']),
					'qq' => trim($_GET['qq']),
					'msn' => trim($_GET['msn']),
					'site' => trim($_GET['site']),
					'bio' => trim($_GET['bio']),
					'gender' => intval($_GET['gender']),
				);

				if ($up['gender'] != 0 && $up['gender'] != 1 && $up['gender'] != 2) {
					$up['gender'] = 0;
				}
				
				if ($up['site']) {
					if (strtolower(substr($up['site'], 0, 7)) != 'http://') {
						$up['site'] = 'http://'.$up['site'];
					}
				}
				
				$up = dhtmlspecialchars($up);

				C::t('common_member_profile')->update($user['uid'], $up);
				$this->do_saveavatar();
				
				dheader('Location: account.php?mod=profile&id='.$user['uid']);
			}
		}
		
		$u = getuserbyuid($user['uid']);
		if (!$u) showmessage('message_bad_touid', '/');
		$user = array_merge($user, $u);
		
		$profile = C::t('common_member_profile')->fetch($user['uid']);
		$user['avatar_url'] = getuseravatar($user['uid'], $user);
		
		$navtitle = lang('title', CURSCRIPT.'.'.CURMODULE.'_edit');
		include template($this->template);
	}
	
	function on_antiactivate_email() {
		global $_G;
		
		$user = & $this->user;
		
		if ($user['uid'] != $_G['uid'] || !$_G['uid']) {
			showmessage('permission_error', '/');
		}
		if ($_GET['formhash'] != $_G['formhash']) {
			dheader('Location: account.php?mod=profile&id='.$user['uid']);
		}

		$uid = $user['uid'];
		$u = getuserbyuid($uid);
		if (!$u) {
			showmessage('message_bad_touid', '/');
		}
		if ($u['emailstatus'] != 1) {
			showmessage('email_is_not_activated', 'account.php?mod=profile&id='.$user['uid']);
		}
		
		$_GET['email'] = $u['email'];
		if(!function_exists('sendmail')) {
			include libfile('func/mail');
		}
		
		$hashstr = urlencode(authcode("antiactivate\t$_GET[email]\t$_G[timestamp]", 'ENCODE', $_G['config']['security']['authkey']));
		$registerurl = "{$_G[siteurl]}account.php?mod=antiactivate&amp;hash={$hashstr}&amp;uid={$uid}";
		$email_register_message = lang('email', 'email_antiactivate_message', array(
			'bbname' => $this->setting['bbname'],
			'siteurl' => $_G['siteurl'],
			'url' => $registerurl
		));

		if(!sendmail("$_GET[email] <$_GET[email]>", lang('email', 'email_antiactivate_subject'), $email_register_message)) {
			runlog('sendmail', "$_GET[email] sendmail failed.");
		}
		//dreferer()
		showmessage('antiactivate_email_send_succeed', 'account.php?mod=profile&id='.$user['uid'], array('bbname' => $this->setting['bbname']), array('showdialog' => true, 'msgtype' => 3, 'closetime' => 10));
	}
	
	function on_resend_email() {
		global $_G;
		
		$user = & $this->user;
		
		if ($user['uid'] != $_G['uid'] || !$_G['uid']) {
			showmessage('permission_error', '/');
		}
		if ($_GET['formhash'] != $_G['formhash']) {
			dheader('Location: account.php?mod=profile&id='.$user['uid']);
		}

		$uid = $user['uid'];
		$u = getuserbyuid($uid);
		if (!$u) {
			showmessage('message_bad_touid', '/');
		}
		if ($u['emailstatus'] == 1) {
			showmessage('email_is_activated', 'account.php?mod=profile&id='.$user['uid']);
		}
		
		$_GET['email'] = $u['email'];
		if(!function_exists('sendmail')) {
			include libfile('func/mail');
		}

		$hashstr = urlencode(authcode("$_GET[email]\t$_G[timestamp]", 'ENCODE', $_G['config']['security']['authkey']));
		$registerurl = "{$_G[siteurl]}account.php?mod=activate&amp;hash={$hashstr}&amp;uid={$uid}";
		$email_register_message = lang('email', 'email_register_message', array(
			'bbname' => $this->setting['bbname'],
			'siteurl' => $_G['siteurl'],
			'url' => $registerurl
		));

		if(!sendmail("$_GET[email] <$_GET[email]>", lang('email', 'email_register_subject'), $email_register_message)) {
			runlog('sendmail', "$_GET[email] sendmail failed.");
		}
		//dreferer()
		showmessage('register_email_send_succeed', 'account.php?mod=profile&id='.$user['uid'], array('bbname' => $this->setting['bbname']), array('showdialog' => true, 'msgtype' => 3, 'closetime' => 10));
	}
	
	function on_change_email() {
		global $_G;
		
		$user = & $this->user;
		if ($user['uid'] != $_G['uid'] || !$_G['uid']) {
			showmessage('permission_error', '/');
		}
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if ($_POST['formhash'] == $_G['formhash']) {
				
				if ($_GET['password'] && $_GET['email']) {
					//check email
					$email = strtolower(trim($_GET['email']));
					$sendurl = true;
					checkemail($email);
					if ($email) {
						$uid = $user['uid'];
						$account = C::t('account')->fetch($uid);
						if (!phpbb_check_hash($_GET['password'], $account['password'])) {
							showmessage('profile_passwd_wrong', 'account.php?mod=profile&do=change_email&id='.$user['uid']);
						}
						$u = getuserbyuid($uid);
						if (!$u) {
							showmessage('message_bad_touid', '/');
						}
						if ($u['emailstatus'] == 1) {
							showmessage('email_is_activated', 'account.php?mod=profile&do=change_email&id='.$user['uid']);
						}
						
						if(!function_exists('sendmail')) {
							include libfile('func/mail');
						}

						C::t('common_member')->update($uid, array('email' => $email, 'emailstatus' => 0));
						$_GET['email'] = $email;
						
						if($sendurl) {
							$hashstr = urlencode(authcode("$_GET[email]\t$_G[timestamp]", 'ENCODE', $_G['config']['security']['authkey']));
							$registerurl = "{$_G[siteurl]}account.php?mod=activate&amp;hash={$hashstr}&amp;uid={$uid}";
							$email_register_message = lang('email', 'email_register_message', array(
								'bbname' => $this->setting['bbname'],
								'siteurl' => $_G['siteurl'],
								'url' => $registerurl
							));
			
							if(!sendmail("$_GET[email] <$_GET[email]>", lang('email', 'email_register_subject'), $email_register_message)) {
								runlog('sendmail', "$_GET[email] sendmail failed.");
							}
							//dreferer()
							showmessage('register_email_send_succeed', 'account.php?mod=profile&id='.$user['uid'], array('bbname' => $this->setting['bbname']), array('showdialog' => true, 'msgtype' => 3, 'closetime' => 10));
						}
					}
				}
			}
			
			dheader('Location: account.php?mod=profile&id='.$user['uid']);
		}
		
		$u = getuserbyuid($user['uid']);
		if (!$u) showmessage('message_bad_touid', '/');
		$user = array_merge($user, $u);
		
		$profile = C::t('common_member_profile')->fetch($user['uid']);
		$user['avatar_url'] = getuseravatar($user['uid'], $user);
		
		$navtitle = lang('title', CURSCRIPT.'.'.CURMODULE.'_change_email');
		include template($this->template);
	}
}

?>