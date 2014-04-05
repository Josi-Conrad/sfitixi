/*
-- Query: SELECT * FROM tixi.users
LIMIT 0, 1000

-- Date: 2014-03-20 22:41
*/
INSERT INTO `role` (`id`,`name`,`role`) VALUES (1,'Benutzer','ROLE_USER');
INSERT INTO `role` (`id`,`name`,`role`) VALUES (2,'Manager','ROLE_MANAGER');
INSERT INTO `role` (`id`,`name`,`role`) VALUES (3,'Admin','ROLE_ADMIN');

INSERT INTO `user` (`id`,`is_active`,`username`,`password`,`email`) VALUES (1,1,'admin','$2y$12$d1lUQkXSUK/6YKJz59Kkbep1egTCJfyCLu7oIrspFBlhcxkXMPgGa', 'rschelbe@hsr.ch');
INSERT INTO `user` (`id`,`is_active`,`username`,`password`,`email`) VALUES (2,1,'manager','$2y$12$yRvd9NQEtkBKMXFkG68wleAFVppdJvtk24601QysAB/WqObfhxrVu', 'manager@tixi.ch');
INSERT INTO `user` (`id`,`is_active`,`username`,`password`,`email`) VALUES (3,1,'user','$2y$12$uythl5SYahaKi9v06WJPpOHosK/pNHTja0/q4iVtVoDpa29vJ9Bl2', 'test@tixi.ch');

INSERT INTO `user_to_role` (`user_id`,`role_id`) VALUES (1,1);
INSERT INTO `user_to_role` (`user_id`,`role_id`) VALUES (1,2);
INSERT INTO `user_to_role` (`user_id`,`role_id`) VALUES (1,3);
INSERT INTO `user_to_role` (`user_id`,`role_id`) VALUES (2,1);
INSERT INTO `user_to_role` (`user_id`,`role_id`) VALUES (2,2);
INSERT INTO `user_to_role` (`user_id`,`role_id`) VALUES (3,1);
