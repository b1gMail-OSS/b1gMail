<?php
/*
 * b1gMail
 * Copyright (c) 2021 Patrick Schlangen et al
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 */

$lang_setup['showchmod']                = 'Show <tt>chmod</tt> commands';
$lang_setup['dbmails_note']				= 'Your database contains emails stored in a deprecated format which is not supported anymore as of b1gMail 7.4. Please revert copying the update files (i.e. restore your backup), log in to the b1gMail ACP, go to &quot;Preferences&quot; - &quot;Email&quot; and ensure that mails are stored in files. Then, go to &quot;Tools&quot; - &quot;Optimize&quot; - &quot;File system&quot; and let b1gMail move all remaining mails stored in DB to the file system. Afterwards, retry the update.';
$lang_setup['dbmails']					= 'Mails saved in deprecated mode (DB)';
$lang_setup['setupmode'] 				= 'Setup mode';
$lang_setup['mode_public']				= 'Public email service';
$lang_setup['mode_public_desc']			= 'Configures b1gMail for public email service providing. Enabled a public signup form which is accessible for visitors to register email addresses.';
$lang_setup['mode_private']				= 'Internal email system';
$lang_setup['mode_private_desc']		= 'Configures b1gMail as an internal email system. The public signup form will be disabled and new users can only be created by the administrator.';
$lang_setup['mode_note']				= 'b1gMail will be pre-configured according to the setup mode chosen below. The configuration can be changed at any time after installation.';
$lang_setup['accounting']               = 'Accounting';
$lang_setup['credit_text']              = 'Credits (charge account)';
$lang_setup['setup']					= 'Setup';
$lang_setup['selectlanguage']			= 'Language selection';
$lang_setup['selectlanguage_text']		= 'Please select your language. / Bitte w&auml;hlen Sie Ihre Sprache aus.';
$lang_setup['next']						= 'Next';
$lang_setup['welcome']					= 'Welcome';
$lang_setup['welcome_text']				= 'Welcome to the b1gMail setup wizard! This wizard will guide you through the installation of b1gMail. Please try to provide correct data during the installation to ensure that b1gMail can be installed successfully. If you are in doubt, you can find detailed instructions in the b1gMail manual.';
$lang_setup['syscheck']					= 'System check';
$lang_setup['syscheck_text']			= 'Setup is testing your system for compatibility with b1gMail.';
$lang_setup['required']					= 'Required';
$lang_setup['available']				= 'Available';
$lang_setup['phpversion']				= 'PHP version';
$lang_setup['mysqlext']					= 'MySQLi extension';
$lang_setup['yes']						= 'Yes';
$lang_setup['no']						= 'No';
$lang_setup['writeable']				= 'Writeable';
$lang_setup['notwriteable']				= 'Not writeable';
$lang_setup['checkfail_text']			= 'At least one requirement is not fullfilled. Please fix the problem and click at &quot;Next &raquo;&quot; to retry.';
$lang_setup['checkok_text']				= 'The system check did not find any problems. Please click at &quot;Next &raquo;&quot; to continue.';
$lang_setup['db']						= 'MySQL database';
$lang_setup['dbfresh_text']				= 'Please enter the login to the MySQL database you would like to install b1gMail into. This database must not contain any existing b1gMail installation!';
$lang_setup['mysql_host']				= 'MySQL server';
$lang_setup['mysql_user']				= 'MySQL user';
$lang_setup['mysql_pass']				= 'MySQL password';
$lang_setup['mysql_db']					= 'MySQL database name';
$lang_setup['dbfail_text']				= 'A connection to the database cannot be established. Please check your input and try again.';
$lang_setup['dbexists_text']			= 'There is already a b1gMail installation in this database. Please delete all tables of the old installation first or use another database. If you would like to update from a previous b1gMail version, please use the corresponding update installation type.';
$lang_setup['emailcfg']					= 'E-Mail configuration';
$lang_setup['emailcfg_text']			= 'Please configure the receive and send method you would like to use. You can find further information in the b1gMail manual.';
$lang_setup['receiving']				= 'Receive method';
$lang_setup['pop3gateway']				= 'POP3 gateway(CatchAll)';
$lang_setup['pipe']						= 'b1gMailServer or Pipe/Transportmap gateway';
$lang_setup['pop3_host']				= 'POP3 server';
$lang_setup['pop3_user']				= 'POP3 user';
$lang_setup['pop3_pass']				= 'POP3 password';
$lang_setup['sending']					= 'Send method';
$lang_setup['phpmail']					= 'PHP mail';
$lang_setup['smtp']						= 'SMTP';
$lang_setup['sendmail']					= 'Sendmail';
$lang_setup['sendmail_path']			= 'Sendmail path';
$lang_setup['smtp_host']				= 'SMTP server';
$lang_setup['emailcfgpop3fail_text']	= 'A connection to the POP3 mail account cannot be established. Please check your input and try again.';
$lang_setup['emailcfgsmfail_text']		= 'Sendmail cannot be found at the specified path or is not executable by the web server. Please check your input and try again.';
$lang_setup['misc']						= 'Miscellaneous';
$lang_setup['misc_text']				= 'Please fill in the following form and click at &quot;Next &raquo;&quot; to start the installation process.';
$lang_setup['adminuser']				= 'Administration username';
$lang_setup['adminpw']					= 'Administration password';
$lang_setup['domains']					= 'E-Mail domains (one domain per line)';
$lang_setup['url']						= 'b1gMail URL';
$lang_setup['installing']				= 'Installing...';
$lang_setup['installing_text']			= 'Please wait while setup installs b1gMail. You can find a status report below.';
$lang_setup['inst_dbstruct']			= 'Installing database structure (version %s)...';
$lang_setup['inst_defaultcfg']			= 'Creating default configuration...';
$lang_setup['inst_admin']				= 'Creating administrator user...';
$lang_setup['inst_defaultgroup']		= 'Creating default user group...';
$lang_setup['inst_exdata']				= 'Installing sample data...';
$lang_setup['inst_postmaster']			= 'Creating postmaster...';
$lang_setup['inst_config']				= 'Writing configuration...';
$lang_setup['defaultgroup']				= 'Default group';
$lang_setup['log_text']					= 'During the installation, log message were geenerated. You can find an installation protocol below.';
$lang_setup['finished_text']			= 'The installation is finished. Please note the following information about your b1gMail installation.';
$lang_setup['userlogin']				= 'User login';
$lang_setup['adminlogin']				= 'Administration login';
$lang_setup['dbnotexists_text']			= 'There is no existing installation of b1gMail 6.3.1 in this database. If you want to update from an older b1gMail version, please update it to version 6.3.1 first.';
$lang_setup['update']					= 'Update';
$lang_setup['update_text']				= 'After clicking &quot;Next &raquo;&quot;, setup starts to update your b1gMail6 installation to b1gMail7. Please pay attention to the following important aspects.';
$lang_setup['update_note1']				= 'After updating, b1gMail6 will not be compatible with the database anymore and thus will not work anymore.';
$lang_setup['update_note2']				= 'Updating may take several minutes to several hours, depending on the amount of data. Your web and MySQL server may experience heavy usage during this time.';
$lang_setup['update_note3']				= '<strong>Necessarily</strong> create a full backup of all files and MySQL tables before starting. We are not liable for any data losses and cannot guarantee the integrity of your data.';
$lang_setup['update_note4']				= 'Do not interrupt the update process in any case! Your browser has to stay open during the whole update process. The network connection to the server must not be interrupted.';
$lang_setup['update_note5']				= 'The following data cannot be taken over from b1gMail6: Current spam filter training level, spelling dictionary, badwords, drafts, certified mails.';
$lang_setup['update_text2']				= 'In case you are sure you want to start the update process, please click &quot;Next &raquo;&quot;.';
$lang_setup['updating']					= 'Updating...';
$lang_setup['updating_text']			= 'b1gMail will be updated to the current version in several steps now. You can find the current status below.';
$lang_setup['updating_text2']			= 'Do not interrupt the update process in any case! You will be notified once the update process is finished.';
$lang_setup['step']						= 'Step';
$lang_setup['progress']					= 'Progress';
$lang_setup['update_prepare']			= 'Preparing update';
$lang_setup['update_struct1']			= 'Preparing database structure';
$lang_setup['update_struct2']			= 'Updating database structure';
$lang_setup['update_struct3']			= 'Optimizing and cleaning up database';
$lang_setup['update_mails']				= 'Refreshing E-Mail meta information';
$lang_setup['update_folders']			= 'Updating folders';
$lang_setup['update_filters']			= 'Updating filters';
$lang_setup['update_autoresponders']	= 'Updating autoresponders';
$lang_setup['update_config']			= 'Updating configuration';
$lang_setup['update_calendar']			= 'Updating calendar dates';
$lang_setup['update_optimize']			= 'Optimizing tables';
$lang_setup['updatedone']				= 'The update is finished. Click at &quot;Next &raquo;&quot; to continue.';
$lang_setup['updatesteps_text']			= 'The update is finished. Please follow the following instructions <b>now</b> in this order:';
$lang_setup['update_step1']				= '<font color="red"><b>Necessarily</b> delete the folder &quot;setup&quot; from your webspace for security reasons!</font>';
$lang_setup['update_step2']				= 'Log in to the <a href="../admin/" target="_blank">administrator control panel</a> and assure that the correct absolute path to the data folder is entered at &quot;Preferences&quot; &raquo; &quot;Common&quot;.';
$lang_setup['update_step3']				= 'Execute the following operations at &quot;Tools&quot; &raquo; &quot;Optimization&quot; &raquo; &quot;Cache&quot; in this order:';
$lang_setup['update_step3a']			= 'Re-calculate E-Mail sizes';
$lang_setup['update_step3b']			= 'Re-calculate user space usage';
$lang_setup['update_step4']				= 'Deactivate the &quot;Maintenance mode&quot; at  &quot;Preferences&quot; &raquo; &quot;Common&quot; to open your b1gMail installation to your visitors.';

