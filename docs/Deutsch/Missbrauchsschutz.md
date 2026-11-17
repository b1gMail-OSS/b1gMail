# Missbrauchs-Schutz

Der Missbrauchs-Schutz vergibt Punkte, wenn ein Benutzer sich auffällig verhält, etwa mehr Mails verschicken will, als seine Gruppe erlaubt. Erreicht die Summe der Punkte eines Accounts das _Sperr-Limit_, kann b1gMail reagieren: nichts tun, den Account sperren oder ihn in eine eigene Gruppe verschieben. Diese Seite beschreibt, wie die Punkte entstehen, wie Sie die Reaktion einrichten und wie Sie einen Account wieder freigeben.

## Ablauf

1. Ein Verstoß, zum Beispiel ein abgelehnter Versand im Webmail, bringt dem Benutzer Punkte (Abschnitt "Wie Punkte entstehen").
2. Punkte verfallen nach der eingestellten Zeit. Es zählen nur Punkte, die noch nicht verfallen sind.
3. Bei **jedem Lauf von `cron.php`** prüft b1gMail, ob die Punkte eines Accounts das _Sperr-Limit_ erreichen, und führt die eingestellte Reaktion aus. Ohne Cronjob passiert nichts, auch wenn Punkte vergeben werden. Das Verfallen der Punkte prüft b1gMail einmal pro Stunde.
4. Optional bekommt der Admin eine E-Mail. Die betroffenen Accounts stehen im Admin-Bereich unter _Benutzer & Gruppen_ > _Missbrauchs-Schutz_.

## Wie Punkte entstehen

Punkte gibt es nur für Benutzer in Gruppen, bei denen die Option _Missbrauchs-Schutz_ eingeschaltet ist (_Benutzer & Gruppen_ > _Gruppen_, Gruppe bearbeiten). Prüfen Sie die Option bei jeder Gruppe, die geschützt sein soll. Die Punkte je Verstoß-Art stellen Sie unter _Einstellungen_ > _Missbrauchs-Schutz_ ein.

| Verstoß-Art | Auslöser | Standard |
|---|---|---|
| Versuch, das Empfänger-Anzahl-Limit zu überschreiten | Webmail: mehr Empfänger in einer Mail als _Maximale Empfänger-Anzahl_ der Gruppe | 5 |
| Versuch, das Versand-Frequenz-Limit zu überschreiten | Webmail: mehr Empfänger als das _Versand-Limit_ der Gruppe erlaubt, zum Beispiel 10 Empfänger in 1440 Minuten | 25 |
| Versuch, an für den Versand gesperrte Empfänger zu senden | Webmail: Empfänger steht in der Liste _Geblockte Empfänger_ | 15 |
| Zu geringer Abstand zwischen Formular-Aufruf und Mail-Versand | Webmail: Das Formular wurde schneller abgeschickt als der eingestellte Abstand (Standard 5 Sekunden), typisch für Skripte | 20 |
| Hohe E-Mail-Empfangs-Frequenz | **Empfang**: mehr als _Max. Anzahl_ Mails (Standard 50) in _Zeitraum_ Minuten (Standard 5). Punkte für **jede** weitere Mail, an den Empfänger (siehe Stolpersteine) | 5 |
| Hoher eingehender E-Mail-Traffic | **Empfang**: mehr als _Max. Traffic_ in MB (Standard 100) in _Zeitraum_ Minuten (Standard 5). Punkte für **jede** weitere Mail, an den Empfänger (siehe Stolpersteine) | 5 |
| Versand an existente Domain, aber nicht existenten Empfänger; Versand an nicht existente Domain; SMTP-Versand ohne vorherigen POP3-/IMAP-Login | Diese drei Arten löst der Kern von b1gMail selbst nicht aus. Ob sie in Ihrer Installation Punkte bringen, hängt vom Mail-Server oder von einem Plugin ab. | 10 / 10 / 20 |

Stolpersteine:

* **Versand-Limit und Webmail:** Das _Versand-Limit_ und die Versand-Arten oben gelten für das Verfassen-Formular im Webmail. Mails, die ein Mail-Programm über SMTP einliefert, zählt der Kern von b1gMail dort nicht mit. Gegen missbrauchten SMTP-Zugang hilft die Gruppenoption _SMTP_ (siehe Zielgruppe unten).
* **Empfang bringt dem Empfänger Punkte, und zwar für jede einzelne Mail:** Hat ein Postfach im eingestellten Zeitraum mehr Mails (oder mehr Daten) empfangen als erlaubt, bringt **jede weitere** eingehende Mail die Punkte der Art, nicht nur die erste. Bei 5 Punkten und 500 Mails innerhalb von fünf Minuten sind das 450 Mails über der Schwelle und damit 2250 Punkte, ein Vielfaches jedes üblichen _Sperr-Limits_. Wer Opfer einer Mail-Flut wird, wird so gesperrt oder verschoben. Der Missbrauchs-Schutz kann bei diesen beiden Arten Opfer und Täter nicht unterscheiden, und auch ein Postfach mit vielen berechtigten Mails (Mailingliste, Newsletter) erreicht die Schwelle.
  * **Abhilfe:** Stellen Sie die Punkte beider Empfangs-Arten (_Hohe E-Mail-Empfangs-Frequenz_ und _Hoher eingehender E-Mail-Traffic_) auf **0** und speichern Sie die Seite. Eine Mail-Flut bringt dann keine Punkte mehr. b1gMail schreibt pro Mail weiterhin einen Eintrag mit 0 Punkten.
  * Die Schwellen (_Max. Anzahl_, _Max. Traffic_) hochzusetzen genügt nicht: Wer sie überschreitet, bekommt wieder für jede Mail Punkte.
  * Eine Option nur für diese beiden Arten gibt es nicht. Die Gruppenoption _Missbrauchs-Schutz_ schaltet alle Arten zugleich ab.
* **Punkte müssen gespeichert sein:** Für eine Verstoß-Art ohne gespeicherten Wert vergibt b1gMail 0 Punkte. Bei einer Installation ohne Beispieldaten ist das erst anders, nachdem Sie _Einstellungen_ > _Missbrauchs-Schutz_ einmal mit _Speichern_ gesichert haben. Die Seite zeigt vorher die Standardwerte an, das ist nicht dasselbe wie gespeichert.
* **Das _Sperr-Limit_ muss über den Punkten einer einzelnen Art liegen**, sonst reicht ein einziger Verstoß. Bei 25 Punkten je Versand-Verstoß passt zum Beispiel 100 (vier Verstöße). Die Einstellungsseite warnt, wenn das Limit zu niedrig ist.

## Einrichtung in sechs Schritten

1. **Zielgruppe anlegen** (_Benutzer & Gruppen_ > _Gruppen_), zum Beispiel `Spammer`. Eigene Gruppe, nicht die Standardgruppe der Registrierung und keine Gruppe, in die Kunden sonst wechseln können. Empfohlene Einstellungen:
   * _SMTP_ ausschalten: der Mail-Server nimmt dann keine Mails dieses Accounts über SMTP mehr an.
   * _Versand-Limit_ auf eine E-Mail pro 1440 Minuten stellen: das begrenzt den Versand im Webmail. _SMTP_ allein hält ihn nicht auf.
   * Anmeldung, Empfang und Speicherplatz unverändert lassen, damit der Benutzer die Warnung sieht und sein Postfach nicht verliert.
2. **Punkte prüfen und speichern** (_Einstellungen_ > _Missbrauchs-Schutz_, Abschnitt _Verstoß-Arten_): Werte kontrollieren, die beiden Empfangs-Arten auf **0** stellen (siehe Stolpersteine, sonst werden Opfer einer Mail-Flut verschoben), **Seite einmal speichern**.
3. **Limits setzen:** _Warn-Limit_ (zum Beispiel 50, färbt die Anzeige in der Übersicht gelb und erscheint auf der Startseite des Admin-Bereichs) und _Sperr-Limit_ (zum Beispiel 100). _Punkte laufen ab nach_: 24 bis 72 Stunden. Bei 0 Stunden laufen die Punkte beim nächsten stündlichen Lauf ab. Das _Ablauf-Konzept_: _Alle Punkte laufen ab, sobald der Benutzer für o.g. Zeitraum keine neuen Punkte bekommen hat_ (ein dauerhaft auffälliger Account behält seine Punkte) oder _Punkte laufen individuell ab, sobald sie das o.g. Alter erreicht haben_.
4. **Reaktion wählen:** _Bei Erreichen des Sperr-Limits_ > _Account in eine Gruppe verschieben_ und die _Zielgruppe_ aus Schritt 1 auswählen. Ohne Zielgruppe fällt die Auswahl beim Speichern still auf _Nichts tun_ zurück.
5. **Benachrichtigung einschalten** (_Benachrichtigung_ > _Admin / Support Team per E-Mail informieren_, mit _E-Mail-Adresse_). Sonst erfahren Sie von einer Verschiebung nur durch einen Blick in die Übersicht.
6. **Cronjob prüfen:** `cron.php` muss jede Minute laufen (siehe die Seite zum Cronjob). Der _Letzte Cron-Lauf_ unter _Einstellungen_ > _Allgemein_ ist grün.

Die Funktion ist standardmäßig aus.

## Einstellungen

Unter _Einstellungen_ > _Missbrauchs-Schutz_:

| Einstellung | Bedeutung |
|---|---|
| _Warn-Limit_ | Ab dieser Punktzahl ist der Account in der Übersicht gelb, und die Startseite des Admin-Bereichs nennt die Zahl der Accounts darüber. Es löst keine Reaktion aus. |
| _Sperr-Limit_ | Ab dieser Punktzahl wird die Reaktion ausgelöst. |
| _Punkte laufen ab nach_ | Lebensdauer der Punkte in Stunden. |
| _Ablauf-Konzept_ | Siehe Schritt 3. |
| _Bei Erreichen des Sperr-Limits_ | Genau eine von drei Möglichkeiten: _Nichts tun_ (die Punkte werden nur gesammelt), _Account sperren_ (der Benutzer kann sich nicht mehr anmelden) oder _Account in eine Gruppe verschieben_. |
| _Zielgruppe_ | Die Gruppe, in die missbrauchte Accounts verschoben werden. Sie gilt für die Automatik und für den Button auf der Detailseite des Benutzers. Das Dropdown ist nur aktiv, wenn _Account in eine Gruppe verschieben_ gewählt ist. Die gespeicherte Gruppe bleibt erhalten, auch wenn Sie danach etwas anderes wählen. |
| _Ausgehende E-Mails aus der Warteschlange löschen_ | Nur sichtbar, wenn der b1gMailServer installiert und aktiv ist, und nur wählbar, wenn _Account in eine Gruppe verschieben_ gewählt ist. Beim Verschieben (Automatik, Button und Massenaktion in der Übersicht) werden die wartenden **ausgehenden** Mails des Benutzers aus der Warteschlange des Mail-Servers gelöscht, damit der Spam nach dem Verschieben nicht weiter zugestellt wird. Eingehende Mails bleiben erhalten. Bereits zugestellte Mails lassen sich nicht zurückholen. Das Löschen lässt sich nicht rückgängig machen. Standardmäßig aus. |
| _Benachrichtigung_ | Eine Mail an den Admin oder das Support Team, wenn ein Account automatisch gesperrt oder verschoben wurde (_Admin / Support Team per E-Mail informieren_, dazu die _E-Mail-Adresse_). Sie enthält die Punkte und deren Arten, einen Link auf die Detailseite des Benutzers und bei einer Verschiebung einen Abschnitt zur Warteschlange: wie viele ausgehende Mails gelöscht wurden (oder dass sie nicht geleert wurde) und zu jeder gelöschten Mail den Empfänger, den Betreff und den letzten bekannten Status des Mail-Servers (bis zu 50 Stück). |

Die Texte der Benachrichtigungen ändern Sie unter _Einstellungen_ > _Sprachen_, Reiter _Anpassbare Texte_: _Missbrauchs-Verschiebungs-Benachrichtigung-Betreff_ und _-Text_ (und für das Sperren _Missbrauchs-Sperrungs-Benachrichtigung-Betreff_ und _-Text_). Im Text der Verschiebung steht der Platzhalter `%%queue%%` für den Abschnitt zur Warteschlange. Haben Sie die Texte schon angepasst, fügen Sie ihn selbst ein.

## Warum verschieben statt sperren?

Ein gesperrter Benutzer kann sich nicht mehr anmelden und erfährt nie, was los ist. Bei missbrauchten Accounts, deren Passwort gestohlen wurde, ist das ungünstig: Der Kunde bemerkt nur, dass nichts mehr geht. Verschiebt man den Account in eine Gruppe, die nichts mehr versenden darf, kann der Benutzer sich weiter anmelden. Er sieht dann auf jeder Seite eine Warnung, die sich nicht schließen lässt: sein Account wird missbraucht, er soll sofort sein Passwort ändern.

**Wichtig:** Das Verschieben macht das gestohlene Passwort nicht ungültig, und offene Sitzungen und angemeldete Mail-Programme laufen weiter, soweit die Gruppe sie erlaubt. Der Missbrauch endet erst, wenn das Passwort geändert ist. Fordern Sie den Benutzer dazu auf, oder setzen Sie auf der Bearbeiten-Seite des Benutzers (_Neues Passwort_) selbst ein neues.

## Mit einem Klick verschieben

Ist eine Zielgruppe eingestellt, gibt es auf der Detailseite jedes Benutzers im Missbrauchs-Schutz (_Benutzer & Gruppen_ > _Missbrauchs-Schutz_, Klick auf den Benutzer) neben _Sperren_ einen Button _In Gruppe "Spammer" verschieben_. Das gilt unabhängig davon, ob das automatische Verschieben an ist. Ist keine Zielgruppe eingestellt oder gibt es die eingestellte Gruppe nicht mehr, steht dort stattdessen ein Hinweis mit einem Link auf die Einstellungen. Der Button fehlt außerdem, wenn der Benutzer schon in der Zielgruppe ist.

