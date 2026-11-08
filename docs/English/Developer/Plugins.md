# Plugins

b1gMail provides an extensive module interface that allows hooks into many processes and workflows while helping keep your plugin compatible with future b1gMail versions. Plugin code lives in a dedicated plugin package, so users do not need to modify core b1gMail files to install a plugin.

This SDK includes plugin examples and the b1gMail Plugin Builder (BMPluginBuilder) for creating `.bmplugin` packages installable from the admin area without FTP.

## Introduction

b1gMail plugins generally consist of at least one PHP file containing the plugin code, plus optional template files, images, and CSS/JS assets for user and administrator interaction.

## Directory structure

Each b1gMail installation provides these directories relevant to plugin development:

* `plugins/` – PHP plugin files. All plugins in this folder are loaded on each b1gMail request.
* `plugins/templates/` – Plugin template files.
* `plugins/templates/images/` – Plugin image assets (PNG, GIF, JPG).

From b1gMail 7.2 onward, these additional directories are available:

* `plugins/css/` – Plugin stylesheets.
* `plugins/js/` – Plugin JavaScript files.

### File naming rules

To avoid conflicts between modules, use a unique internal abbreviation for your plugin (e.g. the PremiumAccount module uses `pacc`):

* Prefix each template filename with the abbreviation and a dot (e.g. `pacc.prefs.tpl` instead of `prefs.tpl`).
* Prefix each image, CSS, and JS filename with the abbreviation and an underscore (e.g. `pacc_packages.png`).

## Plugin PHP file and classes

A plugin PHP file must contain at least one plugin class extending the b1gMail base class `BMPlugin`. Register all classes b1gMail should use with the plugin manager at the end of the file.

Each plugin class represents one plugin. The constructor must set meta information (name, version, author). Event handlers are functions called by b1gMail when specific events occur.

Register classes with:

```php
$plugins->registerPlugin('TestPlugin');
```

## Development tips

* Enable b1gMail debug mode during development for detailed PHP errors.
* Keep HTML and JavaScript in plugin templates; keep logic in the PHP plugin file.
* Avoid modifying core b1gMail code when possible.
* Omit trailing whitespace after the closing `?>` tag, or omit the closing tag entirely.
* Distribute plugins as `.bmplugin` packages when possible for easier installation.

## Distribution

Plugins can be distributed as a file archive (requires FTP/webspace access) or as a `.bmplugin` package installable from the admin area without FTP access.

For the full German SDK reference (event handlers, properties, BMPluginBuilder), see [Plugins (German)](../../../Deutsch/b1gMail/Entwickler/Plugins.md).

## Standards from b1gMail 7.5.0

> **Applies from b1gMail 7.5.0 onward**
>
> The requirements and APIs below (route registry, `BMPush`, global CSRF enforcement) **only apply from version 7.5.0 onward**. Older versions may behave differently or lack these features. Set `designedfor` accordingly.
>
### Routing

Register plugin routes in `RegisterRoutes()` via `BMRoute()`:

```php
public function RegisterRoutes()
{
    // NLI: /demo -> index.php?action=demoRoutes
    BMRoute()->nli('demo', array('action' => 'demoRoutes'));

    // LI: /start/demo -> start.php?action=demoRoutes
    BMRoute()->liStart('demoRoutes', 'start/demo');

    // Dynamic + legacy conversion
    $this->registerRouteMatcher('_matchDemoPaths', '_legacyDemoPaths');
}
```

**Path conventions:**

- Paths without a leading slash (`start/demo`, not `/start/demo`)
- Stable, human-readable slugs
- Legacy converters for old query URLs (`registerRouteMatcher(..., legacyFn)`)

**FileHandler:**

- Check `$file` and `$action` explicitly
- Return early when the route does not match
- Escape user-controlled output (`htmlspecialchars`)

### Web push (`BMPush`)

Send push messages through `BMPush::send()`, not custom HTTP/VAPID implementations.

Register push types via `GetPushTypes` so users can configure preferences:

```php
public function GetPushTypes(&$types, $area)
{
    if($area !== BMPush::AREA_USER)
        return;

    $types['myplugin.alert'] = 'My Plugin: Alert';
}
```

Example send call:

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

Result fields: `sent`, `failed`, `removed`, `reason`, `lastError`.

Recommended checks: `BMPush::isEnabled()`, `targetId > 0`, user push-type preferences, short plain-text payload.

### CSRF (plugin forms and backend)

- Every state-changing `POST` must include a valid CSRF token (`csrf_token` field or `X-CSRF-Token` header).
- Treat all plugin `POST` handlers as write operations.
- Rely on global CSRF enforcement; do not add bypasses.

**Smarty** (`plugins/templates/`) – include `{csrffield}` in every POST form:

```smarty
<form method="post" action="index.php?action=savePlugin">
  {csrffield}
  <input type="text" name="title" value="{$title|escape}" />
  <button type="submit">Save</button>
</form>
```

**JavaScript** (plugin JS under `plugins/js/`):

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

### Migrating existing addons

| Area | Question | If "No" |
|---|---|---|
| CSRF | Does every POST form include `{csrffield}`? | Add to all plugin `.tpl` files (see above). |
| CSRF/AJAX | Does JS send `X-CSRF-Token`? | Update fetch/ajax wrappers. |
| Routing | Routes in `RegisterRoutes()`? | Migrate to `BMRoute()`. |
| Routing | Legacy URL converters? | Add `registerRouteMatcher(..., legacyFn)`. |
| Web push | Sending via `BMPush::send()`? | Remove custom transport code. |
| Web push | Push types registered? | Implement `GetPushTypes`. |

