<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: class/ei/cron.php 2012-06-13 12:33:00 Beijing tengattack $
 */

if(!defined('IN_EI')) {
	exit('Access Denied');
}

class ei_cron
{

	public static function run($cronid = 0) {
		global $_G;
		$cron = $cronid ? C::t('common_cron')->fetch($cronid) : C::t('common_cron')->fetch_nextrun(TIMESTAMP);

		$processname ='EI_CRON_'.(empty($cron) ? 'CHECKER' : $cron['cronid']);

		if($cronid && !empty($cron)) {
			ei_process::unlock($processname);
		}

		if(ei_process::islocked($processname, 600)) {
			return false;
		}

		if($cron) {

			$cron['filename'] = str_replace(array('..', '/', '\\'), '', $cron['filename']);
			$cronfile = EI_ROOT.'./src/inc/cron/'.$cron['filename'];

			$cron['minute'] = explode("\t", $cron['minute']);
			self::setnextime($cron);

			@set_time_limit(1000);
			@ignore_user_abort(TRUE);

			if(!@include $cronfile) {
				return false;
			}
		}

		self::nextcron();
		ei_process::unlock($processname);
		return true;
	}

	private static function nextcron() {
		$cron = C::t('common_cron')->fetch_nextcron();
		if($cron && isset($cron['nextrun'])) {
			savecache('cronnextrun', $cron['nextrun']);
		} else {
			savecache('cronnextrun', TIMESTAMP + 86400 * 365);
		}
		return true;
	}

	private static function setnextime($cron) {

		if(empty($cron)) return FALSE;

		list($yearnow, $monthnow, $daynow, $weekdaynow, $hournow, $minutenow) = explode('-', gmdate('Y-m-d-w-H-i', TIMESTAMP + getglobal('setting/timeoffset') * 3600));

		if($cron['weekday'] == -1) {
			if($cron['day'] == -1) {
				$firstday = $daynow;
				$secondday = $daynow + 1;
			} else {
				$firstday = $cron['day'];
				$secondday = $cron['day'] + gmdate('t', TIMESTAMP + getglobal('setting/timeoffset') * 3600);
			}
		} else {
			$firstday = $daynow + ($cron['weekday'] - $weekdaynow);
			$secondday = $firstday + 7;
		}

		if($firstday < $daynow) {
			$firstday = $secondday;
		}

		if($firstday == $daynow) {
			$todaytime = self::todaynextrun($cron);
			if($todaytime['hour'] == -1 && $todaytime['minute'] == -1) {
				$cron['day'] = $secondday;
				$nexttime = self::todaynextrun($cron, 0, -1);
				$cron['hour'] = $nexttime['hour'];
				$cron['minute'] = $nexttime['minute'];
			} else {
				$cron['day'] = $firstday;
				$cron['hour'] = $todaytime['hour'];
				$cron['minute'] = $todaytime['minute'];
			}
		} else {
			$cron['day'] = $firstday;
			$nexttime = self::todaynextrun($cron, 0, -1);
			$cron['hour'] = $nexttime['hour'];
			$cron['minute'] = $nexttime['minute'];
		}

		$nextrun = @gmmktime($cron['hour'], $cron['minute'] > 0 ? $cron['minute'] : 0, 0, $monthnow, $cron['day'], $yearnow) - getglobal('setting/timeoffset') * 3600;
		$data = array('lastrun' => TIMESTAMP, 'nextrun' => $nextrun);
		if(!($nextrun > TIMESTAMP)) {
			$data['available'] = '0';
		}
		C::t('common_cron')->update($cron['cronid'], $data);

		return true;
	}

	private static function todaynextrun($cron, $hour = -2, $minute = -2) {

		$hour = $hour == -2 ? gmdate('H', TIMESTAMP + getglobal('setting/timeoffset') * 3600) : $hour;
		$minute = $minute == -2 ? gmdate('i', TIMESTAMP + getglobal('setting/timeoffset') * 3600) : $minute;

		$nexttime = array();
		if($cron['hour'] == -1 && !$cron['minute']) {
			$nexttime['hour'] = $hour;
			$nexttime['minute'] = $minute + 1;
		} elseif($cron['hour'] == -1 && $cron['minute'] != '') {
			$nexttime['hour'] = $hour;
			if(($nextminute = self::nextminute($cron['minute'], $minute)) === false) {
				++$nexttime['hour'];
				$nextminute = $cron['minute'][0];
			}
			$nexttime['minute'] = $nextminute;
		} elseif($cron['hour'] != -1 && $cron['minute'] == '') {
			if($cron['hour'] < $hour) {
				$nexttime['hour'] = $nexttime['minute'] = -1;
			} elseif($cron['hour'] == $hour) {
				$nexttime['hour'] = $cron['hour'];
				$nexttime['minute'] = $minute + 1;
			} else {
				$nexttime['hour'] = $cron['hour'];
				$nexttime['minute'] = 0;
			}
		} elseif($cron['hour'] != -1 && $cron['minute'] != '') {
			$nextminute = self::nextminute($cron['minute'], $minute);
			if($cron['hour'] < $hour || ($cron['hour'] == $hour && $nextminute === false)) {
				$nexttime['hour'] = -1;
				$nexttime['minute'] = -1;
			} else {
				$nexttime['hour'] = $cron['hour'];
				$nexttime['minute'] = $nextminute;
			}
		}

		return $nexttime;
	}

	private static function nextminute($nextminutes, $minutenow) {
		foreach($nextminutes as $nextminute) {
			if($nextminute > $minutenow) {
				return $nextminute;
			}
		}
		return false;
	}
}

?>