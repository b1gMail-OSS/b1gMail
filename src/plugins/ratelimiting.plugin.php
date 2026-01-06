<?php
/*
 * b1gMail rate limiting plugin
 * (c) 2025 Patrick Schlangen et al
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
class RateLimitingPlugin extends BMPlugin
{
    public function __construct()
    {
        $this->type             = BMPLUGIN_DEFAULT;
        $this->name             = 'Rate Limiting Plugin';
        $this->author           = 'b1gMail Project';
        $this->version          = '1.0.0';

        $this->admin_pages      = true;
        $this->admin_page_title = 'Rate Limiting';

        $this->types            = [];
    }

    private function getBucket($timestamp)
    {
        return floor($timestamp / 10);
    }

    private function getIp()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    private function recordEvent($type)
    {
        global $db;

        $ip = $this->getIp();
        $bucket = $this->getBucket(time());

        $db->Query('INSERT INTO {pre}mod_ratelimiting_events(`ip`, `type`, `bucket`, `count`) VALUES(?, ?, ?, ?) ON DUPLICATE KEY UPDATE `count`=`count`+1',
            $ip,
            $type,
            $bucket,
            1);
    }

    private function checkLimit($type, $maxEvents, $inSeconds)
    {
        global $db;

        $ip = $this->getIp();
        $startBucket = $this->getBucket(time() - $inSeconds);

        $res = $db->Query('SELECT SUM(`count`) FROM {pre}mod_ratelimiting_events WHERE `ip`=? AND `type`=? AND `bucket`>=?',
            $ip,
            $type,
            $startBucket);
        while ($row = $res->FetchArray(MYSQLI_NUM))
        {
            return $row[0] < $maxEvents;
        }
        $res->Free();

        return true;
    }

    private function enforceLimit($type)
    {
        $typePrefs = $this->types[$type];

        if (!$this->checkLimit($type, $typePrefs['max_events'], $typePrefs['in_seconds']))
        {
            PutLog(sprintf('Request of type <%s> from <%s> blocked by rate limiting plugin',
                $type, $this->getIp()),
                PRIO_PLUGIN,
                __FILE__,
                __LINE__);

            http_response_code(429);
            die('Too many requests');
        }
        else
        {
            $this->recordEvent($type);
        }
    }

    public function AfterInit()
    {
        global $db;

        $this->types = [];

        $res = $db->Query('SELECT `type`, `max_events`, `in_seconds` FROM {pre}mod_ratelimiting_types');
        while ($row = $res->FetchArray(MYSQLI_ASSOC))
        {
            $this->types[$row['type']] = [
                'type'          => $row['type'],
                'max_events'    => intval($row['max_events']),
                'in_seconds'    => intval($row['in_seconds'])
            ];
        }
        $res->Free();
    }

    public function OnCron()
    {
        global $db;

        $startBucket = $this->getBucket(time() - 86400);

        $db->Query('DELETE FROM {pre}mod_ratelimiting_events WHERE `bucket`<?',
            $startBucket);
    }

    public function Install()
    {
        global $db;

        $db->Query('CREATE TABLE IF NOT EXISTS {pre}mod_ratelimiting_events('
            . '  `ip` varchar(64) NOT NULL,'
            . '  `type` varchar(32) NOT NULL,'
            . '  `bucket` int(11) NOT NULL,'
            . '  `count` int(11) NOT NULL,'
            . '  PRIMARY KEY(`ip`, `type`, `bucket`),'
            . '  KEY(`bucket`)'
            . ')');
        $db->Query('CREATE TABLE IF NOT EXISTS {pre}mod_ratelimiting_types('
            . '  `type` varchar(32) NOT NULL,'
            . '  `max_events` int(11) NOT NULL,'
            . '  `in_seconds` int(11) NOT NULL,'
            . '  PRIMARY KEY(`type`)'
            . ')');

        $db->Query('INSERT IGNORE INTO {pre}mod_ratelimiting_types(`type`, `max_events`, `in_seconds`) VALUES(?, ?, ?)',
            'login',
            10,
            120);

        return true;
    }

    public function Uninstall()
    {
        global $db;

        $db->Query('DROP TABLE IF EXISTS {pre}mod_ratelimiting_events');

        return true;
    }

    public function OnLoginAttempt($email)
    {
        $this->enforceLimit('login');
    }

    public function OnReadLang(&$lang_user, &$lang_client, &$lang_custom, &$lang_admin, $lang)
    {
        if ($lang === 'deutsch')
        {
            $lang_admin['ratelimiting_event'] = 'Ereignis';
            $lang_admin['ratelimiting_max_events'] = 'Maximale Anzahl';
            $lang_admin['ratelimiting_in_seconds'] = 'In Zeitraum (Sekunden)';
        }
        else
        {
            $lang_admin['ratelimiting_event'] = 'Event';
            $lang_admin['ratelimiting_max_events'] = 'Maximum count';
            $lang_admin['ratelimiting_in_seconds'] = 'In timeframe (seconds)';
        }
    }

    function AdminHandler()
    {
        global $tpl, $lang_admin, $db;

        if (!isset($_REQUEST['action']))
        {
            $_REQUEST['action'] = 'types';
        }

        $tabs = [
            0 => [
                'title'     => 'Rate Limiting',
                'link'      => $this->_adminLink() . '&',
                'active'    => $_REQUEST['action'] == 'types',
                'icon'      => 'templates/images/abuse32.png',
            ]
        ];

        $tpl->assign('tabHeaderText',	'Rate Limiting');
        $tpl->assign('tabs', 			$tabs);
        $tpl->assign('pageURL', 		$this->_adminLink());

        if ($_REQUEST['action'] === 'types')
        {
            if (isset($_REQUEST['save']) && isset($_POST['types']))
            {
                foreach ($_POST['types'] as $type => $prefs)
                {
                    $db->Query('UPDATE {pre}mod_ratelimiting_types SET `max_events`=?, `in_seconds`=? WHERE `type`=?',
                        $prefs['max_events'],
                        $prefs['in_seconds'],
                        $type);
                }

                $this->AfterInit();
            }

            $tpl->assign('types', $this->types);
            $tpl->assign('page', $this->_templatePath('ratelimiting.types.tpl'));
        }
    }
}

$plugins->registerPlugin('RateLimitingPlugin');
