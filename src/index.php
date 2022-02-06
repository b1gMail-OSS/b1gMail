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

include('./serverlib/init.inc.php');

/**
 * languages
 */
$availableLanguages = GetAvailableLanguages();
$tpl->assign('languageList', $availableLanguages);

$tpl->assign('ssl_url',				$bm_prefs['ssl_url']);
$tpl->assign('ssl_login_enable',	$bm_prefs['ssl_login_enable'] == 'yes');
$tpl->assign('ssl_login_option',	$bm_prefs['ssl_login_option'] == 'yes');
$tpl->assign('ssl_signup_enable',	$bm_prefs['ssl_signup_enable'] == 'yes');
$tpl->assign('domain_combobox',		$bm_prefs['domain_combobox'] == 'yes');
$tpl->assign('domainList', 			GetDomainList('login'));
$tpl->assign('timezone',			date('Z'));
$tpl->assign('year',				date('Y'));
$tpl->assign('mobileURL',			$bm_prefs['mobile_url']);

/**
 * file handler for modules
 */
ModuleFunction('FileHandler',
	array(substr(__FILE__, strlen(__DIR__)+1),
	isset($_REQUEST['action']) ? $_REQUEST['action'] : ''));

/**
 * default action = login
 */
if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'login';

/**
 * mobile redirection?
 */
$nonMobileActions = array('codegen', 'checkAddressAvailability', 'resetPassword', 'forgetCookie', 'confirmAlias', 'readCertMail', 'completeAddressBookEntry', 'activateAccount', 'showAddressSugestions', 'initiateSession');
if($bm_prefs['redirect_mobile'] == 'yes'
	&& IsMobileUserAgent()
	&& !isset($_COOKIE['noMobileRedirect'])
	&& !in_array($_REQUEST['action'], $nonMobileActions))
{
	header('Location: ' . $bm_prefs['mobile_url']);
	exit();
}

/**
 * terms of service
 */
if($_REQUEST['action'] == 'tos')
{
	// terms of service
	$tpl->assign('pageTitle', $lang_user['tos']);
	$tpl->assign('tos', nl2br(HTMLFormat($lang_custom['tos'])));
	$tpl->assign('page', 'nli/tos.tpl');
}

/**
 * imprint
 */
else if($_REQUEST['action'] == 'imprint')
{
	if($bm_prefs['contactform'] == 'yes')
	{
		if(!class_exists('BMCaptcha'))
			include(B1GMAIL_DIR . 'serverlib/captcha.class.php');
		$captcha = BMCaptcha::createDefaultProvider();

		if(isset($_POST['do']) && $_POST['do'] == 'submitContactForm')
		{
			$invalidFields = array();
			$errorMsg = $lang_user['checkfields'];
			$subject = 'b1gMail ' . $lang_user['contactform'];

			if($bm_prefs['contactform_name'] == 'yes')
			{
				$name = trim($_POST['name']);
				if(strlen($name) < 2)
					$invalidFields[] = 'name';
			}
			else
				$name = '';

			if($bm_prefs['contactform_subject'] == 'yes')
			{
				$subject = $_POST['subject'] . ' (' . $subject . ')';
				if(trim($_POST['subject']) == '')
					$invalidFields[] = 'subject';
			}

			$email = ExtractMailAddress(trim($_POST['email']));
			if(strlen($email) < 5)
				$invalidFields[] = 'email';

			$text = trim($_POST['text']);
			if(strlen($text) < 5)
				$invalidFields[] = 'text';

			if(!$captcha->check())
			{
				$invalidFields[] = 'safecode';
				$errorMsg .= ' ' . $lang_user['invalidcode'];
			}

			if(count($invalidFields) == 0)
			{
				if($name)
					$from = sprintf('"%s" <%s>', str_replace(array("\r", "\n", "\t", "\""), '', $name), $email);
				else
					$from = $email;

				if(!class_exists('BMMailBuilder'))
					include(B1GMAIL_DIR . 'serverlib/mailbuilder.class.php');

				$mail = _new('BMMailBuilder', array(true));
				$mail->SetUserID(USERID_SYSTEM);
				$mail->AddHeaderField('From',				$from);
				$mail->AddHeaderField('To',					$bm_prefs['contactform_to']);
				$mail->AddHeaderField('Subject',			$subject);
				if($bm_prefs['write_xsenderip'] == 'yes')
					$mail->AddHeaderField('X-Sender-IP',	$_SERVER['REMOTE_ADDR']);
				$mail->AddText($text, 'plain', $currentCharset);
				$result = $mail->Send() !== false;
				$mail->CleanUp();

				if($result)
				{
					$tpl->assign('success', true);
				}
				else
				{
					$tpl->assign('errorMsg', $lang_user['cform_senderror']);
				}
			}
			else
			{
				$tpl->assign('invalidFields', $invalidFields);
				$tpl->assign('errorMsg', $errorMsg);
			}
		}

		$tpl->assign('captchaHTML', $captcha->getHTML());
		$tpl->assign('captchaInfo', $captcha->getInfo());
	}

	$tpl->assign('contactform', $bm_prefs['contactform'] == 'yes');
	$tpl->assign('contactform_name', $bm_prefs['contactform_name'] == 'yes');
	$tpl->assign('contactform_subject', $bm_prefs['contactform_subject'] == 'yes');
	$tpl->assign('contactform_subjects', array_filter(array_map('trim', explode("\n", $lang_custom['contact_subjects']))));
	$tpl->assign('pageTitle', $lang_user['contact']);
	$tpl->assign('imprint', $lang_custom['imprint']);
	$tpl->assign('page', 'nli/imprint.tpl');
}

/**
 * faq
 */
else if($_REQUEST['action'] == 'faq')
{
	// faq
	$faq = array();
	$res = $db->Query('SELECT id,frage,antwort FROM {pre}faq WHERE (lang=? OR lang=?) AND (typ=? OR typ=?) ORDER BY frage ASC',
		':all:',
		$currentLanguage,
		'both',
		'nli');
	while($row = $res->FetchArray(MYSQLI_ASSOC))
	{
		$answer = $row['antwort'];
		$answer = str_replace('%%hostname%%', $_SERVER['HTTP_HOST'], $answer);
		$answer = str_replace('%%selfurl%%', $bm_prefs['selfurl'], $answer);

		array_push($faq, array(
			'question'		=> $row['frage'],
			'answer'		=> $answer
		));
	}
	$res->Free();

	$tpl->assign('pageTitle', $lang_user['faq']);
	$tpl->assign('faq', $faq);
	$tpl->assign('page', 'nli/faq.tpl');
}