**Order:** Inventory write endpoints → CSRF in templates/JS → routing + legacy → push types and `BMPush::send()` → test invalid token, legacy URLs, push on/off.

**Push – before/after:**

```php
// Before: custom cURL push implementation

// After:
BMPush::send(array(
    'area' => BMPush::AREA_USER,
    'targetId' => (int)$userId,
    'type' => 'myplugin.alert',
    'title' => 'Title',
    'body' => 'Text',
    'url' => 'start.php?action=myplugin'
));
```

### Review / acceptance criteria (from 7.5.0)

- No state-changing endpoint without CSRF protection
- Routes declared via route registry and documented
- Legacy URLs still work or are migrated cleanly
- Push types named, configurable, and preference-aware
- Delivery failures diagnosable (`reason`, logging)

### Code references (from 7.5.0)

- CSRF/session: `serverlib/session.inc.php`
- Routing registry: `serverlib/route.registry.inc.php`
- Example plugin: `plugins/example.routes.plugin.php`
- Push API: `serverlib/push.class.php`
- Push transport: `serverlib/push.web.php`

## Checks before releasing a new plugin version

Before publishing a new plugin version, verify that existing installations can **upgrade without data loss** and that all features still work on the **target b1gMail version**. Use this as a release checklist—not every row applies to every plugin, but all relevant rows should be checked.

### Plugin templates and assets in version updates

If the new plugin version changes files under `plugins/templates/`, `plugins/css/`, or `plugins/js/`:

| Check | Done? |
|---|---|
| Every **new** POST form in `plugins/templates/` includes `{csrffield}` | |
| Changed AJAX/fetch calls send `X-CSRF-Token` | |
| Renamed `.tpl`: all PHP `fetch()`/`display()` calls updated | |
| Renamed form fields: handlers read old values or migrate | |
| Filenames use plugin prefix (see [directory structure](#directory-structure)) | |
| Plugin popups/widget prefs: layout and save tested | |

### Versioning and compatibility

| Check | Done? |
|---|---|
| Plugin `version` bumped in class and `.bmplugin` package | |
| `designedfor` set to the tested b1gMail version (e.g. `7.5.0`) | |
| Minimum supported b1gMail version documented in release notes | |
| Breaking changes (removed hooks, renamed actions, DB field changes) documented | |
| No PHP warnings/notices in debug mode on target version | |

### Install and upgrade scenarios

Test at least these three paths—issues often appear only on upgrade, not on fresh install:

| Scenario | What to verify | Done? |
|---|---|---|
| **Fresh install** | Install package, activate, exercise core features | |
| **Upgrade** | Replace previous version with new `.bmplugin`; settings and stored data preserved | |
| **Deactivate / reactivate** | Toggle plugin off and on; no broken state, background logic still runs | |

If your plugin creates database tables or config keys:

- Install/migration code is **idempotent** (safe to run more than once).
- Old config values are read or migrated cleanly (no empty admin pages after upgrade).
- For manual ZIP/FTP installs: list new files and document removed files.

### Core functional checks

| Area | Check | Done? |
|---|---|---|
| **Event handlers** | All hooks you use still fire (e.g. login, mail receive, cron) | |
| **Admin area** | Config pages load; save via POST works | |
| **User area** | Logged-in (LI) and optional NLI pages reachable | |
| **FileHandler** | Each `action` behaves as expected; unknown actions ignored | |
| **Widget** (if `BMPLUGIN_WIDGET`) | Display, settings dialog, saving widget prefs | |
| **Permissions** | Admin-only features not callable as normal user | |
| **Error cases** | Invalid IDs, empty required fields, expired session → sensible message, no fatal error | |

### Security (required from 7.5.0, recommended in general)

| Check | Done? |
|---|---|
| All POST forms and AJAX writes use CSRF (see [CSRF above](#csrf-plugin-forms-and-backend)) | |
| Invalid/expired CSRF token → error, no data change | |
| User-controlled output escaped (`htmlspecialchars`, `{$var\|escape}`) | |
| No sensitive delete/save operations via GET URLs | |

### URLs, routing, and links

| Check | Done? |
|---|---|
| New pretty URLs (`BMRoute`) reachable (from 7.5.0) | |
| Old links (`index.php?action=…`, `start.php?action=…`) work via legacy converters | |
| Links in mail, push notifications, and bookmarks still valid | |
| `url` in `BMPush::send()` updated after routing changes | |

### Web push (only if your plugin uses push)

| Check | Done? |
|---|---|
| Push types in `GetPushTypes` unchanged or migration documented | |
| Send tested with push enabled/disabled and without subscription | |
| User preferences for push type respected | |

### Regression matrix (recommended)

Combine the most important cases and test each cell at least once:

|  | Fresh install | Upgrade from previous | Deactivated → active |
|---|---|---|---|
| Save admin configuration | | | |
| Main user-facing feature | | | |
| Most critical POST/AJAX endpoint | | | |
| Oldest supported b1gMail version (if stated) | | | |
| Target version (e.g. 7.5.0) | | | |

### Short pre-release checklist

- Debug mode **off**: one pass as a customer would run it (no hidden notices).
- `.bmplugin` tested on **fresh** and **existing** installation.
- Changelog: features, fixes, **breaking changes**, minimum b1gMail version.
- No whitespace/output after PHP closing tag in plugin files.
- File naming convention followed (no collision with other plugins).
