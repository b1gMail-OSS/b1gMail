#!/usr/bin/env php
<?php
/**
 * reset-admin-password.php
 * Reset the Superadmin-Password
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
 * 
 * Usage:
 *   php reset-admin-password.php <username> <new_password>
 * 
 * Example:
 *   php reset-admin-password.php admin new_password_123
 */

define('SRC_DIR', dirname(dirname(dirname(__FILE__))) . '/src/');
// CLI-only
if (php_sapi_name() !== 'cli') {
    die("Can only executed in CLI!\n");
}

echo "\n";
echo "═══════════════════════════════════════\n";
echo "  b1gMail Admin Password Reset\n";
echo "═══════════════════════════════════════\n";
echo "\n";

// Check Arguments
if ($argc < 3) {
    echo "Usage:\n";
    echo "  php reset-admin-password.php <username> <new_password>\n";
    echo "\n";
    echo "Example:\n";
    echo "  php reset-admin-password.php admin my_new_password\n";
    echo "\n";
    exit(1);
}

$username = $argv[1];
$newPassword = $argv[2];

echo "Username:     $username\n";
echo "New Password: " . str_repeat('*', strlen($newPassword)) . "\n";
echo "\n";

// b1gMail init
if (!file_exists(SRC_DIR.'serverlib/init.inc.php')) {
    die("❌ ERROR: b1gMail init.inc.php not found!\n");
}

// Surpress PHP Errors
error_reporting(E_ERROR | E_PARSE | E_DEPRECATED);

define('NO_SESSION', true);
define('STANDALONE_SCRIPT', true);

// Load b1gMail
require_once(SRC_DIR.'serverlib/init.inc.php');

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "1/3  Search Admin\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\n";

// Search Admins
$admin = $db->Query("SELECT * FROM {pre}admins WHERE `username`=? LIMIT 1", $username);

if ($admin->RowCount() == 0) {
    echo "❌ ERROR: Admin '$username' not found!\n";
    echo "\n";
    echo "Available Admins:\n";
    $allAdmins = $db->Query("SELECT `adminid`, `username`, `firstname`, `lastname` FROM {pre}admins ORDER BY adminid");
    while ($row = $allAdmins->FetchArray(MYSQLI_ASSOC)) {
        echo "  - ID {$row['adminid']}: {$row['username']} ({$row['firstname']} {$row['lastname']})\n";
    }
    echo "\n";
    exit(1);
}

$adminData = $admin->FetchArray(MYSQLI_ASSOC);
echo "✅ Admin found:\n";
echo "   ID:       {$adminData['adminid']}\n";
echo "   Username: {$adminData['username']}\n";
echo "   Name:     {$adminData['firstname']} {$adminData['lastname']}\n";
echo "\n";

// 2. Generate Password hash
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "2/3  Generate Password hash\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\n";

// b1gMail admin users use salted MD5 hashes (until 7.4.1)
// Format: MD5(salt + password)
$salt = substr(md5(mt_rand()), 0, 8);
$passwordHash = md5($newPassword.$salt);

echo "Hashmethod: MD5(password+salt)\n";
echo "Salt:         $salt\n";
echo "Hash:         $passwordHash\n";
echo "\n";

// 3. Save Password in Database
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "3/3  Save Password in Database        \n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\n";

$db->Query("UPDATE {pre}admins SET `password`=?, `password_salt`=? WHERE `adminid`=?",
    $passwordHash,
    $salt,
    $adminData['adminid']
);

// Verify
$verify = $db->Query("SELECT `password`, `password_salt` FROM {pre}admins WHERE `adminid`=? LIMIT 1", $adminData['adminid']);
$verifyData = $verify->FetchArray(MYSQLI_ASSOC);

if ($verifyData['password'] === $passwordHash && $verifyData['password_salt'] === $salt) {
    echo "✅ Password updated successfully!\n";
} else {
    echo "❌ ERROR: Password Update failed!\n";
    exit(1);
}

echo "\n";

// Summary
echo "═══════════════════════════════════════\n";
echo "  ✅ PASSWORD SUCCESSFULLY RESETTED\n";
echo "═══════════════════════════════════════\n";
echo "\n";
echo "Login details:\n";
echo "  Username: $username\n";
echo "  Password: $newPassword\n";
echo "\n";
echo "IMPORTANT: Please note that credential details in a secure way!\n";
echo "\n";

exit(0);
