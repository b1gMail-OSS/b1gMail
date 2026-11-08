# Synchronisation mit Mobilgeräten

Für b1gMail ist ein Backend für das Open-Source-Synchronisations-Framework [Z-Push](https://z-push.org/) verfügbar. Mit Hilfe von Z-Push und dem b1gMail-Backend können Benutzer einer b1gMail-Installation ihre E-Mails, Kontakte, Aufgaben und Termine mit Exchange-kompatiblen Mobilgeräten wie iPhone/iPad und Android-Smartphones synchronisieren. Der Dienst ist Push-fähig, sodass neue Objekte wie E-Mails und Termine umgehend auf dem Mobilgerät zur Verfügung stehen.

Dieses Dokument beschreibt, wie Z-Push samt b1gMail-Backend auf dem Server eingerichtet wird und wie das Mobilgerät konfiguriert wird.

## Wichtige Hinweise 

* Zur Synchronisation stellen die Mobilgeräte u.U. eine dauerhafte Verbindung zum Server her. Bei vielen gleichzeitig aktiven Benutzern kann dies den Server natürlich entsprechend belasten.
* Z-Push und das b1gMail-Backend für Z-Push stehen unter der [AGPL](http://www.gnu.org/licenses/agpl-3.0.html)-Lizenz. Über die Implikationen dieser Lizenz sollte Klarheit herrschen. Insbesondere müssen eventuelle Änderungen am Code an die Community zurückgegeben werden.

## Anforderungen 

Zur Einrichtung sollten grundlegende Kenntnisse zur Administrations des Servers vorhanden sein.
Weiterhin muss der Server folgende Anforderungen erfüllen:

* Dedizierter Server oder vServer mit Root-Zugriff. Eine Installation ohne Root-Zugriff ist möglicherweise mit Einschränkungen auch möglich, wird jedoch nicht durch diese Anleitung abgedeckt.
* b1gMail 7.3.0-Beta5 oder neuer.
* PHP 5.1 oder neuer.
* Folgende PHP-Erweiterungen (können meist über den Paketmanager der Linux-Distribution einfach nachinstalliert werden):
  * soap (Paket <tt>php-soap</tt> bzw. <tt>php5-soap</tt>)
  * pcntl (Paket <tt>php-process</tt> bzw. <tt>php5-pcntl</tt>)
  * sysvshm (Paket <tt>php-process</tt> bzw. <tt>php5-sysvshm</tt>)
  * sysvsem (Paket <tt>php-process</tt> bzw. <tt>php5-sysvsem</tt>)
* Webserver mit SSL-Support (HTTPS) wird dringend empfohlen. Der Betrieb über Nicht-SSL-Verbindungen ist unsicher (Übertragung aller Daten im Klartext!) und bedeutet auf vielen mobilen Endgeräte eine verwirrende/komplizierte Konfiguration. Selbst signierte SSL-Zertifikate sind okay, verursachen aber meist eine kurze Warnung bei Einrichtung des Accounts auf dem mobilen Endgerät.

## Installation von Z-Push mit b1gMail-Backend 


1. Die erforderlichen/empfohlenen PHP-Erweiterungen (siehe oben) installieren, z.B. mit dem Paketmanager der Linux-Distribution.
1. Die aktuelle Version von Z-Push [herunterladen](https://github.com/Z-Hub/Z-Push/releases) und auf den eigenen Server hochladen.
1. Per SSH auf den Server einloggen und das Z-Push-Archiv entpacken: `tar xzvf z-push-*.tar.gz`
1. Das Verzeichnis <tt>/usr/share/z-push/</tt> erstellen und den Inhalt des soeben entpackten Ordners in dieses kopieren:
  ```
  mkdir -p /usr/share/z-push
  cp -R z-push-*/* /usr/share/z-push/
  ```
1. Log- und Daten-Ordner erstellen und mit den nötigen Zugriffsrechten versehen:
   ```
   mkdir -p /var/log/z-push
   chmod 0777 /var/log/z-push
   ```
1. Das b1gMail-Z-Push-Backend [herunterladen](https://github.com/b1gMail-OSS/zpush-b1gmail), entpacken die PHP-Dateien nach <tt>/usr/share/z-push/backend/b1gmail/</tt> kopieren:
  ```
  unzip master.zip
  mkdir /usr/share/z-push/backend/b1gmail/
  cp b1gmail.php db.php sendmail.php /usr/share/z-push/backend/b1gmail/
  ```
1. Die Konfigurationsdatei von Z-Push, <tt>/usr/share/z-push/config.php</tt>, in einem Texteditor öffnen. Die Zeile `define('BACKEND_PROVIDER', "BackendZarafa");` suchen und ersetzen durch folgendes, wobei die MySQL-Zugangsdaten durch die Zugangsdaten zu Ihrer b1gMail-Datenbank ersetzt werden müssen:
  ```
  define('BACKEND_PROVIDER', 'BackendB1GMail');
  define('B1GMAIL_DB_HOST',               'localhost');
  define('B1GMAIL_DB_USER',               'root');
  define('B1GMAIL_DB_PASS',               'passwort');
  define('B1GMAIL_DB_DB',                 'b1gmail');
  define('B1GMAIL_DB_PREFIX',             'bm60_');
  ```

  Falls Z-Push >= 2.2 verwendet wird, muss außerdem die Zeile `define('USE_FULLEMAIL_FOR_LOGIN', false);` ersetzt werden durch `define('USE_FULLEMAIL_FOR_LOGIN', true);`
1. Die Konfigurationsdatei des Webservers (z.B. Apache) in einem Texteditor öffnen und folgende Zeile hinzufügen: `Alias /Microsoft-Server-ActiveSync /usr/share/z-push/index.php`
1. Den Webserver-Dienst neu starten, z.B. `/etc/init.d/httpd restart`
1. Prüfen, ob unter https://server-domain.xy/Microsoft-Server-ActiveSync eine Passwort-Abfrage erscheint. Falls ja, ist die Installation erfolgreich verlaufen. Ansonsten sollte eine Fehlermeldung angezeigt werden, die auf das Problem hinweist.

## Konfiguration im b1gMail-Adminbereich 

Im b1gMail-Adminbereich muss lediglich unter 'Benutzer und Gruppen' -> 'Gruppen' das Häkchen bei 'Synchronisierung?' für alle Benutergruppen, die zur Nutzung des Synchronisations-Dienstes befugt sein sollen, aktiviert werden.

## Konfiguration des Mobil-Geräts 

### iPhone/iPad 

...

### Android 

...
