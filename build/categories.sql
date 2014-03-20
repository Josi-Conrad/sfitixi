CREATE DATABASE  IF NOT EXISTS `itixitest` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `itixitest`;

--
-- Dumping data for table `driver_category`
--

LOCK TABLES `driver_category` WRITE;
/*!40000 ALTER TABLE `driver_category` DISABLE KEYS */;
INSERT INTO `driver_category` VALUES ('Freiwillig'),('Mitglied'),('Zivildienst');
/*!40000 ALTER TABLE `driver_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `vehicle_category`
--

LOCK TABLES `vehicle_category` WRITE;
/*!40000 ALTER TABLE `vehicle_category` DISABLE KEYS */;
INSERT INTO `vehicle_category` VALUES (1,'Movano',5,1),(2,'VM Maxi',4,1),(3,'VM Caddy',4,2);
/*!40000 ALTER TABLE `vehicle_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `handicap`
--

LOCK TABLES `handicap` WRITE;
/*!40000 ALTER TABLE `handicap` DISABLE KEYS */;
INSERT INTO `handicap` VALUES ('AHV'),('IV');
/*!40000 ALTER TABLE `handicap` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `poi_keyword`
--

LOCK TABLES `poi_keyword` WRITE;
/*!40000 ALTER TABLE `poi_keyword` DISABLE KEYS */;
INSERT INTO `poi_keyword` VALUES (1,'Therapie'),(2,'Arztpraxis'),(3,'Werkstatt'),(4,'Arbeitsplatz');
/*!40000 ALTER TABLE `poi_keyword` ENABLE KEYS */;
UNLOCK TABLES;

-- Dump completed on 2014-03-19 21:06:44
