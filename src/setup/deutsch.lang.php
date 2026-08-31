<?php
/*
 * b1gMail
 * Copyright (c) 2021 Patrick Schlangen et al
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 */

$lang_setup['showchmod']                = '<tt>chmod</tt>-Kommandos anzeigen';
$lang_setup['dbmails_note']				= 'In Ihrer Datenbank befinden sich E-Mails, die in einem veralteten Format gespeichert sind. Dieses Format wird in b1gMail ab 7.4 nicht mehr unterst&uuml;tzt. Bitte stellen Sie den alten Stand der b1gMail-Dateien wieder her (d.h. wie vor Kopieren der Update-Dateien), loggen Sie sich in den Adminbereich ein, gehen Sie zu &quot;Einstellungen&quot; - &quot;E-Mail&quot; und stellen Sie sicher, dass als Speichermethode die Speicherung in Dateien gew&auml;hlt ist. Danach rufen Sie bitte &quot;Tools&quot; - &quot;Optimierung&quot; - &quot;Dateisystem&quot; auf und lassen alle noch in der Datenbank gespeicherten Mails in Dateien umspeichern. Danach kann das Update wiederholt werden.';
$lang_setup['dbmails']					= 'In veraltetem Modus gespeicherte Mails (DB)';
$lang_setup['setupmode'] 				= 'Einrichtungs-Modus';
$lang_setup['mode_public']				= '&Ouml;ffentlicher E-Mail-Dienst';
$lang_setup['mode_public_desc']			= 'Konfiguriert b1gMail zum Betrieb eines &ouml;ffentlichen E-Mail-Dienstes vor. Ein f&uuml;r Besucher zug&auml;ngliches Registrierungs-Formular zum Anlegen neuer E-Mail-Adressen wird aktiviert.';
$lang_setup['mode_private']				= 'Internes E-Mail-System';
$lang_setup['mode_private_desc']		= 'Konfiguriert b1gMail zum Betrieb eines internen E-Mail-Systems vor. Das &ouml;ffentliche Registrierungs-Formular wird deaktiviert; neue Benutzer k&ouml;nnen nur durch den Administrator angelegt werden.';
$lang_setup['mode_note']				= 'Der Einrichtungs-Modus bestimmt, wie Ihre b1gMail-Installation vorkonfiguriert wird. Die Konfiguration ist nach der Installation jederzeit &auml;nderbar.';
$lang_setup['accounting']               = 'Buchhaltung';
$lang_setup['credit_text']              = 'Credits (Konto aufladen)';
$lang_setup['setup']					= 'Installation';
$lang_setup['selectlanguage']			= 'Sprache ausw&auml;hlen';
$lang_setup['selectlanguage_text']		= 'Bitte w&auml;hlen Sie Ihre Sprache aus. / Please select your language.';
$lang_setup['next']						= 'Weiter';
$lang_setup['welcome']					= 'Willkommen';
$lang_setup['welcome_text']				= 'Herzlichen Willkommen zur Installation von b1gMail! Im Folgenden werden Sie Schritt f&uuml;r Schritt durch die Installation von b1gMail geleitet. Bitte achten Sie auf korrekte Angaben, damit b1gMail ordnungsgem&auml;&szlig; installiert werden kann. Im Zweifelsfall finden Sie eine ausf&uuml;hrliche Anleitung in der b1gMail-Dokumentation.';
$lang_setup['syscheck']					= 'System-Check';
$lang_setup['syscheck_text']			= 'Ihr System wird nun auf Kompatibilit&auml;t mit b1gMail &uuml;berpr&uuml;ft.';
$lang_setup['required']					= 'Erforderlich';
$lang_setup['available']				= 'Vorhanden';
$lang_setup['phpversion']				= 'PHP-Version';
$lang_setup['mysqlext']					= 'MySQLi-Erweiterung';
$lang_setup['yes']						= 'Ja';
$lang_setup['no']						= 'Nein';
$lang_setup['writeable']				= 'Beschreibbar';
$lang_setup['notwriteable']				= 'Nicht beschreibbar';
$lang_setup['checkfail_text']			= 'Mindestens eine Voraussetzung zum Betrieb von b1gMail ist nicht erf&uuml;llt. Bitte korrigieren Sie das Problem und klicken Sie auf &quot;Weiter &raquo;&quot;, um den System-Check erneut durchzuf&uuml;hren.';
$lang_setup['checkok_text']				= 'Der System-Check wurde erfolgreich durchgef&uuml;hrt. Klicken Sie auf &quot;Weiter &raquo;&quot;, um fortzufahren.';
$lang_setup['db']						= 'MySQL-Datenbank';
$lang_setup['dbfresh_text']				= 'Bitte geben Sie die Login-Informationen zur Datenbank ein, in die b1gMail installiert werden soll. In dieser Datenbank darf sich keine bestehende b1gMail-Installation befinden!';
$lang_setup['mysql_host']				= 'MySQL-Server';
$lang_setup['mysql_user']				= 'MySQL-Benutzer';
$lang_setup['mysql_pass']				= 'MySQL-Passwort';
$lang_setup['mysql_db']					= 'MySQL-Datenbankname';
$lang_setup['dbfail_text']				= 'Mit den angegebenen Daten konnte keine Verbindung zu einer MySQL-Datenbank aufgebaut werden. Bitte pr&uuml;fen Sie die Daten und versuchen Sie es erneut.';
$lang_setup['dbexists_text']			= 'In der angegebenen Datenbank existiert bereits eine Installation von b1gMail. Bitte l&ouml;schen Sie zuerst alle Tabellen der alten Installation oder w&auml;hlen Sie eine andere Datenbank. Wenn Sie von einer &auml;lteren b1gMail-Version updaten m&ouml;chten, verwenden Sie bitte die entsprechene Update-Routine.';
$lang_setup['emailcfg']					= 'E-Mail-Konfiguration';
$lang_setup['emailcfg_text']			= 'Bitte machen Sie im Folgenden Angaben zur gew&uuml;nschten Empfangs- und Versandmethode. Weitere Informationen dazu finden Sie in der b1gMail-Dokumentation.';
$lang_setup['receiving']				= 'Empfangs-Methode';
$lang_setup['pop3gateway']				= 'POP3-Gateway (CatchAll)';
$lang_setup['pipe']						= 'b1gMailServer oder Pipe-/Transportmap-Gateway';
$lang_setup['pop3_host']				= 'POP3-Server';
$lang_setup['pop3_user']				= 'POP3-Benutzer';
$lang_setup['pop3_pass']				= 'POP3-Passwort';
$lang_setup['sending']					= 'Versand-Methode';
$lang_setup['phpmail']					= 'PHP-Mail';
$lang_setup['smtp']						= 'SMTP';
$lang_setup['sendmail']					= 'Sendmail';
$lang_setup['sendmail_path']			= 'Pfad zu Sendmail';
$lang_setup['smtp_host']				= 'SMTP-Server';
$lang_setup['emailcfgpop3fail_text']	= 'Mit den angegebenen Daten konnte keine Verbindung zu einem POP3-Mail-Account aufgebaut werden. Bitte pr&uuml;fen Sie die Daten und versuchen Sie es erneut.';
$lang_setup['emailcfgsmfail_text']		= 'Der angegebenen Sendmail-Pfad existiert nicht oder ist nicht ausf&uuml;hrbar. Bitte pr&uuml;fen Sie Ihre Angaben und versuchen Sie es erneut.';
$lang_setup['misc']						= 'Sonstige Angaben';
$lang_setup['misc_text']				= 'Bitte machen Sie noch folgende Angaben und klicken Sie dann auf &quot;Weiter &raquo;&quot;, um die Installation durchzuf&uuml;hren.';
$lang_setup['adminuser']				= 'Administrations-Benutzername';
$lang_setup['adminpw']					= 'Administrations-Passwort';
$lang_setup['domains']					= 'E-Mail-Domains (eine Domain pro Zeile)';
$lang_setup['url']						= 'URL zu b1gMail';
$lang_setup['installing']				= 'Installieren...';
$lang_setup['installing_text']			= 'b1gMail wird nun installiert. Im Folgenden finden Sie alle durchgef&uuml;hrten Schritte mit Status-Meldung.';
$lang_setup['inst_dbstruct']			= 'Datenbank-Struktur (Version %s) installieren...';
$lang_setup['inst_defaultcfg']			= 'Standard-Konfiguration erstellen...';
$lang_setup['inst_admin']				= 'Administrator-Benutzer erstellen...';
$lang_setup['inst_defaultgroup']		= 'Standard-Gruppe erstellen...';
$lang_setup['inst_exdata']				= 'Beispiel-Daten installieren...';
$lang_setup['inst_postmaster']			= 'Postmaster anlegen...';
$lang_setup['inst_config']				= 'Konfigurationsdatei schreiben...';
$lang_setup['defaultgroup']				= 'Standard-Gruppe';
$lang_setup['log_text']					= 'W&auml;hrend der Installation wurden Log-Meldungen generiert. Im Folgenden finden Sie das Installations-Protokoll.';
$lang_setup['finished_text']			= 'Die Installation wurde abgeschlossen. Bitte beachten Sie die folgenden Daten zu Ihrer b1gMail-Installation.';
$lang_setup['userlogin']				= 'Benutzer-Login';
$lang_setup['adminlogin']				= 'Administrations-Login';
$lang_setup['dbnotexists_text']			= 'In der angegebenen Datenbank existiert keine Installation von b1gMail 6.3.1. Wenn Sie von einer fr&uuml;heren b1gMail-Version updaten m&ouml;chten, aktualisieren Sie diese bitte zuerst auf Version 6.3.1.';
$lang_setup['update']					= 'Update';
$lang_setup['update_text']				= 'Nach einem Klick auf &quot;Weiter &raquo;&quot; wird mit dem Update von b1gMail6 auf b1gMail7 begonnen. Bitte beachten Sie die folgenden wichtigen Aspekte.';
$lang_setup['update_note1']				= 'Das Update &auml;ndert die b1gMail-Tabellen so, dass b1gMail6 danach nicht mehr mit den Daten kompatibel sein wird.';
$lang_setup['update_note2']				= 'Das Update kann je nach Datenbestand einige Minuten bis einige Stunden dauern. Der Web- und MySQL-Server kann dabei stark beansprucht werden.';
$lang_setup['update_note3']				= 'Legen Sie vor dem Update <strong>unbedingt</strong> ein vollst&auml;ndiges Backup aller Dateien und MySQL-Tabellen an. F&uuml;r eventuelle Datenverluste wird keine Haftung &uuml;bernommen; die Integrit&auml;t der aktualisierten Daten kann nicht gew&auml;hrleistet werden.';
$lang_setup['update_note4']				= 'Unterbrechen Sie den Update-Prozess keinesfalls! Ihr Browser muss bis zum vollst&auml;ndigen Abschluss des Updates ge&ouml;ffnet bleiben. Die Netzwerkverbindung zum Server darf nicht unterbrochen werden.';
$lang_setup['update_note5']				= 'Folgende Daten k&ouml;nnen nicht in b1gMail7 &uuml;bernommen werden und gehen beim Update verloren: Aktueller Spam-Filter-Trainingszustand, Rechtschreib-W&ouml;rterbuch, Badwords, Entw&uuml;rfe, Einschreiben.';
$lang_setup['update_text2']				= 'Wenn Sie sicher sind, dass Sie den Update-Prozess nun beginnen m&ouml;chten, klicken Sie auf &quot;Weiter &raquo;&quot;.';
$lang_setup['updating']					= 'Updaten...';
$lang_setup['updating_text']			= 'Das Update wird nun in mehreren Schritten automatisch durchgef&uuml;hrt. Im Folgenden sehen Sie den Fortschritt des Updates.';
$lang_setup['updating_text2']			= 'Unterbrechen Sie den Update-Prozess keinesfalls! Sie werden bei Fertigstellung des Updates informiert.';
$lang_setup['step']						= 'Schritt';
$lang_setup['progress']					= 'Fortschritt';
$lang_setup['update_prepare']			= 'Update vorbereiten';
$lang_setup['update_struct1']			= 'Datenbank-Struktur vorbereiten';
$lang_setup['update_struct2']			= 'Datenbank-Struktur aktualisieren';
$lang_setup['update_struct3']			= 'Datenbank optimieren und aufr&auml;umen';
$lang_setup['update_mails']				= 'E-Mail-Meta-Informationen aktualisieren';
$lang_setup['update_folders']			= 'Ordner aktualisieren';
$lang_setup['update_filters']			= 'Filter aktualisieren';
$lang_setup['update_autoresponders']	= 'Autoresponder aktualisieren';
$lang_setup['update_config']			= 'Konfiguration aktualisieren';
$lang_setup['update_calendar']			= 'Kalender aktualisieren';
$lang_setup['update_optimize']			= 'Tabellen optimieren';
$lang_setup['updatedone']				= 'Das Update wurde abgeschlossen. Klicken Sie auf &quot;Weiter &raquo;&quot;, um fortzufahren.';
$lang_setup['updatesteps_text']			= 'Das Update wurde abgeschlossen. Bitte f&uuml;hren Sie nun <b>unbedingt</b> folgende Schritte in dieser Reihenfolge aus:';
$lang_setup['update_step1']				= '<font color="red">L&ouml;schen Sie den Ordner &quot;setup&quot; aus Sicherheitsgr&uuml;nden nun <b>unbedingt</b> von Ihrem Webspace!</font>';
$lang_setup['update_step2']				= 'Loggen Sie sich in den <a href="../admin/" target="_blank">Admin-Bereich</a> ein und vergewissern Sie sich unter &quot;Einstellungen&quot; &raquo; &quot;Allgemein&quot;, dass der Pfad zum Data-Verzeichnis korrekt angegeben ist.';
$lang_setup['update_step3']				= 'F&uuml;hren Sie unter &quot;Tools&quot; &raquo; &quot;Optimierung&quot; &raquo; &quot;Cache&quot; in dieser Reinhefolge folgende Operationen durch:';
$lang_setup['update_step3a']			= 'E-Mail-Gr&ouml;&szlig;en neu berechnen';
$lang_setup['update_step3b']			= 'Benutzer-Speichernutzung neu berechnen';
$lang_setup['update_step4']				= 'Deaktivieren Sie unter &quot;Einstellungen&quot; &raquo; &quot;Allgemein&quot; den &quot;Wartungsmodus&quot;, um Ihre b1gMail-Installation f&uuml;r Besucher zug&auml;nglich zu machen.';

