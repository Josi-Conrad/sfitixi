INSERT INTO `city` (`id`,`name`) VALUES (4,'Baar');
INSERT INTO `city` (`id`,`name`) VALUES (3,'Cham');
INSERT INTO `city` (`id`,`name`) VALUES (2,'Hühneberg');
INSERT INTO `city` (`id`,`name`) VALUES (1,'Zug');

INSERT INTO `country` (`id`,`name`) VALUES (2,'Deutschland');
INSERT INTO `country` (`id`,`name`) VALUES (4,'Frankreich');
INSERT INTO `country` (`id`,`name`) VALUES (3,'Italien');
INSERT INTO `country` (`id`,`name`) VALUES (6,'Lichtenstein');
INSERT INTO `country` (`id`,`name`) VALUES (5,'Österreich');
INSERT INTO `country` (`id`,`name`) VALUES (1,'Schweiz');

INSERT INTO `postal_code` (`id`,`code`) VALUES (2,'6310');
INSERT INTO `postal_code` (`id`,`code`) VALUES (1,'6330');
INSERT INTO `postal_code` (`id`,`code`) VALUES (4,'6331');
INSERT INTO `postal_code` (`id`,`code`) VALUES (3,'6430');

INSERT INTO `address` (`id`,`postal_code_id`,`city_id`,`country_id`,`name`,`street`,`lat`,`lng`,`type`) VALUES (1,1,1,1,'','Zugerstrasse 23',48.867456,8.456340,'Haus');
INSERT INTO `address` (`id`,`postal_code_id`,`city_id`,`country_id`,`name`,`street`,`lat`,`lng`,`type`) VALUES (2,2,2,2,'Zuger Kantonsspital','Landhausstrasse 11',48.867456,8.456340,'Spital');
INSERT INTO `address` (`id`,`postal_code_id`,`city_id`,`country_id`,`name`,`street`,`lat`,`lng`,`type`) VALUES (3,3,3,3,'Brauerei Baar','Baarerstrasse 5',48.576360,8.512345,'Bar');