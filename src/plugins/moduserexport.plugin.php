<?php
/*
 * b1gMail User Export Plugin
 * Copyright (c) 2018-2025, Home of the Sebijk.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * NOTE:
 * starting with Version 1.0.2 the license is changed from LGPL to AGPL.
 */
class moduserexport extends BMPlugin
{
	/*
	* Eigenschaften des Plugins
	*/
	function __construct()
	{
		$this->name					= 'User Export';
		$this->version				= '1.0.2';
		$this->designedfor			= '7.4.2';
		$this->type					= BMPLUGIN_DEFAULT;

		$this->author				= 'Home of the Sebijk.com';
		$this->web					= 'http://www.sebijk.com';
		$this->mail					= 'sebijk@web.de';

		$this->update_url = 'https://service.b1gmail.org/plugin_updates/';

		// group option
		$this->RegisterGroupOption('userexport',
			FIELD_CHECKBOX,
			'User Export?');
	}

	/*
	 * installation routine
	 */
	public function Install()
	{
		global $db;


		PutLog('Plugin "'. $this->name .' - '. $this->version .'" wurde erfolgreich installiert.', PRIO_PLUGIN, __FILE__, __LINE__);
		return(true);
	}

	/*
	 * uninstallation routine
	 */
	public function Uninstall()
	{
		global $db;

		PutLog('Plugin "'. $this->name .' - '. $this->version .'" wurde erfolgreich deinstalliert.', PRIO_PLUGIN, __FILE__, __LINE__);
		return(true);
	}

	/*
	 *  Sprachvariablen
   */
    public function OnReadLang(&$lang_user, &$lang_client, &$lang_custom, &$lang_admin, $lang)
    {
		global $lang_user;
		if(strpos($lang, 'deutsch') !== false) {
				$lang_user['userdata_export'] 			= "Benutzerdaten exportieren";
				$lang_user['prefs_d_userdata_export']	= 'Hier können Sie Ihre Benutzerdaten im JSON-Format herunterladen.';
		}
		else {
				$lang_user['userdata_export'] = "Export User data";
				$lang_user['prefs_d_userdata_export']	= 'Export your user data as JSON Format.';
		}
    }

	/**
	 * User area setup
	 *
	 * @param string $file
	 * @param string $action
	 */
	public function FileHandler($file, $action)
	{
		if($file == 'prefs.php' && $this->GetGroupOptionValue('userexport') ==1)
		{
			$GLOBALS['prefsItems']['userdata_export'] = true;
			$GLOBALS['prefsfaIcons']['userdata_export'] = 'fa-id-card';
		}
	}

	/**
	 * User area
	 *
	 * @param string $action
	 * @return bool
	 */
	public function UserPrefsPageHandler($action)
	{
		global $thisUser, $userRow;

		if($action != 'userdata_export' || $this->GetGroupOptionValue('userexport') ==0)
			return(false);

		$exportuserRow = $userRow;
		// localize in english
		$exportuserRow['firstname'] = $userRow['vorname'];
		$exportuserRow['surname'] = $userRow['nachname'];
		$exportuserRow['surname'] = $userRow['nachname'];
		$exportuserRow['group'] = $userRow['gruppe'];
		$exportuserRow['dateformat'] = $userRow['datumsformat'];
		$exportuserRow['street'] = $userRow['strasse'];
		$exportuserRow['zip'] = $userRow['plz'];
		$exportuserRow['city'] = $userRow['ort'];

		$exportuserRow['emailaliases'] = $thisUser->GetAliases();
		$exportuserRow['signatures'] = $thisUser->GetSignatures();
		$exportuserRow['pop3accounts'] = $thisUser->GetPOP3Accounts();
		$exportuserRow['autoresponders'] = $thisUser->GetAutoresponder();
		$exportuserRow['spamindexsize'] = $thisUser->GetSpamIndexSize();
		$exportuserRow['filters'] = $thisUser->GetFilters('orderpos', 'asc');
		$exportuserRow['generatedAt'] = time();
		$exportuserRow['Generator'] = "b1gMail ".$this->name." ".$this->version;
		
		// remove german phrases
		unset($exportuserRow['vorname']);
		unset($exportuserRow['nachname']);
		unset($exportuserRow['gruppe']);
		unset($exportuserRow['dateformat']);
		unset($exportuserRow['strasse']);
		unset($exportuserRow['plz']);
		unset($exportuserRow['ort']);
		// Remove not relevant infos
		unset($exportuserRow['id']);
		unset($exportuserRow['passwort']);
		unset($exportuserRow['passwort_salt']);
		unset($exportuserRow['sms_validation_code']);
		unset($exportuserRow['pw_reset_new']);
		unset($exportuserRow['pw_reset_key']);
		header('Content-Type: application/json; charset=utf-8');
    	header('Content-Disposition: attachment; filename="userdata_export.json"');
		echo json_encode($exportuserRow, JSON_PRETTY_PRINT);
	}
	
}
/**
 * register plugin
 */
$plugins->registerPlugin('moduserexport');