/**
 * sign up
 */
else if($_REQUEST['action'] == 'signup')
{
	$tpl->assign('pageTitle', $lang_user['signup']);

	// sign up ip lock?
	if($bm_prefs['regenabled'] == 'yes'
		&& ($bm_prefs['user_count_limit'] == 0 || BMUser::GetUserCount() < $bm_prefs['user_count_limit']))
	{
		$res = $db->Query('SELECT COUNT(*) FROM {pre}users WHERE reg_ip=? AND reg_date>?',
			$_SERVER['REMOTE_ADDR'],
			time()-$bm_prefs['reg_iplock']);
		$row = $res->FetchArray();
		$res->Free();

 		// dnsbl check
		$isInDNSBL = false;
		if($row[0] == 0 && $bm_prefs['signup_dnsbl_enable'] == 'yes' && $bm_prefs['signup_dnsbl'] != '')
		{
			$reverseIP = implode('.', array_reverse(explode('.', $_SERVER['REMOTE_ADDR'])));
			$dnsblLists = explode(':', $bm_prefs['signup_dnsbl']);
			foreach($dnsblLists as $dnsblHostname)
			{
				if(strpos($dnsblHostname, '.') === false)
					continue;

				$lookup = $reverseIP . '.' . strtolower($dnsblHostname);
				if(substr($lookup, -1) != '.')
					$lookup .= '.';

				if(@gethostbyname($lookup) != $lookup)
				{
					$isInDNSBL = true;

					PutLog(sprintf('User IP <%s> is in DNSBL <%s>',
						$_SERVER['REMOTE_ADDR'],
						$dnsblHostname),
						PRIO_DEBUG,
						__FILE__,
						__LINE__);

					break;
				}
			}
		}

		if($row[0] != 0)
		{
			// block sign up
			$tpl->assign('msg', $lang_user['reglock']);
			$tpl->assign('page', 'nli/regdone.tpl');
		}
		else if($isInDNSBL && $bm_prefs['signup_dnsbl_action'] == 'block')
		{
			// block sign up
			$tpl->assign('msg', $lang_user['reglockdnsbl']);
			$tpl->assign('page', 'nli/regdone.tpl');
		}
		else
		{
			$showForm = true;

			$captcha = false;
			if($bm_prefs['f_safecode'] == 'p')
			{
				if(!class_exists('BMCaptcha'))
					include(B1GMAIL_DIR . 'serverlib/captcha.class.php');
				$captcha = BMCaptcha::createDefaultProvider();
			}

			if(isset($_POST['do']) && $_POST['do']=='createAccount')
			{
				$showForm = false;
				$invalidFields = array();
				$errorInfo = '';

				//
				// check fields
				//

				// email domain
				$suEMailDomain = trim(EncodeDomain($_POST['email_domain']));
				if(!in_array($suEMailDomain, GetDomainList('signup')))
					$invalidFields[] = 'email_domain';

				// email
				$suEMailLocal = trim($_POST['email_local']);
				$suEMail = $suEMailLocal . '@' . $suEMailDomain;
				if(!BMUser::AddressValid($suEMail) || !BMUser::AddressAvailable($suEMail)
					|| BMUser::AddressLocked($suEMailLocal)
					|| strlen($suEMailLocal) < $bm_prefs['minuserlength'])
					$invalidFields[] = 'email_local';

				// first name
				$suFirstname = trim($_POST['firstname']);
				if(strlen($suFirstname) < 2)
					$invalidFields[] = 'firstname';

				// last name
				$suSurname = trim($_POST['surname']);
				if(strlen($suSurname) < 2)
					$invalidFields[] = 'surname';

				// salutation
				if($bm_prefs['f_anrede'] != 'n')
				{
					$suSalutation = trim($_POST['salutation']);
					if((!in_array($suSalutation, array('herr', 'frau')) && $bm_prefs['f_anrede'] == 'p')
					   || ($bm_prefs['f_anrede'] == 'v' && !in_array($suSalutation, array('herr', 'frau', ''))))
						$invalidFields[] = 'salutation';
				}
				else
					$suSalutation = '';

				// 'strasse'-group
				if($bm_prefs['f_strasse'] != 'n')
				{
					// street
					$suStreet = trim($_POST['street']);
					if((strlen($suStreet) < 3) && (strlen($suStreet) > 0 || $bm_prefs['f_strasse'] == 'p'))
						$invalidFields[] = 'street';

					// no
					$suNo = trim($_POST['no']);
					if((strlen($suNo) < 1) && (strlen($suNo) > 0 || $bm_prefs['f_strasse'] == 'p'))
						$invalidFields[] = 'no';

					// zip
					$suZIP = trim($_POST['zip']);
					if((strlen($suZIP) < 3) && (strlen($suZIP) > 0 || $bm_prefs['f_strasse'] == 'p'))
						$invalidFields[] = 'zip';

					// city
					$suCity = trim($_POST['city']);
					if((strlen($suCity) < 3) && (strlen($suCity) > 0 || $bm_prefs['f_strasse'] == 'p'))
						$invalidFields[] = 'city';

					// country
					$suCountry = (int)$_POST['country'];
					if($bm_prefs['f_strasse'] == 'p' && !in_array($suCountry, array_keys(CountryList())))
						$invalidFields[] = 'country';

					// zip/city check?
					if(!in_array('zip', $invalidFields)
						&& !in_array('city', $invalidFields)
						&& !in_array('country', $invalidFields)
						&& $bm_prefs['plz_check'] == 'yes'
						&& !ZIPCheck($suZIP, $suCity, $suCountry))
					{
						$invalidFields[] = 'zip';
						$invalidFields[] = 'city';
						$errorInfo .= ' ' . $lang_user['plzerror'];
					}
				}
				else if($bm_prefs['f_strasse'] == 'n')
				{
					$suStreet = $suNo = $suZIP = $suCity = '';
					$suCountry = $bm_prefs['std_land'];
				}

				// 'telefon'-field
				if($bm_prefs['f_telefon'] != 'n')
				{
					$suPhone = trim($_POST['phone']);
					if((strlen($suPhone) < 5) && (strlen($suPhone) > 0 || $bm_prefs['f_telefon'] == 'p'))
						$invalidFields[] = 'phone';
				}
				else if($bm_prefs['f_telefon'] == 'n')
				{
					$suPhone = '';
				}

				// safecode
				if($bm_prefs['f_safecode'] == 'p' && !$captcha->check())
				{
					$invalidFields[] = 'safecode';
				}

				// 'fax'-field
				if($bm_prefs['f_fax'] != 'n')
				{
					$suFax = trim($_POST['fax']);
					if((strlen($suFax) < 5) && (strlen($suFax) > 0 || $bm_prefs['f_fax'] == 'p'))
						$invalidFields[] = 'fax';
				}
				else if($bm_prefs['f_fax'] == 'n')
				{
					$suFax = '';
				}

				// 'altmail'-field
				if($bm_prefs['f_alternativ'] != 'n'
					|| $bm_prefs['reg_validation'] == 'email')
				{
					$suAltMail = trim($_POST['altmail']);
					if((strlen($suAltMail) > 0 || $bm_prefs['f_alternativ'] == 'p' || $bm_prefs['reg_validation'] == 'email')
					   && (strtolower(trim($suAltMail)) == strtolower(trim($suEMail)) || !BMUser::AddressValid($suAltMail, false) || AltMailLocked($suAltMail) || ($bm_prefs['alt_check'] == 'yes' && !ValidateMailAddress($suAltMail))))
					{
						$invalidFields[] = 'altmail';

						if(AltMailLocked($suAltMail))
							$errorInfo .= ' ' . $lang_user['altmaillocked'];
					}
					else if($bm_prefs['check_double_altmail'] == 'yes'
							&& strlen($suAltMail) > 0)
					{
						$res = $db->Query('SELECT COUNT(*) FROM {pre}users WHERE `altmail`=?',
										  $suAltMail);
						list($altMailCount) = $res->FetchArray(MYSQLI_NUM);
						$res->Free();

						if($altMailCount > 0)
						{
							$invalidFields[] = 'altmail';
							$errorInfo .= ' ' . $lang_user['doublealtmail'];
						}
					}
				}
				else if($bm_prefs['f_alternativ'] == 'n')
				{
					$suAltMail = '';
				}

				// 'mail2sms_nummer'-field
				if($bm_prefs['f_mail2sms_nummer'] != 'n'
					|| $bm_prefs['reg_validation'] == 'sms')
				{
					$suMobileNr = trim(preg_replace('/[^0-9]/', '', str_replace('+', '00', $_POST['mail2sms_nummer'])));
					if((strlen($suMobileNr) < 6) && (strlen($suMobileNr) > 0 || $bm_prefs['f_mail2sms_nummer'] == 'p'
						|| $bm_prefs['reg_validation'] == 'sms'))
					{
						$invalidFields[] = 'mail2sms_nummer';
					}
					else if($bm_prefs['check_double_cellphone'] == 'yes'
							&& strlen($suMobileNr) > 0)
					{
						$res = $db->Query('SELECT COUNT(*) FROM {pre}users WHERE `mail2sms_nummer`=?',
										  $suMobileNr);
						list($mobileNrCount) = $res->FetchArray(MYSQLI_NUM);
						$res->Free();

						if($mobileNrCount > 0)
						{
							$invalidFields[] = 'mail2sms_nummer';
							$errorInfo .= ' ' . $lang_user['doublecellphone'];
						}
					}
				}
				else if($bm_prefs['f_mail2sms_nummer'] == 'n')
				{
					$suMobileNr = '';
				}

				// password
				$suPass1 = CharsetDecode($_POST['pass1'], false, 'ISO-8859-15');
				$suPass2 = CharsetDecode($_POST['pass2'], false, 'ISO-8859-15');
				if(strlen($suPass1) < $bm_prefs['min_pass_length'] || $suPass1 != $suPass2 || $suPass1 == $suEMailLocal)
				{
					$invalidFields[] = 'pass1';
					$invalidFields[] = 'pass2';
					$errorInfo .= ' ' . sprintf($lang_user['pwerror'], $bm_prefs['min_pass_length']);
				}

				// coupon
				$suCoupon = isset($_POST['code']) ? trim($_POST['code']) : '';
				if($suCoupon != '' && !BMUser::CouponValid($suCoupon, 'signup'))
				{
					$invalidFields[] = 'code';
					$errorInfo .= ' ' . $lang_user['signupcouponerror'];
				}

				// tos
				if(!isset($_POST['tos']) || $_POST['tos'] != 'true')
				{
					$invalidFields[] = 'tos';
					$errorInfo .= ' ' . $lang_user['toserror'];
				}

				// profile fields
				$dateFields = array();
				$suProfile = array();
				$res = $db->Query("SELECT id,rule,pflicht,typ FROM {pre}profilfelder WHERE show_signup='yes'");
				while($row = $res->FetchArray())
				{
					$feld_ok = false;
					$feld_name = 'field_' . $row['id'];
					switch($row['typ'])
					{
					case FIELD_CHECKBOX:
						$feld_ok = true;
						$suProfile[$row['id']] = isset($_POST[$feld_name]);
						break;
					case FIELD_DROPDOWN:
						$feld_ok = true;
						if($feld_ok)
							$suProfile[$row['id']] = $_POST[$feld_name];
						break;
					case FIELD_RADIO:
						$feld_ok = isset($_POST[$feld_name]);
						if($feld_ok)
							$suProfile[$row['id']] = $_POST[$feld_name];
						break;
					case FIELD_TEXT:
						$feld_ok = (trim($row['rule']) == '') || (preg_match('/'.$row['rule'].'/', $_POST[$feld_name]));
						if(isset($_POST[$feld_name]))
							$suProfile[$row['id']] = $_POST[$feld_name];
						break;
					case FIELD_DATE:
						$feld_ok = !empty($_POST[$feld_name.'Day'])
									&& !empty($_POST[$feld_name.'Month'])
									&& !empty($_POST[$feld_name.'Year'])
									&& $_POST[$feld_name.'Day'] != '--'
									&& $_POST[$feld_name.'Month'] != '--'
									&& $_POST[$feld_name.'Year'] != '--'
									&& CheckDateValidity(mktime(0, 0, 0, $_POST[$feld_name.'Month'], $_POST[$feld_name.'Day'], $_POST[$feld_name.'Year']), $row['rule']);
						if($feld_ok)
						{
							$suProfile[$row['id']] = sprintf('%04d-%02d-%02d',
								$_POST[$feld_name.'Year'],
								$_POST[$feld_name.'Month'],
								$_POST[$feld_name.'Day']);
							$dateFields[$feld_name] = $suProfile[$row['id']];
						}
						break;
					}
					if(($row['pflicht']=='yes' || (isset($_POST[$feld_name]) && strlen($_POST[$feld_name]) > 0)) && (!$feld_ok))
					{
						if($row['typ'] != FIELD_DATE)
						{
							$invalidFields[] = $feld_name;
						}
						else
						{
							$invalidFields[] = $feld_name . 'Day';
							$invalidFields[] = $feld_name . 'Month';
							$invalidFields[] = $feld_name . 'Year';
						}
					}
				}
				$res->Free();
				$tpl->assign('dateFields', $dateFields);

				// go on
				if(count($invalidFields) > 0)
				{
					// errors => mark fields red and show form again
					$showForm = true;
					$tpl->assign('errorStep', true);
					$tpl->assign('errorInfo', $lang_user['checkfields'] . $errorInfo);
					$tpl->assign('invalidFields', $invalidFields);
				}
				else
				{
					// create account
					$userId = BMUser::CreateAccount($suEMail,
						$suFirstname,
						$suSurname,
						$suStreet,
						$suNo,
						$suZIP,
						$suCity,
						$suCountry,
						$suPhone,
						$suFax,
						$suAltMail,
						$suMobileNr,
						$suPass1,
						$suProfile,
						true,
						'',
						$suSalutation,
						$isInDNSBL);

					// successful?
					if($userId !== false && $userId > 0)
					{
						// dnsbl log message?
						if($isInDNSBL)
						{
							PutLog(sprintf('New user <%s> (%d) locked because IP <%s> was found in DNSBL',
								$suEMail,
								$userId,
								$_SERVER['REMOTE_ADDR']),
								PRIO_NOTE,
								__FILE__,
								__LINE__);
						}

						// redeem coupon?
						if($suCoupon != '')
						{
							$theNewUser = _new('BMUser', array($userId));
							$theNewUser->RedeemCoupon($suCoupon, 'signup');
						}

						// account created
						Add2Stat('signup');
						$showForm = false;
						$tpl->assign('msg', 			sprintf($bm_prefs['usr_status'] == 'locked'
															? $lang_user['regdonelocked']
															: $lang_user['regdone'], $suEMail));
						$tpl->assign('page', 			'nli/regdone.tpl');


						// module handler
						ModuleFunction('AfterSuccessfulSignup', array($userId, $suEMail));
					}
					else
					{
						// error occured
						$showForm = true;
						$tpl->assign('errorStep', 		true);
						$tpl->assign('errorInfo', 		$lang_user['regerror']);
						$tpl->assign('invalidFields', 	array());
					}
				}
			}

			if($showForm)
			{
				// codes?
				$res = $db->Query('SELECT COUNT(*) FROM {pre}codes WHERE `valid_signup`=\'yes\'');
				$row = $res->FetchArray(MYSQLI_NUM);
				$res->Free();
				$tpl->assign('code', $row[0] > 0);

				// safe code
				if($captcha !== false)
				{
					$tpl->assign('captchaHTML',	 	$captcha->getHTML());
					$tpl->assign('captchaInfo', 	$captcha->getInfo());
				}

				// profile fields?
				$profilfelder = array();
				$res = $db->Query('SELECT feld,pflicht,id,extra,typ FROM {pre}profilfelder WHERE show_signup=\'yes\'');
				while($row = $res->FetchArray())
				{
					array_push($profilfelder, array(
						'feld'			=> $row['feld'],
						'pflicht'		=> $row['pflicht']=='yes',
						'id'			=> $row['id'],
						'extra'			=> explode(',', $row['extra']),
						'typ'			=> $row['typ']
					));
				}
				$res->Free();
				if(count($profilfelder) > 0)
					$tpl->assign('profilfelder', $profilfelder);

				// required fields
				$tpl->assign('f_anrede', 			$bm_prefs['f_anrede']);
				$tpl->assign('f_strasse', 			$bm_prefs['f_strasse']);
				$tpl->assign('f_telefon', 			$bm_prefs['f_telefon']);
				$tpl->assign('f_fax', 				$bm_prefs['f_fax']);
				$tpl->assign('f_alternativ',	 	$bm_prefs['reg_validation'] == 'email' ? 'p' : $bm_prefs['f_alternativ']);
				$tpl->assign('f_mail2sms_nummer', 	$bm_prefs['reg_validation'] == 'sms' ? 'p' : $bm_prefs['f_mail2sms_nummer']);
				$tpl->assign('f_safecode', 			$captcha !== false && $captcha->isAvailable() ? $bm_prefs['f_safecode'] : 'n');

				// show page
				$tpl->assign('signupSuggestions',	$bm_prefs['signup_suggestions']=='yes');
				$tpl->assign('countryList',			CountryList());
				$tpl->assign('defaultCountry',		$bm_prefs['std_land']);
				$tpl->assign('tos',					HTMLFormat($lang_custom['tos']));
				$tpl->assign('tos_html',			nl2br(HTMLFormat($lang_custom['tos'])));
				$tpl->assign('domainListSignup',	GetDomainList('signup'));
				$tpl->assign('minPassLength',		$bm_prefs['min_pass_length']);
				$tpl->assign('minPassText',			sprintf($lang_user['minchars'], $bm_prefs['min_pass_length']));
				$tpl->assign('page', 				'nli/signup.tpl');
			}
		}
	}
	else
	{
		// sign up disabled
		$tpl->assign('msg', $lang_user['regdisabled']);
		$tpl->assign('page', 'nli/regdone.tpl');
	}
}

