LOCK TABLES `driver_category` WRITE;
INSERT INTO `driver_category` VALUES
(1, 'Freiwillig'),(2, 'Mitglied'),(3, 'Zivildienst');
UNLOCK TABLES;

LOCK TABLES `vehicle_category` WRITE;
INSERT INTO `vehicle_category` VALUES
(1,'Movano',5,1),(2,'VM Maxi',4,1),(3,'VM Caddy',4,2);
UNLOCK TABLES;

LOCK TABLES `handicap` WRITE;
INSERT INTO `handicap` VALUES
(1, 'sehbehindert'),(2, 'gehbehindert'),(3, 'hörbehindert'),(4, 'blind');
UNLOCK TABLES;

LOCK TABLES `insurance` WRITE;
INSERT INTO `insurance` VALUES
(1, 'AHV'),(2, 'IV');
UNLOCK TABLES;

LOCK TABLES `poi_keyword` WRITE;
INSERT INTO `poi_keyword` VALUES
(1,'Therapie'),(2,'Arztpraxis'),(3,'Werkstatt'),(4,'Arbeitsplatz');
UNLOCK TABLES;
