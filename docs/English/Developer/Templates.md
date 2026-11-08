# Templates

An overview of the b1gMail template folder structure (design sets such as `default` or `tabler` under `templates/`):

## Folder structure

Each template set lives in its own subfolder of `templates/`:

* `templates/` – All template sets, one folder per set. Use only `0-9`, `a-z`, `A-Z`, `-`, `_` in folder names—**no spaces**.
* `templates/_(name)_/cache/` – Internal cache (filled by b1gMail). When distributing a set, include only an empty `dummy` file (some FTP clients skip empty directories).
* `templates/_(name)_/images/` – Graphics for the set.
* `templates/_(name)_/js/` – JavaScript; often copied from `default` or `tabler` when creating a new set.
* `templates/_(name)_/li/` – Logged-in area. Most pages (except popups/layers) use `index.tpl` as a shell with other `.tpl` files included.
* `templates/_(name)_/m/` – Mobile interface.
* `templates/_(name)_/nli/` – Not logged in, also built around `index.tpl`.
* `templates/_(name)_/share/` – Webdisk sharing.
* `templates/_(name)_/style/` – Stylesheets:
   * `dialog.css` – popups and layers
   * `dtree.css` – folder tree in mail area
   * `loggedin.css` – LI only
   * `notloggedin.css` – NLI only
   * `print.css` – printing mail
   * `share.css` – webdisk sharing

The `index.tpl` files in `li/` and `nli/` are the layout shell. Often customizing both `index.tpl` files, stylesheets, and some images is enough for a new look.

In practice: copy an existing set (**`tabler`** or `default`) and modify it rather than writing every `.tpl` from scratch.

## Template sets: `default` and `tabler`

| Set | Typical use |
|---|---|
| `default` | Classic b1gMail layout; reference for older customizations |
| `tabler` | Modern Tabler-based UI; commonly the active set on current installations |

Both use the same folder layout (`li`, `nli`, `m`, `style`, …). For custom themes, **`tabler` is a good starting point** because navigation and many core pages are already up to date.

The active template is chosen in b1gMail admin settings—not by folder name alone.

## Creating a new template set

1. Create `templates/my-theme/` (follow naming rules).
2. Copy all files from `templates/tabler/` (or `default/`).
3. Ship `cache/` with only a `dummy` file, not compiled cache from your dev machine.
4. Adjust `li/index.tpl`, `nli/index.tpl`, `style/*.css`, and `images/`.
5. Activate in b1gMail and test LI/NLI, spot-check `m/` and popups.

After large `.tpl` changes, clear Smarty cache under `templates/my-theme/cache/` (keep `dummy`) if layout updates do not appear.

## CSRF in template customizations (from b1gMail 7.5.0)

> **Applies from 7.5.0** – for **new or changed forms** under `templates/`.

From 7.5.0, state-changing `POST` requests are validated globally. Custom theme forms must include `{csrffield}` (as in `tabler`, e.g. `li/email.folders.edit.tpl`).

```smarty
<form method="post" action="start.php?page=...">
  {csrffield}
  ...
</form>
```

For AJAX in `js/loggedin.js` (or your theme JS), send `X-CSRF-Token: window.csrfToken` on POST—b1gMail provides this in the logged-in area.

## Checks before publishing a template set

Before shipping a new or updated theme folder:

| Check | Done? |
|---|---|
| LI: login, mailbox, preferences, important popups/layers | |
| NLI: login, signup, imprint, etc. | |
| Mobile (`m/`), if included: critical pages | |
| Custom POST forms include `{csrffield}` (from 7.5.0) | |
| Changed JS: POST sends `X-CSRF-Token` | |
| Stylesheets consistent for LI/NLI; image paths valid | |
| `cache/` has no stale Smarty compile files, only `dummy` | |
| Does not overwrite other sets (use your own folder, leave `templates/tabler` intact) | |
| After customer upgrade: template selected in admin, hard reload / cache | |

If you maintain a **fork of tabler**, test on the **same b1gMail version** you target; after core upgrades, check whether new core `.tpl` files were added to `tabler` that you should merge into your set.