/**
 * safe code image dump
 */
else if($_REQUEST['action'] == 'codegen')
{
	if(!class_exists('BMCaptcha'))
		include(B1GMAIL_DIR . 'serverlib/captcha.class.php');
	$captcha = BMCaptcha::createDefaultProvider();
	$captcha->generate();
	exit();
}

/**
 * address availability check (RPC)
 */
else if($_REQUEST['action'] == 'checkAddressAvailability')
{
	if(!isset($_GET['address']))
		exit();

	$address = EncodeEMail($_GET['address']);

	// check address availability
	$result = BMUser::AddressValid($address) ? 1 : 2;

	if($result == 1)
	{
		list($localPart) = explode('@', $address);
		if(strlen(trim($localPart)) < $bm_prefs['minuserlength']
			|| BMUser::AddressLocked($localPart))
			$result = 0;
	}

	if($result == 1)
		$result = BMUser::AddressAvailable($address) ? 1 : 0;

	// respond
	$response = array(
		'available'		=> $result
	);

	Array2XML($response);
	exit();
}

/**
 * custom page
 */
else if($_REQUEST['action'] == 'page' && isset($_GET['page']))
{
	$page = preg_replace('/([^a-zA-Z0-9]*)/', '', $_GET['page']);
	$tpl->assign('page', 'custompages/' . $page . '.tpl');
}