$lang_setup['error']					= 'Error';
$lang_setup['notice']					= 'Notice';
$lang_setup['success']					= 'Success';
$lang_setup['update_welcome_text']		= 'Welcome! This wizard will update your b1gMail copy from version <b>%s</b> to the current version <b>%s</b>. Click at &quot;Next &raquo;&quot; to continue.';
$lang_setup['uptodate']					= 'Your b1gMail copy is already up to date (<b>%s</b>).';
$lang_setup['unknownversion']			= 'The version of your b1gMail copy (<b>%s</b>) is unknown and cannot be updated to version <b>%s</b> using this wizard.';
$lang_setup['update_resetcache']		= 'Reseting cache';
$lang_setup['update_complete']			= 'Finishing update';
$lang_setup['updatedonefinal']			= 'The update is finished. Necessarily delete the folder &quot;setup&quot; from your webspace <b>now</b>!';
$lang_setup['dbnotconverted']           = 'Your database is still in Latin1 format / ISO charset. Please continue with <a href="setup/utf8convert.php">setup/utf8convert.php</a> and delete the folder &quot;setup&quot; after sucessfull <b>convert</b> from your Webspace!';

$lang_setup['utf8convert']				= 'UTF-8 converter';
$lang_setup['convert_welcome_text']		= 'Welcome! This wizard will convert your b1gMail installation from <b>ISO-8859-15</b> encoding to <b>UTF-8</b>. <font color="red">Use this converter only if your database is in ISO-8859-15 / latin1 encoding!</font> Click at &quot;Next &raquo;&quot; to continue.';
$lang_setup['mbiconvext']				= 'Encoding extension';
$lang_setup['mysqlversion']				= 'MySQL version';
$lang_setup['convert_syscheck_text']	= 'Your system is checked for compatibility with b1gMail\'s UTF-8 mode.';
$lang_setup['converting']				= 'Converting...';
$lang_setup['converting_text']			= 'The database will be converted to UTF-8 in several steps now. You can find the current status below.';
$lang_setup['converting_text2']			= 'Do not interrupt the conversion process in any case! You will be notified once the conversion process is finished.';
$lang_setup['convert_prepare']			= 'Preparing conversion';
$lang_setup['convert_analyzedb']		= 'Analyzing database';
$lang_setup['convert_prepare_tables']	= 'Preparing tables';
$lang_setup['convert_convertdata']		= 'Converting data';
$lang_setup['convert_collations']		= 'Refreshing database/table collations';
$lang_setup['convert_langfiles']		= 'Converting language files';
$lang_setup['convert_resetcache']		= 'Resetting cache';
$lang_setup['convert_complete']			= 'Finishing conversion';
$lang_setup['convert_alreadyutf8']		= 'Your b1gMail installation has already been converted to UTF-8 mode.';
$lang_setup['convertdonefinal']			= 'The database was converted successfully.';
$lang_setup['back']						= 'Back';
$lang_setup['lock_detected']			= 'A lock file was found. Remove <code>lock</code>, <code>lock_update</code>, <code>lock_utf8</code> or <code>lock_utf8mb4</code> in the <code>setup/</code> folder if you want to run the wizard again.';
$lang_setup['already_installed']		= 'An existing installation was found (<code>serverlib/config.inc.php</code>). Only remove the lock file and replace the configuration if you really want to reinstall.';
$lang_setup['lock_failed']				= 'The lock file could not be written. Delete the <strong>setup</strong> folder from your webspace now.';
$lang_setup['csrf_fail']				= 'The session is invalid or has expired. Please start the wizard again.';
$lang_setup['mysql_prefix']				= 'Table prefix';
$lang_setup['adminpw_confirm']			= 'Confirm password';
$lang_setup['pw_too_short']				= 'The administration password must be at least 8 characters long.';
$lang_setup['password_mismatch']		= 'The two passwords do not match.';
$lang_setup['pop3_port']				= 'POP3 port';
$lang_setup['pop3_tls']					= 'TLS/SSL (port 995)';
$lang_setup['smtp_port']				= 'SMTP port';
$lang_setup['smtp_auth']				= 'SMTP authentication';
$lang_setup['smtp_user']				= 'SMTP user';
$lang_setup['smtp_pass']				= 'SMTP password';
$lang_setup['ext_mysqli']				= 'MySQLi';
$lang_setup['ext_mbstring']				= 'mbstring / iconv';
$lang_setup['ext_json']					= 'JSON';
$lang_setup['ext_openssl']				= 'OpenSSL';
$lang_setup['ext_session']				= 'Session';
$lang_setup['optional']					= 'optional';
$lang_setup['remove_setup']				= 'For security reasons, delete the <strong>setup</strong> folder from your webspace now.';
$lang_setup['copy']						= 'Copy';
