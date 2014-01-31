-- MySQL dump 10.13  Distrib 5.6.12, for Win32 (x86)
--
-- Host: 127.0.0.1    Database: itixi
-- ------------------------------------------------------
-- Server version	5.6.12-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `itixi`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `itixi` /*!40100 DEFAULT CHARACTER SET latin1 COLLATE latin1_german1_ci */;

USE `itixi`;

--
-- Table structure for table `menutree`
--

DROP TABLE IF EXISTS `menutree`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menutree` (
  `ORDER` int(11) DEFAULT NULL,
  `LOCATION` varchar(50) COLLATE latin1_german1_ci DEFAULT NULL,
  `ROUTE` varchar(72) COLLATE latin1_german1_ci DEFAULT NULL,
  `PARENT` varchar(50) COLLATE latin1_german1_ci DEFAULT NULL,
  `ENABLED` int(11) DEFAULT NULL,
  `URL` varchar(67) COLLATE latin1_german1_ci DEFAULT NULL,
  `CAPTION` varchar(50) COLLATE latin1_german1_ci DEFAULT NULL,
  `BREADCRUMB` varchar(67) COLLATE latin1_german1_ci DEFAULT NULL,
  `DESCRIPTION` varchar(100) COLLATE latin1_german1_ci DEFAULT NULL,
  `PERMISSION` varchar(50) COLLATE latin1_german1_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menutree`
--

