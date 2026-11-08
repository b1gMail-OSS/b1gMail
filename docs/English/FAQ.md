# Frequently Asked Questions

This page contains a list of frequently asked questions with associated answers.

## General

### What are the system requirements for b1gMail?
In order to run b1gMail, your webspace will have to fulfill the following system requirements:

1. PHP 8.1.0 or newer  
1. At least 20 MB free web space  
1. MySQL database with at least 30 MB free space  
1. One of the supported receive methods described in [Receive method](Receivemethod.md)

You can use our server test tool to check your webspace for compatibility with b1gMail. For dedicated servers, we recommend Linux or Unix as the operating system and Apache as the web server software. 

### Do I need an own dedicated root server?
No. b1gMail is, in contrast to many other webmail solutions, usable without a dedicated root server. A separate root server is only required if you want to offer POP3/IMAP/SMTP access in addition to the web interface.

### How many users / emails can be managed with b1gMail?
b1gMail does not limit the count of users or emails. The maximum amount of users and emails solely depends on your server / web space. Please take into account that you should have enough web space to store the data of your users. (But in practice, most users do not use 100% of their available email space.) 

## E-mail send/receive

### Where does b1gMail store received emails?
b1gMail stores your users mails in files on your web space and simultaneously manages an index for fast access to important e-mail information in the MySQL database. This way the advantages of database and file system are combined and even large amounts of emails can be managed efficiently. 

### All incoming emails are marked as spam or end up in the spam folder
On some systems with a DNS search domain that has a wildcard entry, emails may be incorrectly marked as spam if the DNSBL filter is enabled. To fix:

1. Log into the admin area of your b1gMail installation.  
2. Go to "Settings" → "Email" → "Anti-Spam".  
3. Change the list of DNSBL servers so that there is a "." at the end of each line or server name (no preceding space). Example:
```text
dnsbl.domain1.xy.
dnsbl.domain2.xy.
```

If the problem still occurs after that, please check other possible causes, such as an incorrectly or improperly trained statistical filter. You can reset this in the admin area on the same page (for global training mode) or in the user account (for local training mode). Another possible cause is an invalid reception rule.


### How does b1gMail receive emails?
Depending on server/webspace configuration, you can use one of the following receiving methods.

#### When having an own dedicated root server
In case you have an own dedicated root server, you can use b1gMail's own email server b1gMailServer to operate b1gMail in stand-alone mode without having to install a third-party mail server. b1gMailServer accepts emails at port 25 and delivers them to the b1gMail accounts of your users.

In case you are using the Postfix mail server and do not want to use b1gMailServer, you can set up an interface between Postfix and b1gMail (using transport maps). 

#### With shared webspace / without own dedicated root server
If you do not have an own root server, i.e. you are using b1gMail on shared webspace, you can use the CatchAll feature of your domain. When enabling this feature, all emails to your domain will be delivered to a shared POP3 account. b1gMail connects to this POP3 account in regular intervals, fetches all the emails contained in it and distributes them to the b1gMail accounts of your users. 

#### Further methods
b1gMail comes with an optimized interface for the cPanel control panel software. In case your webspace is administrated using cPanel, you can use this method to connect b1gMail to cPanel-managed domains efficiently. 

## Other

### How do I transfer b1gMail to a new server?
If you want to transfer b1gMail to a new server/webspace, please read this document: [Server-Umzug](../../Deutsch/b1gMail/Installation/Server-Umzug.md) (currently german only).

### Do I need the b1gMailServer AddOn in order to use b1gMail?
No. b1gMailServer is only required if you want to offer POP3, SMTP and/or IMAP access to users in addition to the web interface.
