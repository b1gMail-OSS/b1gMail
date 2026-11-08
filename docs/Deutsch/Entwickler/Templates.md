# Templates

Eine Übersicht über die Struktur des Template-Ordners von b1gMail (Design-Sets wie `default` oder `tabler` unter `templates/`):

## Verzeichnis-Struktur

Jedes Template-Set liegt in einem eigenen Unterordner von `templates/`:

* `templates/` – Enthält alle Template-Sets, 1 Set pro Ordner. Für Ordnernamen nur Zeichen aus `0-9`, `a-z`, `A-Z`, `-`, `_` verwenden – **keine Leerzeichen**.
* `templates/_(name)_/cache/` – Internes Cache-Verzeichnis (von b1gMail befüllt). Bei Weitergabe nur eine leere Datei `dummy` mitliefern (manche FTP-Clients legen leere Ordner nicht an).
* `templates/_(name)_/images/` – Grafiken des Template-Sets.
* `templates/_(name)_/js/` – JavaScript; beim neuen Set oft zunächst aus `default` oder `tabler` übernommen.
* `templates/_(name)_/li/` – Eingeloggter Bereich („logged in“). Seiten (außer Popups/Layer) bauen auf `index.tpl` auf, in das weitere `.tpl`-Dateien eingebunden werden.
* `templates/_(name)_/m/` – Mobil-Interface.
* `templates/_(name)_/nli/` – Nicht eingeloggt („not logged in“), ebenfalls mit `index.tpl` als Gerüst.
* `templates/_(name)_/share/` – Webdisk-Freigabe.
* `templates/_(name)_/style/` – Stylesheets:
   * `dialog.css` – Popups und Layer
   * `dtree.css` – Ordnerbaum im E-Mail-Bereich
   * `loggedin.css` – nur LI
   * `notloggedin.css` – nur NLI
   * `print.css` – E-Mail-Druck
   * `share.css` – Webdisk-Freigabe

Die `index.tpl` in `li/` und `nli/` sind das Layout-Gerüst. Oft reichen Anpassungen an beiden `index.tpl`, den Stylesheets und einigen Grafiken für ein neues Erscheinungsbild.

In der Praxis: ein bestehendes Set (**`tabler`** oder `default`) kopieren und anpassen, statt alle `.tpl`-Dateien neu zu schreiben.

## Template-Sets: `default` und `tabler`

| Set | Typische Verwendung |
|---|---|
| `default` | Klassisches b1gMail-Layout, Referenz für ältere Anpassungen |
| `tabler` | Modernes UI auf Basis von Tabler; in aktuellen Installationen häufig das aktive Set |

Beide folgen derselben Ordnerstruktur (`li`, `nli`, `m`, `style`, …). Bei eigenen Themes lohnt sich **`tabler` als Ausgangsbasis**, weil dort Layout, Navigation und viele Core-Seiten bereits auf dem aktuellen Stand sind.

Das aktive Template wählt der Administrator in den b1gMail-Einstellungen (nicht per Dateiname allein).

## Neues Template-Set anlegen

1. Ordner `templates/mein-theme/` anlegen (Namenskonvention beachten).
2. Inhalt von `templates/tabler/` (oder `default/`) vollständig kopieren.
3. `cache/` nur mit Datei `dummy` ausliefern, keine kompilierten Cache-Dateien aus der Entwicklungsumgebung.
4. `li/index.tpl`, `nli/index.tpl`, `style/*.css` und `images/` anpassen.
5. In b1gMail aktivieren und LI/NLI sowie stichprobenartig `m/` und Popups testen.

Nach größeren `.tpl`-Änderungen kann der Smarty-Cache unter `templates/mein-theme/cache/` geleert werden (nur `dummy` behalten), falls Layout-Änderungen nicht sichtbar werden.

## CSRF in Template-Anpassungen (ab b1gMail 7.5.0)

> **Gültig ab b1gMail 7.5.0** – gilt für **neue oder geänderte Formulare** in `templates/`.

Ab 7.5.0 werden zustandsändernde `POST`-Anfragen global mit CSRF geprüft. Wenn Sie im Theme eigene Formulare ergänzen oder Core-Formulare kopieren, muss `{csrffield}` enthalten sein (wie in `tabler` bereits umgesetzt, z. B. in `li/email.folders.edit.tpl`).

```smarty
<form method="post" action="start.php?page=...">
  {csrffield}
  ...
</form>
```

Für AJAX in `js/loggedin.js` (bzw. Ihrem Theme-JS) bei POST den Header `X-CSRF-Token: window.csrfToken` setzen – das stellt b1gMail im eingeloggten Bereich bereit.

## Prüfungen vor Veröffentlichung eines Template-Sets

Vor Auslieferung eines neuen oder aktualisierten Theme-Ordners:

| Prüfpunkt | Erledigt? |
|---|---|
| LI: Login, Postfach, Einstellungen, wichtige Popups/Layer | |
| NLI: Login, Registrierung, Impressum o. ä. | |
| Mobil (`m/`), falls mitgeliefert: kritische Seiten | |
| Eigene POST-Formulare mit `{csrffield}` (ab 7.5.0) | |
| Geändertes JS: POST mit `X-CSRF-Token` | |
| Stylesheets in `li` und `nli` konsistent, keine kaputten Pfade zu `images/` | |
| `cache/` ohne alte Smarty-Kompilate, nur `dummy` | |
| Kein Überschreiben fremder Sets (`templates/tabler` unverändert lassen, eigenen Ordner nutzen) | |
| Nach Update beim Kunden: Template in Admin gewählt, Hard-Reload / Cache | |

Bei Anpassungen an einem **Fork von tabler**: mit der **gleichen b1gMail-Version** testen, für die das Set gedacht ist; nach b1gMail-Core-Updates prüfen, ob neue Core-`.tpl`-Dateien in `tabler` ergänzt wurden, die Sie in Ihr Set übernehmen sollten.
