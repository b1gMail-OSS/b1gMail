# First Installation

If you are planning a new / first installation of b1gMail, please follow the following instructions.

Set up a new POP3 account an configure your CatchAll feature to redirect all incoming mails for the domains you would like to use with b1gMail to this POP3 account (This step is not required in case you plan to install and use b1gMailServer for email receiving). More about [Receive method](Receivemethod.md)

Upload all files and folders contained in the folder "upload" of this package to your webspace (e.g. using a FTP client). 

Provide the following files and folders at your webspace with writing permissions (normally chmod 666 for files and chmod 777 for folders):

* serverlib/config.inc.php
* serverlib/version.inc.php
* admin/templates/cache/
* languages/
* logs/
* plugins/
* plugins/templates/
* plugins/templates/images/
* temp/
* temp/session/
* temp/cache/
* templates/modern/cache/
* data/

Open the set up wizard of b1gMail by visiting the folder /setup/ in your browser (e.g. http://www.your-domain.xy/b1gmail/setup/) and follow the on-screen instructions. After installing b1gMail successfully, necessarily delete the folder "setup" from your webspace.
