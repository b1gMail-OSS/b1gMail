# Häufig gestellte Fragen

Diese Seite enthält eine Liste häufig gestellter Fragen mit zugehörigen Antworten.

## Allgemein

### Welche Voraussetzungen muss mein System für b1gMail erfüllen?
Um b1gMail installieren und verwenden zu können, benötigen Sie Webspace, der mindestens die folgenden Anforderungen erfüllen muss:

1. PHP ab Version 8.1.0  
2. Mind. 20 MB freier Speicherplatz  
3. MySQL-Datenbank mit mind. 30 MB Speicher  
4. Eine der unterstützten Empfangsmethode, die unter [Empfangsmethode](Empfangsmethode.md) beschrieben ist

Sie können ein Server-Test-Skript verwenden, um Ihren Webspace automatisch auf Kompatibilität mit b1gMail prüfen zu lassen. Bei dedizierten Servern empfehlen wir Linux/Unix als Betriebssystem und Apache als Webserver-Software.

### Benötige ich zum Betrieb von b1gMail einen eigenen Root-Server?
Nein. b1gMail ist im Gegensatz zu vielen anderen Webmail-Lösungen auch ohne eigenen Root-Server verwendbar.

Ein eigener Root-Server wird nur benötigt, wenn Sie Ihren Benutzern neben dem Zugriff per Web-Interface auch Zugriff per POP3/IMAP/SMTP anbieten möchten.

### Wie viele Benutzer / E-Mails kann ich mit b1gMail verwalten?
Die maximale Anzahl an E-Mails und Benutzern ist durch b1gMail nicht beschränkt und hängt ausschließlich von der Leistung Ihres Servers ab. Bedenken Sie, dass Sie für jeden Benutzer den zur Verfügung gestellten Speicherplatz auf dem Server bereitstellen sollten. In der Regel nutzt ein Benutzer jedoch nicht 100% seines Speichers aus.

## E-Mail-Versand/-Empfang

### Wo speichert b1gMail empfangene E-Mails?
b1gMail speichert die E-Mails Ihrer Benutzer in Dateien auf Ihrem Webspace und verwaltet gleichzeitig einen Index zum Schnellzugriff auf wichtige E-Mail-Informationen in der MySQL-Datenbank. Dadurch werden die Vorteile von Datenbank und Dateisystem kombiniert und auch große Mengen an E-Mails können effizient verwaltet werden.

### Alle eingehenden E-Mails werden als Spam markiert bzw. landen im Spam-Ordner
Auf einigen Systemen, auf denen in der DNS-Konfiguration eine Suchdomain mit Wildcard-Eintrag gesetzt ist, kann es bei aktiviertem DNSBL-Filter dazu kommen, dass alle E-Mails fälschlicherweise als Spam bewertet werden. Zur Lösung:

1. Loggen Sie sich in den Adminbereich Ihrer b1gMail-Installation ein.  
2. Rufen Sie die Seite "Einstellungen" → "E-Mail" → "Anti-Spam" auf.  
3. Ändern Sie die Liste der DNSBL-Server so, dass am Ende jeder Zeile bzw. jedes Servernamens ein "." steht (ohne vorangestelltes Leerzeichen). Beispiel:
```text
dnsbl.domain1.xy.
dnsbl.domain2.xy.
```

Tritt das Problem weiterhin auf, prüfen Sie andere Ursachen wie einen falsch oder ungünstig trainierten statistischen Filter (zurücksetzbar im Adminbereich bei globalem Trainingsmodus oder im User-Account bei lokalem Trainingsmodus) oder eine ungültige Empfangsregel.

### Wie empfängt b1gMail E-Mails?
Je nach Rahmenbedingungen gibt es verschiedene Empfangsmethoden.

#### Mit eigenem, dedizierten Root-Server
Mit eigenem Root-Server können Sie den b1gMail-eigenen E-Mail-Server b1gMailServer verwenden; dieser akzeptiert E-Mails auf Port 25 und stellt sie in die b1gMail-Postfächer zu. Bei Verwendung von Postfix können Sie alternativ eine Schnittstelle zwischen Postfix und b1gMail einrichten (per Transportmap).

#### Mit gewöhnlichem Webspace ohne eigenen Server
Auf gewöhnlichem (geteiltem) Webspace wird die CatchAll-Funktion des Providers verwendet: Alle Domain-E-Mails werden in ein gemeinsames Postfach zugestellt. b1gMail ruft dieses Postfach regelmäßig ab, liest die E-Mails aus und sortiert sie anhand der Empfängerinformationen in die jeweiligen b1gMail-Postfächer.

#### Weitere Methoden
Für cPanel gibt es eine optimierte Empfangsmethode, nutzbar wenn Ihr Webspace über cPanel verwaltet wird (Shared-Webspace oder eigener cPanel-Server).

## Sonstiges

### Wie ziehe ich b1gMail auf einen neuen Server um?
Siehe das Dokument [Server-Umzug](Installation/Server-Umzug.md).

### Benötige ich zum Betrieb von b1gMail das b1gMailServer-AddOn?
Nein. b1gMailServer wird nur benötigt, wenn Sie Zugriff per POP3, SMTP und/oder IMAP zusätzlich zum Web-Interface anbieten möchten.