$lang_setup['error']					= 'Fehler';
$lang_setup['notice']					= 'Hinweis';
$lang_setup['success']					= 'Erfolgreich';
$lang_setup['update_welcome_text']		= 'Herzlich Willkommen! Dieser Assistent aktualisiert Ihre b1gMail-Version <b>%s</b> auf die aktuelle Version <b>%s</b>. Klicken Sie auf &quot;Weiter &raquo;&quot;, um fortzufahren.';
$lang_setup['uptodate']					= 'Ihre b1gMail-Version befindet sich bereits auf dem aktuellsten Stand (<b>%s</b>).';
$lang_setup['unknownversion']			= 'Ihre b1gMail-Version (<b>%s</b>) ist unbekannt und kann daher mit diesem Assistenten nicht auf <b>%s</b> aktualisiert werden.';
$lang_setup['update_resetcache']		= 'Cache zur&uuml;cksetzen';
$lang_setup['update_complete']			= 'Update abschlie&szlig;en';
$lang_setup['updatedonefinal']			= 'Das Update wurde abgeschlossen. L&ouml;schen Sie nun bitte <b>unbedingt</b> den Ordner &quot;setup&quot; von Ihrem Webspace!';
$lang_setup['dbnotconverted']           = 'Ihre Datenbank liegt noch im Latin1-Format vor. Bitte fahren Sie mit <a href="setup/utf8convert.php">setup/utf8convert.php</a> fort und löschen den  Ordner &quot;setup&quot; erst <b>nach Konvertierung</b> von Ihrem Webspace!';

