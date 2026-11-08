# Decision for a receiving method

In order for b1gMail to receive emails, they must be passed from the receiving mail server to
b1gMail.

This handover can be done in different ways. b1gMail therefore allows reception by means of different receiving methods, which differ in their efficiency and especially in their system requirements.

Below you will find an overview of the possible receiving methods with their respective requirements. The decision for a reception method should be made depending on the possibilities offered by your web space or the rights you have on the server where b1gMail is to be installed.

If you do not have root/administrator access to the web space or server you are using, the "POP3 gateway" reception method is usually the right choice.

## POP3 gateway
The reception method "POP3-Gateway" is the most frequently used reception method, because it can be set up very easily and is compatible with any server configuration that meets the system requirements of b1gMail.

With this method, all emails sent to a recipient under your domain (*@example.com) are stored in a central mailbox. From this mailbox b1gMail can now retrieve the e-mails and distribute them to the configured users.
users. The disadvantage of this reception method, however, is that incoming e-mails cannot be processed individually in real time, but must be retrieved periodically and processed simultaneously. In the case of very high e-mail volumes (which, however, usually require a root
a root server is required), we recommend the use of another reception method.

## Pipe gateway

The "Pipe-Gateway" reception method requires either root access on the server used or the ability to pipe incoming emails to a PHP script. As soon as an email arrives, it is passed directly to b1gMail, which immediately processes the email and delivers it to the target user. Compared to the POP3 gateway
this method has the advantage that the server resources are used more evenly and thus a higher e-mail volume can be processed.

## Postfix transport map gateway

This receiving method assumes that you have root access to your server and are using Postfix as your mail server. This method works basically like the pipe gateway, but takes advantage of one more feature of Postfix that allows more reliable determination of the recipient of the mail.

## b1gMailServer

If you have root access and do not yet use an existing mail server or do not depend on using a specific mail server, you can use b1gMailServer as your mail server. The mail server included in b1gMailServer is optimized for use with b1gMail and therefore works optimally with b1gMail. The setup of b1gMailServer is described in the b1gMailServer documentation.


# Setup of the receiving method

Before the actual installation of b1gMail, the receiving method you decided on in step 2 must now be set up. Please proceed according to the choice you made in step 2.

## POP3 gateway
The setup of the "POP3 Gateway" reception method is described in general terms below, which are used by many web hosts.

The following steps can usually be done in the administration interface of your web hoster or server.
1. set up a new POP3 mailbox and make a note of the access data to the mailbox you have just set up (server, username and password)
2. activate the CatchAll function of the domain you want to use with b1gMail and configure it to save all incoming emails to the POP3 mailbox you just set up - usually this can be achieved by setting up an email address with the name '*' (e.g. '*@example.com').

## Pipe gateway

Setting up the `pipe gateway` reception method requires root/shell access to your server and basic system knowledge, especially of the mail server you are using.

### General
The mail server must be configured to pass all incoming mail for the domain you want to use b1gMail with to b1gMail's `interface/pipe.php` script.


### Postfix
The following is an example of how to set up the pipe gateway for the Postfix mail server under Linux. The setup process is usually similar for other mail servers. You can usually find valuable additional information in the documentation for the mail server.
1. log in to your server via SSH as user `root`
1. create a new system user (e.g. `b1gmailuser`), who will be responsible for receiving the mails - as user group use the group, under which the installed webserver (usually Apache) is running (mostly `www`)
`useradd -m -d /home/b1gmailuser -G www b1gmailuser`
1. change to the home directory of the user
`cd /home/b1gmailuser`
1. create a new file with the name `.forward`.
`vi .forward`
1. type the following line into the file, replacing the path `/srv/www/htdocs/web2/html/b1gmail/` with the directory where you uploaded b1gMail; replace `/usr/bin/php` with the absolute path to the PHP interpreter: 
`|"/usr/bin/php -q /srv/www/htdocs/web2/html/b1gmail/interface/pipe.php"`
1. save the file and close the editor
1. open the file `/etc/postfix/virtual` with `vi /etc/postfix/virtual`
1. at the end of the file, for each domain you want to use with b1gMail, create the following two lines, replacing b1gmailuser with the user from step 2 and example.com with the respective domain:

   `example.com b1gmailuser`

   `@example.com b1gmailuser`

1. save the file and close the editor
1. apply the changes by typing `postmap /etc/postfix/virtual` and `service postfix restart`

### Postfix transport map gateway

Setting up the `postfix transport map gateway` receiving method requires root/shell access to your server, using Postfix as a mail server, and basic system and Postfix knowledge.

Please follow the instructions below to set it up. Depending on your system configuration, the steps required for your system may differ slightly.

1. log in to your server via SSH as user "root"
1. open the file "/etc/postfix/master.cf" in a text editor, e.g. "vi"
`vi /etc/postfix/master.cf`
1. add the following line at the end of the file, replacing the path `/srv/www/htdocs/web2/html/b1gmail/` with the directory where you uploaded b1gMail and `wwrun` with the user your web server is running under; please replace `/usr/bin/php` with the absolute path to the PHP interpreter:
`b1gmailtransport unix - n n - 15 pipe flags=Rq user=wwwrun argv=/usr/bin/php /srv/www/htdocs/web2/html/b1gmail/interface/pipe.php ${sender} -- ${recipient}`
1. save the file and close the editor
1. open the file "/etc/postfix/transport" in a text editor - if it does not exist, create the file
`vi /etc/postfix/transport`.
1. at the end of the file, create the following line for each domain you want to use with b1gMail, replacing example.com with the respective domain: `example.com b1gmailtransport:dummy`
1. save the file and close the editor
1. open the file "/etc/postfix/main.cf" in a text editor `vi /etc/postfix/main.cf`.
1. find the line starting with relay_domains - if such a line already exists, add all domains you want to use with b1gMail; otherwise add a new line; example:
`relay_domains = example.com example.net example.org`.
1. if the following line is not present in the file, please add it: `transport_maps = hash:/etc/postfix/transport`.
1. make sure that no domain you want to use with b1gMail is in the `my_destination` line you can find in the file.
1. save the file and close the editor.
1. activate the new configuration by entering `postmap /etc/postfix/transport` and `service postfix restart`.


**Note**: Never edit configuration files of your Linux/Unix server using a Windows PC or a Mac with line breaks other than "\n". This could otherwise cause the configuration files to no longer be recognized correctly.