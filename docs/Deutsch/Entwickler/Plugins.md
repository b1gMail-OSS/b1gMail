# Plugins

b1gMail bietet eine umfangreiche Modul-Schnittstelle, die Eingriff in viele Prozesse und Abläufe von b1gMail gestattet und dabei sicherstellt, dass Ihr Plugin auch mit kommenden b1gMail-Versionen bestmöglich kompatibel bleibt. Plugin-Code wird dabei in ein eigenes Plugin-Paket ausgelagert, sodass keine Code-Modifikationen seitens des Benutzers zur Installation des Plugins nötig sind.

Dieses SDK enthält neben dieser Einleitung und Referenz auch Plugin-Beispiele und den b1gMail Plugin Builder (BMPluginBuilder), der die Erstellung von Plugin-Paketen erlaubt, die der Benutzer einfach aus dem Admin-Bereich heraus installieren kann, ohne sein FTP-Programm nutzen zu müssen.

## Einführung

b1gMail-Plugins bestehen generell aus mindestens einer PHP-Datei, die den Plugin-Code enthält, sowie optional aus Template-Dateien, Grafiken und CSS-/JS-Dateien, die zur Benutzer-/Administrator-Interaktion dienen.

## Verzeichnis-Struktur

Jede b1gMail-Installation enthält folgende Verzeichnisse, die für die Plugin-Entwicklung relevant sind:

* plugins/ - Enthält die PHP-Dateien der Plugins. Bei jedem Aufruf von b1gMail werden alle Plugins aus diesem im b1gMail-Kontext geladen („include“).
* plugins/templates/ - Enthält alle mit Plugins ausgelieferten Templates. Das Plugin selbst kann durch von b1gMail zur Verfügung gestellte Funktionen auf diese Templates zugreifen.
* plugins/templates/images/ - Enthält alle mit Plugins ausgelieferten Grafik-Ressourcen (i.d.R. PNG-, GIF- und/oder JPG-Dateien).

Ab b1gMail 7.2 stehen die folgenden zusätzlichen Verzeichnisse bereit:

* plugins/css/ - Enthält alle mit Plugins ausgelieferten Stylesheets (CSS-Dateien).
* plugins/js/ - Enthält alle mit Plugins ausgelieferten JavaScript-Dateien (JS).

### Regeln zur Datei-Benennung

Um Konflikte zu vermeiden, die durch gleiche Dateinamen zwischen verschiedenen Modulen entstehen, beachten Sie bitte folgende einfache Benennungs-Regeln:

* Legen Sie eine interne, eindeutige Abkürzung für Ihr Plugin fest (das PremiumAccount-Modul verwendet z.B. „pacc“ als interne Abkürzung)
* Beginnen Sie den Dateinamen jedes Templates Ihres Plugins mit seiner internen Abkürzung, gefolgt von einem Punkt („.“) – verwenden Sie also z.B. „pacc.prefs.tpl“ statt einfach „prefs.tpl“
* Beginnen Sie den Dateinamen jeder Bild-, CSS- und JS-Datei Ihres Plugins mit seiner internen Abkürzung, gefolgt von einem Unterstrich („_“) – verwenden Sie also z.B. „pacc_packages.png“ statt einfach „packages.png“

## Die Plugin-PHP-Datei

Eine Plugin-PHP-Datei kann einen beliebigen Aufbau und beliebige Bestandteile haben, muss aber mindestens eine Plugin-Klasse enthalten, die die durch b1gMail zur Verfügung gestellte Plugin-Basisklasse BMPlugin erweitert. Am Ende der Datei müssen alle Plugin-Klassen, die b1gMail nutzen soll, mit dem Plugin-System registriert werden. Je nachdem, ob das Plugin durch den Administrator aktiviert oder deaktiviert ist, entscheidet b1gMail bei Registrierung der Plugin-Klasse, ob das Plugin verwendet wird oder nicht.

## Plugin-Klassen

