# Installation von b1gMail mit Plesk auf Ubuntu 16.04

Diese Anleitung beschreibt die Installation von b1gMail mit Plesk auf einem frisch aufgesetzten Ubuntu 16.04‑Server. Plesk wird so installiert, dass Komponenten wie Postfix und Dovecot während der Installationsroutine weggelassen werden.

## Vorbereitung

Zuerst prüfen, ob Updates verfügbar sind:
```bash
apt-get update && apt-get upgrade
```
Nach Abschluss ggf. System neu starten.

Hostname ggf. anpassen (kann Installationsprobleme vermeiden). Die Dateien bearbeiten:
```bash
nano /etc/hosts
nano /etc/hostname
```
Beispiel‑Hostname: MAIL.DEINEDOMAIN.DE

Anschließend mit der Plesk‑Installation beginnen.

## Installation von Plesk 17

Installationsdatei herunterladen:
```bash
wget https://autoinstall.plesk.com/plesk-installer
```
Ausführbar machen:
```bash
chmod +x plesk_installer
```
Installer starten:
```bash
./plesk_installer
```
- Die Abfrage zur Modifikation des Servers mit "F" bestätigen.
- Gewünschte Plesk‑Version auswählen (z. B. Plesk 17.5.3 stable) und mit "F" bestätigen.
- Installationsart: **Custom** wählen und mit "F" bestätigen.

Features anpassen:
- Wenn b1gMailServer auf dem gleichen Server installiert werden soll, **Webmail** und **Mailhosting** abwählen.
  - Menü "12" wählen, Unterpunkte "1 + F" und "2 + F" abwählen.
  - Mit "B" zurück zum Hauptmenü.
  - Im Menü "Mail Hosting" Punkt "5" abwählen; Bestätigung mit "y", dann "B" zurück.
- Installation mit "F" starten und die Paketanzahl mit "F" bestätigen.

Nach Abschluss erscheint die Meldung mit Link zur Plesk‑Weboberfläche:
"https://HOSTNAME:8443"

Plesk ist nun ohne Postfix und Dovecot installiert.

## Erstkonfiguration über Weboberfläche

Anmelden an:
```
https://SERVERNAME_ODER_IP:8443
```
- Erster Login: Benutzer `root` und Root‑Passwort.
- Einrichtungsassistent (IP‑Adresse, Administratorpasswort — sollte nicht identisch mit Root‑Passwort sein).
- Verwendungsart von Plesk auswählen.
- Ersten Webspace und FTP‑User anlegen (bei Webseite + b1gMail empfiehlt es sich, Hauptdomain anzulegen und später Subdomain für b1gMail).
- Lizenzschlüssel von Ihrem Serverprovider eintragen.

b1gMail via FTP hochladen und einrichten. Da Plesk den Mailserver nicht installiert hat, ist die Installation von b1gMailServer ohne Hürden möglich.

## Installation von b1gMailServer

1. Paket aus dem Kundencenter herunterladen und Plugin‑Datei in b1gMail über die Pluginverwaltung installieren.
2. Installationsdatei `b1gmailserver.x.x.x.run` per SFTP (Filezilla, WinSCP, etc.) auf den Server hochladen.
3. Rechte setzen:
```bash
chmod 777 b1gmailserver.x.x.x.run
```
4. Datei ausführen:
```bash
./b1gmailserver.x.x.x.run
```
5. Installer startet und fragt nach Sprache.
6. Nach Sprachauswahl wird eine grafische Installationsoberfläche angezeigt. Im Browser die SERVER‑IP mit Port 8089 öffnen:
```
SERVERIP:8089
```
- Authentifizierungsdaten werden in der Konsole angezeigt; diese im Browser eingeben.
- Im grafischen Installer Bestimmungen akzeptieren.
- Lizenzschlüssel aus dem Kundencenter eingeben.
- Server‑IP angeben.
- MySQL‑Daten der b1gMail‑Installation eingeben (damit sich b1gMailServer in die Datenbank integrieren kann).
- Daten überprüfen und Installation starten.

Nach Abschluss beendet sich das zuvor in der Konsole aufgerufene Script automatisch.

Zum Abschluss xinetd neu starten:
```bash
service xinetd restart
```

Fertig — Maildienst ist eingerichtet.