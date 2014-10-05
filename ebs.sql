CREATE TABLE IF NOT EXISTS `ebs_messages` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tstamp` int(11) NOT NULL,
  `content` varchar(300) NOT NULL,
  `trip` char(10) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;
