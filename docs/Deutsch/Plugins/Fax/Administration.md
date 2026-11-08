# Administration

## Aufruf

Sie erreichen den Konfigurationsbereich des Fax-Plugins über den Administrationsbereich von b1gMail. Nachdem Sie sich in diesen eingeloggt haben, klicken Sie einfach links im Menü auf „Plugins“ und auf „Fax“.

## Übersicht

Die Übersichtsseite zeigt Ihnen generelle Informationen zum Plugin und allgemeine Statistiken an.

## Vorwahlen

In der Ansicht „Vorwahlen“ können Sie Vorwahlen definieren, zu denen Sie den Fax-Versand erlauben oder verbieten möchten. Außerdem können Sie pro Vorwahl einen individuellen Preis angeben und ein individuelles Gateway auswählen.

Wenn Sie Zugänge bei mehreren Gateways besitzen, können Sie so für die jeweilige Vorwahl immer das Gateway auswählen, das beim Versand an diese Vorwahl am günstigsten oder zuverlässigsten ist.

Zu einem Vorwahl-Eintrag kann auch eine Landes-Vorwahl angegeben werden.

Durch dieses Konzept können z.B. Faxe an Sonder-Rufnummern oder ins Ausland individuell berechnet werden.

Zu jedem Vorwahl-Eintrag sehen Sie die folgenden Informationen:

* Vorwahl – Landes-Vorwahl und Vorwahl. Das Zeichen „\*“ kann als Platzhalter („Wildcard“) für alle Vorwahlen verwendet werden, zu denen kein eigener passender Eintrag vorhanden ist.
* Gateway – Das für diese Vorwahl zu verwendende Gateway.
* Preis (erste Seite) – Der Preis (in Credits) für die erste Fax-Seite an diese Vorwahl.
* Preis (weitere Seiten) – Der Preis (in Credits) für alle weiteren Fax-Seiten an diese Vorwahl.

### Vorwahl hinzufügen

Unter der Vorwahl-Liste finden Sie ein Formular, mit dem Sie neue Vorwahl-Einträge anlegen können.

Sie können hier folgende Angaben machen:
* Landes-Vorwahl – Die Landes-Vorwahl. Verwenden Sie „\*“, wenn der Eintrag für alle Landes-Vorwahlen gelten soll, zu denen kein anderer passender Eintrag vorhanden ist.
* Vorwahl – Die Vorwahl für diesen Eintrag. Verwenden Sie „\*“, wenn der Eintrag für alle Vorwahlen gelten soll, zu denen kein anderer passender Eintrag vorhanden ist.
* Gateway – Das Gateway, das zum Versand an diese Vorwahl verwendet werden soll. Bei Auswahl von „(Standard-Gateway)“ wird immer das in den Einstellungen definierte Standard-Gateway verwendet, bei Auswahl von „(Nummern-Bereich verbieten)“ wird der Versand an diese Vorwahl für alle Benutzter verboten.
* Preis (erste Seite) – Der Preis (in Credits) für die erste Fax-Seite an diese Vorwahl.
* Preis (weitere Seiten) – Der Preis (in Credits) für alle weiteren Fax-Seiten an diese Vorwahl. 

## Gateways

Die Ansicht „Gateways“ erlaubt die Verwaltung der Fax-Gateways, die zum Senden von Faxen verwendet werden.

Die Ansicht ist in zwei Varianten verfügbar: Im vereinfachten und im erweiterten Modus. Standardmäßig wird zunächst der vereinfachte Modus angezeigt

### Vereinfachter Modus

Im vereinfachten Modus können Sie die Zugangsdaten zu jedem Gateway-Eintrag bearbeiten, sonst aber keine Einstellungen vornehmen. Dieser Modus ist ausreichend, wenn Sie eines der Gateways nutzen möchten, die vom Plugin von Haus aus unterstützt werden.

Sie können für jedes Gateway individuelle Zugangsdaten angeben. Achten Sie bei Angabe von Benutzername und Passwort bitte auf korrekte Schreibweise und korrekte Groß-/Kleinschreibung.

Mit einem Klick auf „Speichern“ werden die Zugangsdaten für alle Gateways gespeichert.

### Erweiterter Modus

Erweiterter Modus

Im erweiterten Modus können Sie neue Gateways hinzufügen oder die Art und Weise, wie b1gMail ein Gateway anspricht, anpassen. Bei der Nutzung des erweiterten Modus ist die Dokumentation des jeweiligen Gateway-Anbieters eine nützliche Hilfe.

Sie erreichen den erweiterten Modus, indem Sie die Register-Karte „Gateways“ aufrufen und dann links unten auf „Erweiterter Modus“ klicken.

Oben auf der Seite sehen Sie dann eine Liste aller eingerichteten Gateways.

Zu jedem Gateway sehen Sie dessen Titel und den Einlieferungsweg zum Gateway.

## Signaturen

In der Ansicht „Signaturen“ können Sie (Werbe-)Texte verwalten, die in ausgehende Faxe eingefügt werden.

Zu jeder Signatur sehen Sie den Text, die Gewichtung und die Anzahl, wie oft die Signatur verwendet wurde.

Per Klick auf das Bearbeiten-Symbol können Sie den Eintrag bearbeiten; per Klick auf das rote X können Sie den Eintrag löschen. Das Grüne Häkchen-Symbol  zeigt eine aktive Signatur an, das Symbol  steht für eine pausierte Signatur. Per Klick auf das AusrufezeichenSymbol können Sie den Status jeweils ändern.

### Signatur hinzufügen

Unter der Signatur-Liste finden Sie ein Formular, mit dem Sie neue Signaturen hinzufügen können.

Sie können folgende Angaben machen:

* Signatur-Text – Enthält den (Werbe-)Text der Signatur.
* Schriftart – Die zu verwendende Schriftart.
* Schriftgröße – Die zu verwendende Schritftgröße. Auf dem Bildschirm dargestellt mit der Einheit Pixel, auf dem Papier in Punkt.
* Ausrichtung – Die Text-Ausrichtung.
* Fett / Kursiv / Unterstrichen – Schrift-Stil.
* Trenn-Linie – Hier können Linien aktiviert werden, die über/unter dem Text erscheinen.
* Anzeigen auf – Gibt an, auf welchen Seiten die Signatur eingeblendet werden soll.
* Platzierung – Gibt an, ob die Signatur oben oder unten auf der Seite eingeblendet werden soll.
* Reservierte Höhe – Gibt ab, wie viel Platz in der Höhe für die Signatur reserviert werden soll. Vor allem relevant bei Platzierung unten auf der Seite.
* Gewichtung – Gibt die Gewichtung der Signatur an. Wenn für die Gruppe eines Benutzers mehrere Signaturen vorhanden sind, werden die Signaturen entsprechend der angegebenen Gewichtung durch gewechselt.
* Gruppen – Gibt an, bei welchen Benutzer-Gruppen die Signatur zur Verwendung kommen soll.
* Pausiert – Gibt an, ob die Signatur pausiert ist und daher (vorerst) nicht verwendet werden soll.

## Statistiken

Auf der Register-Karte „Statistiken“ finden Sie Statistiken des Fax-Plugins.

Folgende statistischen Auswertungen stehen zur Verfügung:

* Faxe – Zeigt eine Seite mit Graphen/Tabellen zur Anzahl gesendeter Faxe, erfolgreicher und fehlerhafter Übermittlungen.
* Credits – Zeigt eine Seite mit Graphen/Tabellen zur Anzahl abgerechneter und zurückerstatteter Credits.