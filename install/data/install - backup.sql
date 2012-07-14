--
-- EI INSTALL MAKE SQL DUMP V1.0
-- DO NOT modify this file
--
-- Create: 2012-06-13 17:35:00
--

DROP TABLE IF EXISTS pre_account;
CREATE TABLE pre_account (
  uid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  username char(15) NOT NULL DEFAULT '',
  username_clean char(15) NOT NULL DEFAULT '',
  `password` char(36) NOT NULL DEFAULT '',
  PRIMARY KEY (uid),
  UNIQUE KEY username_clean (username_clean)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_banned;
CREATE TABLE pre_common_banned (
  id smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  ip1 smallint(3) NOT NULL DEFAULT '0',
  ip2 smallint(3) NOT NULL DEFAULT '0',
  ip3 smallint(3) NOT NULL DEFAULT '0',
  ip4 smallint(3) NOT NULL DEFAULT '0',
  admin varchar(15) NOT NULL DEFAULT '',
  dateline int(10) unsigned NOT NULL DEFAULT '0',
  expiration int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_block;
CREATE TABLE pre_common_block (
  bid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  blockclass varchar(255) NOT NULL DEFAULT '0',
  blocktype tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  title text NOT NULL,
  classname varchar(255) NOT NULL DEFAULT '',
  summary text NOT NULL,
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  username varchar(255) NOT NULL DEFAULT '',
  styleid smallint(6) unsigned NOT NULL DEFAULT '0',
  blockstyle text NOT NULL,
  picwidth smallint(6) unsigned NOT NULL DEFAULT '0',
  picheight smallint(6) unsigned NOT NULL DEFAULT '0',
  target varchar(255) NOT NULL DEFAULT '',
  dateformat varchar(255) NOT NULL DEFAULT '',
  dateuformat tinyint(1) NOT NULL DEFAULT '0',
  script varchar(255) NOT NULL DEFAULT '',
  param text NOT NULL,
  shownum smallint(6) unsigned NOT NULL DEFAULT '0',
  cachetime int(10) NOT NULL DEFAULT '0',
  cachetimerange char(5) NOT NULL DEFAULT '',
  punctualupdate tinyint(1) NOT NULL DEFAULT '0',
  hidedisplay tinyint(1) NOT NULL DEFAULT '0',
  dateline int(10) unsigned NOT NULL DEFAULT '0',
  notinherited tinyint(1) NOT NULL DEFAULT '0',
  isblank tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (bid)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_block_favorite;
CREATE TABLE pre_common_block_favorite (
  favid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  bid mediumint(8) unsigned NOT NULL DEFAULT '0',
  dateline int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (favid),
  KEY uid (uid,dateline)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_block_item;
CREATE TABLE pre_common_block_item (
  itemid int(10) unsigned NOT NULL AUTO_INCREMENT,
  bid mediumint(8) unsigned NOT NULL DEFAULT '0',
  id int(10) unsigned NOT NULL DEFAULT '0',
  idtype varchar(255) NOT NULL DEFAULT '',
  itemtype tinyint(1) NOT NULL DEFAULT '0',
  title varchar(255) NOT NULL DEFAULT '',
  url varchar(255) NOT NULL DEFAULT '',
  pic varchar(255) NOT NULL DEFAULT '',
  picflag tinyint(1) NOT NULL DEFAULT '0',
  makethumb tinyint(1) NOT NULL DEFAULT '0',
  thumbpath varchar(255) NOT NULL DEFAULT '',
  summary text NOT NULL,
  showstyle text NOT NULL,
  related text NOT NULL,
  `fields` text NOT NULL,
  displayorder smallint(6) NOT NULL DEFAULT '0',
  startdate int(10) unsigned NOT NULL DEFAULT '0',
  enddate int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (itemid),
  KEY bid (bid)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_block_item_data;
CREATE TABLE pre_common_block_item_data (
  dataid int(10) unsigned NOT NULL AUTO_INCREMENT,
  bid mediumint(8) unsigned NOT NULL DEFAULT '0',
  id int(10) unsigned NOT NULL DEFAULT '0',
  idtype varchar(255) NOT NULL DEFAULT '',
  itemtype tinyint(1) NOT NULL DEFAULT '0',
  title varchar(255) NOT NULL DEFAULT '',
  url varchar(255) NOT NULL DEFAULT '',
  pic varchar(255) NOT NULL DEFAULT '',
  picflag tinyint(1) NOT NULL DEFAULT '0',
  makethumb tinyint(1) NOT NULL DEFAULT '0',
  summary text NOT NULL,
  showstyle text NOT NULL,
  related text NOT NULL,
  `fields` text NOT NULL,
  displayorder smallint(6) NOT NULL DEFAULT '0',
  startdate int(10) unsigned NOT NULL DEFAULT '0',
  enddate int(10) unsigned NOT NULL DEFAULT '0',
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  username varchar(255) NOT NULL DEFAULT '',
  dateline int(10) unsigned NOT NULL DEFAULT '0',
  isverified tinyint(1) NOT NULL DEFAULT '0',
  verifiedtime int(10) unsigned NOT NULL DEFAULT '0',
  stickgrade tinyint(2) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (dataid),
  KEY bid (bid,stickgrade,verifiedtime)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_block_permission;
CREATE TABLE pre_common_block_permission (
  bid mediumint(8) unsigned NOT NULL DEFAULT '0',
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  allowmanage tinyint(1) NOT NULL DEFAULT '0',
  allowrecommend tinyint(1) NOT NULL DEFAULT '0',
  needverify tinyint(1) NOT NULL DEFAULT '0',
  inheritedtplname varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (bid,uid),
  KEY uid (uid)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_block_pic;
CREATE TABLE pre_common_block_pic (
  picid int(10) unsigned NOT NULL AUTO_INCREMENT,
  bid mediumint(8) unsigned NOT NULL DEFAULT '0',
  itemid int(10) unsigned NOT NULL DEFAULT '0',
  pic varchar(255) NOT NULL DEFAULT '',
  picflag tinyint(1) NOT NULL DEFAULT '0',
  `type` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (picid),
  KEY bid (bid,itemid)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_block_style;
CREATE TABLE pre_common_block_style (
  styleid smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  blockclass varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  template text NOT NULL,
  `hash` varchar(255) NOT NULL DEFAULT '',
  getpic tinyint(1) NOT NULL DEFAULT '0',
  getsummary tinyint(1) NOT NULL DEFAULT '0',
  makethumb tinyint(1) NOT NULL DEFAULT '0',
  settarget tinyint(1) NOT NULL DEFAULT '0',
  `fields` text NOT NULL,
  moreurl tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (styleid),
  KEY `hash` (`hash`),
  KEY blockclass (blockclass)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_block_xml;
CREATE TABLE pre_common_block_xml (
  id smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  version varchar(255) NOT NULL,
  url varchar(255) NOT NULL,
  clientid varchar(255) NOT NULL,
  `key` varchar(255) NOT NULL,
  signtype varchar(255) NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (id)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_cache;
CREATE TABLE pre_common_cache (
  cachekey varchar(255) NOT NULL DEFAULT '',
  cachevalue mediumblob NOT NULL,
  dateline int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (cachekey)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_connect_guest;
CREATE TABLE pre_common_connect_guest (
  conopenid char(32) NOT NULL DEFAULT '',
  conuin char(40) NOT NULL DEFAULT '',
  conuinsecret char(16) NOT NULL DEFAULT '',
  conqqnick char(100) NOT NULL DEFAULT '',
  PRIMARY KEY (conopenid)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_cron;
CREATE TABLE pre_common_cron (
  cronid smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  available tinyint(1) NOT NULL DEFAULT '0',
  `type` enum('user','system') NOT NULL DEFAULT 'user',
  `name` char(50) NOT NULL DEFAULT '',
  filename char(50) NOT NULL DEFAULT '',
  lastrun int(10) unsigned NOT NULL DEFAULT '0',
  nextrun int(10) unsigned NOT NULL DEFAULT '0',
  weekday tinyint(1) NOT NULL DEFAULT '0',
  `day` tinyint(2) NOT NULL DEFAULT '0',
  `hour` tinyint(2) NOT NULL DEFAULT '0',
  `minute` char(36) NOT NULL DEFAULT '',
  PRIMARY KEY (cronid),
  KEY nextrun (available,nextrun)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_diy_data;
CREATE TABLE pre_common_diy_data (
  targettplname varchar(100) NOT NULL DEFAULT '',
  tpldirectory varchar(80) NOT NULL DEFAULT '',
  primaltplname varchar(255) NOT NULL DEFAULT '',
  diycontent mediumtext NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  username varchar(15) NOT NULL DEFAULT '',
  dateline int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (targettplname,tpldirectory)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_failedlogin;
CREATE TABLE pre_common_failedlogin (
  ip char(15) NOT NULL DEFAULT '',
  username char(32) NOT NULL DEFAULT '',
  count tinyint(1) unsigned NOT NULL DEFAULT '0',
  lastupdate int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (ip,username)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_friendlink;
CREATE TABLE pre_common_friendlink (
  id smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  displayorder tinyint(3) NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT '',
  url varchar(255) NOT NULL DEFAULT '',
  description mediumtext NOT NULL,
  logo varchar(255) NOT NULL DEFAULT '',
  `type` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_grouppm;
CREATE TABLE pre_common_grouppm (
  id smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  authorid mediumint(8) unsigned NOT NULL DEFAULT '0',
  author varchar(15) NOT NULL DEFAULT '',
  dateline int(10) unsigned NOT NULL DEFAULT '0',
  message text NOT NULL,
  numbers mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_invite;
CREATE TABLE pre_common_invite (
  id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  `code` char(20) NOT NULL DEFAULT '',
  fuid mediumint(8) unsigned NOT NULL DEFAULT '0',
  fusername char(20) NOT NULL DEFAULT '',
  `type` tinyint(1) NOT NULL DEFAULT '0',
  email char(40) NOT NULL DEFAULT '',
  inviteip char(15) NOT NULL DEFAULT '',
  appid mediumint(8) unsigned NOT NULL DEFAULT '0',
  dateline int(10) unsigned NOT NULL DEFAULT '0',
  endtime int(10) unsigned NOT NULL DEFAULT '0',
  regdateline int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  orderid char(32) NOT NULL DEFAULT '',
  PRIMARY KEY (id),
  KEY uid (uid,dateline)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_magic;
CREATE TABLE pre_common_magic (
  magicid smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  available tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL,
  identifier varchar(40) NOT NULL,
  description varchar(255) NOT NULL,
  displayorder tinyint(3) NOT NULL DEFAULT '0',
  credit tinyint(1) NOT NULL DEFAULT '0',
  price mediumint(8) unsigned NOT NULL DEFAULT '0',
  num smallint(6) unsigned NOT NULL DEFAULT '0',
  salevolume smallint(6) unsigned NOT NULL DEFAULT '0',
  supplytype tinyint(1) NOT NULL DEFAULT '0',
  supplynum smallint(6) unsigned NOT NULL DEFAULT '0',
  useperoid tinyint(1) NOT NULL DEFAULT '0',
  usenum smallint(6) unsigned NOT NULL DEFAULT '0',
  weight tinyint(3) unsigned NOT NULL DEFAULT '1',
  magicperm text NOT NULL,
  useevent tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (magicid),
  UNIQUE KEY identifier (identifier),
  KEY displayorder (available,displayorder)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_magiclog;
CREATE TABLE pre_common_magiclog (
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  magicid smallint(6) unsigned NOT NULL DEFAULT '0',
  `action` tinyint(1) NOT NULL DEFAULT '0',
  dateline int(10) unsigned NOT NULL DEFAULT '0',
  amount smallint(6) unsigned NOT NULL DEFAULT '0',
  credit tinyint(1) unsigned NOT NULL DEFAULT '0',
  price mediumint(8) unsigned NOT NULL DEFAULT '0',
  targetid int(10) unsigned NOT NULL DEFAULT '0',
  idtype char(6) DEFAULT NULL,
  targetuid mediumint(8) unsigned NOT NULL DEFAULT '0',
  KEY uid (uid,dateline),
  KEY `action` (`action`),
  KEY targetuid (targetuid,dateline),
  KEY magicid (magicid,dateline)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_mailcron;
CREATE TABLE pre_common_mailcron (
  cid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  touid mediumint(8) unsigned NOT NULL DEFAULT '0',
  email varchar(100) NOT NULL DEFAULT '',
  sendtime int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (cid),
  KEY sendtime (sendtime)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_mailqueue;
CREATE TABLE pre_common_mailqueue (
  qid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  cid mediumint(8) unsigned NOT NULL DEFAULT '0',
  `subject` text NOT NULL,
  message text NOT NULL,
  dateline int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (qid),
  KEY mcid (cid,dateline)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_member;
CREATE TABLE pre_common_member (
  uid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  email char(40) NOT NULL DEFAULT '',
  username char(15) NOT NULL DEFAULT '',
  `password` char(32) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  emailstatus tinyint(1) NOT NULL DEFAULT '0',
  avatarstatus tinyint(1) NOT NULL DEFAULT '0',
  videophotostatus tinyint(1) NOT NULL DEFAULT '0',
  adminid tinyint(1) NOT NULL DEFAULT '0',
  groupid smallint(6) unsigned NOT NULL DEFAULT '0',
  groupexpiry int(10) unsigned NOT NULL DEFAULT '0',
  extgroupids char(20) NOT NULL DEFAULT '',
  regdate int(10) unsigned NOT NULL DEFAULT '0',
  credits int(10) NOT NULL DEFAULT '0',
  notifysound tinyint(1) NOT NULL DEFAULT '0',
  timeoffset char(4) NOT NULL DEFAULT '',
  newpm smallint(6) unsigned NOT NULL DEFAULT '0',
  newprompt smallint(6) unsigned NOT NULL DEFAULT '0',
  accessmasks tinyint(1) NOT NULL DEFAULT '0',
  allowadmincp tinyint(1) NOT NULL DEFAULT '0',
  onlyacceptfriendpm tinyint(1) NOT NULL DEFAULT '0',
  conisbind tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (uid),
  UNIQUE KEY username (username),
  KEY email (email),
  KEY groupid (groupid),
  KEY conisbind (conisbind)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_member_action_log;
CREATE TABLE pre_common_member_action_log (
  id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  `action` tinyint(5) NOT NULL DEFAULT '0',
  dateline int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  KEY dateline (dateline,`action`,uid)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_member_connect;
CREATE TABLE pre_common_member_connect (
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  conuin char(40) NOT NULL DEFAULT '',
  conuinsecret char(16) NOT NULL DEFAULT '',
  conopenid char(32) NOT NULL DEFAULT '',
  conisfeed tinyint(1) unsigned NOT NULL DEFAULT '0',
  conispublishfeed tinyint(1) unsigned NOT NULL DEFAULT '0',
  conispublisht tinyint(1) unsigned NOT NULL DEFAULT '0',
  conisregister tinyint(1) unsigned NOT NULL DEFAULT '0',
  conisqzoneavatar tinyint(1) unsigned NOT NULL DEFAULT '0',
  conisqqshow tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (uid),
  KEY conuin (conuin),
  KEY conopenid (conopenid)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_member_count;
CREATE TABLE pre_common_member_count (
  uid mediumint(8) unsigned NOT NULL,
  extcredits1 int(10) NOT NULL DEFAULT '0',
  extcredits2 int(10) NOT NULL DEFAULT '0',
  extcredits3 int(10) NOT NULL DEFAULT '0',
  extcredits4 int(10) NOT NULL DEFAULT '0',
  extcredits5 int(10) NOT NULL DEFAULT '0',
  extcredits6 int(10) NOT NULL DEFAULT '0',
  extcredits7 int(10) NOT NULL DEFAULT '0',
  extcredits8 int(10) NOT NULL DEFAULT '0',
  friends smallint(6) unsigned NOT NULL DEFAULT '0',
  posts mediumint(8) unsigned NOT NULL DEFAULT '0',
  threads mediumint(8) unsigned NOT NULL DEFAULT '0',
  digestposts smallint(6) unsigned NOT NULL DEFAULT '0',
  doings smallint(6) unsigned NOT NULL DEFAULT '0',
  blogs smallint(6) unsigned NOT NULL DEFAULT '0',
  albums smallint(6) unsigned NOT NULL DEFAULT '0',
  sharings smallint(6) unsigned NOT NULL DEFAULT '0',
  attachsize int(10) unsigned NOT NULL DEFAULT '0',
  views mediumint(8) unsigned NOT NULL DEFAULT '0',
  oltime smallint(6) unsigned NOT NULL DEFAULT '0',
  todayattachs smallint(6) unsigned NOT NULL DEFAULT '0',
  todayattachsize int(10) unsigned NOT NULL DEFAULT '0',
  feeds mediumint(8) unsigned NOT NULL DEFAULT '0',
  follower mediumint(8) unsigned NOT NULL DEFAULT '0',
  following mediumint(8) unsigned NOT NULL DEFAULT '0',
  newfollower mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (uid),
  KEY posts (posts)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_member_crime;
CREATE TABLE pre_common_member_crime (
  cid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  operatorid mediumint(8) unsigned NOT NULL DEFAULT '0',
  operator varchar(15) NOT NULL,
  `action` tinyint(5) NOT NULL,
  reason text NOT NULL,
  dateline int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (cid),
  KEY uid (uid,`action`,dateline)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_member_log;
CREATE TABLE pre_common_member_log (
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  `action` char(10) NOT NULL DEFAULT '',
  dateline int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (uid)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_member_magic;
CREATE TABLE pre_common_member_magic (
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  magicid smallint(6) unsigned NOT NULL DEFAULT '0',
  num smallint(6) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (uid,magicid)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_member_medal;
CREATE TABLE pre_common_member_medal (
  uid mediumint(8) unsigned NOT NULL,
  medalid smallint(6) unsigned NOT NULL,
  PRIMARY KEY (uid,medalid)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_member_profile;
CREATE TABLE pre_common_member_profile (
  uid mediumint(8) unsigned NOT NULL,
  realname varchar(255) NOT NULL DEFAULT '',
  gender tinyint(1) NOT NULL DEFAULT '0',
  birthyear smallint(6) unsigned NOT NULL DEFAULT '0',
  birthmonth tinyint(3) unsigned NOT NULL DEFAULT '0',
  birthday tinyint(3) unsigned NOT NULL DEFAULT '0',
  constellation varchar(255) NOT NULL DEFAULT '',
  zodiac varchar(255) NOT NULL DEFAULT '',
  telephone varchar(255) NOT NULL DEFAULT '',
  mobile varchar(255) NOT NULL DEFAULT '',
  idcardtype varchar(255) NOT NULL DEFAULT '',
  idcard varchar(255) NOT NULL DEFAULT '',
  address varchar(255) NOT NULL DEFAULT '',
  zipcode varchar(255) NOT NULL DEFAULT '',
  nationality varchar(255) NOT NULL DEFAULT '',
  birthprovince varchar(255) NOT NULL DEFAULT '',
  birthcity varchar(255) NOT NULL DEFAULT '',
  birthdist varchar(20) NOT NULL DEFAULT '',
  birthcommunity varchar(255) NOT NULL DEFAULT '',
  resideprovince varchar(255) NOT NULL DEFAULT '',
  residecity varchar(255) NOT NULL DEFAULT '',
  residedist varchar(20) NOT NULL DEFAULT '',
  residecommunity varchar(255) NOT NULL DEFAULT '',
  residesuite varchar(255) NOT NULL DEFAULT '',
  graduateschool varchar(255) NOT NULL DEFAULT '',
  company varchar(255) NOT NULL DEFAULT '',
  education varchar(255) NOT NULL DEFAULT '',
  occupation varchar(255) NOT NULL DEFAULT '',
  position varchar(255) NOT NULL DEFAULT '',
  revenue varchar(255) NOT NULL DEFAULT '',
  affectivestatus varchar(255) NOT NULL DEFAULT '',
  lookingfor varchar(255) NOT NULL DEFAULT '',
  bloodtype varchar(255) NOT NULL DEFAULT '',
  height varchar(255) NOT NULL DEFAULT '',
  weight varchar(255) NOT NULL DEFAULT '',
  alipay varchar(255) NOT NULL DEFAULT '',
  icq varchar(255) NOT NULL DEFAULT '',
  qq varchar(255) NOT NULL DEFAULT '',
  yahoo varchar(255) NOT NULL DEFAULT '',
  msn varchar(255) NOT NULL DEFAULT '',
  taobao varchar(255) NOT NULL DEFAULT '',
  site varchar(255) NOT NULL DEFAULT '',
  bio text NOT NULL,
  interest text NOT NULL,
  field1 text NOT NULL,
  field2 text NOT NULL,
  field3 text NOT NULL,
  field4 text NOT NULL,
  field5 text NOT NULL,
  field6 text NOT NULL,
  field7 text NOT NULL,
  field8 text NOT NULL,
  PRIMARY KEY (uid)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_member_profile_setting;
CREATE TABLE pre_common_member_profile_setting (
  fieldid varchar(255) NOT NULL DEFAULT '',
  available tinyint(1) NOT NULL DEFAULT '0',
  invisible tinyint(1) NOT NULL DEFAULT '0',
  needverify tinyint(1) NOT NULL DEFAULT '0',
  title varchar(255) NOT NULL DEFAULT '',
  description varchar(255) NOT NULL DEFAULT '',
  displayorder smallint(6) unsigned NOT NULL DEFAULT '0',
  required tinyint(1) NOT NULL DEFAULT '0',
  unchangeable tinyint(1) NOT NULL DEFAULT '0',
  showincard tinyint(1) NOT NULL DEFAULT '0',
  showinthread tinyint(1) NOT NULL DEFAULT '0',
  showinregister tinyint(1) NOT NULL DEFAULT '0',
  allowsearch tinyint(1) NOT NULL DEFAULT '0',
  formtype varchar(255) NOT NULL,
  size smallint(6) unsigned NOT NULL DEFAULT '0',
  choices text NOT NULL,
  validate text NOT NULL,
  PRIMARY KEY (fieldid)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_member_security;
CREATE TABLE pre_common_member_security (
  securityid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  username varchar(255) NOT NULL DEFAULT '',
  fieldid varchar(255) NOT NULL DEFAULT '',
  oldvalue text NOT NULL,
  newvalue text NOT NULL,
  dateline int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (securityid),
  KEY uid (uid,fieldid),
  KEY dateline (dateline)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_member_stat_field;
CREATE TABLE pre_common_member_stat_field (
  optionid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  fieldid varchar(255) NOT NULL DEFAULT '',
  fieldvalue varchar(255) NOT NULL DEFAULT '',
  `hash` varchar(255) NOT NULL DEFAULT '',
  users mediumint(8) unsigned NOT NULL DEFAULT '0',
  updatetime int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (optionid),
  KEY fieldid (fieldid)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_member_status;
CREATE TABLE pre_common_member_status (
  uid mediumint(8) unsigned NOT NULL,
  regip char(15) NOT NULL DEFAULT '',
  lastip char(15) NOT NULL DEFAULT '',
  lastvisit int(10) unsigned NOT NULL DEFAULT '0',
  lastactivity int(10) unsigned NOT NULL DEFAULT '0',
  lastpost int(10) unsigned NOT NULL DEFAULT '0',
  lastsendmail int(10) unsigned NOT NULL DEFAULT '0',
  invisible tinyint(1) NOT NULL DEFAULT '0',
  buyercredit smallint(6) NOT NULL DEFAULT '0',
  sellercredit smallint(6) NOT NULL DEFAULT '0',
  favtimes mediumint(8) unsigned NOT NULL DEFAULT '0',
  sharetimes mediumint(8) unsigned NOT NULL DEFAULT '0',
  profileprogress tinyint(2) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (uid),
  KEY lastactivity (lastactivity,invisible)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_member_validate;
CREATE TABLE pre_common_member_validate (
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  submitdate int(10) unsigned NOT NULL DEFAULT '0',
  moddate int(10) unsigned NOT NULL DEFAULT '0',
  admin varchar(15) NOT NULL DEFAULT '',
  submittimes tinyint(3) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  message text NOT NULL,
  remark text NOT NULL,
  PRIMARY KEY (uid),
  KEY `status` (`status`)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_member_verify;
CREATE TABLE pre_common_member_verify (
  uid mediumint(8) unsigned NOT NULL,
  verify1 tinyint(1) NOT NULL DEFAULT '0',
  verify2 tinyint(1) NOT NULL DEFAULT '0',
  verify3 tinyint(1) NOT NULL DEFAULT '0',
  verify4 tinyint(1) NOT NULL DEFAULT '0',
  verify5 tinyint(1) NOT NULL DEFAULT '0',
  verify6 tinyint(1) NOT NULL DEFAULT '0',
  verify7 tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (uid),
  KEY verify1 (verify1),
  KEY verify2 (verify2),
  KEY verify3 (verify3),
  KEY verify4 (verify4),
  KEY verify5 (verify5),
  KEY verify6 (verify6),
  KEY verify7 (verify7)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_member_verify_info;
CREATE TABLE pre_common_member_verify_info (
  vid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  username varchar(30) NOT NULL DEFAULT '',
  verifytype tinyint(1) NOT NULL DEFAULT '0',
  flag tinyint(1) NOT NULL DEFAULT '0',
  field text NOT NULL,
  dateline int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (vid),
  KEY verifytype (verifytype,flag),
  KEY uid (uid,verifytype,dateline)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_myinvite;
CREATE TABLE pre_common_myinvite (
  id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  typename varchar(100) NOT NULL DEFAULT '',
  appid mediumint(8) NOT NULL DEFAULT '0',
  `type` tinyint(1) NOT NULL DEFAULT '0',
  fromuid mediumint(8) unsigned NOT NULL DEFAULT '0',
  touid mediumint(8) unsigned NOT NULL DEFAULT '0',
  myml text NOT NULL,
  dateline int(10) unsigned NOT NULL DEFAULT '0',
  `hash` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  KEY `hash` (`hash`),
  KEY uid (touid,dateline)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_nav;
CREATE TABLE pre_common_nav (
  id smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  parentid smallint(6) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  title varchar(255) NOT NULL,
  url varchar(255) NOT NULL,
  identifier varchar(255) NOT NULL,
  target tinyint(1) NOT NULL DEFAULT '0',
  `type` tinyint(1) NOT NULL DEFAULT '0',
  available tinyint(1) NOT NULL DEFAULT '0',
  displayorder tinyint(3) NOT NULL,
  highlight tinyint(1) NOT NULL DEFAULT '0',
  `level` tinyint(1) NOT NULL DEFAULT '0',
  subtype tinyint(1) NOT NULL DEFAULT '0',
  subcols tinyint(1) NOT NULL DEFAULT '0',
  icon varchar(255) NOT NULL,
  subname varchar(255) NOT NULL,
  suburl varchar(255) NOT NULL,
  navtype tinyint(1) NOT NULL DEFAULT '0',
  logo varchar(255) NOT NULL,
  PRIMARY KEY (id),
  KEY navtype (navtype)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_onlinetime;
CREATE TABLE pre_common_onlinetime (
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  thismonth smallint(6) unsigned NOT NULL DEFAULT '0',
  total mediumint(8) unsigned NOT NULL DEFAULT '0',
  lastupdate int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (uid)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_patch;
CREATE TABLE pre_common_patch (
  `serial` varchar(10) NOT NULL DEFAULT '',
  rule text NOT NULL,
  note text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  dateline int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`serial`)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_plugin;
CREATE TABLE pre_common_plugin (
  pluginid smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  available tinyint(1) NOT NULL DEFAULT '0',
  adminid tinyint(1) unsigned NOT NULL DEFAULT '0',
  `name` varchar(40) NOT NULL DEFAULT '',
  identifier varchar(40) NOT NULL DEFAULT '',
  description varchar(255) NOT NULL DEFAULT '',
  datatables varchar(255) NOT NULL DEFAULT '',
  `directory` varchar(100) NOT NULL DEFAULT '',
  copyright varchar(100) NOT NULL DEFAULT '',
  modules text NOT NULL,
  version varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (pluginid),
  UNIQUE KEY identifier (identifier)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_pluginvar;
CREATE TABLE pre_common_pluginvar (
  pluginvarid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  pluginid smallint(6) unsigned NOT NULL DEFAULT '0',
  displayorder tinyint(3) NOT NULL DEFAULT '0',
  title varchar(100) NOT NULL DEFAULT '',
  description varchar(255) NOT NULL DEFAULT '',
  variable varchar(40) NOT NULL DEFAULT '',
  `type` varchar(20) NOT NULL DEFAULT 'text',
  `value` text NOT NULL,
  extra text NOT NULL,
  PRIMARY KEY (pluginvarid),
  KEY pluginid (pluginid)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_process;
CREATE TABLE pre_common_process (
  processid char(32) NOT NULL,
  expiry int(10) DEFAULT NULL,
  extra int(10) DEFAULT NULL,
  PRIMARY KEY (processid),
  KEY expiry (expiry)
) TYPE=HEAP;

DROP TABLE IF EXISTS pre_common_regip;
CREATE TABLE pre_common_regip (
  ip char(15) NOT NULL DEFAULT '',
  dateline int(10) unsigned NOT NULL DEFAULT '0',
  count smallint(6) NOT NULL DEFAULT '0',
  KEY ip (ip)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_relatedlink;
CREATE TABLE pre_common_relatedlink (
  id smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  url varchar(255) NOT NULL DEFAULT '',
  extent tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_report;
CREATE TABLE pre_common_report (
  id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  urlkey char(32) NOT NULL DEFAULT '',
  url varchar(255) NOT NULL DEFAULT '',
  message text NOT NULL,
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  username varchar(15) NOT NULL DEFAULT '',
  dateline int(10) unsigned NOT NULL DEFAULT '0',
  num smallint(6) unsigned NOT NULL DEFAULT '1',
  opuid mediumint(8) unsigned NOT NULL DEFAULT '0',
  opname varchar(15) NOT NULL DEFAULT '',
  optime int(10) unsigned NOT NULL DEFAULT '0',
  opresult varchar(255) NOT NULL DEFAULT '',
  fid mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  KEY urlkey (urlkey),
  KEY fid (fid)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_searchindex;
CREATE TABLE pre_common_searchindex (
  searchid int(10) unsigned NOT NULL AUTO_INCREMENT,
  srchmod tinyint(3) unsigned NOT NULL,
  keywords varchar(255) NOT NULL DEFAULT '',
  searchstring text NOT NULL,
  useip varchar(15) NOT NULL DEFAULT '',
  uid mediumint(10) unsigned NOT NULL DEFAULT '0',
  dateline int(10) unsigned NOT NULL DEFAULT '0',
  expiration int(10) unsigned NOT NULL DEFAULT '0',
  threadsortid smallint(6) unsigned NOT NULL DEFAULT '0',
  num smallint(6) unsigned NOT NULL DEFAULT '0',
  ids text NOT NULL,
  PRIMARY KEY (searchid),
  KEY srchmod (srchmod)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_secquestion;
CREATE TABLE pre_common_secquestion (
  id smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL,
  question text NOT NULL,
  answer varchar(255) NOT NULL,
  PRIMARY KEY (id)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_session;
CREATE TABLE pre_common_session (
  sid char(6) NOT NULL DEFAULT '',
  ip1 tinyint(3) unsigned NOT NULL DEFAULT '0',
  ip2 tinyint(3) unsigned NOT NULL DEFAULT '0',
  ip3 tinyint(3) unsigned NOT NULL DEFAULT '0',
  ip4 tinyint(3) unsigned NOT NULL DEFAULT '0',
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  username char(15) NOT NULL DEFAULT '',
  groupid smallint(6) unsigned NOT NULL DEFAULT '0',
  invisible tinyint(1) NOT NULL DEFAULT '0',
  `action` tinyint(1) unsigned NOT NULL DEFAULT '0',
  lastactivity int(10) unsigned NOT NULL DEFAULT '0',
  lastolupdate int(10) unsigned NOT NULL DEFAULT '0',
  fid mediumint(8) unsigned NOT NULL DEFAULT '0',
  tid mediumint(8) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY sid (sid),
  KEY uid (uid)
) TYPE=HEAP;

DROP TABLE IF EXISTS pre_common_setting;
CREATE TABLE pre_common_setting (
  skey varchar(255) NOT NULL DEFAULT '',
  svalue text NOT NULL,
  PRIMARY KEY (skey)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_stat;
CREATE TABLE pre_common_stat (
  daytime int(10) unsigned NOT NULL DEFAULT '0',
  login int(10) unsigned NOT NULL DEFAULT '0',
  mobilelogin int(10) unsigned NOT NULL DEFAULT '0',
  connectlogin int(10) unsigned NOT NULL DEFAULT '0',
  register int(10) unsigned NOT NULL DEFAULT '0',
  invite int(10) unsigned NOT NULL DEFAULT '0',
  appinvite int(10) unsigned NOT NULL DEFAULT '0',
  doing int(10) unsigned NOT NULL DEFAULT '0',
  blog int(10) unsigned NOT NULL DEFAULT '0',
  pic int(10) unsigned NOT NULL DEFAULT '0',
  poll int(10) unsigned NOT NULL DEFAULT '0',
  activity int(10) unsigned NOT NULL DEFAULT '0',
  `share` int(10) unsigned NOT NULL DEFAULT '0',
  thread int(10) unsigned NOT NULL DEFAULT '0',
  docomment int(10) unsigned NOT NULL DEFAULT '0',
  blogcomment int(10) unsigned NOT NULL DEFAULT '0',
  piccomment int(10) unsigned NOT NULL DEFAULT '0',
  sharecomment int(10) unsigned NOT NULL DEFAULT '0',
  reward int(10) unsigned NOT NULL DEFAULT '0',
  debate int(10) unsigned NOT NULL DEFAULT '0',
  trade int(10) unsigned NOT NULL DEFAULT '0',
  `group` int(10) unsigned NOT NULL DEFAULT '0',
  groupjoin int(10) unsigned NOT NULL DEFAULT '0',
  groupthread int(10) unsigned NOT NULL DEFAULT '0',
  grouppost int(10) unsigned NOT NULL DEFAULT '0',
  post int(10) unsigned NOT NULL DEFAULT '0',
  wall int(10) unsigned NOT NULL DEFAULT '0',
  poke int(10) unsigned NOT NULL DEFAULT '0',
  click int(10) unsigned NOT NULL DEFAULT '0',
  sendpm int(10) unsigned NOT NULL DEFAULT '0',
  friend int(10) unsigned NOT NULL DEFAULT '0',
  addfriend int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (daytime)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_statuser;
CREATE TABLE pre_common_statuser (
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  daytime int(10) unsigned NOT NULL DEFAULT '0',
  `type` char(20) NOT NULL DEFAULT '',
  KEY uid (uid)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_style;
CREATE TABLE pre_common_style (
  styleid smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  available tinyint(1) NOT NULL DEFAULT '1',
  templateid smallint(6) unsigned NOT NULL DEFAULT '0',
  extstyle varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (styleid)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_stylevar;
CREATE TABLE pre_common_stylevar (
  stylevarid smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  styleid smallint(6) unsigned NOT NULL DEFAULT '0',
  variable text NOT NULL,
  substitute text NOT NULL,
  PRIMARY KEY (stylevarid),
  KEY styleid (styleid)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_syscache;
CREATE TABLE pre_common_syscache (
  cname varchar(32) NOT NULL,
  ctype tinyint(3) unsigned NOT NULL,
  dateline int(10) unsigned NOT NULL,
  `data` mediumblob NOT NULL,
  PRIMARY KEY (cname)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_tag;
CREATE TABLE pre_common_tag (
  tagid smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  tagname char(20) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (tagid),
  KEY tagname (tagname),
  KEY `status` (`status`,tagid)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_tagitem;
CREATE TABLE pre_common_tagitem (
  tagid smallint(6) unsigned NOT NULL DEFAULT '0',
  itemid mediumint(8) unsigned NOT NULL DEFAULT '0',
  idtype char(10) NOT NULL DEFAULT '',
  UNIQUE KEY item (tagid,itemid,idtype),
  KEY idtype (idtype,itemid)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_template;
CREATE TABLE pre_common_template (
  templateid smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '',
  `directory` varchar(100) NOT NULL DEFAULT '',
  copyright varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (templateid)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_template_block;
CREATE TABLE pre_common_template_block (
  targettplname varchar(100) NOT NULL DEFAULT '',
  tpldirectory varchar(80) NOT NULL DEFAULT '',
  bid mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (targettplname,tpldirectory,bid),
  KEY bid (bid)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_template_permission;
CREATE TABLE pre_common_template_permission (
  targettplname varchar(100) NOT NULL DEFAULT '',
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  allowmanage tinyint(1) NOT NULL DEFAULT '0',
  allowrecommend tinyint(1) NOT NULL DEFAULT '0',
  needverify tinyint(1) NOT NULL DEFAULT '0',
  inheritedtplname varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (targettplname,uid),
  KEY uid (uid)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_uin_black;
CREATE TABLE pre_common_uin_black (
  uin char(40) NOT NULL,
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  dateline int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (uin),
  UNIQUE KEY uid (uid)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_usergroup;
CREATE TABLE pre_common_usergroup (
  groupid smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  radminid tinyint(3) NOT NULL DEFAULT '0',
  `type` enum('system','special','member') NOT NULL DEFAULT 'member',
  system varchar(255) NOT NULL DEFAULT 'private',
  grouptitle varchar(255) NOT NULL DEFAULT '',
  creditshigher int(10) NOT NULL DEFAULT '0',
  creditslower int(10) NOT NULL DEFAULT '0',
  stars tinyint(3) NOT NULL DEFAULT '0',
  color varchar(255) NOT NULL DEFAULT '',
  icon varchar(255) NOT NULL DEFAULT '',
  allowvisit tinyint(1) NOT NULL DEFAULT '0',
  allowsendpm tinyint(1) NOT NULL DEFAULT '1',
  allowinvite tinyint(1) NOT NULL DEFAULT '0',
  allowmailinvite tinyint(1) NOT NULL DEFAULT '0',
  maxinvitenum tinyint(3) unsigned NOT NULL DEFAULT '0',
  inviteprice smallint(6) unsigned NOT NULL DEFAULT '0',
  maxinviteday smallint(6) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (groupid),
  KEY creditsrange (creditshigher,creditslower)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_usergroup_field;
CREATE TABLE pre_common_usergroup_field (
  groupid smallint(6) unsigned NOT NULL,
  readaccess tinyint(3) unsigned NOT NULL DEFAULT '0',
  allowpost tinyint(1) NOT NULL DEFAULT '0',
  allowreply tinyint(1) NOT NULL DEFAULT '0',
  allowpostpoll tinyint(1) NOT NULL DEFAULT '0',
  allowpostreward tinyint(1) NOT NULL DEFAULT '0',
  allowposttrade tinyint(1) NOT NULL DEFAULT '0',
  allowpostactivity tinyint(1) NOT NULL DEFAULT '0',
  allowdirectpost tinyint(1) NOT NULL DEFAULT '0',
  allowgetattach tinyint(1) NOT NULL DEFAULT '0',
  allowgetimage tinyint(1) NOT NULL DEFAULT '0',
  allowpostattach tinyint(1) NOT NULL DEFAULT '0',
  allowpostimage tinyint(1) NOT NULL DEFAULT '0',
  allowvote tinyint(1) NOT NULL DEFAULT '0',
  allowsearch tinyint(1) NOT NULL DEFAULT '0',
  allowcstatus tinyint(1) NOT NULL DEFAULT '0',
  allowinvisible tinyint(1) NOT NULL DEFAULT '0',
  allowtransfer tinyint(1) NOT NULL DEFAULT '0',
  allowsetreadperm tinyint(1) NOT NULL DEFAULT '0',
  allowsetattachperm tinyint(1) NOT NULL DEFAULT '0',
  allowposttag tinyint(1) NOT NULL DEFAULT '0',
  allowhidecode tinyint(1) NOT NULL DEFAULT '0',
  allowhtml tinyint(1) NOT NULL DEFAULT '0',
  allowanonymous tinyint(1) NOT NULL DEFAULT '0',
  allowsigbbcode tinyint(1) NOT NULL DEFAULT '0',
  allowsigimgcode tinyint(1) NOT NULL DEFAULT '0',
  allowmagics tinyint(1) unsigned NOT NULL,
  disableperiodctrl tinyint(1) NOT NULL DEFAULT '0',
  reasonpm tinyint(1) NOT NULL DEFAULT '0',
  maxprice smallint(6) unsigned NOT NULL DEFAULT '0',
  maxsigsize smallint(6) unsigned NOT NULL DEFAULT '0',
  maxattachsize int(10) unsigned NOT NULL DEFAULT '0',
  maxsizeperday int(10) unsigned NOT NULL DEFAULT '0',
  maxthreadsperhour tinyint(3) unsigned NOT NULL DEFAULT '0',
  maxpostsperhour tinyint(3) unsigned NOT NULL DEFAULT '0',
  attachextensions char(100) NOT NULL DEFAULT '',
  raterange char(150) NOT NULL DEFAULT '',
  mintradeprice smallint(6) unsigned NOT NULL DEFAULT '1',
  maxtradeprice smallint(6) unsigned NOT NULL DEFAULT '0',
  minrewardprice smallint(6) unsigned NOT NULL DEFAULT '1',
  maxrewardprice smallint(6) unsigned NOT NULL DEFAULT '0',
  magicsdiscount tinyint(1) NOT NULL,
  maxmagicsweight smallint(6) unsigned NOT NULL,
  allowpostdebate tinyint(1) NOT NULL DEFAULT '0',
  tradestick tinyint(1) unsigned NOT NULL,
  exempt tinyint(1) unsigned NOT NULL,
  maxattachnum smallint(6) NOT NULL DEFAULT '0',
  allowposturl tinyint(1) NOT NULL DEFAULT '3',
  allowrecommend tinyint(1) unsigned NOT NULL DEFAULT '1',
  allowpostrushreply tinyint(1) NOT NULL DEFAULT '0',
  maxfriendnum smallint(6) unsigned NOT NULL DEFAULT '0',
  maxspacesize int(10) unsigned NOT NULL DEFAULT '0',
  allowcomment tinyint(1) NOT NULL DEFAULT '0',
  allowcommentarticle smallint(6) NOT NULL DEFAULT '0',
  searchinterval smallint(6) unsigned NOT NULL DEFAULT '0',
  searchignore tinyint(1) NOT NULL DEFAULT '0',
  allowblog tinyint(1) NOT NULL DEFAULT '0',
  allowdoing tinyint(1) NOT NULL DEFAULT '0',
  allowupload tinyint(1) NOT NULL DEFAULT '0',
  allowshare tinyint(1) NOT NULL DEFAULT '0',
  allowblogmod tinyint(1) unsigned NOT NULL DEFAULT '0',
  allowdoingmod tinyint(1) unsigned NOT NULL DEFAULT '0',
  allowuploadmod tinyint(1) unsigned NOT NULL DEFAULT '0',
  allowsharemod tinyint(1) unsigned NOT NULL DEFAULT '0',
  allowcss tinyint(1) NOT NULL DEFAULT '0',
  allowpoke tinyint(1) NOT NULL DEFAULT '0',
  allowfriend tinyint(1) NOT NULL DEFAULT '0',
  allowclick tinyint(1) NOT NULL DEFAULT '0',
  allowmagic tinyint(1) NOT NULL DEFAULT '0',
  allowstat tinyint(1) NOT NULL DEFAULT '0',
  allowstatdata tinyint(1) NOT NULL DEFAULT '0',
  videophotoignore tinyint(1) NOT NULL DEFAULT '0',
  allowviewvideophoto tinyint(1) NOT NULL DEFAULT '0',
  allowmyop tinyint(1) NOT NULL DEFAULT '0',
  magicdiscount tinyint(1) NOT NULL DEFAULT '0',
  domainlength smallint(6) unsigned NOT NULL DEFAULT '0',
  seccode tinyint(1) NOT NULL DEFAULT '1',
  disablepostctrl tinyint(1) NOT NULL DEFAULT '0',
  allowbuildgroup tinyint(1) unsigned NOT NULL DEFAULT '0',
  allowgroupdirectpost tinyint(1) unsigned NOT NULL DEFAULT '0',
  allowgroupposturl tinyint(1) unsigned NOT NULL DEFAULT '0',
  edittimelimit smallint(6) unsigned NOT NULL DEFAULT '0',
  allowpostarticle tinyint(1) NOT NULL DEFAULT '0',
  allowdownlocalimg tinyint(1) NOT NULL DEFAULT '0',
  allowdownremoteimg tinyint(1) NOT NULL DEFAULT '0',
  allowpostarticlemod tinyint(1) unsigned NOT NULL DEFAULT '0',
  allowspacediyhtml tinyint(1) NOT NULL DEFAULT '0',
  allowspacediybbcode tinyint(1) NOT NULL DEFAULT '0',
  allowspacediyimgcode tinyint(1) NOT NULL DEFAULT '0',
  allowcommentpost tinyint(1) NOT NULL DEFAULT '2',
  allowcommentitem tinyint(1) NOT NULL DEFAULT '0',
  allowcommentreply tinyint(1) NOT NULL DEFAULT '0',
  allowreplycredit tinyint(1) NOT NULL DEFAULT '0',
  ignorecensor tinyint(1) unsigned NOT NULL DEFAULT '0',
  allowsendallpm tinyint(1) unsigned NOT NULL DEFAULT '0',
  allowsendpmmaxnum smallint(6) unsigned NOT NULL DEFAULT '0',
  maximagesize mediumint(8) unsigned NOT NULL DEFAULT '0',
  allowmediacode tinyint(1) NOT NULL DEFAULT '0',
  allowat smallint(6) unsigned NOT NULL DEFAULT '0',
  allowsetpublishdate tinyint(1) unsigned NOT NULL DEFAULT '0',
  allowfollowcollection tinyint(1) unsigned NOT NULL DEFAULT '0',
  allowcommentcollection tinyint(1) unsigned NOT NULL DEFAULT '0',
  allowcreatecollection smallint(6) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (groupid)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_word;
CREATE TABLE pre_common_word (
  id smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  admin varchar(15) NOT NULL DEFAULT '',
  `type` smallint(6) NOT NULL DEFAULT '1',
  find varchar(255) NOT NULL DEFAULT '',
  replacement varchar(255) NOT NULL DEFAULT '',
  extra varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (id)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_common_word_type;
CREATE TABLE pre_common_word_type (
  id smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  typename varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (id)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_connect_disktask;
CREATE TABLE pre_connect_disktask (
  taskid int(10) unsigned NOT NULL AUTO_INCREMENT,
  aid int(10) unsigned NOT NULL DEFAULT '0',
  uid int(10) unsigned NOT NULL DEFAULT '0',
  openid char(32) NOT NULL DEFAULT '',
  filename varchar(255) NOT NULL DEFAULT '',
  verifycode char(32) NOT NULL DEFAULT '',
  `status` smallint(6) unsigned NOT NULL DEFAULT '0',
  dateline int(10) unsigned NOT NULL DEFAULT '0',
  downloadtime int(10) unsigned NOT NULL DEFAULT '0',
  extra text,
  PRIMARY KEY (taskid),
  KEY openid (openid),
  KEY `status` (`status`)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_connect_feedlog;
CREATE TABLE pre_connect_feedlog (
  flid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  tid mediumint(8) unsigned NOT NULL DEFAULT '0',
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  publishtimes mediumint(8) unsigned NOT NULL DEFAULT '0',
  lastpublished int(10) unsigned NOT NULL DEFAULT '0',
  dateline int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (flid),
  UNIQUE KEY tid (tid)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_connect_memberbindlog;
CREATE TABLE pre_connect_memberbindlog (
  mblid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  uin char(40) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  dateline int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (mblid),
  KEY uid (uid),
  KEY uin (uin),
  KEY dateline (dateline)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_connect_tthreadlog;
CREATE TABLE pre_connect_tthreadlog (
  twid char(16) NOT NULL,
  tid mediumint(8) unsigned NOT NULL DEFAULT '0',
  conopenid char(32) NOT NULL,
  pagetime int(10) unsigned DEFAULT '0',
  lasttwid char(16) DEFAULT NULL,
  nexttime int(10) unsigned DEFAULT '0',
  updatetime int(10) unsigned DEFAULT '0',
  dateline int(10) unsigned DEFAULT '0',
  PRIMARY KEY (twid),
  KEY nexttime (tid,nexttime),
  KEY updatetime (tid,updatetime)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_security_evilpost;
CREATE TABLE pre_security_evilpost (
  pid int(10) unsigned NOT NULL,
  tid mediumint(8) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(1) NOT NULL DEFAULT '0',
  evilcount int(10) NOT NULL DEFAULT '0',
  eviltype mediumint(8) unsigned NOT NULL DEFAULT '0',
  createtime int(10) unsigned NOT NULL DEFAULT '0',
  operateresult tinyint(1) unsigned NOT NULL DEFAULT '0',
  isreported tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (pid),
  KEY `type` (tid,`type`),
  KEY operateresult (operateresult,createtime)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_security_eviluser;
CREATE TABLE pre_security_eviluser (
  uid int(10) unsigned NOT NULL,
  evilcount int(10) NOT NULL DEFAULT '0',
  eviltype mediumint(8) unsigned NOT NULL DEFAULT '0',
  createtime int(10) unsigned NOT NULL DEFAULT '0',
  operateresult tinyint(1) unsigned NOT NULL DEFAULT '0',
  isreported tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (uid),
  KEY operateresult (operateresult,createtime)
) TYPE=InnoDB;

DROP TABLE IF EXISTS pre_security_failedlog;
CREATE TABLE pre_security_failedlog (
  id int(11) NOT NULL AUTO_INCREMENT,
  reporttype char(20) NOT NULL,
  tid int(10) unsigned NOT NULL DEFAULT '0',
  pid int(10) unsigned NOT NULL DEFAULT '0',
  uid int(10) unsigned NOT NULL DEFAULT '0',
  failcount int(10) unsigned NOT NULL DEFAULT '0',
  createtime int(10) unsigned NOT NULL DEFAULT '0',
  posttime int(10) unsigned NOT NULL DEFAULT '0',
  delreason char(255) NOT NULL,
  scheduletime int(10) unsigned NOT NULL DEFAULT '0',
  lastfailtime int(10) unsigned NOT NULL DEFAULT '0',
  extra1 int(10) unsigned NOT NULL,
  extra2 char(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  KEY pid (pid),
  KEY uid (uid)
) TYPE=InnoDB;