Jede Plugin-Klasse repräsentiert ein eigenes Plugin. Somit kann eine einzige Plugin-PHP-Datei mehrere Plugins enthalten, die auch als mehrere einzelne Plugins im Admin-Bereich von b1gMail angezeigt werden.

Plugin-Klassen sind gewöhnliche PHP-Klassen, die jedoch die Plugin-Basisklasse BMPlugin, die durch b1gMail zur Verfügung gestellt wird, erweitert.

Im Konstruktor einer Plugin-Klasse muss die Plugin-Klasse Meta-Informationen wie Name, Version und Hersteller setzen. Diese Informationen werden z.B. in der Plugin-Verwaltung im Admin-Bereich angezeigt.

Neben dem Konstruktor kann die Plugin-Klasse so genannte Ereignis-Handler enthalten. Ein Ereignis-Handler ist eine Funktion in der Plugin-Klasse, die durch b1gMail aufgerufen wird, sobald ein bestimmtes Ereignis eintritt. In diesem Ereignis-Handler kann das Plugin dann eigenen Code ausführen und so in Prozesse und Abläufe von b1gMail eingreifen und oft auch deren Ausgang beeinflussen. Eine Liste aller Ereignis-Handler, die b1gMail kennt, finden Sie im Kapitel Referenz.

Die Plugin-Basisklasse stellt weiterhin einige Hilf-Funktionen bereit, die Sie aus Ihrem Plugin heraus aufrufen können. Eine Dokumentation zu diesen Funktionen finden Sie ebenfalls im Kapitel Referenz.

## Plugin-Klassen-Registrierung

Die Registrierung der Plugin-Klassen findet meist am Ende der Plugin-PHP-Datei statt, vor dem abschließenden PHP-End-Tag „?>“.

Zur Registrierung wird die Funktion „registerPlugin“ des Plugin-Managers  ($plugins im globalen Variablenraum) aufgerufen, wobei als Parameter der Name der zu registrierenden Plugin-Klasse übergeben wird. Die Instantiierung der Klasse erfolgt durch den Plugin-Manager.

Der folgende Beispiel-Code registriert die Plugin-Klasse „TestPlugin“:

`$plugins->registerPlugin('TestPlugin');`

## Hinweise und Tipps zur Entwicklung

* Aktivieren Sie während der Plugin-Entwicklung den Debug-Modus von b1gMail, um erweiterte Hinweise bei Fehlern und alle PHP-Fehlermeldungen (auch Notices) zu erhalten. Informationen zur Aktivierung des Debug-Modus finden Sie im Anhang.
* Lagern Sie möglichst allen Plugin-PHP-Code in die Plugin-PHP-Datei aus und jeglichen HTML- und JavaScript-Code in die Plugin-Templates.
* Versuchen Sie, Ihre Ziele ohne Modifikation von b1gMail-Code zu erreichen. In vielen Fällen ist dies sehr einfach möglich, in anderen Fällen ist ein tieferer Griff in die Trick-Kiste nötig ;-)
* Achten Sie darauf, am Ende Ihrer Plugin-Datei nach dem PHP-End-Tag „?>“ keine Leerzeichen oder –zeilen einzufügen, um keine unerwünschte Ausgabe zu erzeugen. Sie können den PHP-End-Tag „?>“ auch weglassen, um das Problem zu vermeiden.
* Speichern Sie Ihr Plugin, sofern möglich, im ISO-889-15-Zeichensatz und lassen Sie Ihre Sprachvariablen dann mit dem Code aus dem vorherigen Kapitel in das von b1gMail verwendete Charset konvertieren, um Kompatibilität mit älteren b1gMail-Versionen zu erhalten.
* Verteilen Sie Ihr Plugin als b1gMail-Plugin-Paket, um eine einfache und vertraute Installation zu ermöglichen. So reduzieren Sie auch Ihren Support-Aufwand.

## Plugin-Verteilung

Die Verteilung von Plugins kann auf zwei verschiedene Versionen erfolgen:

