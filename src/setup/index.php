<?php
 /*
 * b1gMail setup wizard.
 * Copyright (c) 2021 Patrick Schlangen et al, 2022 b1gMail.eu	
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
require './common.inc.php';
require_once './SetupInstaller.php';

define('STEP_SELECT_LANGUAGE', 0);
define('STEP_WELCOME', 1);
define('STEP_SYSTEMCHECK', 3);
define('STEP_MYSQL', 5);
define('STEP_CHECK_MYSQL', 6);
define('STEP_CHECK_EMAIL', 7);
define('STEP_INSTALL', 8);

$step = isset($_REQUEST['step']) ? (int)$_REQUEST['step'] : STEP_SELECT_LANGUAGE;
ReadLanguage();
if (!SetupCsrfOk()) {
    $step = STEP_SELECT_LANGUAGE;
    $setupError = $lang_setup['csrf_fail'];
}
if (!defined('DB_INSTALL_PREFIX')) {
    define('DB_INSTALL_PREFIX', SetupNormalizePrefix(SetupInput('mysql_prefix', SETUP_DEFAULT_PREFIX)));
}
SetupAbortIfLocked('lock');
SetupAbortIfAlreadyInstalled();

function SetupRenderTemplate(string $name, array $data = []): void
{
    extract($data, EXTR_SKIP);
    require __DIR__.'/templates/'.$name.'.php';
}

pageHeader();
if (!empty($setupError)) {
    echo SetupAlert('danger', $setupError);
}

if ($step === STEP_SELECT_LANGUAGE) {
    $nextStep = STEP_WELCOME;
    SetupRenderTemplate('select-language');
} elseif ($step === STEP_WELCOME) {
    $nextStep = STEP_SYSTEMCHECK;
    $backStep = STEP_SELECT_LANGUAGE;
    SetupRenderTemplate('welcome');
} elseif ($step === STEP_SYSTEMCHECK) {
    $nextStep = STEP_MYSQL;
    $backStep = STEP_WELCOME;
    $phpOk = version_compare(PHP_VERSION, SETUP_PHP_MIN, '>=');
    $rows = [[
        'label' => $lang_setup['phpversion'],
        'required' => SETUP_PHP_MIN,
        'available' => PHP_VERSION,
        'ok' => $phpOk,
    ]];
    foreach (SetupRequiredExtensions() as $ext => $ok) {
        $rows[] = [
            'label' => $lang_setup['ext_'.$ext] ?? $ext,
            'required' => $lang_setup['yes'],
            'available' => $ok ? $lang_setup['yes'] : $lang_setup['no'],
            'ok' => $ok,
        ];
        if (!$ok) {
            $phpOk = false;
        }
    }
    [$fileRows, $chmodCommands, $filesOk] = SetupCollectWritableRows($writeableFiles);
    $rows = array_merge($rows, $fileRows);
    if (!$phpOk || !$filesOk) {
        $nextStep = STEP_SYSTEMCHECK;
    }
    SetupRenderTemplate('system-check', ['rows' => $rows, 'chmodCommands' => $chmodCommands]);
} elseif ($step === STEP_MYSQL) {
    $nextStep = STEP_CHECK_MYSQL;
    $backStep = STEP_SYSTEMCHECK;
    SetupRenderTemplate('database');
} elseif ($step === STEP_CHECK_MYSQL) {
    $connection = CheckMySQLLogin(SetupInput('mysql_host'), SetupInput('mysql_user'), SetupInput('mysql_pass'), SetupInput('mysql_db'));
    if (!$connection) {
        $nextStep = STEP_MYSQL;
        $backStep = STEP_SYSTEMCHECK;
        SetupRenderTemplate('database-error');
    } else {
        $b1gMailInDB = false;
        $res = mysqli_query($connection, 'SHOW TABLES');
        if ($res) {
            while ($row = mysqli_fetch_array($res, MYSQLI_NUM)) {
                if (($row[0] ?? '') === DB_INSTALL_PREFIX.'prefs') {
                    $b1gMailInDB = true;
                    break;
                }
            }
            mysqli_free_result($res);
        }
        mysqli_close($connection);
        if ($b1gMailInDB) {
            $nextStep = STEP_MYSQL;
            $backStep = STEP_SYSTEMCHECK;
            SetupRenderTemplate('database-exists');
        } else {
            $nextStep = STEP_CHECK_EMAIL;
            $backStep = STEP_MYSQL;
            $receiveMethod = SetupInput('receive_method', 'pipe');
            $sendMethod = SetupInput('send_method', 'php');
            SetupRenderTemplate('email-config', compact('receiveMethod', 'sendMethod'));
        }
    }
} elseif ($step === STEP_CHECK_EMAIL) {
    $receiveMethod = SetupInput('receive_method', 'pipe');
    $sendMethod = SetupInput('send_method', 'php');
    $pop3Ok = $receiveMethod !== 'pop3' || CheckPOP3Login(
        SetupInput('pop3_host'), SetupInput('pop3_user'), SetupInput('pop3_pass'),
        (int)SetupInput('pop3_port', '110'), SetupInput('pop3_tls') !== ''
    );
    $sendmailPath = SetupInput('sendmail_path', '/usr/sbin/sendmail');
    $sendmailOk = $sendMethod !== 'sendmail' || (is_file($sendmailPath) && is_executable($sendmailPath));
    if (!$pop3Ok || !$sendmailOk) {
        $nextStep = STEP_CHECK_MYSQL;
        $backStep = STEP_MYSQL;
        $errorMessage = !$pop3Ok ? $lang_setup['emailcfgpop3fail_text'] : $lang_setup['emailcfgsmfail_text'];
        SetupRenderTemplate('email-error', ['errorMessage' => $errorMessage]);
    } else {
        $nextStep = STEP_INSTALL;
        $backStep = STEP_CHECK_MYSQL;
        if (SetupInput('adminpw') === '') {
            $_POST['adminpw'] = $_POST['adminpw2'] = GeneratePW();
        }
        if (SetupInput('adminuser') === '') {
            $_SESSION['setup_data']['adminuser'] = 'admin';
        }
        if (SetupInput('url') === '') {
            $_SESSION['setup_data']['url'] = SetupDetectSelfUrl();
        }
        SetupRenderTemplate('admin-details');
    }
} elseif ($step === STEP_INSTALL) {
    $adminPlain = SetupInput('adminpw');
    $adminPlain2 = SetupInput('adminpw2');
    $adminUser = SetupNormalizeAdminUser(SetupInput('adminuser', 'admin'));
    if (strlen($adminPlain) < 8) {
        $nextStep = STEP_CHECK_EMAIL;
        $backStep = STEP_CHECK_MYSQL;
        SetupRenderTemplate('install-error', ['errorMessage' => $lang_setup['pw_too_short']]);
    } elseif ($adminPlain !== $adminPlain2) {
        $nextStep = STEP_CHECK_EMAIL;
        $backStep = STEP_CHECK_MYSQL;
        SetupRenderTemplate('install-error', ['errorMessage' => $lang_setup['password_mismatch']]);
    } else {
        try {
            $installer = new SetupInstaller([
                'mysql_host' => SetupInput('mysql_host'),
                'mysql_user' => SetupInput('mysql_user'),
                'mysql_pass' => SetupInput('mysql_pass'),
                'mysql_db' => SetupInput('mysql_db'),
                'mysql_prefix' => SetupInput('mysql_prefix', SETUP_DEFAULT_PREFIX),
                'adminuser' => $adminUser,
                'adminpw' => $adminPlain,
                'adminpw2' => $adminPlain2,
                'domains' => SetupInput('domains', "example.com\nexample.net\nexample.org"),
                'url' => SetupInput('url', SetupDetectSelfUrl()),
                'setup_mode' => SetupInput('setup_mode', 'public'),
                'receive_method' => SetupInput('receive_method', 'pipe'),
                'send_method' => SetupInput('send_method', 'php'),
                'smtp_host' => SetupInput('smtp_host', 'localhost'),
                'smtp_port' => SetupInput('smtp_port', '25'),
                'smtp_auth' => SetupInput('smtp_auth', 'no'),
                'smtp_user' => SetupInput('smtp_user'),
                'smtp_pass' => SetupInput('smtp_pass'),
                'pop3_host' => SetupInput('pop3_host'),
                'pop3_port' => SetupInput('pop3_port', '110'),
                'pop3_user' => SetupInput('pop3_user'),
                'pop3_pass' => SetupInput('pop3_pass'),
                'sendmail_path' => SetupInput('sendmail_path', '/usr/sbin/sendmail'),
                'lng' => $lang,
            ]);
            $result = $installer->install();
            $lockOk = SetupWriteLock('lock');
            unset($_SESSION['setup_data']);
            SetupRenderTemplate('install-result', compact('result', 'lockOk'));
        } catch (Throwable $e) {
            $nextStep = STEP_CHECK_EMAIL;
            $backStep = STEP_CHECK_MYSQL;
            SetupRenderTemplate('install-error', ['errorMessage' => $e->getMessage()]);
        }
    }
}

pageFooter();
