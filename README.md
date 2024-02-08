<div align="center">
  <a href="https://www.b1gmail.eu">
    <img width="300" height="212" src="https://www.b1gmail.eu/b1gmaillogo.png">
  </a>
  <br>
  <h1>b1gMail 7.4 OpenSource version</h1>
  <br>
  <p>
    Email for your website, Modern user interface, Easy to administrate
  </p>
  <br>
</div>

## About b1gMail
A big thanks goes to b1gMail founder Patrick Schlangen. He released b1gMail as freeware back then and since version 6 it was commercial. With version 7.4.1 the license is changed to GPL and all proprietary components were removed.

## Getting started
It is recommended to install the b1gMail developer copy on a local web server,
e.g. standard Apache/PHP/MySQL on Linux or Wamp on Windows. Even better results
on Windows can be achieved with a WSL setup. If you use Docker, you can also 
use our docker template in `docker-dev`.

In order to install a development environment, proceed as follows:
1. Clone the repository
2. Go to `src/serverlib/` and copy `config.default.inc.php` to `config.inc.php` and copy `version.default.inc.php` to `version.inc.php`.
3. Remove the file `lock` in `src/setup/`
4. Open the folder `src` in your web browser, e.g. `http://localhost/b1gMail/src/`
5. Follow the setup instructs

## Staying up to date
When pulling new changes from the server, you will need to update your database
structure in case it changed. In order to do so, you can use the `tools/db_sync.php`
script or log in to the ACP of your b1gMail development copy, go to "Tools" -> "Optimize" 
and chose "Check structure". Let the ACP fix any issues it found.

## Contributing
You want to contribute to the b1gMail code? Great! In order to do so, it's
probably the best idea to fork the b1gMail repository here and start creating your own commits. 
As soon as you feel the commit is mature and you would like to integrate it into the b1gMail code base, 
create a merge request to the main repository and we will review it.

### Basic guidelines for commits
* Adhere to the b1gMail coding style
* If your commit requires database structure changes, include the updated database
  structure in the commit (you can export it using the `tools/db_struct.php` tool)
* If your commit requires other DB changes (i.e. change values), include update code
  in the update script (it should be executed when updating to the next major version)

## Migrating from the commercial to the GPL version
look at b1gMail Wiki.

## Installation
look at b1gMail Wiki.