* Durch Weitergabe aller Plugin-Dateien, z.B. in einem ZIP-Archiv. Der Benutzer benötigt zur Installation dann jedoch Zugriff auf seinen Webspace (z.B. per FTP) und die Deinstallation gestaltet sich recht kompliziert.
* Durch Erstellung eines b1gMail-Plugin-Pakets (.bmplugin-Datei). Ein solches Paket enthält alle Dateien Ihres Plugins und kann mit einem Klick im b1gMail-Adminbereich installiert werden, ohne dass dazu Zugriff auf den Webspace nötig ist. Das Plugin-Paket kann genau so einfach wieder deinstalliert werden.

Zur Erstellung von b1gMail-Plugin-Paketen lesen Sie bitte das folgende Kapitel.

## Erstellung von b1gMail-Plugin-Paketen

Zur Erstellung von b1gMail-Plugin-Paketen (.bmplugin-Dateien) benötigen Sie das Tool „BMPluginBuilder“, das im b1gMail-Plugin-SDK enthalten ist.

Das Hauptfenster besteht aus zwei Register-Karten: „Meta-Informationen“ enthält alle Informationen, die während der Installation des Plugins angezeigt werden sowie eine Liste aller Plugin-Klassen, die automatisch aktiviert werden sollen; „Dateien“ enthält die Plugin-Dateien, die installiert werden sollen.

### Meta-Informationen

Die Register-Karte „Meta-Informationen“ enthält folgende Einstellungs-Möglichkeiten:

* Plugin-Paket-Name – Der Name/Titel des Plugin-Pakets
* Plugin-Paket-Version – Die Versionsnummer des Plugin-Pakets
* Entwickelt für b1gMail-Version – Die b1gMail-Version, für die das Plugin entwickelt wurde, z.B. „7.0.0-Beta3“ oder „7.0.0“
* Hersteller – Der Name des Plugin-Herstellers
* Hersteller-Webseite  – URL der Webseite des Plugin-Herstellers, z.B. „http://www.example.com/“
* Hersteller-E-Mail – E-Mail-Adresse des Plugin-Herstellers
* Plugin-Klassen – Eine Liste der Namen aller Plugin-Klassen, die mit dem Plugin-Paket installiert und aktiviert werden sollen

Tipp: Nachdem Sie Plugin-PHP-Dateien unter „Dateien“ hinzugefügt haben, können Sie die Plugin-Klassen-Namen per Klick auf das Lupen-Symbol unten rechts neben der Plugin-Klassen-Liste auch automatisch vom Plugin-Builder erkennen lassen.

### Dateien

Die Register-Karte „Dateien“ enthält eine Liste aller Dateien, die mit dem Plugin-Paket installiert werden sollen.

Folgende Datei-Typen können zur Liste hinzugefügt werden:

* PHP-Dateien (*.php) werden in das Verzeichnis plugins/ installiert
* Template-Dateien (*.tpl) werden in das Verzeichnis plugins/templates/ installiert
* Bild-Dateien (*.gif, *.jpg, *.png) werden in das Verzeichnis plugins/templates/images/ installiert
* Stylesheets (*.css) werden in das Verzeichnis plugins/templates/css/ installiert
* JavaScripts (*.js) werden in das Verzeichnis plugins/templates/js/ installiert

Per Klick auf den Button „+“ können Sie Dateien hinzufügen, ein Klick auf den Button „-“ entfernt alle in der Liste markierten Dateien.

Tipp: Sie können Dateien auch per Drag&Drop aus dem Explorer/Finder direkt in die Datei-Liste ziehen.

### Erstellen

Nach Angabe aller benötigen Informationen können Sie das Plugin-Paket per Klick auf „Erstellen“ in der Symbolleiste erstellen lassen. Sie können daraufhin auswählen, wo das Plugin-Paket gespeichert werden soll.

Das generierte Plugin-Paket kann dann verteilt werden und mit Hilfe der Register-Karte „Installieren“ im b1gMail-Adminbereich unter „Plugins“ installiert werden.

### Projekte

