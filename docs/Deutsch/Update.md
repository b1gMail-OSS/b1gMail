# Update von vorheriger b1gMail7-Version
Wenn Sie bereits eine ältere b1gMail7-Version einsetzen, verwenden Sie unbedingt statt des Komplett-Pakets das jeweilige Update-Paket und gehen Sie nach der folgenden Anleitung vor.

**Legen Sie vor Updates immer eine komplette Datensicherung (Datenbank und Dateien) an!**

1. Wenn Sie von b1gMail in einer älteren Version als 7.4.0-Beta1 aktualisieren, loggen Sie sich in den Adminbereich ein, gehen Sie zu _Einstellungen_ » _E-Mail_ und stellen Sie sicher, dass die Mail-Speicherung auf _Dateisystem_ eingestellt ist. Gehen Sie, auch wenn dies bereits der Fall war, dann zu _Tools_ » _Optimierung_ » _Dateisystem_ und führen Sie die Funktion _Mail-Speicherung_ (Datenbank -> Dateien) aus. Dies ist nötig, um sicherzustellen, dass sich keine E-Mails mehr in der Datenbank befinden, da diese Speichermethode ab Version 7.4.0-Beta1 entfernt wurde. Sollte dieser Schritt nicht wie beschrieben durchgeführt werden, wird der Updater das Update u.U. verweigern!
1. Aktivieren Sie den Wartungsmodus (im Admin-Bereich unter _Einstellungen_ » _Allgemein_).
1. Laden Sie alle Dateien und Ordner aus dem Ordner "src" des passenden Update-Pakets auf Ihren Webspace hoch (z.B. mittels eines FTP-Programms). Lassen Sie dabei bereits existierende Dateien überschreiben.
1. Versehen Sie die folgenden Dateien und Ordner auf Ihrem Webspace mit Schreibrechten (i.d.R. chmod 666 für Dateien und chmod 777 für Ordner):
    * serverlib/config.inc.php
    * serverlib/version.inc.php
    * admin/templates/cache/
    * languages/
    * logs/
    * plugins/
    * plugins/css/
    * plugins/js/
    * plugins/templates/
    * plugins/templates/images/
    * temp/
    * temp/session/
    * temp/cache/
    * templates/modern/cache/
    * data/
1. Rufen Sie die Updateroutine von b1gMail durch Aufruf der Datei /setup/update.php in Ihrem Browser auf (z.B. http://www.ihre-domain.xy/b1gmail/setup/update.php) und folgen Sie den Anweisungen des Update-Assistenten.
1. Wenn Sie b1gMail derzeit nicht im UTF-8-Modus einsetzen und in den UTF-8-Modus wechseln möchten, rufen Sie die Datei /setup/utf8convert.php in Ihrem Browser auf und folgen Sie den Anweisungen des Konvertierungs-Assistenten.
1. Optional: Wenn Sie b1gMail derzeit im UTF-8-Modus einsetzen und die volle UTF-8-Funktionalität möchten, rufen Sie die Datei /setup/utf8mb4convert.php in Ihrem Browser auf und folgen Sie den Anweisungen des Konvertierungs-Assistenten.
1. Löschen Sie nach erfolgtem Update den Ordner "setup" unbedingt vollständig von Ihrem Webspace.
1. Falls Sie b1gMailServer verwenden, starten Sie nun bitte den Warteschleifendienst (bms-queue) neu.

Ab b1gMail 7.3.0-Beta1 ist für den Login in den Adminbereich ein Benutzername erforderlich. Nach dem Update von einer vorherigen Version ist der Standard-Administrator-Benutzername "admin".

# Update von b1gMail6
Wenn Sie bereits b1gMail6 einsetzen, aktualisieren Sie bitte zuerst auf b1gMail 7.2.0 und dann auf diese Version. 