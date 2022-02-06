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

include('../serverlib/init.inc.php');

/**
 * delete no redirect cookie, if exists
 */
if(isset($_COOKIE['noMobileRedirect']))
{
	setcookie('noMobileRedirect', false, time()-TIME_ONE_HOUR, '/');
	unset($_COOKIE['noMobileRedirect']);
}

/**
 * default action = login
 */
if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'login';

/**
 * login
 */
if($_REQUEST['action'] == 'login')
{
	if(isset($_REQUEST['do']) && $_REQUEST['do']=='login')
	{
		// get login
		$password 	= $_REQUEST['password'];
		$email 		= $_REQUEST['email'];

		// login
		list($result, $param) = BMUser::Login($email, $password);

		// login ok?
		if($result == USER_OK)
		{
			// allowed to access mobile interface?
			$user = _new('BMUser', array(BMUser::GetID($email)));
			$group = $user->GetGroup();
			if($group->_row['wap'] != 'yes')
			{
				$tpl->assign('msg', 		$lang_user['mobiledenied']);
				$tpl->assign('backLink',	'./');
				$tpl->assign('page',		'm/message.tpl');
				$tpl->assign('pageTitle', 	$bm_prefs['titel']);
				$tpl->display('m/index.tpl');
				exit();
			}

			// stats
			Add2Stat('mobile_login');

			// save login?
			if(isset($_POST['savelogin']))
			{
				// set cookies
				setcookie('bm_msavedUser', 		$email, 		time() + TIME_ONE_YEAR);
				setcookie('bm_msavedPassword', 	$password, 		time() + TIME_ONE_YEAR);
			}
			else
			{
				// delete cookies
				setcookie('bm_msavedUser', 		'', 			time() - TIME_ONE_HOUR);
				setcookie('bm_msavedPassword', 	'', 			time() - TIME_ONE_HOUR);
			}

			// redirect to target page
			header('Location: email.php?sid=' . $param);
			exit();
		}
		else
		{
			// tell user what happened
			switch($result)
			{
			case USER_BAD_PASSWORD:
				$tpl->assign('msg',	sprintf($lang_user['badlogin'], $param));
				break;
			case USER_DOES_NOT_EXIST:
				$tpl->assign('msg', $lang_user['baduser']);
				break;
			case USER_LOCKED:
				$tpl->assign('msg', $lang_user['userlocked']);
				break;
			case USER_LOGIN_BLOCK:
				$tpl->assign('msg', sprintf($lang_user['loginblocked'], FormatDate($param)));
				break;
			}
			$tpl->assign('backLink',	'./');
			$tpl->assign('page',		'm/message.tpl');
		}
	}
	else
	{
		$tpl->assign('page', 'm/login.tpl');
	}

	// assign
	$tpl->assign('pageTitle', $bm_prefs['titel']);
	$tpl->display('m/index.tpl');
}
?>