Der Plugin-Builder bietet Ihnen die Möglichkeit, Ihre Eingaben als Projekt-Datei zu speichern. Diese können Sie dann später erneut öffnen, um einfach neue Versionen Ihres Plugins in eine .bmplugin-Datei zu verpacken.

Über die Schaltflächen „Neu“, „Öffnen“ und „Speichern“ können Sie Ihre Projekte verwalten.

In der .bmproj-Projektdatei werden neben den Meta-Informationen auch die Verweise auf im Projekt enthaltene Dateien gespeichert. Dabei speichert der Plugin-Builder nur den Ort und nicht den Inhalt der Datei, sodass Sie die Dateien nach Änderungen nicht erneut hinzufügen müssen.

## Referenz

### Eigenschaften

Der Konstruktor einer Plugin-Klasse kann und sollte die folgenden Eigenschaften setzen:

* titel – Titel des Plugins
* autor – Autor des Plugins
* web – Web-Seite des Autors bzw. des Plugins, z.B. http://www.firma.xy
* mail – E-Mail-Adresse des Autors
* version – Versionsnummer des Plugins
* designedfor – Versionsnummer der für das Plugin empfohlenen b1gMail-Version
* type – BMPLUGIN_DEFAULT (Konstante) für ein Plugin ohne Widget-Funktionalitäten, BMPLUGIN_WIDGET (Konstante) für ein Plugin mit Widget-Funktionalitäten

Folgende Eigenschaften können gesetzt werden, um die Funktionen bzw. Fähigkeiten des Plugins zu kennzeichnen:

* admin_pages – „true“, wenn das Plugin im Adminbereich konfigurierbar sein soll
* admin_page_title – Titel des Plugins im Menü des Adminbereichs
* widgetTitle – Titel des Widgets (nur bei Widgets)
* widgetTemplate – Template-Datei des Widgets innerhalb des plugins/templates/-Ordners (nur bei Widgets)
* widgetIcon – Symbol des Widgets (16x16) innerhalb des Verzeichnisses plugins/templates/images/ (nur bei Widgets, verfügbar ab b1gMail 7.2)
* widgetPrefs – Gibt an, ob das Widget eine Einstellungs-Seite (true) hat, oder nicht (false) (nur bei Widgets, verfügbar ab b1gMail 7.2)
* widgetPrefsWidth – Gibt die Breite der Widget-Einstellungs-Seite in Pixel an (nur bei Widgets mit Einstellungs-Seite, verfügbar ab b1gMail 7.2)
* widgetPrefsHeight – Gibt die Höhe der Widget-Einstellungs-Seite in Pixel an (nur bei Widgets mit Einstellungs-Seite, verfügbar ab b1gMail 7.2)
* update_url – URL zu einem Update-Service, bei dem auf neue Versionen des Plugins geprüft werden kann. Bei auf my.b1gMail.com veröffentlichten Plugins kann hier der my.b1gMail.com-Update-Service angegeben werden, um den Benutzer auf neue Updates aufmerksam zu machen:
http://my.b1gmail.com/update_service/
Hinweis: Eine Prüfung auf Updates funktioniert dann erst, sobald das Plugin auf my.b1gMail.com veröffentlicht und freigeschaltet wurde.

Folgende Eigenschaften sollten nur gelesen werden:

* internal_name – Internet Name des Plugins, d.h. der Name des Plugin-Klasse
* installed – „true“, falls das Plugin installiert und aktiviert ist

[...]

## Standards ab b1gMail 7.5.0

> **Gültig ab b1gMail 7.5.0**
>
> Die folgenden Anforderungen und APIs (Routing-Registry, `BMPush`, globale CSRF-Prüfung) gelten **erst ab Version 7.5.0**. Für ältere Versionen können abweichende Verhaltensweisen oder fehlende Funktionen bestehen. Setzen Sie die Zielversion in `designedfor` entsprechend.
>
### Routing

Registrieren Sie Plugin-Routen in `RegisterRoutes()` über `BMRoute()`:

```php
public function RegisterRoutes()
{
    // NLI: /demo -> index.php?action=demoRoutes
    BMRoute()->nli('demo', array('action' => 'demoRoutes'));

    // LI: /start/demo -> start.php?action=demoRoutes
    BMRoute()->liStart('demoRoutes', 'start/demo');

    // Dynamisch + Legacy-Konvertierung
    $this->registerRouteMatcher('_matchDemoPaths', '_legacyDemoPaths');
}
```

**Pfad-Konventionen:**

- Pfade ohne führenden Schrägstrich (`start/demo`, nicht `/start/demo`)
- Stabile, lesbare Slugs
- Legacy-Konverter für alte Query-URLs (`registerRouteMatcher(..., legacyFn)`)

**FileHandler:**

- `$file` und `$action` explizit prüfen
- Früh beenden, wenn die Route nicht passt
- Benutzerkontrollierte Ausgaben escapen (`htmlspecialchars`)

### Web Push (`BMPush`)

Push-Nachrichten immer über `BMPush::send()` senden, nicht über eigene HTTP/VAPID-Implementierungen.

Push-Typen registrieren (Hook `GetPushTypes`), damit Benutzer Einstellungen vornehmen können:

```php
public function GetPushTypes(&$types, $area)
{
    if($area !== BMPush::AREA_USER)
        return;

    $types['myplugin.alert'] = 'My Plugin: Alert';
}
```

Beispiel zum Senden:

```php
$result = BMPush::send(array(
    'area'     => BMPush::AREA_USER,
    'targetId' => (int)$userId,
    'type'     => 'myplugin.alert',
    'title'    => 'My Plugin',
    'body'     => 'A new event is available.',
    'url'      => 'start.php?action=myplugin',
    'icon'     => 'pwa-icon.php?size=192',
    'tag'      => 'myplugin-alert'
));
```

Ergebnisfelder: `sent`, `failed`, `removed`, `reason`, `lastError`.

Empfohlene Prüfungen: `BMPush::isEnabled()`, `targetId > 0`, Benutzer-Präferenzen für Push-Typen, kurzer Klartext-Payload.

### CSRF (Plugin-Formulare und Backend)

- Jede zustandsändernde `POST`-Anfrage muss ein gültiges CSRF-Token liefern (Formularfeld `csrf_token` oder Header `X-CSRF-Token`).
- Alle Plugin-`POST`-Handler als Schreiboperationen behandeln.
- Auf die globale CSRF-Prüfung verlassen, keine Umgehungen einbauen.

**Smarty** (`plugins/templates/`) – `{csrffield}` in jedes POST-Formular:

```smarty
<form method="post" action="index.php?action=savePlugin">
  {csrffield}
  <input type="text" name="title" value="{$title|escape}" />
  <button type="submit">Speichern</button>
</form>
```

**JavaScript** (Plugin-JS unter `plugins/js/`):

```javascript
fetch("index.php?action=myPluginSave", {
  method: "POST",
  headers: {
    "Content-Type": "application/json",
    "X-CSRF-Token": window.csrfToken
  },
  body: JSON.stringify(payload)
});
```

### Migration bestehender Addons

| Bereich | Frage | Bei „Nein“ |
|---|---|---|
| CSRF | Enthält jedes POST-Formular `{csrffield}`? | In alle Plugin-`.tpl` einfügen (siehe oben). |
| CSRF/AJAX | Sendet JS `X-CSRF-Token`? | fetch/ajax-Wrapper anpassen. |
| Routing | Routen in `RegisterRoutes()`? | Auf `BMRoute()` migrieren. |
| Routing | Legacy-URL-Konverter? | `registerRouteMatcher(..., legacyFn)`. |
| Web Push | Versand über `BMPush::send()`? | Eigene Transport-Implementierung entfernen. |
| Web Push | Push-Typen registriert? | `GetPushTypes` implementieren. |

**Reihenfolge:** Schreib-Endpunkte erfassen → CSRF in Templates/JS → Routing + Legacy → Push-Typen und `BMPush::send()` → Tests (ungültiges Token, Legacy-URLs, Push an/aus).

