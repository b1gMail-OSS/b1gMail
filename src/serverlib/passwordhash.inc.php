<?php
/*
 * b1gMail password hashing policy (user + admin)
 */

/**
 * @return void
 */
function EnsurePasswordHashPrefColumns()
{
	global $db;

	$columns = array(
		'pw_hash_li_algo'     => "enum('bcrypt','argon2id') NOT NULL DEFAULT 'bcrypt'",
		'pw_hash_li_cost'     => 'int(11) NOT NULL DEFAULT 12',
		'pw_hash_admin_algo'  => "enum('bcrypt','argon2id') NOT NULL DEFAULT 'bcrypt'",
		'pw_hash_admin_cost'  => 'int(11) NOT NULL DEFAULT 12',
	);

	foreach($columns as $column => $definition)
	{
		$res = $db->Query('SHOW COLUMNS FROM {pre}prefs LIKE ?', $column);
		$exists = $res->RowCount() > 0;
		$res->Free();

		if(!$exists)
			$db->Query('ALTER TABLE {pre}prefs ADD COLUMN `' . $column . '` ' . $definition);
	}
}

/**
 * @return void
 */
function PasswordHashApplyPrefDefaults()
{
	global $bm_prefs;

	if(!isset($bm_prefs['pw_hash_li_algo']) || !PasswordHashAlgoIsValid($bm_prefs['pw_hash_li_algo']))
		$bm_prefs['pw_hash_li_algo'] = 'bcrypt';
	if(!isset($bm_prefs['pw_hash_admin_algo']) || !PasswordHashAlgoIsValid($bm_prefs['pw_hash_admin_algo']))
		$bm_prefs['pw_hash_admin_algo'] = 'bcrypt';

	$bm_prefs['pw_hash_li_cost'] = PasswordHashNormalizeCost(
		isset($bm_prefs['pw_hash_li_cost']) ? $bm_prefs['pw_hash_li_cost'] : 12,
		$bm_prefs['pw_hash_li_algo']);
	$bm_prefs['pw_hash_admin_cost'] = PasswordHashNormalizeCost(
		isset($bm_prefs['pw_hash_admin_cost']) ? $bm_prefs['pw_hash_admin_cost'] : 12,
		$bm_prefs['pw_hash_admin_algo']);
}

/**
 * @param string $algo
 * @return bool
 */
function PasswordHashAlgoIsValid($algo)
{
	return $algo === 'bcrypt' || $algo === 'argon2id';
}

/**
 * @return bool
 */
function PasswordHashArgon2Available()
{
	return defined('PASSWORD_ARGON2ID');
}

/**
 * @param string $context 'li' or 'admin'
 * @return string bcrypt|argon2id
 */
function PasswordHashPrefAlgo($context)
{
	global $bm_prefs;

	$key = ($context === 'admin') ? 'pw_hash_admin_algo' : 'pw_hash_li_algo';
	$algo = isset($bm_prefs[$key]) ? $bm_prefs[$key] : 'bcrypt';

	if($algo === 'argon2id' && !PasswordHashArgon2Available())
		return 'bcrypt';

	if(!PasswordHashAlgoIsValid($algo))
		return 'bcrypt';

	return $algo;
}

/**
 * @param string $context
 * @return int
 */
function PasswordHashPrefCost($context)
{
	global $bm_prefs;

	$key = ($context === 'admin') ? 'pw_hash_admin_cost' : 'pw_hash_li_cost';
	$algo = PasswordHashPrefAlgo($context);

	return PasswordHashNormalizeCost(isset($bm_prefs[$key]) ? $bm_prefs[$key] : 12, $algo);
}

/**
 * @param int|string $cost
 * @param string     $algo
 * @return int
 */
function PasswordHashNormalizeCost($cost, $algo)
{
	$cost = (int)$cost;

	if($algo === 'argon2id')
		return max(2, min(6, $cost > 0 ? $cost : 4));

	return max(10, min(15, $cost > 0 ? $cost : 12));
}

/**
 * @param string $context li|admin
 * @return array{algo:int,options:array}
 */
function PasswordHashParams($context)
{
	$algoName = PasswordHashPrefAlgo($context);
	$cost = PasswordHashPrefCost($context);

	if($algoName === 'argon2id')
	{
		return array(
			'algo'    => PASSWORD_ARGON2ID,
			'options' => array(
				'memory_cost' => 65536,
				'time_cost'   => $cost,
				'threads'     => 1,
			),
		);
	}

	return array(
		'algo'    => PASSWORD_BCRYPT,
		'options' => array('cost' => $cost),
	);
}

/**
 * @param string $plain
 * @param string $context li|admin
 * @return string
 */
function PasswordHashCreate($plain, $context = 'li')
{
	$params = PasswordHashParams($context);

	return password_hash((string)$plain, $params['algo'], $params['options']);
}

/**
 * @param string $hash
 * @return bool
 */
function PasswordHashIsModern($hash)
{
	return is_string($hash) && strlen($hash) >= 4 && $hash[0] === '$';
}

