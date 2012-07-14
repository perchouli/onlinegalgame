--
-- EI INSTALL MAKE SQL DUMP V1.0
-- DO NOT modify this file
--
-- Create: 2012-06-13 17:35:00
--

DROP TABLE IF EXISTS pre_story;
CREATE TABLE pre_story (
  sid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  aid mediumint(8) unsigned NOT NULL DEFAULT '0',
  createtime int(10) unsigned NOT NULL DEFAULT '0',
  updatetime int(10) unsigned NOT NULL DEFAULT '0',
  view mediumint(8) unsigned NOT NULL DEFAULT '0',
  private tinyint(1) unsigned NOT NULL DEFAULT '0',
  name varchar(255) NOT NULL,
  description varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (sid),
  KEY uid (uid),
  KEY aid (aid)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_story_scene;
CREATE TABLE pre_story_scene (
  ssid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  sid mediumint(8) unsigned NOT NULL DEFAULT '0',
  sortid mediumint(8) unsigned NOT NULL DEFAULT '0',
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  aid mediumint(8) unsigned NOT NULL DEFAULT '0',
  createtime int(10) unsigned NOT NULL DEFAULT '0',
  updatetime int(10) unsigned NOT NULL DEFAULT '0',
  name varchar(255) NOT NULL,
  description varchar(255) NOT NULL DEFAULT '',
  script TEXT NOT NULL,
  PRIMARY KEY (ssid),
  KEY sid (sid),
  KEY sortid (sortid),
  KEY uid (uid),
  KEY aid (aid)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_story_tag;
CREATE TABLE pre_story_tag (
  sid mediumint(8) unsigned NOT NULL,
  tid mediumint(8) unsigned NOT NULL DEFAULT '0',
  KEY sid (sid),
  KEY tid (tid)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_story_item;
CREATE TABLE pre_story_item (
  siid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  aid mediumint(8) unsigned NOT NULL DEFAULT '0',
  type varchar(16) NOT NULL,
  time int(10) unsigned NOT NULL DEFAULT '0',
  name varchar(255) NOT NULL,
  description varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (siid),
  KEY uid (uid),
  KEY aid (aid)
) TYPE=InnoDB;
