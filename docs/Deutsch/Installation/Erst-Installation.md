# Erst-Installation
Wenn Sie eine Neu-/Erst-Installation von b1gMail durchführen möchten, gehen Sie bitte wie folgt vor.
Richten Sie ein POP3-Postfach ein und konfigurieren Sie die CatchAll-Funktion aller Domains, die Sie mit b1gMail verwenden möchten, so, dass die E-Mails an diese Domains dieses POP3-Postfach erreichen (Dieser Schritt ist nicht nötig, wenn Sie den Einsatz von b1gMailServer zum Mail-Empfang planen). Mehr über [Empfangsmethoden](../Empfangsmethode.md)

Laden Sie alle Dateien und Ordner aus dem Ordner "upload" dieses Paketes auf Ihren Webspace hoch (z.B. mittels eines FTP-Programms).

Versehen Sie die folgenden Dateien und Ordner auf Ihrem Webspace mit Schreibrechten (i.d.R. chmod 666 für Dateien und chmod 777 für Ordner):

* serverlib/config.inc.php
* serverlib/version.inc.php
* admin/templates/cache/
* languages/
* logs/
* plugins/
* plugins/templates/
* plugins/templates/images/
* temp/
* temp/session/
* temp/cache/
* templates/modern/cache/
* data/

Rufen Sie die Installationsroutine von b1gMail durch Aufruf des Ordners /setup/ in Ihrem Browser auf (z.B. http://www.ihre-domain.xy/b1gmail/setup/) und folgen Sie den Anweisungen des Installations-Assistenten.
Löschen Sie nach erfolgter Installation den Ordner "setup" unbedingt vollständig von Ihrem Webspace.
