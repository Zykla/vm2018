CREATE TABLE `vm2018match` (
  `id` int(11) NOT NULL auto_increment,
  `grupp` char(1) NOT NULL default '',
  `matchdatum` date NOT NULL default '0000-00-00',
  `hemmalag` varchar(50) NOT NULL default '',
  `bortalag` varchar(50) NOT NULL default '',
  `hemmamal` int(11) default NULL,
  `bortamal` int(11) default NULL,
  `matchstad` varchar(100) default NULL,
  `tvkanal` varchar(20) default NULL,
  `tvtid` time default NULL,
  PRIMARY KEY  (`id`)
);

# --------------------------------------------------------

CREATE TABLE `vm2018tippare` (
  `id` int(11) NOT NULL auto_increment,
  `namn` varchar(50) NOT NULL default '',
  `emailadress` varchar(100) NOT NULL default '',
  `pwd` varchar(50) NOT NULL default '',
  `userid` varchar(50) NOT NULL default '',
  `betalat` date default NULL,
  PRIMARY KEY  (`id`)
);

# --------------------------------------------------------
CREATE TABLE `vm2018matchtips` (
  `tippare` int(11) NOT NULL default '0',
  `matchid` int(11) NOT NULL default '0',
  `hemmamal` int(11) default NULL,
  `bortamal` int(11) default NULL,
  `poang` int(11) default NULL,
  PRIMARY KEY  (`tippare`,`matchid`)
);
