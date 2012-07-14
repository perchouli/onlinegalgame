<?php

/**
 *      [EarlyImbrian] (C)2009-2012 EarlyImbrian Committee.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: class/ei/table_tag.php 2012-06-22 14:24:00 Beijing tengattack $
 */

if(!defined('IN_EI')) {
	exit('Access Denied');
}

class ei_table_tag extends ei_table
{

	public function delete_tags($val, $arrtagids) {
		$sql = 'DELETE FROM '.DB::table($this->_table).' WHERE '.DB::field($this->_pk, intval($val)).' AND ( ';
		foreach ((array)$arrtagids as $key => $tagid) {
			if ($key != 0) $sql .= ' OR ';
			$sql .= 'tid='.$tagid;
		}
		$sql .= ' )';
		return DB::query($sql);
	}
	
	public function add_tags($val, $arrtags) {
		$ctags = C::t('tag')->check_tags($arrtags);

		foreach ($arrtags as $rkey => $rtag) {
			foreach ($ctags as $key => $tag) {
				if ($rtag['name_clean'] == $tag['name_clean']) {
					$arrtags[$rkey]['tid'] = $tag['tid'];
					break;
				}
			}
			if (!$arrtags[$rkey]['tid']) {
				$arrtags[$rkey]['tid'] = C::t('tag')->insert($rtag, true);
			}
			if ($arrtags[$rkey]['tid']) {
				$this->insert(array($this->_pk => $val, 'tid' => $arrtags[$rkey]['tid']));
			}
		}
		
		return true;
	}
	
	public function update_tags($val, $tags) {
		
		if (!$val) return false;
		
		$oldtags = $this->get_tags($val);
		
		if ($tags) {
			$tags = dhtmlspecialchars($tags);
			$arrtags = explode(' ', $tags);
			$arrtags = array_unique($arrtags);
			$newtags = array();
			$deltagids = array();
			
			include_once EI_ROOT.'./src/inc/utf/utf_tools.php';
			
			foreach ($arrtags as $key => $tag) {
				$name_clean = utf8_clean_string($tag);
				$bfind = false;
				foreach ($oldtags as $rkey => $oldtag) {
					if ($oldtag['name_clean'] == $name_clean) {
						$bfind = true;
						$oldtags[$rkey]['exist'] = true;
						break;
					}
				}
				if (!$bfind) {
					$newtags[] = array(
						'name' => $tag,
						'name_clean' => $name_clean,
					);
				}
			}
			
			foreach ($oldtags as $rkey => $oldtag) {
				if (!$oldtag['exist']) {
					$deltagids[] = $oldtag['tid'];
				}
			}
			
			if ($deltagids) $this->delete_tags($val, $deltagids);
			if ($newtags) $this->add_tags($val, $newtags);
			
		} else {
			//clear
			if ($oldtags) $this->delete($val);
		}
		return true;
	}
	
	public function get_tags($val) {
		$arrtags = array();
		$val = intval($val);
		
		$query = DB::query("SELECT r.".$this->_pk.", t.*
				FROM ". DB::table($this->_table)." r
				INNER JOIN ".DB::table('tag')." t USING(tid)
				WHERE r.tid=t.tid AND r.".$this->_pk."=".$val);
		while($tt = DB::fetch($query)) {
			$arrtags[] = $tt;
		}
		
		return $arrtags;
	}
}

?>