$lang_setup['utf8convert']				= 'UTF-8-Konverter';
$lang_setup['convert_welcome_text']		= 'Herzlich Willkommen! Dieser Assistent konvertiert Ihre b1gMail-Datenbank von den <b>ISO-8859-15</b>-Zeichensatz nach <b>UTF-8</b>. <font color="red">Nutzen Sie diesen Konverter nur, wenn Ihre Datenbank in ISO-8859-15-/Latin1-Codierung vorliegt!</font> Klicken Sie auf &quot;Weiter &raquo;&quot;, um fortzufahren.';
$lang_setup['mbiconvext']				= 'Zeichensatz-Erweiterung';
$lang_setup['mysqlversion']				= 'MySQL-Version';
$lang_setup['convert_syscheck_text']	= 'Ihr System wird nun auf Kompatibilit&auml;t mit dem Betrieb von b1gMail im UTF-8-Modus &uuml;berpr&uuml;ft.';
$lang_setup['converting']				= 'Konvertieren...';
$lang_setup['converting_text']			= 'Die Konvertierung wird nun in mehreren Schritten automatisch durchgef&uuml;hrt. Im Folgenden sehen Sie den Fortschritt des Vorgangs.';
$lang_setup['converting_text2']			= 'Unterbrechen Sie den Konvertierungs-Prozess keinesfalls! Sie werden bei Fertigstellung der Konvertierung informiert.';
$lang_setup['convert_prepare']			= 'Konvertierung vorbereiten';
$lang_setup['convert_analyzedb']		= 'Datenbank analysieren';
$lang_setup['convert_prepare_tables']	= 'Tabellen vorbereiten';
$lang_setup['convert_convertdata']		= 'Daten konvertieren';
$lang_setup['convert_collations']		= 'Datenbank-/Tabellen-Kollationen aktualisieren';
$lang_setup['convert_langfiles']		= 'Sprachdateien konvertieren';
$lang_setup['convert_resetcache']		= 'Cache zur&uuml;cksetzen';
$lang_setup['convert_complete']			= 'Konvertierung abschlie&szlig;en';
$lang_setup['convert_alreadyutf8']		= 'Ihre b1gMail-Datenbank wurde bereits nach UTF-8 konvertiert.';
$lang_setup['convertdonefinal']			= 'Die Konvertierung wurde erfolgreich abgeschlossen.';
$lang_setup['back']						= 'Zur&uuml;ck';
$lang_setup['lock_detected']			= 'Es wurde eine Lock-Datei gefunden. Entfernen Sie die Datei <code>lock</code>, <code>lock_update</code>, <code>lock_utf8</code> bzw. <code>lock_utf8mb4</code> im Ordner <code>setup/</code>, wenn Sie den Assistenten erneut ausf&uuml;hren m&ouml;chten.';
$lang_setup['already_installed']		= 'Es wurde bereits eine Installation gefunden (<code>serverlib/config.inc.php</code>). Entfernen Sie die Lock-Datei und ersetzen Sie die Konfiguration nur, wenn Sie wirklich neu installieren m&ouml;chten.';
$lang_setup['lock_failed']				= 'Die Lock-Datei konnte nicht geschrieben werden. L&ouml;schen Sie den Ordner <strong>setup</strong> jetzt unbedingt von Ihrem Webspace.';
$lang_setup['csrf_fail']				= 'Die Sitzung ist ung&uuml;ltig oder abgelaufen. Bitte starten Sie den Assistenten erneut.';
$lang_setup['mysql_prefix']				= 'Tabellen-Pr&auml;fix';
$lang_setup['adminpw_confirm']			= 'Passwort best&auml;tigen';
$lang_setup['pw_too_short']				= 'Das Administrations-Passwort muss mindestens 8 Zeichen lang sein.';
$lang_setup['password_mismatch']		= 'Die beiden Passw&ouml;rter stimmen nicht &uuml;berein.';
$lang_setup['pop3_port']				= 'POP3-Port';
$lang_setup['pop3_tls']					= 'TLS/SSL (Port 995)';
$lang_setup['smtp_port']				= 'SMTP-Port';
$lang_setup['smtp_auth']				= 'SMTP-Authentifizierung';
$lang_setup['smtp_user']				= 'SMTP-Benutzer';
$lang_setup['smtp_pass']				= 'SMTP-Passwort';
$lang_setup['ext_mysqli']				= 'MySQLi';
$lang_setup['ext_mbstring']				= 'mbstring / iconv';
$lang_setup['ext_json']					= 'JSON';
$lang_setup['ext_openssl']				= 'OpenSSL';
$lang_setup['ext_session']				= 'Session';
$lang_setup['optional']					= 'optional';
$lang_setup['remove_setup']				= 'L&ouml;schen Sie den Ordner <strong>setup</strong> aus Sicherheitsgr&uuml;nden jetzt unbedingt von Ihrem Webspace.';
$lang_setup['copy']						= 'Kopieren';
