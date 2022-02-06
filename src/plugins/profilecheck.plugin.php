<?php
/*
 * b1gMail profile check plugin
 * (c) 2021 Patrick Schlangen et al
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

if(!defined('FIELD_DATE'))
	define('FIELD_DATE', 32);

/**
 * Profile check plugin
 *
 */
class ProfileCheckPlugin extends BMPlugin
{
	function __construct()
	{
		// plugin info
		$this->type					= BMPLUGIN_DEFAULT;
		$this->name					= 'Profile check';
		$this->author				= 'b1gMail Project';
		$this->web					= 'https://www.b1gmail.org/';
		$this->mail					= 'info@b1gmail.org';
		$this->version				= '1.2';
		$this->update_url			= 'https://service.b1gmail.org/plugin_updates/';
		$this->website				= 'https://www.b1gmail.org/';
	}

	/**
	 * language stuff
	 *
	 */
	function OnReadLang(&$lang_user, &$lang_client, &$lang_custom, &$lang_admin, $lang)
	{
		if($lang == 'deutsch')
		{
			$lang_admin['profilecheck_msg']		= 'Ihr Kontakt-Profil ist derzeit unvollst&auml;ndig.<br />Bitte pr&uuml;fen Sie die mit einem Sternchen (*) markierten Pflichtfelder und speichern Sie das Profil, um Ihren Account wieder nutzen zu k&ouml;nnen.';
		}
		else
		{
			$lang_admin['profilecheck_msg']		= 'Your contact profile is incomplete.<br />Please check the obligatory fields marked with an asterisk (*) and save your profile to use your account again.';
		}
	}

	/**
	 * user page handler
	 *
	 */
	function FileHandler($file, $action)
	{
		global $userRow, $tpl, $lang_admin, $s_loggedin;

		if(!isset($userRow) || !is_array($userRow))
			return(false);

		if(isset($_SESSION['ProfileCheckPlugin_profileChecked'])
		   && $_SESSION['ProfileCheckPlugin_profileChecked'])
			return(false);

		$file = strtolower($file);
		$action = strtolower($action);

		$invalidFields = $this->_checkProfile();

		if(count($invalidFields) == 0)
		{
			$_SESSION['ProfileCheckPlugin_profileChecked'] = true;
			return(false);
		}

		if($file != 'index.php' && !($file == 'prefs.php' && $action == 'contact')
								&& !($file == 'start.php' && $action == 'logout')
								&& !($file == 'start.php' && $action == 'usersearch'))
		{
			header('Location: prefs.php?action=contact&sid=' . session_id());
			exit();
		}

		else if($file == 'start.php' && $action == 'usersearch')
		{
			exit();
		}

		else if($file == 'prefs.php' && $action == 'contact' && !isset($_REQUEST['do']))
		{
			$tpl->assign('errorStep', 		true);
			$tpl->assign('errorInfo', 		$lang_admin['profilecheck_msg']);
			$tpl->assign('invalidFields', 	$invalidFields);
		}
	}

	/**
	 * check profile
	 *
	 * @return array Invalid fields
	 */
	function _checkProfile()
	{
		global $userRow, $bm_prefs, $db;

		$invalidFields = array();

		if(!isset($userRow) || !is_array($userRow))
			return(true);

		// name
		if(strlen(trim($userRow['vorname'])) < 2 || strlen(trim($userRow['nachname'])) < 2)
		{
			$invalidFields[] = 'vorname';
			$invalidFields[] = 'nachname';
		}

		// anrede
		if(isset($bm_prefs['f_anrede']) && $bm_prefs['f_anrede'] == 'p'
		   && !in_array($userRow['anrede'], array('herr', 'frau')))
		{
			$invalidFields[] = 'salutation';
		}

		// 'strasse' group
		if($bm_prefs['f_strasse'] == 'p')
		{
			if(strlen(trim($userRow['strasse'])) < 3)
			{
				$invalidFields[] = 'strasse';
			}

			if(strlen(trim($userRow['hnr'])) < 1)
			{
				$invalidFields[] = 'hnr';
			}

			if(strlen(trim($userRow['plz'])) < 3)
			{
				$invalidFields[] = 'plz';
			}

			if(strlen(trim($userRow['ort'])) < 3)
			{
				$invalidFields[] = 'ort';
			}
		}

		// mail2sms_nummer
		if($bm_prefs['f_mail2sms_nummer'] == 'p'
		   && strlen(trim($userRow['mail2sms_nummer'])) < 6)
		{
			$invalidFields[] = 'mail2sms_nummer';
		}

		// telefon
		if($bm_prefs['f_telefon'] == 'p'
		   && strlen(trim($userRow['tel'])) < 5)
		{
			$invalidFields[] = 'tel';
		}

		// fax
		if($bm_prefs['f_fax'] == 'p'
		   && strlen(trim($userRow['fax'])) < 5)
		{
			$invalidFields[] = 'fax';
		}

		// altmail
		if($bm_prefs['f_alternativ'] == 'p'
		   && strlen(trim($userRow['altmail'])) < 5)
		{
			$invalidFields[] = 'altmail';
		}

		// profile fields
		$profileFields = @unserialize($userRow['profilfelder']);

		if(!is_array($profileFields))
			$profileFields = array();
		$res = $db->Query('SELECT id,rule,typ FROM {pre}profilfelder WHERE pflicht=? AND typ IN ?',
						  'yes',
						  array(FIELD_RADIO, FIELD_TEXT, FIELD_DATE));
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			if($row['typ'] == FIELD_RADIO)
			{
				if(!isset($profileFields[$row['id']]))
				{
					$invalidFields[] = 'field_' . $row['id'];
				}
			}

			else if($row['typ'] == FIELD_TEXT)
			{
				if(!isset($profileFields[$row['id']])
				   || !preg_match('/'.$row['rule'].'/', $profileFields[$row['id']]))
				{
					$invalidFields[] = 'field_' . $row['id'];
				}
			}

			else if($row['typ'] == FIELD_DATE)
			{
				if(!isset($profileFields[$row['id']])
				   || empty($profileFields[$row['id']]))
				{
					$invalidFields[] = 'field_' . $row['id'] . 'Day';
					$invalidFields[] = 'field_' . $row['id'] . 'Month';
					$invalidFields[] = 'field_' . $row['id'] . 'Year';
				}
			}
		}
		$res->Free();

		return($invalidFields);
	}
}

/**
 * register plugin
 */
$plugins->registerPlugin('ProfileCheckPlugin');