/**
 * forget cookies
 */
else if($_REQUEST['action'] == 'forgetCookie')
{
	// delete cookies
	setcookie('bm_savedUser', 		'',		 		time() - TIME_ONE_HOUR);
	setcookie('bm_savedPassword', 	'',		 		time() - TIME_ONE_HOUR);
	setcookie('bm_savedToken', 		'',		 		time() - TIME_ONE_HOUR);

	// reload
	header('Location: index.php');
	exit();
}

/**
 * forgot password
 */
else if($_REQUEST['action'] == 'lostPassword'
		&& ((isset($_REQUEST['email_local'])
				&& isset($_REQUEST['email_domain'])
				&& trim($_REQUEST['email_local']) != '')
			|| (isset($_REQUEST['email_full'])
				&& trim($_REQUEST['email_full']) != '')))
{
	$tpl->assign('pageTitle', $lang_user['lostpw']);

	$userMail = EncodeEMail(isset($_REQUEST['email_full'])
					? trim($_REQUEST['email_full'])
					: trim($_REQUEST['email_local']) . '@' . $_REQUEST['email_domain']);

	if(BMUser::LostPassword($userMail))
	{
		// send PW link
		$tpl->assign('msg', $lang_user['pwresetsuccess']);
	}
	else
	{
		// unknown address
		$tpl->assign('msg', $lang_user['pwresetfailed']);
	}

	$tpl->assign('title', $lang_user['lostpw']);
	$tpl->assign('page', 'nli/msg.tpl');
}