/**
 * @param string $hash
 * @param string $context li|admin
 * @return bool
 */
function PasswordHashNeedsUpgrade($hash, $context = 'li')
{
	if(!PasswordHashIsModern($hash))
		return true;

	$params = PasswordHashParams($context);

	return password_needs_rehash($hash, $params['algo'], $params['options']);
}

/**
 * @param int    $userID
 * @param string $passwordPlain
 * @param array  $row
 * @return void
 */
function PasswordHashUpgradeUser($userID, $passwordPlain, $row)
{
	global $db;

	if(!is_array($row) || !isset($row['passwort']))
		return;

	if(!PasswordHashNeedsUpgrade($row['passwort'], 'li'))
		return;

	$passwordPlain = CharsetDecode($passwordPlain, false, 'ISO-8859-15');
	$newHash = PasswordHashCreate($passwordPlain, 'li');

	$db->Query('UPDATE {pre}users SET passwort=?, passwort_salt=? WHERE id=?',
		$newHash,
		'',
		(int)$userID);
	if(function_exists('ClientApiRememberPassword'))
		ClientApiRememberPassword((int)$userID, $passwordPlain);
}

/**
 * @param int    $adminID
 * @param string $passwordPlain
 * @param string $currentHash
 * @return string|false New hash or false if unchanged
 */
function PasswordHashUpgradeAdmin($adminID, $passwordPlain, $currentHash)
{
	global $db;

	if(!PasswordHashNeedsUpgrade($currentHash, 'admin'))
		return false;

	$newHash = PasswordHashCreate($passwordPlain, 'admin');
	$db->Query('UPDATE {pre}admins SET `password`=?,`password_salt`=? WHERE `adminid`=?',
		$newHash,
		'',
		(int)$adminID);

	return $newHash;
}

/**
 * @param int    $userID
 * @param string $passwordPlain
 * @return void
 */
function PasswordHashSetUserPassword($userID, $passwordPlain)
{
	global $db;

	$passwordPlain = CharsetDecode($passwordPlain, false, 'ISO-8859-15');
	$newHash = PasswordHashCreate($passwordPlain, 'li');

	$db->Query('UPDATE {pre}users SET passwort=?, passwort_salt=? WHERE id=?',
		$newHash,
		'',
		(int)$userID);
	if(function_exists('ClientApiRememberPassword'))
		ClientApiRememberPassword((int)$userID, $passwordPlain);
}

/**
 * @param int    $adminID
 * @param string $passwordPlain
 * @return string
 */
function PasswordHashSetAdminPassword($adminID, $passwordPlain)
{
	global $db;

	$newHash = PasswordHashCreate($passwordPlain, 'admin');
	$db->Query('UPDATE {pre}admins SET `password`=?,`password_salt`=? WHERE `adminid`=?',
		$newHash,
		'',
		(int)$adminID);

	return $newHash;
}

/**
 * @return array
 */
function PasswordHashAdminAlgoChoices()
{
	$choices = array('bcrypt' => 'bcrypt');
	if(PasswordHashArgon2Available())
		$choices['argon2id'] = 'Argon2id';

	return $choices;
}

/**
 * Widen pw_reset_key and add pw_reset_expires (idempotent).
 *
 * @return void
 */
function EnsurePwResetColumns()
{
	global $db;

	$res = $db->Query('SHOW COLUMNS FROM {pre}users LIKE ?', 'pw_reset_expires');
	$exists = $res->RowCount() > 0;
	$res->Free();
	if(!$exists)
		$db->Query('ALTER TABLE {pre}users ADD COLUMN `pw_reset_expires` int(11) NOT NULL DEFAULT 0');

	$res = $db->Query('SHOW COLUMNS FROM {pre}users LIKE ?', 'pw_reset_key');
	$col = $res->FetchArray(MYSQLI_ASSOC);
	$res->Free();
	$type = (isset($col['Type']) && is_string($col['Type'])) ? strtolower($col['Type']) : '';
	if($type === '' || strpos($type, 'varchar(64)') === false)
	{
		if($col)
			$db->Query('ALTER TABLE {pre}users MODIFY COLUMN `pw_reset_key` varchar(64) NOT NULL DEFAULT \'\'');
		else
			$db->Query('ALTER TABLE {pre}users ADD COLUMN `pw_reset_key` varchar(64) NOT NULL DEFAULT \'\'');

		$db->Query('UPDATE {pre}users SET pw_reset_new=?,pw_reset_key=?,pw_reset_expires=? WHERE pw_reset_key!=?',
			'',
			'',
			0,
			'');
	}

	$hasIndex = false;
	$res = $db->Query('SHOW INDEX FROM {pre}users');
	while($row = $res->FetchArray(MYSQLI_ASSOC))
	{
		if(isset($row['Column_name']) && $row['Column_name'] === 'pw_reset_key')
		{
			$hasIndex = true;
			break;
		}
	}
	$res->Free();
	if(!$hasIndex)
		$db->Query('ALTER TABLE {pre}users ADD INDEX `pw_reset_key` (`pw_reset_key`)');
}