**Push – Vorher/Nachher:**

```php
// Vorher: eigene cURL-Push-Implementierung

// Nachher:
BMPush::send(array(
    'area' => BMPush::AREA_USER,
    'targetId' => (int)$userId,
    'type' => 'myplugin.alert',
    'title' => 'Titel',
    'body' => 'Text',
    'url' => 'start.php?action=myplugin'
));
```

### Review- / Abnahmekriterien (ab 7.5.0)

- Kein zustandsändernder Endpunkt ohne CSRF-Schutz
- Routen über Route-Registry deklariert und dokumentiert
- Legacy-URLs funktionieren weiter oder sind migriert
- Push-Typen benannt, konfigurierbar, präferenzbewusst
- Zustellfehler diagnostizierbar (`reason`, Logging)

### Quellcode-Referenzen (ab 7.5.0)

- CSRF/Session: `serverlib/session.inc.php`
- Routing-Registry: `serverlib/route.registry.inc.php`
- Beispiel-Plugin: `plugins/example.routes.plugin.php`
- Push-API: `serverlib/push.class.php`
- Push-Transport: `serverlib/push.web.php`

## Prüfungen vor einer neuen Plugin-Version

Vor dem Veröffentlichen einer neuen Plugin-Version sollten Sie sicherstellen, dass bestehende Installationen **ohne Datenverlust** aktualisiert werden können und alle Funktionen auf der **Ziel-b1gMail-Version** weiterhin laufen. Die folgende Liste ist als Release-Checkliste gedacht – nicht jeder Punkt trifft auf jedes Plugin zu, alle relevanten Zeilen sollten aber abgehakt sein.

### Plugin-Templates und Assets bei Version-Updates

Wenn die neue Plugin-Version Dateien unter `plugins/templates/`, `plugins/css/` oder `plugins/js/` ändert:

