CREATE DATABASE  IF NOT EXISTS `itixitest` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `itixitest`;

--
-- Dumping data for table `role`
--

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
INSERT INTO `role` VALUES (1,'Benutzer','ROLE_USER'),(2,'Manager','ROLE_ADMIN'),(3,'Admin','ROLE_SUPER_ADMIN');
/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (4,1,'admin','$2y$12$0f1955e2aaca87e5c949ceZZbt85ODSkxp6zouDmrMsxr8ankH/42','0f1955e2aaca87e5c949ce9003fc2ba2788edf07'),(5,1,'manager','$2y$12$f6835ade807d0c19bfac8uDheiclSMHn6j5T7Zu/v9vMCdkhM5mLa','f6835ade807d0c19bfac88c4f9a1b37e2402690a'),(6,1,'user','$2y$12$1feb3a3dcdb60ce8b44deOQef06N2q8ShwcWnOEVFNWV/0ZsPYaVm','1feb3a3dcdb60ce8b44ded6d5ff60fd60ef08e84');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Dumping data for table `user_roles`
--

LOCK TABLES `user_roles` WRITE;
/*!40000 ALTER TABLE `user_roles` DISABLE KEYS */;
INSERT INTO `user_roles` VALUES (4,1),(4,2),(4,3),(5,1),(5,2),(6,1);
/*!40000 ALTER TABLE `user_roles` ENABLE KEYS */;
UNLOCK TABLES;
