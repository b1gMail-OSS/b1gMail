# Entscheidung für eine Empfangsmethode

Damit b1gMail E-Mails empfangen kann, müssen diese vom empfangenden Mail-Server an
b1gMail übergeben werden.

Diese Übergabe kann auf verschiedene Weisen durchgeführt werden. b1gMail ermöglicht daher den Empfang mittels verschiedener Empfangsmethoden, die sich in ihrer Effizienz und vor allem in ihren Systemanforderungen unterscheiden.

Im Folgenden finden Sie eine Übersicht der möglichen Empfangsmethoden mit den jeweiligen Anforderungen. Die Entscheidung für eine Empfangsmethode sollten Sie je nach dem, welche Möglichkeiten Ihr Webspace bietet bzw. welche Rechte Sie auf dem Server besitzen, auf dem b1gMail installiert werden soll, treffen.

Sofern Sie nicht über Root-/Administrator-Zugriff auf den verwendeten Webspace bzw. Server verfügen, ist i.d.R. die Empfangsmethode „POP3-Gateway“ die passende Wahl.

## POP3-Gateway
Die Empfangsmethode „POP3-Gateway“ ist die am häufigsten genutzte Empfangsmethode, da sie sehr einfach eingerichtet werden kann und mit jeder Server-Konfiguration kompatibel ist, die die Systemanforderungen von b1gMail erfüllt.

Bei dieser Methode werden alle E-Mails, die an einen Empfänger unter Ihrer Domain gesendet werden (*@example.com), in einem zentralen Postfach gespeichert. Aus diesem Postfach kann b1gMail nun die E-Mails abrufen und diese an die eingerichteten Benutzer
verteilen. Der Nachteil dieser Empfangsmethode ist jedoch, dass eingehende E-Mails nicht einzeln in Echtzeit verarbeitet werden können, sondern periodisch abgerufen und gleichzeitig verarbeitet werden müssen. Bei sehr hohem E-Mail-Aufkommen (welches jedoch in der Regel
schon deswegen einen Root-Server voraussetzt) empfehlen wir daher die Nutzung einer anderen Empfangsmethode.

## Pipe-Gateway

Die „Pipe-Gateway“-Empfangsmethode erfordert zur Einrichtung entweder Root-Zugriff auf dem verwendeten Server oder die Möglichkeit, eingehende E-Mails per Pipe an ein PHP-Script weiterleiten zu lassen. Sobald eine E-Mail eintrifft, wird diese direkt an b1gMail weitergegeben, welches die E-Mail umgehend verarbeitet und an den Ziel-Benutzer zustellt. Gegenüber dem POP3-Gateway
hat diese Methode den Vorteil, dass die Server-Ressourcen gleichmäßiger ausgenutzt werden und somit ein höheres E-Mail-Aufkommen verarbeitet werden kann.

## Postfix-Transport-Map-Gateway

Diese Empfangsmethode setzt voraus, dass Sie über Root-Zugriff auf Ihren Server verfügen und Postfix als Mail-Server benutzen. Diese Methode arbeitet prinzipiell wie das Pipe-Gateway, nutzt jedoch noch eine Möglichkeit von Postfix aus, die eine zuverlässigere Bestimmung des Empfängers der E-Mail ermöglicht.

## b1gMailServer

Falls Sie über Root-Zugriff verfügen und noch keinen bestehenden Mail-Server einsetzen oder nicht auf die Nutzung eines bestimmten Mail-Servers angewiesen sind, können Sie b1gMailServer als Mail-Server verwenden. Der in b1gMailServer enthaltene Mail-Server ist für die Verwendung mit b1gMail optimiert und arbeitet daher optimal mit b1gMail zusammen. Die Einrichtung von b1gMailServer ist in der Dokumentation zu b1gMailServer beschrieben.


# Einrichtung der Empfangsmethode

Vor der eigentlichen Installation von b1gMail muss nun die Empfangsmethode, für die Sie sich in Schritt 2 entschieden haben, eingerichtet werden. Gehen Sie dazu bitte entspre chend der in Schritt 2 getroffenen Wahl vor.

## POP3-Gateway
Die Einrichtung der „POP3-Gateway“-Empfangsmethode ist im Folgenden allgemein beschrieben, die bei vielen Web-Hostern Verwendung finden.

Die folgenden Schritte können in der Regel in der Verwaltungs-Oberfläche Ihres Web-Hosters bzw. Servers erledigt werden.
1. Richten Sie ein neues POP3-Postfach ein und notieren Sie sich die Zugangsdaten zum soeben eingerichteten Postfach (Server, Benutzername und Passwort)
2. Aktivieren Sie die CatchAll-Funktion der Domain, die Sie mit b1gMail verwenden möchten, und konfigurieren Sie diese so, dass alle eingehenden E-Mails in das soeben eingerichtete POP3-Postfach gespeichert werden – meist kann dies durch das Einrichten einer E-Mail-Adresse mit dem Namen `*` (z.B. `*@example.com`) erreicht werden.

## Pipe-Gateway

Die Einrichtung der „Pipe-Gateway“-Empfangsmethode erfordert Root-/Shell-Zugriff auf Ihren Server und grundlegende Systemkenntnisse, vor allem vom eingesetzten Mail-Server.

### Allgemein
Der Mail-Server muss so konfiguriert werden, dass dieser alle E-Mails, die für die Domain, mit der Sie b1gMail verwenden möchten, eingehen, an das Script „interface/pipe.php“ von b1gMail weitergibt.