| Prüfpunkt | Erledigt? |
|---|---|
| Jedes **neue** POST-Formular in `plugins/templates/` enthält `{csrffield}` | |
| Geänderte AJAX-/fetch-Aufrufe senden `X-CSRF-Token` | |
| Umbenannte `.tpl`: alle PHP-`fetch()`/`display()`-Aufrufe angepasst | |
| Geänderte Formularfeldnamen: Handler lesen alte Werte oder migrieren | |
| Dateinamen mit Plugin-Präfix (siehe [Verzeichnis-Struktur](#verzeichnis-struktur)) | |
| Plugin-Popups/Widget-Prefs: Layout und Speichern getestet | |

### Versionierung und Kompatibilität

| Prüfpunkt | Erledigt? |
|---|---|
| Plugin-`version` in der Klasse und im `.bmplugin`-Paket erhöht | |
| `designedfor` auf die getestete b1gMail-Version gesetzt (z. B. `7.5.0`) | |
| Unterstützte b1gMail-Mindestversion in Release Notes dokumentiert | |
| Breaking Changes (entfernte Hooks, umbenannte Actions, geänderte DB-Felder) dokumentiert | |
| Keine PHP-Warnungen/Notices im Debug-Modus auf der Zielversion | |

### Installations- und Update-Szenarien

Testen Sie mindestens diese drei Wege – Fehler treten oft nur beim Update auf, nicht bei Neuinstallation:

| Szenario | Was prüfen? | Erledigt? |
|---|---|---|
| **Neuinstallation** | Plugin-Paket installieren, aktivieren, Grundfunktionen nutzen | |
| **Update** | Bestehende Version durch neues `.bmplugin` ersetzen; Einstellungen und gespeicherte Daten bleiben erhalten | |
| **Deaktivieren / Reaktivieren** | Plugin aus, wieder ein; keine kaputten Zustände, Cronjobs/Hintergrundlogik startet korrekt | |

Zusätzlich, falls Ihr Plugin Datenbanktabellen oder Konfigurationsschlüssel anlegt:

- Migrations-/Install-Code ist **idempotent** (mehrfaches Ausführen schadet nicht).
- Alte Konfigurationswerte werden gelesen oder sauber migriert (keine leeren Admin-Seiten nach Update).
- Bei manueller Installation per ZIP/FTP: alle neuen Dateien dokumentiert, entfernte Dateien in der Anleitung genannt.

### Funktionale Kernprüfung

| Bereich | Prüfpunkt | Erledigt? |
|---|---|---|
| **Ereignis-Handler** | Alle genutzten Hooks feuern noch (z. B. Login, Mail-Empfang, Cron) | |
| **Admin-Bereich** | Konfigurationsseiten laden, Speichern per POST funktioniert | |
| **Benutzer-Bereich** | Eingeloggte Funktionen (LI) und ggf. NLI-Seiten erreichbar | |
| **FileHandler** | Jede `action` liefert erwartetes Ergebnis; unbekannte Actions werden ignoriert | |
| **Widget** (falls `BMPLUGIN_WIDGET`) | Anzeige, Einstellungs-Dialog, Speichern der Widget-Prefs | |
| **Berechtigungen** | Admin-only-Funktionen nicht als normaler Benutzer aufrufbar | |
| **Fehlerfälle** | Ungültige IDs, leere Pflichtfelder, abgelaufene Sessions → sinnvolle Meldung, kein PHP-Fatal | |

### Sicherheit (Pflicht ab b1gMail 7.5.0, empfohlen generell)

| Prüfpunkt | Erledigt? |
|---|---|
| Alle POST-Formulare und AJAX-Schreibzugriffe mit CSRF (siehe [CSRF oben](#csrf-plugin-formulare-und-backend)) | |
| Ungültiges/abgelaufenes CSRF-Token → Fehlerseite, keine Änderung | |
| Ausgabe von Benutzerdaten escaped (`htmlspecialchars`, `{$var\|escape}`) | |
| Keine sensiblen Daten in URLs (GET) für Löschen/Speichern | |

### URLs, Routing und Verlinkung

| Prüfpunkt | Erledigt? |
|---|---|
| Neue Pretty-URLs (`BMRoute`) erreichbar (ab 7.5.0) | |
| Alte Links (`index.php?action=…`, `start.php?action=…`) funktionieren über Legacy-Konverter | |
| Links in E-Mails, Push-Nachrichten und Lesezeichen zeigen auf gültige Ziele | |
| `url`-Parameter bei `BMPush::send()` nach Routing-Änderung angepasst | |

### Web Push (nur wenn das Plugin Push nutzt)

| Prüfpunkt | Erledigt? |
|---|---|
| Push-Typen in `GetPushTypes` unverändert oder mit Migration dokumentiert | |
| Versand mit Push an/aus und ohne Abonnement getestet | |
| Benutzer-Präferenzen für den Push-Typ werden respektiert | |

### Regression-Matrix (empfohlen)

Kombinieren Sie die wichtigsten Fälle in einer kurzen Matrix und testen Sie jede Zelle mindestens einmal:

|  | Neuinstallation | Update von Vorgänger | Deaktiviert → aktiv |
|---|---|---|---|
| Admin-Konfiguration speichern | | | |
| Hauptfunktion (Benutzer) | | | |
| Kritischster POST/AJAX-Endpunkt | | | |
| Älteste unterstützte b1gMail-Version (falls angegeben) | | | |
| Zielversion (z. B. 7.5.0) | | | |

### Kurz-Check vor dem Release

- Debug-Modus **aus**: ein Durchlauf wie beim Kunden (keine versteckten Notices).
- Plugin-Paket (`.bmplugin`) einmal auf **frischer** und einmal auf **bestehender** Testinstallation installiert.
- Changelog: neue Features, Fixes, **Breaking Changes**, Mindest-b1gMail-Version.
- Keine Leerzeilen/Ausgabe nach dem PHP-End-Tag in Plugin-PHP-Dateien.
- Dateinamen-Konvention eingehalten (keine Kollision mit anderen Plugins).