/**
 * reset password
 */
else if($_REQUEST['action'] == 'resetPassword'
		&& isset($_REQUEST['user'])
		&& isset($_REQUEST['key']))
{
	header('Pragma: no-cache');
	header('Cache-Control: no-cache');
	header('X-Robots-Tag: noindex');
	$tpl->assign('robotsNoIndex', true);

	$tpl->assign('pageTitle', $lang_user['lostpw']);

	$userID = (int)$_REQUEST['user'];
	$resetKey = trim($_REQUEST['key']);

	if(BMUser::ResetPassword($userID, $resetKey))
	{
		// delete cookies
		setcookie('bm_savedUser', 		'',		 		time() - TIME_ONE_HOUR);
		setcookie('bm_savedToken', 		'',		 		time() - TIME_ONE_HOUR);
		setcookie('bm_savedPassword', 	'',		 		time() - TIME_ONE_HOUR);

		// ok
		$tpl->assign('msg', $lang_user['pwresetsuccess2']);
	}
	else
	{
		// invalid id/key
		$tpl->assign('msg', $lang_user['pwresetfailed2']);
	}

	$tpl->assign('title', $lang_user['lostpw']);
	$tpl->assign('page', 'nli/msg.tpl');
}

/**
 * confirm alias
 */
else if($_REQUEST['action'] == 'confirmAlias'
		&& isset($_REQUEST['id'])
		&& isset($_REQUEST['code']))
{
	header('Pragma: no-cache');
	header('Cache-Control: no-cache');
	header('X-Robots-Tag: noindex');
	$tpl->assign('robotsNoIndex', true);

	$tpl->assign('pageTitle', $lang_user['confirmaliastitle']);

	if(BMUser::ConfirmAlias((int)$_REQUEST['id'], $_REQUEST['code']))
		$tpl->assign('msg', $lang_user['confirmaliasok']);
	else
		$tpl->assign('msg', $lang_user['confirmaliaserr']);

	$tpl->assign('title', $lang_user['confirmaliastitle']);
	$tpl->assign('page', 'nli/msg.tpl');
}

/**
 * read cert mail
 */
else if($_REQUEST['action'] == 'readCertMail'
		&& isset($_REQUEST['id'])
		&& isset($_REQUEST['key']))
{
	header('Pragma: no-cache');
	header('Cache-Control: no-cache');
	header('X-Robots-Tag: noindex');
	$tpl->assign('robotsNoIndex', true);

	$tpl->assign('pageTitle', $lang_user['certmail']);

	$id = (int)$_REQUEST['id'];
	$key = trim($_REQUEST['key']);

	if(!class_exists('BMMailbox'))
		include('./serverlib/mailbox.class.php');

	$mail = BMMailbox::GetCertMail($id, $key);

	if($mail)
	{
		// get text part
		$textParts = $mail->GetTextParts();
		if(isset($textParts['html']))
		{
			$textMode = 'html';
			$text = $textParts['html'];
		}
		else if(isset($textParts['text']))
		{
			$textMode = 'text';
			$text = formatEMailText($textParts['text']);
		}
		else
		{
			$textMode = 'text';
			$text = '';
		}

		// get attachments
		$attachments = $mail->GetAttachments();

		// show text only?
		if(isset($_REQUEST['showText']))
		{
			if($textMode == 'html')
				$text = '<base target="_blank" /><font face="arial" size="2">' . formatEMailHTMLText(isset($textParts['html']) ? $textParts['html'] : '', true, $attachments, (int)$_REQUEST['id']) . '</font>';
			else
				$text = '<base target="_blank" /><font face="arial" size="2">' . formatEMailText(isset($textParts['text']) ? $textParts['text'] : '') . '</font>';
			echo($text);
			exit();
		}

		// get attachment?
		if(isset($_REQUEST['downloadAttachment']))
		{
			$parts = $mail->GetPartList();
			if(isset($parts[$_REQUEST['attachment']]))
			{
				$part = $parts[$_REQUEST['attachment']];

				header('Pragma: public');
				if(isset($part['charset']) && trim($part['charset']) != '')
					header('Content-Type: ' . $part['content-type'] . '; charset=' . $part['charset']);
				else
					header('Content-Type: ' . $part['content-type']);
				header(sprintf('Content-Disposition: %s; filename="%s"',
							'attachment',
							addslashes($part['filename'])));

				$attData = &$part['body'];
				$attData->Init();
				while($block = $attData->DecodeBlock(PART_CHUNK_SIZE))
				{
					echo $block;
				}
				$attData->Finish();

				exit();
			}
		}

		// assign
		$tpl->assign('mailID',				$id);
		$tpl->assign('key',					$key);
		$tpl->assign('subject',				$mail->GetHeaderValue('subject'));
		$tpl->assign('fromAddresses', 		ParseMailList($mail->GetHeaderValue('from')));
		$tpl->assign('toAddresses', 		ParseMailList($mail->GetHeaderValue('to')));
		$tpl->assign('ccAddresses', 		ParseMailList($mail->GetHeaderValue('cc')));
		$tpl->assign('replyToAddresses',	ParseMailList($mail->GetHeaderValue('reply-to')));
		$tpl->assign('flags', 				$mail->flags);
		$tpl->assign('date',				$mail->date);
		$tpl->assign('priority', 			(int)$mail->priority);
		$tpl->assign('text', 				$text);
		$tpl->assign('textMode', 			$textMode);
		$tpl->assign('attachments', 		$attachments);
		$tpl->assign('page', 				'nli/certmail.read.tpl');
	}
	else
	{
		$tpl->assign('msg', 				$lang_user['certmailerror']);
		$tpl->assign('title', 				$lang_user['certmail']);
		$tpl->assign('page', 				'nli/msg.tpl');
	}
}