In der Übersicht listet die Massenaktion _Verschieben_ alle Gruppen auf. Wählen Sie dort die Zielgruppe, wird der Account genauso verschoben wie mit dem Button, mit Notiz, Log-Eintrag und gegebenenfalls Warteschlange.

Verschieben Sie einen Account auf einem anderen Weg in die Zielgruppe (Massenaktion in der normalen Benutzerliste, Bearbeiten-Seite des Benutzers), wird nur die Gruppe geändert, ohne Notiz, Log-Eintrag und ohne die Warteschlange zu leeren. Benutzen Sie darum die Übersicht des Missbrauchs-Schutzes.

## Was beim Verschieben passiert

* Die Gruppe des Benutzers wird geändert. Er bleibt entsperrt.
* An die _Notizen_ des Benutzers wird eine Zeile mit Datum, Uhrzeit, Admin (oder dem Hinweis, dass der Missbrauchs-Schutz es selbst getan hat), der Zielgruppe, den Punkten, dem Sperr-Limit und der **bisherigen Gruppe** angehängt. Damit lässt sich der Account später zurückstellen.
* Es gibt einen Eintrag im System-Log.
* Ist die Option zur Warteschlange an, werden die wartenden ausgehenden Mails des Benutzers gelöscht und die Anzahl im System-Log vermerkt. Erreicht b1gMail den Mail-Server nicht, wird nur die Tabelle der Warteschlange angepasst, der Löschbefehl erreicht den Mail-Server dann nicht. Prüfen Sie in dem Fall die Warteschlange im Mail-Server selbst.
* Die Punkte bleiben erhalten. Sie dokumentieren den Vorfall und verfallen nach _Ablauf-Konzept_ und _Punkte laufen ab nach_.
* Die Automatik fasst Accounts nicht an, die schon in der Zielgruppe oder die gesperrt sind, und auch keine in Gruppen, bei denen _Missbrauchs-Schutz_ ausgeschaltet ist.

## Account wieder freigeben

1. Lesen Sie die _Notizen_ des Benutzers (Bearbeiten-Seite). Dort steht die **bisherige Gruppe**.
2. Verschieben Sie den Account zurück: in der Übersicht des Missbrauchs-Schutzes den Benutzer anhaken und die Massenaktion _Verschieben_ > _in "bisherige Gruppe"_ ausführen, oder auf der Bearbeiten-Seite des Benutzers die Gruppe ändern, oder in der Benutzerliste die Massenaktion _Verschieben_ benutzen.
3. Die Punkte des Benutzers laufen dabei ab, wie bei _Entsperren_, und an die _Notizen_ wird eine Zeile mit Datum, Uhrzeit, Admin und den Punkten angehängt. Sonst würde die Automatik den Benutzer beim nächsten Lauf wieder verschieben, weil er das _Sperr-Limit_ noch erreicht. Das gilt nur für Benutzer, die in der Zielgruppe waren.
4. Prüfen Sie, dass der Benutzer sein Passwort geändert hat (siehe oben). Sonst wird er bald wieder auffällig.

_Entsperren_ (Übersicht oder Detailseite) setzt die Punkte ebenfalls zurück und hebt eine Sperre auf, ändert aber die Gruppe nicht.

## Warnung für den Benutzer

Benutzer in der Zielgruppe sehen oben auf den Seiten des Benutzerbereichs einen roten Hinweis mit einem Button _Passwort ändern_. Der Hinweis lässt sich nicht schließen. Den Text ändern Sie unter _Einstellungen_ > _Sprachen_, Reiter _Anpassbare Texte_ (_Missbrauchs-Warnung für Benutzer in der Zielgruppe_). Die erste Zeile ist die Überschrift, der Rest der Text darunter. Ist der Text leer, zeigt b1gMail den Standardtext der Sprache des Benutzers, die Warnung verschwindet nicht.

Die Warnung ist in den Templates `modern` und `tabler` enthalten (`li/index.tpl`). Das Aussehen im Template `modern` steht in `style/loggedin.css` (Klasse `.bm-abuse-warning`). Ein eigenes Template braucht denselben Block aus der `index.tpl` dieser Templates, sonst sieht der Benutzer keine Warnung.

## Update

Die Funktion braucht drei neue Spalten in der Tabelle der Einstellungen (`ap_automove`, `ap_automove_group`, `ap_clearqueue`). Ein Update von b1gMail legt sie selbst an. Haben Sie die Dateien nur ersetzt, ohne das Update auszuführen: Sichern Sie die Datenbank, öffnen Sie _Tools_ > _Optimierung_, wählen Sie _Struktur prüfen_ und klicken Sie auf _Ausführen_, danach auf _Struktur reparieren_. Speichern Sie die neuen Einstellungen erst danach.
