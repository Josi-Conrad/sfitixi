/*
-- Query: SELECT * FROM tixi.users
LIMIT 0, 1000

-- Date: 2014-03-20 22:41
*/
INSERT INTO `role` (`id`,`name`,`role`) VALUES (1,'Benutzer','ROLE_USER');
INSERT INTO `role` (`id`,`name`,`role`) VALUES (2,'Manager','ROLE_ADMIN');
INSERT INTO `role` (`id`,`name`,`role`) VALUES (3,'Admin','ROLE_SUPER_ADMIN');

INSERT INTO `user` (`id`,`is_active`,`username`,`password`) VALUES (1,1,'admin','$2y$12$cW/qyrqIwbhCM3gB/skrHeFQzsUAGqnqQwasrhg2EIe6MjmMHDhoW');
INSERT INTO `user` (`id`,`is_active`,`username`,`password`) VALUES (2,1,'manager','$2y$12$srUI4GMqm24fti3BW5CBUe/K4jVksse8Go2NT0FeA7ClNaVOcx1xa');
INSERT INTO `user` (`id`,`is_active`,`username`,`password`) VALUES (3,1,'user','$2y$12$cb0W1UvTFvA63yO3hBgh3uSmGpkhS0OIAS71tAfrDm.jTcSN0saWS');

INSERT INTO `user_to_role` (`user_id`,`role_id`) VALUES (1,1);
INSERT INTO `user_to_role` (`user_id`,`role_id`) VALUES (1,2);
INSERT INTO `user_to_role` (`user_id`,`role_id`) VALUES (1,3);
INSERT INTO `user_to_role` (`user_id`,`role_id`) VALUES (2,1);
INSERT INTO `user_to_role` (`user_id`,`role_id`) VALUES (2,2);
INSERT INTO `user_to_role` (`user_id`,`role_id`) VALUES (3,1);