/**
 * address book completion
 */
else if($_REQUEST['action'] == 'completeAddressBookEntry'
		&& isset($_REQUEST['contact'])
		&& isset($_REQUEST['key']))
{
	header('Pragma: no-cache');
	header('Cache-Control: no-cache');
	header('X-Robots-Tag: noindex');
	$tpl->assign('robotsNoIndex', true);

	$tpl->assign('pageTitle', $lang_user['addrselfcomplete']);

	$contactID = (int)$_REQUEST['contact'];
	$key = trim($_REQUEST['key']);

	if(!class_exists('BMAddressbook'))
		include('./serverlib/addressbook.class.php');

	$contactData = BMAddressbook::GetContactForSelfCompleteInvitation($contactID, $key);
	if($contactData)
	{
		if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'save')
		{
			// save data
			$book = _new('BMAddressbook', array($contactData['user']));
			$book->Change($contactID,
				$_REQUEST['firma'],
				$contactData['vorname'],
				$contactData['nachname'],
				$_REQUEST['strassenr'],
				$_REQUEST['plz'],
				$_REQUEST['ort'],
				$_REQUEST['land'],
				$_REQUEST['tel'],
				$_REQUEST['fax'],
				$_REQUEST['handy'],
				$_REQUEST['email'],
				$_REQUEST['work_strassenr'],
				$_REQUEST['work_plz'],
				$_REQUEST['work_ort'],
				$_REQUEST['work_land'],
				$_REQUEST['work_tel'],
				$_REQUEST['work_fax'],
				$_REQUEST['work_handy'],
				$_REQUEST['work_email'],
				$_REQUEST['anrede'],
				$_REQUEST['position'],
				$_REQUEST['web'],
				$contactData['kommentar'],
				SmartyDateTime('geburtsdatum_'),
				$contactData['default_address'],
				false);
			$book->InvalidateSelfCompleteInvitation($contactID, $key);

			// send mail
			$userData = BMUser::Fetch($contactData['user']);
			$vars = array(
				'vorname'	=> $contactData['vorname'],
				'nachname'	=> $contactData['nachname']
			);
			SystemMail($bm_prefs['passmail_abs'],
				$userData['email'],
				$lang_custom['selfcomp_n_sub'],
				'selfcomp_n_text',
				$vars);

			// log
			PutLog(sprintf('Address book entry completed after accepting invitation (contact id: %d, key: %s, IP: %s)',
				$contactID,
				$key,
				$_SERVER['REMOTE_ADDR']),
				PRIO_NOTE,
				__FILE__,
				__LINE__);

			$tpl->assign('msg', $lang_user['completeok']);
			$tpl->assign('title', $lang_user['addrselfcomplete']);
			$tpl->assign('page', 'nli/msg.tpl');
		}
		else
		{
			// show form
			$tpl->assign('contact', $contactData);
			$tpl->assign('page', 'nli/contact.complete.tpl');
		}
	}
	else
	{
		$tpl->assign('msg', $lang_user['completeerr']);
		$tpl->assign('title', $lang_user['addrselfcomplete']);
		$tpl->assign('page', 'nli/msg.tpl');
	}
}

/**
 * switch language
 */
else if($_REQUEST['action'] == 'switchLanguage'
		&& isset($_REQUEST['lang']))
{
	if(isset($availableLanguages[$_REQUEST['lang']]))
		setcookie('bm_language', $_REQUEST['lang'], time()+TIME_ONE_YEAR);
	header('Location: index.php' . (isset($_REQUEST['target']) ? '?action=' . urlencode($_REQUEST['target']) : ''));
	exit();
}

/**
 * activate account
 */
else if($_REQUEST['action'] == 'activateAccount'
	&& isset($_REQUEST['id'])
	&& isset($_REQUEST['code']))
{
	header('Pragma: no-cache');
	header('Cache-Control: no-cache');
	header('X-Robots-Tag: noindex');
	$tpl->assign('robotsNoIndex', true);

	if(BMUser::ActivateAccount($_REQUEST['id'], $_REQUEST['code']))
	{
		$tpl->assign('msg', $lang_user['validation_ok']);
	}
	else
	{
		$tpl->assign('msg', $lang_user['validation_err']);
	}

	$tpl->assign('title', $lang_user['smsvalidation']);
	$tpl->assign('page', 'nli/msg.tpl');
}

/**
 * show address suggestions
 */
