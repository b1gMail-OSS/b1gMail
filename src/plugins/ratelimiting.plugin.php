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
    /** @var array<string, array{type: string, max_events: int, in_seconds: int}> */
    public $types = [];

    public function __construct()
    {
        $this->type               = BMPLUGIN_DEFAULT;
        $this->name               = 'Rate Limiting Plugin';
        $this->author             = 'b1gMail Project';
        $this->version            = '1.1.0';
        $this->designedfor        = '7.5.0';

        $this->admin_pages        = true;
        $this->admin_page_title   = 'Rate Limiting';
        $this->admin_route_slug   = 'ratelimiting';

        $this->types              = [];
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
            $lang_admin['ratelimiting_event']      = 'Ereignis';
            $lang_admin['ratelimiting_max_events'] = 'Maximale Anzahl';
            $lang_admin['ratelimiting_in_seconds'] = 'In Zeitraum (Sekunden)';
            $lang_admin['ratelimiting_saved']      = 'Die Einstellungen wurden gespeichert.';
        }
        else
        {
            $lang_admin['ratelimiting_event']      = 'Event';
            $lang_admin['ratelimiting_max_events'] = 'Maximum count';
            $lang_admin['ratelimiting_in_seconds'] = 'In timeframe (seconds)';
            $lang_admin['ratelimiting_saved']      = 'Settings have been saved.';
        }
    }

    /**
     * Map pretty URLs and legacy query params to do=types.
     */
    protected function _rlNormalizeRequest()
    {
        $do = $_REQUEST['do'] ?? $_REQUEST['action'] ?? '';

        if ($do === '' || $do === 'types')
            $do = 'types';
        else if ($do === 'save')
        {
            $_REQUEST['save'] = true;
            $do = 'types';
        }

        $_REQUEST['do'] = $do;
    }

    /**
     * Pretty admin URL for this plugin.
     *
     * @param array<string, mixed> $params
     * @param bool                 $trailingAmp
     * @return string
     */
    protected function _rlAdminUrl(array $params = array(), $trailingAmp = true)
    {
        $params = array_merge(array('plugin' => $this->internal_name, 'do' => 'types'), $params);

        if (function_exists('AdminSessionUrl'))
            return AdminSessionUrl('plugin.page.php', $params, $trailingAmp);

        $url = $this->_adminLink();
        unset($params['plugin']);
        foreach ($params as $key => $val)
        {
            if ((string)$val === '')
                continue;
            $url .= '&' . rawurlencode((string)$key) . '=' . rawurlencode((string)$val);
        }
        if ($trailingAmp)
            $url .= (strpos($url, '?') !== false ? '&' : '?');

        return $url;
    }

    /**
     * Save limits (POST + CSRF only).
     */
    protected function _rlHandlePost()
    {
        global $db, $lang_admin;

        if (!isset($_REQUEST['save']) || !isset($_POST['types']) || !is_array($_POST['types']))
            return;

        foreach ($_POST['types'] as $type => $prefs)
        {
            if (!is_string($type) || $type === '' || !isset($this->types[$type]) || !is_array($prefs))
                continue;

            $maxEvents = max(1, (int)($prefs['max_events'] ?? 0));
            $inSeconds = max(1, (int)($prefs['in_seconds'] ?? 0));

            $db->Query('UPDATE {pre}mod_ratelimiting_types SET `max_events`=?, `in_seconds`=? WHERE `type`=?',
                $maxEvents,
                $inSeconds,
                $type);
        }

        $this->AfterInit();

        $_SESSION['ratelimiting_admin_msg'] = array(
            'type' => 'success',
            'text' => $lang_admin['ratelimiting_saved'],
        );

        SessionRedirect($this->_rlAdminUrl(array(), false));
        exit();
    }

    /**
     * @return array{type: string, text: string}|null
     */
    protected function _rlTakeFlashMsg()
    {
        if (!isset($_SESSION['ratelimiting_admin_msg']))
            return null;

        $msg = $_SESSION['ratelimiting_admin_msg'];
        unset($_SESSION['ratelimiting_admin_msg']);

        if (!is_array($msg) || !isset($msg['type'], $msg['text']))
            return null;

        return array(
            'type' => (string)$msg['type'],
            'text' => (string)$msg['text'],
        );
    }

    public function AdminHandler()
    {
        global $tpl, $lang_admin;

        $this->_rlNormalizeRequest();

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST')
            $this->_rlHandlePost();

        $do = $_REQUEST['do'] ?? 'types';

        $tabs = [
            0 => [
                'title'   => $this->admin_page_title,
                'relIcon' => 'abuse32.png',
                'link'   => $this->_rlAdminUrl(array(), true),
                'active' => $do === 'types',
            ],
        ];

        $tpl->assign('tabs', $tabs);
        $tpl->assign('rlPlugin', $this->internal_name);
        $tpl->assign('pageURL', $this->_rlAdminUrl(array(), true));

        if ($do === 'types')
            $this->_rlTypesPage();
    }

    protected function _rlTypesPage()
    {
        global $tpl;

        $tpl->assign('types', $this->types);
        $tpl->assign('rlMsg', $this->_rlTakeFlashMsg());
        $tpl->assign('page', $this->_templatePath('ratelimiting.types.tpl'));
    }
}

$plugins->registerPlugin('RateLimitingPlugin');
