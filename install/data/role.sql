--
-- EI INSTALL MAKE SQL DUMP V1.0
-- DO NOT modify this file
--
-- Create: 2012-06-13 17:35:00
--

DROP TABLE IF EXISTS pre_attachment;
CREATE TABLE pre_attachment (
  aid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  size int(10) unsigned NOT NULL DEFAULT '0',
  uploadtime int(10) unsigned NOT NULL DEFAULT '0',
  type varchar(8) NOT NULL,
  ext varchar(8) NOT NULL,
  PRIMARY KEY (aid),
  KEY uid (uid)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_role;
CREATE TABLE pre_role (
  rid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  aid mediumint(8) unsigned NOT NULL DEFAULT '0',
  updatetime int(10) unsigned NOT NULL DEFAULT '0',
  private tinyint(1) unsigned NOT NULL DEFAULT '0',
  gender tinyint(1) NOT NULL DEFAULT '0',
  name varchar(255) NOT NULL,
  description varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (rid),
  KEY uid (uid),
  KEY aid (aid)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_role_posture;
CREATE TABLE pre_role_posture (
  pid mediumint(8) unsigned NOT NULL DEFAULT '0',
  rid mediumint(8) unsigned NOT NULL DEFAULT '0',
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  aid mediumint(8) unsigned NOT NULL DEFAULT '0',
  name varchar(255) NOT NULL,
  description varchar(255) NOT NULL DEFAULT '',
  KEY (pid),
  KEY rid (rid),
  KEY uid (uid),
  KEY aid (aid)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_role_link;
CREATE TABLE pre_role_link (
  rid mediumint(8) unsigned NOT NULL,
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  KEY rid (rid),
  KEY uid (uid)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_role_tag;
CREATE TABLE pre_role_tag (
  rid mediumint(8) unsigned NOT NULL,
  tid mediumint(8) unsigned NOT NULL DEFAULT '0',
  KEY rid (rid),
  KEY tid (tid)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_tag;
CREATE TABLE pre_tag (
  tid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(255) NOT NULL,
  name_clean varchar(255) NOT NULL,
  PRIMARY KEY (tid)
) TYPE=InnoDB;