else if($_REQUEST['action'] == 'showAddressSugestions'
	&& isset($_POST['domain'])
	&& $bm_prefs['signup_suggestions'] == 'yes')
{
	$firstName = strtolower(trim(AjaxCharsetDecode($_POST['firstName'])));
	$lastName = strtolower(trim(AjaxCharsetDecode($_POST['lastName'])));
	$choice = strtolower(trim(AjaxCharsetDecode($_POST['choice'])));
	$domain = EncodeDomain(AjaxCharsetDecode($_POST['domain']));

	$firstName = preg_replace('/[^a-zA-Z0-9]/', '', $firstName);
	$lastName = preg_replace('/[^a-zA-Z0-9]/', '', $lastName);
	$choice = preg_replace('/[^a-zA-Z0-9_\\-\\.]/', '', $choice);

	$suggestions = array();
	if(!empty($choice))
	{
		$suggestions[] = $choice;
	}
	if(!empty($firstName))
	{
		$suggestions[] = $firstName;
	}
	if(!empty($lastName))
	{
		$suggestions[] = $lastName;
	}
	if(!empty($firstName) && !empty($lastName))
	{
		$suggestions[] = $firstName . '.' . $lastName;
		$suggestions[] = $lastName . '.' . $firstName;
		$suggestions[] = $lastName . $firstName;
		$suggestions[] = $firstName . $lastName;
	}
	if(!empty($choice))
	{
		if(!empty($firstName) && $firstName != $choice)
		{
			$suggestions[] = $choice . '.' . $firstName;
			$suggestions[] = $firstName . '.' . $choice;
		}
		if(!empty($lastName) && $lastName != $choice)
		{
			$suggestions[] = $choice . '.' . $lastName;
			$suggestions[] = $lastName . '.' . $choice;
		}
		if(!empty($firstName) && !empty($lastName))
		{
			$suggestions[] = $choice . '.' . $firstName . $lastName;
			$suggestions[] = $choice . '.' . $lastName . $firstName;
			$suggestions[] = $firstName . $lastName . '.' . $choice;
			$suggestions[] = $lastName . $firstName . '.' . $choice;
		}
	}

	$result = array();
	foreach($suggestions as $local)
	{
		if(count($result) >= 5)
			break;

		$addr = $local . '@' . $domain;

		if(!BMUser::AddressValid($addr) || BMUser::AddressLocked($local)
			|| strlen($local) < $bm_prefs['minuserlength'])
			continue;

		if(BMUser::AddressAvailable($addr))
		{
			$result[] = $addr;
			continue;
		}

		$addr = $local . date('Y') . '@' . $domain;
		if(BMUser::AddressAvailable($addr))
		{
			$result[] = $addr;
			continue;
		}

		for($i=0; $i<5; ++$i)
		{
			$addr = $local . mt_rand(1, 10) . '@' . $domain;
			if(BMUser::AddressAvailable($addr))
			{
				$result[] = $addr;
				break;
			}
		}
	}

	if(count($result) == 0)
	{
		echo $lang_user['nosuggestions'];
	}
	else
	{
		$tpl->assign('suggestions', $result);
		$tpl->display('nli/suggestions.tpl');
	}

	exit();
}

/**
 * initiate web session from tool interface
 */
else if($_REQUEST['action'] == 'initiateSession'
	&& isset($_REQUEST['target'])
	&& isset($_REQUEST['sid']))
{
	if(isset($_REQUEST['secret']))
	{
		setcookie('sessionSecret_'.substr($_REQUEST['sid'], 0, 16), $_REQUEST['secret'], 0, '/');
	}

	if($_REQUEST['target'] == 'compose')
		header('Location: email.compose.php?sid='.$_REQUEST['sid']);
	else if($_REQUEST['target'] == 'membership')
		header('Location: prefs.php?sid='.$_REQUEST['sid'].'&action=membership');
	else if($_REQUEST['target'] == 'inbox')
		header('Location: email.php?sid='.$_REQUEST['sid']);
	else if($_REQUEST['target'] == 'webdisk')
		header('Location: webdisk.php?sid='.$_REQUEST['sid']);
	else
		header('Location: start.php?sid='.$_REQUEST['sid']);

	exit();
}

/**
 * login
 */
