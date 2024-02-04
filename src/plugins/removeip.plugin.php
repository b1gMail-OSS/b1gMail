<?php
/*
 * b1gMail removeIP plugin
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
class RemoveIPPlugin extends BMPlugin 
{
	public function __construct()
	{
		$this->type				= BMPLUGIN_DEFAULT;
		$this->name				= 'RemoveIP Plugin';
		$this->author			= 'b1gMail Project';
		$this->version			= '1.0.1';
	}
	
	public function AfterInit()
	{
		global $bm_prefs;
		
		// set IP to 0.0.0.0
		$_SERVER['REMOTE_ADDR'] = '0.0.0.0';
		
		// disable reg IP lock
		$bm_prefs['reg_iplock']	= 0;
		// disable IP lock
		$bm_prefs['ip_lock']	= 0;
		// disable write X-Sender IP (> 7.4)
		$bm_prefs['write_xsenderip'] = 'no';
	}
}

$plugins->registerPlugin('RemoveIPPlugin');