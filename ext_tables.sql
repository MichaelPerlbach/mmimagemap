#
# Table structure for table 'tx_mmimagemap_domain_model_map'
#
CREATE TABLE tx_mmimagemap_domain_model_map (
	uid int(11) NOT NULL auto_increment,
	pid int(11) NOT NULL DEFAULT '1',
	name tinytext NOT NULL,
	imgfile tinytext NOT NULL,
  altfile varchar(255) NOT NULL DEFAULT '',
	folder tinytext NOT NULL,
	
	PRIMARY KEY (uid)
);

#
# Table structure for table 'tx_mmimagemap_domain_model_area'
#
CREATE TABLE tx_mmimagemap_domain_model_area (
	uid int(11) NOT NULL auto_increment,
	pid int(11) NOT NULL DEFAULT '1',
	mapid int(11) NOT NULL DEFAULT '0',
	areatype int(11) NOT NULL DEFAULT '0',
	arealink tinytext NOT NULL,
	description tinytext NOT NULL,
	color char(7) NOT NULL DEFAULT '',
	param tinytext NOT NULL,
	febordercolor varchar(7) NOT NULL DEFAULT '',
  fevisible tinyint(1) NOT NULL DEFAULT '0',
  feborderthickness tinyint(2) NOT NULL DEFAULT '0',
  fealtfile varchar(255) NOT NULL DEFAULT '',
	
	PRIMARY KEY (uid),
	KEY parent (mapid)
);

#
# Table structure for table 'tx_mmimagemap_domain_model_point'
#
CREATE TABLE tx_mmimagemap_domain_model_point (
	uid int(11) NOT NULL auto_increment,
	pid int(11) NOT NULL DEFAULT '1',
	areaid int(11) NOT NULL DEFAULT '0',
	num int(11) NOT NULL DEFAULT '0',
	x int(11) NOT NULL DEFAULT '0',
	y int(11) NOT NULL DEFAULT '0',
	
	PRIMARY KEY (uid),
	KEY parent (areaid)
);

#
# Table structure for table 'tx_mmimagemap_domain_model_bcolors'
#
CREATE TABLE tx_mmimagemap_domain_model_bcolors (
   uid int(11) NOT NULL auto_increment,
	 pid int(11) NOT NULL DEFAULT '1',
   mapid int(11) NOT NULL DEFAULT '0',
   colorname varchar(255) NOT NULL DEFAULT '',
   color varchar(7) NOT NULL DEFAULT '',
   PRIMARY KEY (uid),
   KEY parent (mapid)
);

#
# Table structure for table 'tx_mmimagemap_domain_model_contentpopup'
#
CREATE TABLE tx_mmimagemap_domain_model_contentpopup (
	uid int(11) NOT NULL auto_increment,
	pid int(11) NOT NULL DEFAULT '1',
	areaid int(11) NOT NULL DEFAULT '0',
  contentid int(11) NOT NULL DEFAULT '0',
  popupwidth int(11) NOT NULL DEFAULT '0',
  popupheight int(11) NOT NULL DEFAULT '0',
  popupx int(11) NOT NULL DEFAULT '0',
  popupy int(11) NOT NULL DEFAULT '0',
  popupbordercolor varchar(8) NOT NULL DEFAULT '',
  popupbackgroundcolor varchar(8) NOT NULL DEFAULT '',
  popupborderwidth int(11) NOT NULL DEFAULT '0',
  active tinyint(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (uid),
	KEY parent (areaid)
);
