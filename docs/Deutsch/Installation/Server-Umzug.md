# Server-Umzug

Dieses Dokument beschreibt die Vorgehensweise, eine b1gMail-Installation auf einen anderen Server umzuziehen. Es handelt sich hierbei nicht um eine Schritt-für-Schritt-Anleitung sondern um eine Schilderung des prinzipiellen Vorgehens.

## Wichtiger Hinweis 

Generell ist es unbedingt zu empfehlen, den alten Server oder Webspace erst dann zu deaktivieren bzw. die Daten der alten Installation zu löschen, wenn der Umzug abgeschlossen wurde und die neue Installation definitiv inklusive aller Daten korrekt funktioniert.
Es empfiehlt sich außerdem, genügend Zeit für den Umzug einzuplanen und diesen nicht in letzter Minute - oder gar kurz vor Kündigungszeitpunkt des alten Servers - durchzuführen.
Wird diese Regel eingehalten, ist der Umzug so gut wie risikofrei (abgesehen von der Downtime des Projekts während des Umzugs).

## Vorgehensweise 

* Den Wartungsmodus der b1gMail-Installation im Adminbereich unter 'Einstellungen' -> 'Allgemein' aktivieren.
* Falls b1gMailServer eingesetzt wird, den Warteschleifendienst und xinetd beenden:  
  ```
   /etc/init.d/bms-queue stop
   /etc/init.d/xinetd stop
  ```
* Den kompletten b1gMail-Ordner inklusive 'data'-Ordner und jeglichem Inhalt auf den neuen Server übertragen.
* Die gesamte b1gMail-MySQL-Datenbank auf den neuen Server übetragen.
* Die Zugriffsrechte sowie Inhaber und Gruppen aller Dateien und Ordner des b1gMail-Ordners kontrollieren und ggf. korrigieren.
* Falls sich die MySQL-Zugangsdaten geändert haben, diese in der Datei <tt>serverlib/config.inc.php</tt> an die neue Umgebung anpassen (siehe [Installationsanleitung](Installation.md)).
* Der Administrationsbereich der neuen b1gMail-Installation sollte nun aufrufbar sein. In den Administrationsbereich einloggen und unter 'Einstellungen' -> 'Allgemein' alle Pfade und URLs kontrollieren und ggf. korrigieren, insbesondere URL zu b1gMail, Pfad zu b1gMail und Pfad zum 'data'-Verzeichnis.
* Alle anderen Einstellungen, die sich durch den Umzug geändert haben könnten, kontrollieren und korrigieren. Insbesondere Empfangs- und Versandeinstellungen müssen oft angepasst werden.
* Die Willkommens-Seite des Adminbereichs auf eventuelle Warnungen/Fehlermeldungen überprüfen.
* Den Wartungsmodus deaktivieren.
* Falls b1gMailServer eingesetzt werden soll, diesen erneut gemäß der [Installationsanleitung](../../b1gMailServer/Installation.md) installieren. Das b1gMailServer-Admin-Plugin muss nicht erneut installiert werden, falls b1gMailServer auch in der alten Installation schon eingesetzt wurde und die Version, die auf dem neuen Server installiert wird, die gleiche wie auf dem alten Server ist.


## Test 

Nach dem Umzug sollte - _vor_ Löschung der alten Installation - die neue Installation gründlich getestet werden. Wichtig ist zum Beispiel (ohne Anspruch auf Vollständigkeit):

* Funktioniert der Login in die neue Installation?
* Lassen sich alte E-Mails aufrufen?
* Lassen sich alte Webdisk-Dateien herunterladen?
* Lassen sich E-Mails versenden und empfangen (von intern nach extern, von extern nach intern, von intern nach intern)?
* Lassen sich Webdisk-Dateien hochladen?
* Funktionieren POP3-/IMAP-/SMTP-Verbindungen (falls b1gMailServer eingesetzt wird)?
* Sind interne Ordner (data, temp) ausreichend gesichert und nicht von Außen zugänglich?
* Sind alle DNS-Einträge aktualisiert?
Erst nach Abschluß aller Tests sollte der alte Webspace/Server gekündigt/gelöscht werden.

## Während des Umzugs eintreffende Mails 

Wird b1gMailServer verwendet und der Mail-Empfang mittels b1gMailServers SMTP-Dienst genutzt, gehen während des Umzugs eingehende Mails i.d.R. nicht verloren, sofern der Umzug weniger als 24 Stunden dauert.
Da während des Umzugs gemäß der oben geschilderten Vorgehensweise der xinetd-Dienst und damit der SMTP-Dienst deaktiviert sind, werden E-Mail-Server, die Mails an den umziehenden Dienst ausliefern möchten, die Zustellung von E-Mails i.d.R. mind. 24 Stunden lang erneut versuchen.

Sollte nicht b1gMailServer zum Empfang eingesetzt werden sondern z.B. die POP3-CatchAll-Methode, empfiehlt es sich, nach Umzug das POP3-Postfach des alten Servers durch b1gMail abrufen zu lassen, falls dort zwischenzeitlich noch E-Mails eingetroffen sind.