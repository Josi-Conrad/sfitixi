<?php
/**
 * Created automatically in HouseKeeper.php
 */

namespace Tixi\HomeBundle\Controller;

final class MenuTree
{
    public static $table = array(
        'tixi_home_page' => array(
            'ORDER' => '1', 
            'LOCATION' => 'app-bar', 
            'PARENT' => '', 
            'ENABLED' => '1', 
            'URL' => '/home', 
            'CAPTION' => 'Home', 
            'BREADCRUMB' => 'Homepage', 
            'DESCRIPTION' => 'Startseite', 
            'PERMISSION' => 'ANONYM'),
        'tixi_about_page' => array(
            'ORDER' => '2', 
            'LOCATION' => 'app-bar', 
            'PARENT' => '', 
            'ENABLED' => '1', 
            'URL' => '/public/about', 
            'CAPTION' => 'About', 
            'BREADCRUMB' => 'About', 
            'DESCRIPTION' => 'Informationen zur iTixi Applikation (Lizenz, Version etc.)', 
            'PERMISSION' => 'ANONYM'),
        'tixi_customer_page' => array(
            'ORDER' => '3', 
            'LOCATION' => 'app-bar', 
            'PARENT' => '', 
            'ENABLED' => '1', 
            'URL' => '/public/customer', 
            'CAPTION' => 'Mandant', 
            'BREADCRUMB' => 'Mandant', 
            'DESCRIPTION' => 'Beschreibung und Informationen über den Mandant', 
            'PERMISSION' => 'ANONYM'),
        'tixi_support_page' => array(
            'ORDER' => '4', 
            'LOCATION' => 'app-bar', 
            'PARENT' => '', 
            'ENABLED' => '1', 
            'URL' => '/public/support', 
            'CAPTION' => 'Support', 
            'BREADCRUMB' => 'Support', 
            'DESCRIPTION' => 'Beschreibung wann und wo Support aufgeboten werden kann', 
            'PERMISSION' => 'ANONYM'),
        'tixi_help_page' => array(
            'ORDER' => '5', 
            'LOCATION' => 'app-bar', 
            'PARENT' => '', 
            'ENABLED' => '1', 
            'URL' => '/public/help', 
            'CAPTION' => 'Hilfe', 
            'BREADCRUMB' => 'Hilfe', 
            'DESCRIPTION' => 'Dokumentation (Hilfe) über die iTixi Applikation.', 
            'PERMISSION' => 'ANONYM'),
        'tixi_login' => array(
            'ORDER' => '6', 
            'LOCATION' => 'app-bar', 
            'PARENT' => '', 
            'ENABLED' => '1', 
            'URL' => '/login', 
            'CAPTION' => 'Anmelden', 
            'BREADCRUMB' => 'Anmelden', 
            'DESCRIPTION' => 'Anmelde-Maske', 
            'PERMISSION' => 'ANONYM'),
        'tixi_logout_page' => array(
            'ORDER' => '7', 
            'LOCATION' => 'app-bar', 
            'PARENT' => '', 
            'ENABLED' => '1', 
            'URL' => '/logout', 
            'CAPTION' => 'Abgemeldet', 
            'BREADCRUMB' => 'Abgemeldet', 
            'DESCRIPTION' => 'Bestätigung der Abmeldung (logout) von der iTixi Applikation.', 
            'PERMISSION' => 'ANONYM'),
        'tixi_preferences_page' => array(
            'ORDER' => '8', 
            'LOCATION' => 'app-bar', 
            'PARENT' => '', 
            'ENABLED' => '1', 
            'URL' => '/app/preferences', 
            'CAPTION' => 'Einstellungen', 
            'BREADCRUMB' => 'Einstellungen', 
            'DESCRIPTION' => 'Einstellungen für ein angemeldete Benutzer.', 
            'PERMISSION' => 'ROLE_USER'),
        'tixi_disposition_page' => array(
            'ORDER' => '9', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => '', 
            'ENABLED' => '0', 
            'URL' => '/app/disposition', 
            'CAPTION' => 'Disposition', 
            'BREADCRUMB' => 'Disposition', 
            'DESCRIPTION' => 'Splash Page Disposition', 
            'PERMISSION' => 'ROLE_USER'),
        'tixi_disposition_produktionsplan_page' => array(
            'ORDER' => '10', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => '', 
            'ENABLED' => '0', 
            'URL' => '/app/disposition/produktionsplan', 
            'CAPTION' => 'Produktionsplan', 
            'BREADCRUMB' => 'Disposition - Produktionsplan', 
            'DESCRIPTION' => 'Planung: Monatstag, Schicht, Anzahl Fahrzeuge', 
            'PERMISSION' => 'ROLE_USER'),
        'tixi_disposition_monatsplan_page' => array(
            'ORDER' => '11', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => '', 
            'ENABLED' => '0', 
            'URL' => '/app/disposition/monatsplan', 
            'CAPTION' => 'Monatsplan', 
            'BREADCRUMB' => 'Disposition - Monatsplan', 
            'DESCRIPTION' => 'Einsatzplan, Fahrereinsatzplan', 
            'PERMISSION' => 'ROLE_USER'),
        'tixi_disposition_tagesplan_page' => array(
            'ORDER' => '12', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => '', 
            'ENABLED' => '0', 
            'URL' => '/app/disposition/tagesplan', 
            'CAPTION' => 'Tagesplan', 
            'BREADCRUMB' => 'Disposition - Tagesplan', 
            'DESCRIPTION' => 'Dauerauftraege / Fahrzeug / Fahrer / Schicht', 
            'PERMISSION' => 'ROLE_USER'),
        'tixi_disposition_fahrauftrag_page' => array(
            'ORDER' => '13', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => '', 
            'ENABLED' => '0', 
            'URL' => '/app/disposition/fahrauftrag', 
            'CAPTION' => 'Fahrauftrag', 
            'BREADCRUMB' => 'Disposition - Fahrauftrag', 
            'DESCRIPTION' => 'Fahrauftrag pro Fahrer (Fahrzeug) und pro Schicht', 
            'PERMISSION' => 'ROLE_USER'),
        'tixi_disposition_fahrwegoptimierung_page' => array(
            'ORDER' => '14', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => '', 
            'ENABLED' => '0', 
            'URL' => '/app/disposition/fahrwegoptimierung', 
            'CAPTION' => 'Fahrwegoptimierung', 
            'BREADCRUMB' => 'Disposition - Fahrwegoptimierung', 
            'DESCRIPTION' => 'Fahrauftrag pro Fahrer (Fahrzeug) und pro Schicht (optimiert)', 
            'PERMISSION' => 'ROLE_USER'),
        'tixi_bereitsellen_page' => array(
            'ORDER' => '15', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => '', 
            'ENABLED' => '0', 
            'URL' => '/app/bereitsellen', 
            'CAPTION' => 'Bereitstellen', 
            'BREADCRUMB' => 'Bereitstellen', 
            'DESCRIPTION' => 'Splash Page Bereitstellen (Drucken/Email)', 
            'PERMISSION' => 'ROLE_USER'),
        'tixi_bereitsellen_fahrauftrag_page' => array(
            'ORDER' => '16', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => '', 
            'ENABLED' => '0', 
            'URL' => '/app/bereitsellen/fahrauftrag', 
            'CAPTION' => 'Fahrauftrag', 
            'BREADCRUMB' => 'Bereitstellen - Fahrauftrag', 
            'DESCRIPTION' => 'Splash Page Fahrauftrag', 
            'PERMISSION' => 'ROLE_USER'),
        'tixi_bereitsellen_fahrauftrag_mails_page' => array(
            'ORDER' => '17', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => '', 
            'ENABLED' => '0', 
            'URL' => '/app/bereitsellen/fahrauftrag/mails', 
            'CAPTION' => 'Mails', 
            'BREADCRUMB' => 'Bereitstellen - Fahrauftrag - Mails', 
            'DESCRIPTION' => 'Alle Fahrauftraege, als eMail an den Fahrer,', 
            'PERMISSION' => 'ROLE_USER'),
        'tixi_bereitsellen_fahrauftrag_drucken_page' => array(
            'ORDER' => '18', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => '', 
            'ENABLED' => '0', 
            'URL' => '/app/bereitsellen/fahrauftrag/drucken', 
            'CAPTION' => 'Drucken', 
            'BREADCRUMB' => 'Bereitstellen - Fahrauftrag - Drucken', 
            'DESCRIPTION' => 'Alle Fahrauftraege, als Papier-Kopien (zweifach)', 
            'PERMISSION' => 'ROLE_USER'),
        'tixi_bereitsellen_einsatzplaene_page' => array(
            'ORDER' => '19', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => '', 
            'ENABLED' => '0', 
            'URL' => '/app/bereitsellen/einsatzplaene', 
            'CAPTION' => 'Einsatzplaene', 
            'BREADCRUMB' => 'Bereitstellen - Einsatzplaene', 
            'DESCRIPTION' => 'Monatlich, als Datei Einsatzplan.pdf', 
            'PERMISSION' => 'ROLE_USER'),
        'tixi_bereitsellen_fahreinsaetze_page' => array(
            'ORDER' => '20', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => '', 
            'ENABLED' => '0', 
            'URL' => '/app/bereitsellen/fahreinsaetze', 
            'CAPTION' => 'Fahreinsaetze', 
            'BREADCRUMB' => 'Bereitstellen - Fahreinsaetze', 
            'DESCRIPTION' => 'Monatlich, als Datei Fahreinsätze.pdf', 
            'PERMISSION' => 'ROLE_USER'),
        'tixi_bereitsellen_monatsrechnung_page' => array(
            'ORDER' => '21', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => '', 
            'ENABLED' => '0', 
            'URL' => '/app/bereitsellen/monatsrechnung', 
            'CAPTION' => 'Monatsrechnung', 
            'BREADCRUMB' => 'Bereitstellen - Monatsrechnung', 
            'DESCRIPTION' => 'Monatlich, als Datei Lieferscheine.xls', 
            'PERMISSION' => 'ROLE_ADMIN'),
        'tixi_fahrgast_page' => array(
            'ORDER' => '22', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => '', 
            'ENABLED' => '1', 
            'URL' => '/app/fahrgast', 
            'CAPTION' => 'Fahrgast', 
            'BREADCRUMB' => 'Fahrgast', 
            'DESCRIPTION' => 'Fahrgast Daten', 
            'PERMISSION' => 'ROLE_USER'),
        'tixi_fahrgast_details_page' => array(
            'ORDER' => '23', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => 'tixi_fahrgast_page', 
            'ENABLED' => '1', 
            'URL' => '/app/fahrgast/details', 
            'CAPTION' => 'Details', 
            'BREADCRUMB' => 'Fahrgast - Details', 
            'DESCRIPTION' => 'Vertrauliche Daten zu Fahrgast', 
            'PERMISSION' => 'ROLE_USER'),
        'tixi_fahrgast_dauerauftrag_page' => array(
            'ORDER' => '24', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => 'tixi_fahrgast_page', 
            'ENABLED' => '0', 
            'URL' => '/app/fahrgast/dauerauftrag', 
            'CAPTION' => 'Dauerauftrag', 
            'BREADCRUMB' => 'Fahrgast - Dauerauftrag', 
            'DESCRIPTION' => 'Wiederholende Fahrauftraege.', 
            'PERMISSION' => 'ROLE_USER'),
        'tixi_fahrgast_abwesenheit_page' => array(
            'ORDER' => '25', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => 'tixi_fahrgast_page', 
            'ENABLED' => '1', 
            'URL' => '/app/fahrgast/abwesenheit', 
            'CAPTION' => 'Abwesenheit', 
            'BREADCRUMB' => 'Fahrgast - Abwesenheit', 
            'DESCRIPTION' => 'Abwesend, annuliert Dauerauftraege waehrend ein oder mehrere Tagen.', 
            'PERMISSION' => 'ROLE_USER'),
        'tixi_fahrgast_anrufmaske_page' => array(
            'ORDER' => '26', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => 'tixi_fahrgast_page', 
            'ENABLED' => '0', 
            'URL' => '/app/fahrgast/anrufmaske', 
            'CAPTION' => 'Anrufmaske', 
            'BREADCRUMB' => 'Fahrgast - Anrufmaske', 
            'DESCRIPTION' => 'Angestossen wenn Fahrgast die Zentrale anruft (Anruf Kennung)', 
            'PERMISSION' => 'ROLE_USER'),
        'tixi_ovi_page' => array(
            'ORDER' => '27', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => '', 
            'ENABLED' => '1', 
            'URL' => '/app/ovi', 
            'CAPTION' => 'OVI', 
            'BREADCRUMB' => 'OVI', 
            'DESCRIPTION' => 'Orte von Interesse (OVI) Daten', 
            'PERMISSION' => 'ROLE_USER'),
        'tixi_ovi_details_page' => array(
            'ORDER' => '28', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => 'tixi_ovi_page', 
            'ENABLED' => '1', 
            'URL' => '/app/ovi/details', 
            'CAPTION' => 'Details', 
            'BREADCRUMB' => 'OVI - Details', 
            'DESCRIPTION' => 'Vertrauliche Daten zu OVI', 
            'PERMISSION' => 'ROLE_ADMIN'),
        'tixi_fahrer_page' => array(
            'ORDER' => '29', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => '', 
            'ENABLED' => '1', 
            'URL' => '/app/fahrer', 
            'CAPTION' => 'Fahrer', 
            'BREADCRUMB' => 'Fahrer', 
            'DESCRIPTION' => 'Fahrer Daten', 
            'PERMISSION' => 'ROLE_USER'),
        'tixi_fahrer_details_page' => array(
            'ORDER' => '30', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => 'tixi_fahrer_page', 
            'ENABLED' => '1', 
            'URL' => '/app/fahrer/details', 
            'CAPTION' => 'Details', 
            'BREADCRUMB' => 'Fahrer - Details', 
            'DESCRIPTION' => 'Vertrauliche Daten zu Fahrer ', 
            'PERMISSION' => 'ROLE_ADMIN'),
        'tixi_fahrer_wochenplan_page' => array(
            'ORDER' => '31', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => 'tixi_fahrer_page', 
            'ENABLED' => '0', 
            'URL' => '/app/fahrer/wochenplan', 
            'CAPTION' => 'Wochenplan', 
            'BREADCRUMB' => 'Fahrer - Wochenplan', 
            'DESCRIPTION' => 'Geplante Einsaetze vom Fahrer, wiederholend jede Woche', 
            'PERMISSION' => 'ROLE_USER'),
        'tixi_fahrer_ferienplan_page' => array(
            'ORDER' => '32', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => 'tixi_fahrer_page', 
            'ENABLED' => '1', 
            'URL' => '/app/fahrer/ferienplan', 
            'CAPTION' => 'Ferienplan', 
            'BREADCRUMB' => 'Fahrer - Ferienplan', 
            'DESCRIPTION' => 'Abwesend und Ferien annuliert Wochenplaene waehrend >= 1 Tagen', 
            'PERMISSION' => 'ROLE_USER'),
        'tixi_fahrer_einsatzplan_page' => array(
            'ORDER' => '33', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => 'tixi_fahrer_page', 
            'ENABLED' => '0', 
            'URL' => '/app/fahrer/einsatzplan', 
            'CAPTION' => 'Einsatzplan', 
            'BREADCRUMB' => 'Fahrer - Einsatzplan', 
            'DESCRIPTION' => 'Geplante Einsaetze vom Fahrer in ein Monat', 
            'PERMISSION' => 'ROLE_USER'),
        'tixi_fahrer_anrufmaske_page' => array(
            'ORDER' => '34', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => 'tixi_fahrer_page', 
            'ENABLED' => '0', 
            'URL' => '/app/fahrer/anrufmaske', 
            'CAPTION' => 'Anrufmaske', 
            'BREADCRUMB' => 'Fahrer - Anrufmaske', 
            'DESCRIPTION' => 'Angestossen wenn Fahrer die Zentrale anruft (Anruf Kennung)', 
            'PERMISSION' => 'ROLE_USER'),
        'tixi_fahrzeug_page' => array(
            'ORDER' => '35', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => '', 
            'ENABLED' => '1', 
            'URL' => '/app/fahrzeug', 
            'CAPTION' => 'Fahrzeug', 
            'BREADCRUMB' => 'Fahrzeug', 
            'DESCRIPTION' => 'Daten des Fahrzeuges', 
            'PERMISSION' => 'ROLE_USER'),
        'tixi_fahrzeug_serviceplan_page' => array(
            'ORDER' => '36', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => 'tixi_fahrzeug_page', 
            'ENABLED' => '1', 
            'URL' => '/app/fahrzeug/serviceplan', 
            'CAPTION' => 'Serviceplan', 
            'BREADCRUMB' => 'Fahrzeug - Serviceplan', 
            'DESCRIPTION' => 'Nicht-Verfuegbarkeitsdaten eines Fahrzeuges', 
            'PERMISSION' => 'ROLE_USER'),
        'tixi_unterhalt_page' => array(
            'ORDER' => '37', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => '', 
            'ENABLED' => '0', 
            'URL' => '/app/unterhalt', 
            'CAPTION' => 'Unterhalt', 
            'BREADCRUMB' => 'Unterhalt', 
            'DESCRIPTION' => 'Splash Page Unterhalt', 
            'PERMISSION' => 'ROLE_USER'),
        'tixi_unterhalt_organisationsdaten_page' => array(
            'ORDER' => '38', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => '', 
            'ENABLED' => '0', 
            'URL' => '/app/unterhalt/organisationsdaten', 
            'CAPTION' => 'Organisationsdaten', 
            'BREADCRUMB' => 'Unterhalt - Organisationsdaten', 
            'DESCRIPTION' => 'Email Adresse der Organisation, z.B. info@tixizug.ch, Postadresse etc.', 
            'PERMISSION' => 'ROLE_ADMIN'),
        'tixi_unterhalt_teamdaten_page' => array(
            'ORDER' => '39', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => '', 
            'ENABLED' => '1', 
            'URL' => '/app/unterhalt/teamdaten', 
            'CAPTION' => 'Teamdaten', 
            'BREADCRUMB' => 'Unterhalt - Teamdaten', 
            'DESCRIPTION' => 'Benutzerdaten definieren; Anmeldename, Passwort, Rolle', 
            'PERMISSION' => 'ROLE_ADMIN'),
        'tixi_unterhalt_logs_page' => array(
            'ORDER' => '40', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => '', 
            'ENABLED' => '0', 
            'URL' => '/app/unterhalt/logs', 
            'CAPTION' => 'Logs', 
            'BREADCRUMB' => 'Unterhalt - Logs', 
            'DESCRIPTION' => 'Kontrolle der Logdateien fuer kritsiche Fehlern', 
            'PERMISSION' => 'ROLE_ADMIN'),
        'tixi_unterhalt_datenbank_page' => array(
            'ORDER' => '41', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => '', 
            'ENABLED' => '0', 
            'URL' => '/app/unterhalt/datenbank', 
            'CAPTION' => 'Datenbank', 
            'BREADCRUMB' => 'Unterhalt - Datenbank', 
            'DESCRIPTION' => 'Splash Page Datenbank', 
            'PERMISSION' => 'ROLE_ADMIN'),
        'tixi_unterhalt_datenbank_backup_page' => array(
            'ORDER' => '42', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => '', 
            'ENABLED' => '1', 
            'URL' => '/app/unterhalt/datenbank/backup', 
            'CAPTION' => 'Backup', 
            'BREADCRUMB' => 'Unterhalt - Datenbank - Backup', 
            'DESCRIPTION' => 'Lokale Datensicherung fuer den Disaster Recovery Fall', 
            'PERMISSION' => 'ROLE_ADMIN'),
        'tixi_unterhalt_datenbank_putzen_page' => array(
            'ORDER' => '43', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => '', 
            'ENABLED' => '0', 
            'URL' => '/app/unterhalt/datenbank/putzen', 
            'CAPTION' => 'Aufraemen', 
            'BREADCRUMB' => 'Unterhalt - Datenbank - Aufraemen', 
            'DESCRIPTION' => 'Daten aufraeumen mittels eingebaute Funktionen', 
            'PERMISSION' => 'ROLE_ADMIN'),
        'tixi_unterhalt_feiertage_page' => array(
            'ORDER' => '44', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => '', 
            'ENABLED' => '1', 
            'URL' => '/app/unterhalt/feiertage', 
            'CAPTION' => 'Feiertage', 
            'BREADCRUMB' => 'Unterhalt - Feiertage', 
            'DESCRIPTION' => 'Nationale und Kantonale Feiertage eintragen', 
            'PERMISSION' => 'ROLE_USER'),
        'tixi_unterhalt_dienste_page' => array(
            'ORDER' => '45', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => '', 
            'ENABLED' => '1', 
            'URL' => '/app/unterhalt/dienste', 
            'CAPTION' => 'Dienste', 
            'BREADCRUMB' => 'Unterhalt - Dienste', 
            'DESCRIPTION' => 'Dienst Zeiten (Anfang - Ende) einrichten', 
            'PERMISSION' => 'ROLE_ADMIN'),
        'tixi_unterhalt_zonenplan_page' => array(
            'ORDER' => '46', 
            'LOCATION' => 'menu-bar', 
            'PARENT' => '', 
            'ENABLED' => '1', 
            'URL' => '/app/unterhalt/zonenplan', 
            'CAPTION' => 'Zonenplan', 
            'BREADCRUMB' => 'Unterhalt - Zonenplan', 
            'DESCRIPTION' => 'Zonenplan für den Lieferschein einrichten', 
            'PERMISSION' => 'ROLE_ADMIN') 
    );

    public static function getRow($row){
        if (array_key_exists($row, self::$table)) {
            return self::$table[$row]; // return an array
        } else {
            return array(); // error exit, empty array
        }
    }

    public static function getCell($row, $col){
        if (array_key_exists($row, self::$table)) {
            if (array_key_exists($col,self::$table[$row])) {
                return self::$table[$row][$col];
            } else {
                return null; // error exit 1
            }
        } else {
            return null; // error exit 2
        }
    }
}