LOCK TABLES `menutree` WRITE;
/*!40000 ALTER TABLE `menutree` DISABLE KEYS */;
INSERT INTO `menutree` VALUES (1,'app-bar','tixi_home_page',NULL,1,'/home','Home','Homepage','Startseite','ANONYM'),(2,'app-bar','tixi_about_page',NULL,1,'/public/about','About','About','Informationen zur iTixi Applikation (Lizenz, Version etc.)','ANONYM'),(3,'app-bar','tixi_customer_page',NULL,1,'/public/customer','Mandant','Mandant','Beschreibung und Informationen über den Mandant','ANONYM'),(4,'app-bar','tixi_support_page',NULL,1,'/public/support','Support','Support','Beschreibung wann und wo Support aufgeboten werden kann','ANONYM'),(5,'app-bar','tixi_help_page',NULL,1,'/public/help','Hilfe','Hilfe','Dokumentation (Hilfe) über die iTixi Applikation.','ANONYM'),(6,'app-bar','tixi_login',NULL,1,'/login','Anmelden','Anmelden','Anmelde-Maske','ANONYM'),(7,'app-bar','tixi_logout_page',NULL,1,'/logout','Abgemeldet','Abgemeldet','Bestätigung der Abmeldung (logout) von der iTixi Applikation.','ANONYM'),(8,'app-bar','tixi_preferences_page',NULL,1,'/app/preferences','Einstellungen','Einstellungen','Einstellungen für ein angemeldete Benutzer.','ROLE_USER'),(9,'menu-bar','tixi_disposition_page',NULL,0,'/app/disposition','Disposition','Disposition','Splash Page Disposition','ROLE_USER'),(10,'menu-bar','tixi_disposition_produktionsplan_page',NULL,0,'/app/disposition/produktionsplan','Produktionsplan','Disposition - Produktionsplan','Planung: Monatstag, Schicht, Anzahl Fahrzeuge','ROLE_USER'),(11,'menu-bar','tixi_disposition_monatsplan_page',NULL,0,'/app/disposition/monatsplan','Monatsplan','Disposition - Monatsplan','Einsatzplan, Fahrereinsatzplan','ROLE_USER'),(12,'menu-bar','tixi_disposition_tagesplan_page',NULL,0,'/app/disposition/tagesplan','Tagesplan','Disposition - Tagesplan','Dauerauftraege / Fahrzeug / Fahrer / Schicht','ROLE_USER'),(13,'menu-bar','tixi_disposition_fahrauftrag_page',NULL,0,'/app/disposition/fahrauftrag','Fahrauftrag','Disposition - Fahrauftrag','Fahrauftrag pro Fahrer (Fahrzeug) und pro Schicht','ROLE_USER'),(14,'menu-bar','tixi_disposition_fahrwegoptimierung_page',NULL,0,'/app/disposition/fahrwegoptimierung','Fahrwegoptimierung','Disposition - Fahrwegoptimierung','Fahrauftrag pro Fahrer (Fahrzeug) und pro Schicht (optimiert)','ROLE_USER'),(15,'menu-bar','tixi_bereitsellen_page',NULL,0,'/app/bereitsellen','Bereitstellen','Bereitstellen','Splash Page Bereitstellen (Drucken/Email)','ROLE_USER'),(16,'menu-bar','tixi_bereitsellen_fahrauftrag_page',NULL,0,'/app/bereitsellen/fahrauftrag','Fahrauftrag','Bereitstellen - Fahrauftrag','Splash Page Fahrauftrag','ROLE_USER'),(17,'menu-bar','tixi_bereitsellen_fahrauftrag_mails_page',NULL,0,'/app/bereitsellen/fahrauftrag/mails','Mails','Bereitstellen - Fahrauftrag - Mails','Alle Fahrauftraege, als eMail an den Fahrer,','ROLE_USER'),(18,'menu-bar','tixi_bereitsellen_fahrauftrag_drucken_page',NULL,0,'/app/bereitsellen/fahrauftrag/drucken','Drucken','Bereitstellen - Fahrauftrag - Drucken','Alle Fahrauftraege, als Papier-Kopien (zweifach)','ROLE_USER'),(19,'menu-bar','tixi_bereitsellen_einsatzplaene_page',NULL,0,'/app/bereitsellen/einsatzplaene','Einsatzplaene','Bereitstellen - Einsatzplaene','Monatlich, als Datei Einsatzplan.pdf','ROLE_USER'),(20,'menu-bar','tixi_bereitsellen_fahreinsaetze_page',NULL,0,'/app/bereitsellen/fahreinsaetze','Fahreinsaetze','Bereitstellen - Fahreinsaetze','Monatlich, als Datei Fahreinsätze.pdf','ROLE_USER'),(21,'menu-bar','tixi_bereitsellen_monatsrechnung_page',NULL,0,'/app/bereitsellen/monatsrechnung','Monatsrechnung','Bereitstellen - Monatsrechnung','Monatlich, als Datei Lieferscheine.xls','ROLE_USER'),(22,'menu-bar','tixi_fahrgast_page',NULL,1,'/app/fahrgast','Fahrgast','Fahrgast','Fahrgast Daten','ROLE_USER'),(23,'menu-bar','tixi_fahrgast_details_page','tixi_fahrgast_page',1,'/app/fahrgast/details','Details','Fahrgast - Details','Vertrauliche Daten zu Fahrgast','ROLE_ADMIN'),(24,'menu-bar','tixi_fahrgast_einzelauftrag_page','tixi_fahrgast_page',0,'/app/fahrgast/einzelauftrag','Einzelauftrag','Fahrgast - Einzelauftrag','Ein einzelne Fahrauftrag (einmalig, wiederholt nicht).','ROLE_USER'),(25,'menu-bar','tixi_fahrgast_dauerauftrag_page','tixi_fahrgast_page',0,'/app/fahrgast/dauerauftrag','Dauerauftrag','Fahrgast - Dauerauftrag','Wiederholende Fahrauftraege.','ROLE_USER'),(26,'menu-bar','tixi_fahrgast_abwesenheit_page','tixi_fahrgast_page',1,'/app/fahrgast/abwesenheit','Abwesenheit','Fahrgast - Abwesenheit','Abwesend, annuliert Dauerauftraege waehrend ein oder mehrere Tagen.','ROLE_USER'),(27,'menu-bar','tixi_fahrgast_anrufmaske_page','tixi_fahrgast_page',0,'/app/fahrgast/anrufmaske','Anrufmaske','Fahrgast - Anrufmaske','Angestossen wenn Fahrgast die Zentrale anruft (Anruf Kennung)','ROLE_USER'),(28,'menu-bar','tixi_ovi_page',NULL,1,'/app/ovi','OVI','OVI','Orte von Interesse (OVI) Daten','ROLE_USER'),(29,'menu-bar','tixi_ovi_details_page','tixi_ovi_page',1,'/app/ovi/details','Details','OVI - Details','Vertrauliche Daten zu OVI','ROLE_ADMIN'),(30,'menu-bar','tixi_fahrer_page',NULL,1,'/app/fahrer','Fahrer','Fahrer','Fahrer Daten','ROLE_USER'),(31,'menu-bar','tixi_fahrer_details_page','tixi_fahrer_page',1,'/app/fahrer/details','Details','Fahrer - Details','Vertrauliche Daten zu Fahrer ','ROLE_ADMIN'),(32,'menu-bar','tixi_fahrer_dauereinsatzplan_page','tixi_fahrer_page',1,'/app/fahrer/dauereinsatzplan','Dauereinsatzplan','Fahrer - Dauereinsatzplan','Geplante Einsaetze vom Fahrer, wiederholend wöchetlich, monatlich','ROLE_USER'),(33,'menu-bar','tixi_fahrer_ferienplan_page','tixi_fahrer_page',1,'/app/fahrer/ferienplan','Ferienplan','Fahrer - Ferienplan','Abwesend und Ferien annuliert Wochenplaene waehrend >= 1 Tagen','ROLE_USER'),(34,'menu-bar','tixi_fahrer_einsatzplan_page','tixi_fahrer_page',1,'/app/fahrer/einsatzplan','Einsatzplan','Fahrer - Einsatzplan','Geplante Einsaetze vom Fahrer in ein Monat','ROLE_USER'),(35,'menu-bar','tixi_fahrer_agenda_page','tixi_fahrer_page',1,'/app/fahrer/agenda','Agenda','Fahrer - Agenda','Uebersicht aller Einsätze, Dauereinsätze, Ferien und Feiertage','ROLE_USER'),(36,'menu-bar','tixi_fahrer_anrufmaske_page','tixi_fahrer_page',0,'/app/fahrer/anrufmaske','Anrufmaske','Fahrer - Anrufmaske','Angestossen wenn Fahrer die Zentrale anruft (Anruf Kennung)','ROLE_USER'),(37,'menu-bar','tixi_fahrzeug_page',NULL,1,'/app/fahrzeug','Fahrzeug','Fahrzeug','Daten des Fahrzeuges','ROLE_USER'),(38,'menu-bar','tixi_fahrzeug_details_page','tixi_fahrzeug_page',1,'/app/fahrzeug/details','Details','Fahrzeug - Details','Vertrauliche Daten zur Fahrzeug','ROLE_ADMIN'),(39,'menu-bar','tixi_fahrzeug_serviceplan_page','tixi_fahrzeug_page',1,'/app/fahrzeug/serviceplan','Serviceplan','Fahrzeug - Serviceplan','Nicht-Verfuegbarkeitsdaten eines Fahrzeuges','ROLE_USER'),(40,'menu-bar','tixi_unterhalt_page',NULL,0,'/app/unterhalt','Unterhalt','Unterhalt','Splash Page Unterhalt','ROLE_USER'),(41,'menu-bar','tixi_unterhalt_organisationsdaten_page',NULL,0,'/app/unterhalt/organisationsdaten','Organisationsdaten','Unterhalt - Organisationsdaten','Email Adresse der Organisation, z.B. info@tixizug.ch, Postadresse etc.','ROLE_ADMIN'),(42,'menu-bar','tixi_unterhalt_teamdaten_page',NULL,1,'/app/unterhalt/teamdaten','Teamdaten','Unterhalt - Teamdaten','Benutzerdaten definieren; Anmeldename, Passwort, Rolle','ROLE_ADMIN'),(43,'menu-bar','tixi_unterhalt_logs_page',NULL,0,'/app/unterhalt/logs','Logs','Unterhalt - Logs','Kontrolle der Logdateien fuer kritsiche Fehlern','ROLE_ADMIN'),(44,'menu-bar','tixi_unterhalt_datenbank_page',NULL,0,'/app/unterhalt/datenbank','Datenbank','Unterhalt - Datenbank','Splash Page Datenbank','ROLE_ADMIN'),(45,'menu-bar','tixi_unterhalt_datenbank_backup_page',NULL,1,'/app/unterhalt/datenbank/backup','Backup','Unterhalt - Datenbank - Backup','Lokale Datensicherung fuer den Disaster Recovery Fall','ROLE_ADMIN'),(46,'menu-bar','tixi_unterhalt_datenbank_putzen_page',NULL,0,'/app/unterhalt/datenbank/putzen','Aufraemen','Unterhalt - Datenbank - Aufraemen','Daten aufraeumen mittels eingebaute Funktionen','ROLE_ADMIN'),(47,'menu-bar','tixi_unterhalt_feiertage_page',NULL,1,'/app/unterhalt/feiertage','Feiertage','Unterhalt - Feiertage','Nationale und Kantonale Feiertage eintragen','ROLE_ADMIN'),(48,'menu-bar','tixi_unterhalt_dienste_page',NULL,1,'/app/unterhalt/dienste','Dienste','Unterhalt - Dienste','Dienst Zeiten (Anfang - Ende) einrichten','ROLE_ADMIN'),(49,'menu-bar','tixi_unterhalt_zonenplan_page',NULL,1,'/app/unterhalt/zonenplan','Zonenplan','Unterhalt - Zonenplan','Zonenplan für den Lieferschein einrichten','ROLE_ADMIN');
/*!40000 ALTER TABLE `menutree` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'itixi'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-01-31 14:04:36
