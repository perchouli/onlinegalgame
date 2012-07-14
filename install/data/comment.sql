--
-- EI INSTALL MAKE SQL DUMP V1.0
-- DO NOT modify this file
--
-- Create: 2012-06-23 20:41:00
--

DROP TABLE IF EXISTS pre_comment;
CREATE TABLE pre_comment (
  cid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  uid mediumint(8) unsigned NOT NULL,
  id1 mediumint(8) unsigned NOT NULL DEFAULT '0',
  id2 mediumint(8) unsigned NOT NULL DEFAULT '0',
  time int(10) unsigned NOT NULL DEFAULT '0',
  type varchar(16) NOT NULL,
  content text NOT NULL,
  PRIMARY KEY (cid),
  KEY uid (uid),
  KEY id1 (id1),
  KEY id2 (id2)
) TYPE=InnoDB;


DROP TABLE IF EXISTS pre_lastest_event;
CREATE TABLE pre_lastest_event (
  eid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  uid mediumint(8) unsigned NOT NULL,
  id1 mediumint(8) unsigned NOT NULL DEFAULT '0',
  id2 mediumint(8) unsigned NOT NULL DEFAULT '0',
  time int(10) unsigned NOT NULL DEFAULT '0',
  type varchar(16) NOT NULL,
  PRIMARY KEY (eid),
  KEY uid (uid),
  KEY id1 (id1),
  KEY id2 (id2)
) TYPE=InnoDB;