else
{
	if(isset($_REQUEST['do']) && $_REQUEST['do']=='login')
	{
		// get login
		$password 	= isset($_REQUEST['password']) && !empty($_REQUEST['password'])
						? AjaxCharsetDecode($_REQUEST['password'])
						: (isset($_REQUEST['passwordMD5']) ? $_REQUEST['passwordMD5'] : '');
		$email 		= EncodeEMail(isset($_REQUEST['email_full'])
						? AjaxCharsetDecode($_REQUEST['email_full'])
						: AjaxCharsetDecode($_REQUEST['email_local'] . '@' . $_REQUEST['email_domain']));

		// saved login?
		if($password == '' && isset($_COOKIE['bm_savedToken']))
		{
			$password = BMUser::LoadLogin($_COOKIE['bm_savedToken']);
		}

		// validation
		$requiresValidation	 = BMUser::RequiresValidation($email);
		$ValidationCode	 	= $requiresValidation && isset($_REQUEST['sms_validation_code'])
								? $_REQUEST['sms_validation_code']
								: '';

		// login
		list($result, $param) = BMUser::Login($email, $password, true, true, $ValidationCode);

		// login ok?
		if($result == USER_OK)
		{
			// delete token?
			if(isset($_COOKIE['bm_savedToken']))
				BMUser::DeleteSavedLogin($_COOKIE['bm_savedToken']);

			// stats
			Add2Stat('login');

			// save login?
			if(isset($_POST['savelogin']))
			{
				$cookieToken = BMUser::SaveLogin($password);

				// set cookies
				setcookie('bm_savedUser', 		$email, 		time() + TIME_ONE_YEAR);
				if(isset($_COOKIE['savedPassword']))
					setcookie('bm_savedPassword', 	'',		 	time() - TIME_ONE_HOUR);
				setcookie('bm_savedToken',		$cookieToken,	time() + TIME_ONE_YEAR);
				setcookie('bm_savedSSL',
					isset($_POST['ssl']) ? true : false,
					time() + TIME_ONE_YEAR);
			}
			else
			{
				// delete cookies
				setcookie('bm_savedUser', 		'', 			time() - TIME_ONE_HOUR);
				if(isset($_COOKIE['savedPassword']))
					setcookie('bm_savedPassword', 	'', 		time() - TIME_ONE_HOUR);
				setcookie('bm_savedToken', 		'', 			time() - TIME_ONE_HOUR);
				setcookie('bm_savedSSL', 		'', 			time() - TIME_ONE_HOUR);
			}

			// register timezone
			$_SESSION['bm_timezone'] = isset($_REQUEST['timezone'])
										? (int)$_REQUEST['timezone']
										: date('Z');

			// redirect to target page
			if(isset($_REQUEST['ajax']))
			{
				header('Access-Control-Allow-Origin: *');
				header('Content-Type: application/json');
				printf('{ "action": "redirect", "url" : "start.php?sid=%s" }', $param);
			}
			else if(!isset($_REQUEST['target']))
			{
				header('Location: start.php?sid=' . $param);
			}
			else if($_REQUEST['target'] == 'inbox')
			{
				header('Location: email.php?folder=0&sid=' . $param);
			}
			else if($_REQUEST['target'] == 'compose')
			{
				header('Location: email.compose.php?sid=' . $param
					. (isset($_REQUEST['draft']) && $_REQUEST['draft']!='' ? '&redirect=' . (int)($_REQUEST['draft']) : '')
					. (isset($_REQUEST['to']) && $_REQUEST['to']!='' ? '&to=' . urlencode($_REQUEST['to']) : '')
					. (isset($_REQUEST['cc']) && $_REQUEST['cc']!='' ? '&subject=' . urlencode($_REQUEST['cc']) : '')
					. (isset($_REQUEST['subject']) && $_REQUEST['subject']!='' ? '&subject=' . urlencode($_REQUEST['subject']) : '')
					. (isset($_REQUEST['text']) && $_REQUEST['text']!='' ? '&text=' . urlencode($_REQUEST['text']) : ''));
			}
			else if($_REQUEST['target'] == 'membership')
			{
				header('Location: prefs.php?sid=' . $param . '&action=membership');
			}
			else if($_REQUEST['target'] == 'webdisk')
			{
				header('Location: webdisk.php?sid=' . $param);
			}
			exit();
		}
		else
		{
			// validation input?
			if($result == USER_LOCKED
				&& $requiresValidation)
			{
				if(isset($_REQUEST['ajax']))
				{
					header('Access-Control-Allow-Origin: *');
					header('Content-Type: application/json');
					printf('{ "action": "resubmit" }');
					exit();
				}

				if($bm_prefs['reg_validation_max_resend_times'] > 0)
				{
					$res = $db->Query('SELECT id,sms_validation_last_send,sms_validation_send_times,altmail,mail2sms_nummer,sms_validation_code FROM {pre}users WHERE email=?',
						$email);
					if($res->RowCount() != 1)
						die('User not found');
					$userRow = $res->FetchArray(MYSQLI_ASSOC);
					$res->Free();

					$allowResend = $bm_prefs['reg_validation_max_resend_times'] > $userRow['sms_validation_send_times'];
					$resendTimeLimit = max(0, $bm_prefs['reg_validation_min_resend_interval'] - (time() - $userRow['sms_validation_last_send']));

					$tpl->assign('enableResend',		true);

					if($allowResend && $resendTimeLimit == 0)
					{
						if(isset($_POST['resendCode']))
						{
							if($bm_prefs['reg_validation'] == 'sms' && $userRow['mail2sms_nummer'] != '')
							{
								PutLog(sprintf('Resending signup validation code by SMS to user <%s> (#%d)',
									$email,
									$userRow['id']),
									PRIO_NOTE,
									__FILE__,
									__LINE__);

								if(!class_exists('BMSMS'))
									include(B1GMAIL_DIR . 'serverlib/sms.class.php');

								$smsText = GetPhraseForUser($userRow['id'], 'lang_custom', 'validationsms');
								$smsText = str_replace('%%code%%', $userRow['sms_validation_code'], $smsText);

								$sms = _new('BMSMS', array(0, false));
								$sms->Send($bm_prefs['mail2sms_abs'], preg_replace('/[^0-9]/', '', str_replace('+', '00', $userRow['mail2sms_nummer'])), $smsText, $bm_prefs['smsvalidation_type'], false, false);
							}
							else if($bm_prefs['reg_validation'] == 'email' && $userRow['altmail'] != '')
							{
								PutLog(sprintf('Resending signup validation code by email to user <%s> (#%d)',
									$email,
									$userRow['id']),
									PRIO_NOTE,
									__FILE__,
									__LINE__);

								$vars = array(
									'activationcode'=> $userRow['sms_validation_code'],
									'email'			=> DecodeEMail($email),
									'url'			=> sprintf('%sindex.php?action=activateAccount&id=%d&code=%s',
													$bm_prefs['selfurl'],
													$userRow['id'],
													$userRow['sms_validation_code'])
								);

								SystemMail($bm_prefs['passmail_abs'],
									   $userRow['altmail'],
									   $lang_custom['activationmail_sub'],
									   'activationmail_text',
									   $vars);
							}

							$db->Query('UPDATE {pre}users SET sms_validation_last_send=?,sms_validation_send_times=sms_validation_send_times+1 WHERE id=?',
								time(),
								$userRow['id']);

							$tpl->assign('resendText',		$lang_user['coderesent']);
							$tpl->assign('allowResend',		false);
						}
						else
						{
							$tpl->assign('resendText',		sprintf($lang_user['validation_resend_text'], $bm_prefs['reg_validation_max_resend_times'] - $userRow['sms_validation_send_times']));
							$tpl->assign('allowResend',		true);
						}
					}
					else if(!$allowResend)
					{
						$tpl->assign('resendText',		sprintf($lang_user['validation_count_limit'], $bm_prefs['reg_validation_max_resend_times']));
						$tpl->assign('allowResend',		false);
					}
					else
					{
						$tpl->assign('resendText',		sprintf($lang_user['validation_time_limit'], floor($resendTimeLimit/60), $resendTimeLimit-60*floor($resendTimeLimit/60)));
						$tpl->assign('allowResend',		false);
					}
				}

				$tpl->assign('email',		$email);
				$tpl->assign('password',	strlen($password) == 32 ? $password : md5($password));
				$tpl->assign('savelogin',	isset($_POST['savelogin']));
				$tpl->assign('page',		'nli/login.smsvalidation.tpl');
			}
			else
			{
				// tell user what happened
				$msg = '?';
				switch($result)
				{
				case USER_BAD_PASSWORD:
					$msg = sprintf($lang_user['badlogin'], $param);
					break;
				case USER_DOES_NOT_EXIST:
					$msg = $lang_user['baduser'];
					break;
				case USER_LOCKED:
					$msg = $lang_user['userlocked'];
					break;
				case USER_LOGIN_BLOCK:
					$msg = sprintf($lang_user['loginblocked'], FormatDate($param));
					break;
				}

				if(isset($_REQUEST['ajax']))
				{
					header('Content-Type: application/json');
					printf('{ "action": "msg", "msg" : "%s" }', addslashes($msg));
					exit();
				}
				else
				{
					$tpl->assign('msg',		$msg);
					$tpl->assign('page',	'nli/loginresult.tpl');
				}
			}
		}
	}
	else
	{
		// lost password and no email entered?
		if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'lostPassword')
		{
			$tpl->assign('invalidFields', array('email_local_pw'));
		}
		$tpl->assign('page', 				'nli/login.tpl');
	}
}

// welcome back
if(isset($_COOKIE['bm_savedUser']))
{
	header('Pragma: no-cache');
	header('Cache-Control: no-cache');
	$tpl->assign('welcomeBack', sprintf($lang_user['welcomeback'], $_COOKIE['bm_savedUser']));
}

$tpl->display('nli/index.tpl');