### Postfix
Im Folgenden ist die Einrichtung des Pipe-Gateways beispielhaft für den Mail-Server Postfix unter Linux beschrieben. Bei anderen Mail-Servern verläuft die Einrichtung meist ähnlich. In der Dokumentation zum Mail-Server finden Sie meist wertvolle weitere Informationen.
1. Loggen Sie sich per SSH als Benutzer „root“ auf Ihren Server ein
2. Erstellen Sie einen neuen Systembenutzer (z.B. `b1gmailuser`), der für den Empfang der E-Mails zuständig sein wird – als Benutzergruppe verwenden Sie die Gruppe, unter der auch der installierte Webserver (i.d.R. Apache) läuft (meist `www`)
`useradd -m -d /home/b1gmailuser -G www b1gmailuser`
3. Wechseln Sie in das Heimatverzeichnis des Benutzers
`cd /home/b1gmailuser`
4. Erstellen Sie eine neue Datei mit dem Namen `.forward`
`vi .forward`
5. Geben Sie folgende Zeile in die Datei ein, wobei Sie den Pfad `/srv/www/htdocs/web2/html/b1gmail/` durch das Verzeichnis ersetzen, in welches Sie b1gMail hochgeladen haben; `/usr/bin/php` ersetzen Sie bitte durch den absoluten Pfad zum PHP-Interpreter: 
`|“/usr/bin/php -q /srv/www/htdocs/web2/html/b1gmail/interface/pipe.php“`
6. Speichern Sie die Datei und schließen Sie den Editor
7. Öffnen Sie die Datei `/etc/postfix/virtual` mit `vi /etc/postfix/virtual`
8. Erstellen Sie am Ende der Datei für jede Domain, die Sie mit b1gMail verwenden möchten, die folgenden zwei Zeilen, wobei Sie b1gmailuser durch den Benutzer aus Schritt 2 und example.com durch die jeweilige Domain ersetzen:

   `example.com			b1gmailuser`

   `@example.com		b1gmailuser`

9. Speichern Sie die Datei und schließen Sie den Editor.
10. Übernehmen Sie die Änderungen durch Eingabe von `postmap /etc/postfix/virtual` und `service postfix restart`

## Postfix-Transport-Map-Gateway

Die Einrichtung der „Postfix-Transport-Map-Gateway“-Empfangsmethode erfordert Root-/Shell-Zugriff auf Ihren Server, den Einsatz von Postfix als Mail-Server sowie grundlegende System- und Postfix-Kenntnisse.

Gehen Sie zur Einrichtung bitte nach der folgenden Beschreibung vor. Je nach Systemkonfiguration können die für Ihr System nötigen Schritte geringfügig abweichen.

1. Loggen Sie sich per SSH als Benutzer „root“ auf Ihren Server ein
2. Öffnen Sie die Datei „/etc/postfix/master.cf“ in einem Texteditor, z.B. „vi“
`vi /etc/postfix/master.cf`
3. Fügen Sie am Ende der Datei die folgende Zeile ein, wobei Sie den Pfad `/srv/www/htdocs/web2/html/b1gmail/` durch das Verzeichnis, in welches Sie b1gMail hochgeladen haben, und `wwwrun` durch den Benutzer, unter dem Ihr Webserver läuft, ersetzen; `/usr/bin/php` ersetzen Sie bitte durch den absoluten Pfad zum PHP-Interpreter:
`b1gmailtransport unix - n n - 15 pipe flags=Rq user=wwwrun argv=/usr/bin/php /srv/www/htdocs/web2/html/b1gmail/interface/pipe.php ${sender} -- ${recipient}`
4. Speichern Sie die Datei und schließen Sie den Editor
5. Öffnen Sie die Datei „/etc/postfix/transport“ in einem Texteditor – falls diese nicht existiert, lassen Sie die Datei erstellen
`vi /etc/postfix/transport`
6. Erstellen Sie am Ende der Datei für jede Domain, die Sie mit b1gMail verwenden möchten, die folgende Zeile, wobei Sie example.com durch die jeweilige Domain ersetzen: `example.com			b1gmailtransport:dummy`
7. Speichern Sie die Datei und schließen Sie den Editor
8. Öffnen Sie die Datei „/etc/postfix/main.cf“ in einem Texteditor `vi /etc/postfix/main.cf`
9. Suchen Sie die Zeile, die mit relay_domains anfängt – wenn eine solche Zeile bereits existiert, ergänzen Sie alle Domains, die Sie mit b1gMail verwenden möchten; ansonsten fügen Sie eine neue Zeile ein; Beispiel:
`relay_domains = example.com example.net example.org`
10. Falls die folgende Zeile in der Datei nicht vorhanden ist, fügen Sie diese bitte ein: `transport_maps = hash:/etc/postfix/transport`
11. Stellen Sie sicher, dass keine Domain, die Sie mit b1gMail verwenden möchten, in der `my_destination`-Zeile steht, die Sie in der Datei finden können.
12. Speichern Sie die Datei und schließen Sie den Editor.
13. Aktivieren Sie die neue Konfiguration durch Eingabe von `postmap /etc/postfix/transport` und `service postfix restart`


**Hinweis**: Bearbeiten Sie Konfigurations-Dateien Ihres Linux-/Unix-Servers nie mit einem Windows-PC oder einem Mac mit anderen Zeilenumbrüchen als „\n“. Dies könnte sonst dazu führen, dass die Konfigurationsdateien nicht mehr korrekt erkannt werden.