# Synchronization with mobile devices

A backend for the open source synchronization framework [Z-Push](http://z-push.org/) is available for b1gMail. Using Z-Push and the b1gMail backend, users of a b1gMail installation can synchronize their emails, contacts, tasks and appointments with Exchange-compatible mobile devices such as iPhone/iPad and Android smartphones. The service is push-enabled, so new items such as emails and appointments are immediately available on the mobile device.

This document describes how to set up Z-Push including the b1gMail backend on the server and how to configure the mobile device.

## Important notes 

* For synchronization, the mobile devices may establish a permanent connection to the server. If there are many users active at the same time, this can of course put a corresponding load on the server.
* Z-Push and the b1gMail backend for Z-Push are licensed under the [AGPL](http://www.gnu.org/licenses/agpl-3.0.html) license. There should be clarity about the implications of this license. In particular, any changes to the code must be returned to the community.

## Requirements 

For the installation basic knowledge for the administration of the server should be present.
Furthermore, the server must meet the following requirements:

* Dedicated server or vServer with root access. An installation without root access may also be possible with restrictions, but is not covered by this guide.
* b1gMail 7.3.0-Beta5 or newer.
* PHP 5.1 or newer.
* The following PHP extensions (can usually be easily installed via the package manager of the Linux distribution):
  * soap (package `php-soap` or `php5-soap`).
  * pcntl (package `php-process` or `php5-pcntl`).
  * sysvshm (package `php-process` or `php5-sysvshm`)
  * sysvsem (package `php-process` or `php5-sysvsem`).
* Web server with SSL support (HTTPS) is strongly recommended. Operating over non-SSL connections is insecure (transmitting all data in plain text!) and means confusing/complicated configuration on many mobile devices. Self-signed SSL certificates are okay, but usually cause a short warning when setting up the account on the mobile device.

## Installing Z-Push with b1gMail backend 

1. Install the required/recommended PHP extensions (see above), e.g. using the package manager of the Linux distribution.
1. Download the latest version of [Z-Push](https://github.com/Z-Hub/Z-Push/releases) and upload it to your own server.
1. Log in to the server via SSH and unpack the Z-Push archive:`tar xzvf z-push-*.tar.gz`.
1. Create the <tt>/usr/share/z-push/</tt> directory and copy the contents of the folder you just unzipped into it:
  ```
  mkdir -p /usr/share/z-push
  cp -R z-push-*/* /usr/share/z-push/
  ```
1. Create log and data folders and give them the necessary permissions:
  ```
  mkdir -p /var/log/z-push
  mkdir -p /var/lib/z-push
  chmod 0777 /var/log/z-push
  chmod 0777 /var/lib/z-push
  ```
1. Download the b1gMail-Z-Push backend [https://github.com/b1gMail-OSS/zpush-b1gmail](https://github.com/b1gMail-OSS/zpush-b1gmail), unzip the PHP files to <tt>/usr/share/z-push/backend/b1gmail/</tt> copy:
  ```
  unzip master.zip
  mkdir /usr/share/z-push/backend/b1gmail/
  cp b1gmail.php db.php sendmail.php /usr/share/z-push/backend/b1gmail/
  ```
1. Open the Z-Push configuration file, <tt>/usr/share/z-push/config.php</tt>, in a text editor. Find the line `define('BACKEND_PROVIDER', "BackendZarafa");` and replace it with the following, replacing the MySQL credentials with the credentials to your b1gMail database:
  ```
  define('BACKEND_PROVIDER', 'BackendB1GMail');
  define('B1GMAIL_DB_HOST', 'localhost');
  define('B1GMAIL_DB_USER', 'root');
  define('B1GMAIL_DB_PASS', 'password');
  define('B1GMAIL_DB_DB', 'b1gmail');
  define('B1GMAIL_DB_PREFIX', 'bm60_');
  ```
  If Z-Push >= 2.2 is used, you also need to add the line `define('USE_FULLEMAIL_FOR_LOGIN', false);` must be replaced by `define('USE_FULLEMAIL_FOR_LOGIN', true);`

1. Open the web server configuration file (e.g. Apache) in a text editor and add the following line: `Alias /Microsoft-Server-ActiveSync /usr/share/z-push/index.php`.
1. Restart the web server service, e.g. `/etc/init.d/httpd restart`.
1. Check if a password prompt appears at https://server-domain.xy/Microsoft-Server-ActiveSync. If so, the installation has been successful. Otherwise, you should see an error message indicating the problem.

## Configuration in the b1gMail admin area 

In the b1gMail admin area, all you need to do is go to _Users and Groups_ -> _Groups_ and check _Synchronization?_ for all user groups that should be authorized to use the synchronization service.

## Mobile device configuration 

### iPhone/iPad 

...

### Android 

...