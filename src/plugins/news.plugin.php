<?php
/*
 * b1gMail news plugin
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

/**
 * News plugin.
 */
class NewsPlugin extends BMPlugin
{
    /** @var string Pretty-URL: /admin/plugin/newsplugin/ */
    public $admin_route_slug = 'newsplugin';

    public function __construct()
    {
        // plugin info
        $this->type = BMPLUGIN_DEFAULT;
        $this->name = 'News';
        $this->author = 'b1gMail Project';
        $this->mail = 'info@b1gmail.org';
        $this->version = '1.8';
        $this->designedfor = '7.5.0';
        $this->update_url = 'https://service.b1gmail.org/plugin_updates/';
        $this->website = 'https://www.b1gmail.org/';

        $this->admin_pages = true;
        $this->admin_page_title = 'News';
        $this->admin_page_icon = 'news_icon.png';
    }

    public function RegisterRoutes()
    {
        BMRoute()->nli('news', array('action' => 'newsPlugin'));
        BMRoute()->liStart('newsPlugin', 'start/news');
    }

    public function OnReadLang(&$lang_user, &$lang_client, &$lang_custom, &$lang_admin, $lang)
    {
        if ($lang == 'deutsch') {
            $lang_admin['news_news'] = 'News';
            $lang_admin['news_addnews'] = 'News hinzuf&uuml;gen';

            $lang_user['news_news'] = 'News';
            $lang_user['news_nonews'] = 'Es konnten keine News gefunden werden.';
            $lang_user['news_text'] = 'Hier finden Sie aktuelle Informationen und Ank&uuml;ndigungen rund um unserem Dienst.';
        } else {
            $lang_admin['news_news'] = 'News';
            $lang_admin['news_addnews'] = 'Add news';

            $lang_user['news_news'] = 'News';
            $lang_user['news_nonews'] = 'Could not find any news.';
            $lang_user['news_text'] = 'Here you can find current information and announcements regarding our service.';
        }
    }

    public function Install()
    {
        global $db;

        // db struct
        $databaseStructure =
              'YToxOntzOjk6ImJtNjBfbmV3cyI7YToyOntzOjY6ImZpZWxkcyI7YTo2OntpOjA7YTo2OntpOjA'
            .'7czo2OiJuZXdzaWQiO2k6MTtzOjc6ImludCgxMSkiO2k6MjtzOjI6Ik5PIjtpOjM7czozOiJQUk'
            .'kiO2k6NDtOO2k6NTtzOjE0OiJhdXRvX2luY3JlbWVudCI7fWk6MTthOjY6e2k6MDtzOjQ6ImRhd'
            .'GUiO2k6MTtzOjc6ImludCgxMSkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAi'
            .'O2k6NTtzOjA6IiI7fWk6MjthOjY6e2k6MDtzOjU6InRpdGxlIjtpOjE7czoxMjoidmFyY2hhcig'
            .'xMjgpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czowOiIiO2k6NTtzOjA6IiI7fWk6Mz'
            .'thOjY6e2k6MDtzOjQ6InRleHQiO2k6MTtzOjQ6InRleHQiO2k6MjtzOjI6Ik5PIjtpOjM7czowO'
            .'iIiO2k6NDtzOjA6IiI7aTo1O3M6MDoiIjt9aTo0O2E6Njp7aTowO3M6ODoibG9nZ2VkaW4iO2k6'
            .'MTtzOjIzOiJlbnVtKCdsaScsJ25saScsJ2JvdGgnKSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI'
            .'7aTo0O3M6NDoiYm90aCI7aTo1O3M6MDoiIjt9aTo1O2E6Njp7aTowO3M6NjoiZ3JvdXBzIjtpOj'
            .'E7czoxMToidmFyY2hhcig2NCkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IioiO'
            .'2k6NTtzOjA6IiI7fX1zOjc6ImluZGV4ZXMiO2E6MTp7czo3OiJQUklNQVJZIjthOjE6e2k6MDtz'
            .'OjY6Im5ld3NpZCI7fX19fQ==';
        $databaseStructure = unserialize(base64_decode($databaseStructure));

        // sync struct
        SyncDBStruct($databaseStructure);

        // log
        PutLog(sprintf('%s v%s installed',
            $this->name,
            $this->version),
            PRIO_PLUGIN,
            __FILE__,
            __LINE__);

        return true;
    }

    /**
     * Pretty-URL path segment (do) → internal action=news + optional sub-do.
     */
    protected function _newsNormalizeRequest()
    {
        if (!isset($_REQUEST['action']) || $_REQUEST['action'] === '') {
            $_REQUEST['action'] = 'news';
        }

        $pathDo = isset($_REQUEST['do']) ? strtolower((string) $_REQUEST['do']) : '';

        if ($pathDo === 'news') {
            $_REQUEST['action'] = 'news';
            unset($_REQUEST['do']);
        } elseif ($pathDo === 'edit') {
            $_REQUEST['action'] = 'news';
        }

        if (isset($_REQUEST['action']) && $_REQUEST['action'] !== '') {
            $_REQUEST['action'] = strtolower((string) $_REQUEST['action']);
        }

        if (isset($_REQUEST['do']) && $_REQUEST['do'] !== '') {
            $_REQUEST['do'] = strtolower((string) $_REQUEST['do']);
        }
    }

    /**
     * @return string
     */
    protected function _newsTabLink()
    {
        return $this->_newsAdminUrl(null, array(), true);
    }

    /**
     * @param string|null          $subDo edit, …
     * @param array<string, mixed> $query
     * @param bool                 $trailingAmp
     * @return string
     */
    protected function _newsAdminUrl($subDo = null, array $query = array(), $trailingAmp = true)
    {
        $params = array_merge(array('plugin' => $this->internal_name), $query);

        if ($subDo !== null && $subDo !== '') {
            $params['do'] = $subDo;
        }

        if (function_exists('AdminSessionUrl')) {
            return AdminSessionUrl('plugin.page.php', $params, $trailingAmp);
        }

        $url = $this->_adminLink();
        unset($params['plugin']);
        foreach ($params as $key => $val) {
            if ((string) $val === '') {
                continue;
            }
            $url .= '&' . rawurlencode((string) $key) . '=' . rawurlencode((string) $val);
        }
        if ($trailingAmp) {
            $url .= (strpos($url, '?') !== false ? '&' : '?');
        }

        return $url;
    }

    /**
     * Legacy GET mutations (ohne CSRF) → Redirect, keine Aktion.
     */
    protected function _newsRedirectLegacyGet()
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
            return;
        }

        if (isset($_REQUEST['delete']) || isset($_REQUEST['add'])) {
            SessionRedirect($this->_newsAdminUrl(null, array(), false));
            exit();
        }
    }

    /**
     * POST: add / delete / save (CSRF via {csrffield} in templates).
     */
    protected function _newsHandlePost()
    {
        global $db;

        if (($_REQUEST['action'] ?? '') !== 'news') {
            return;
        }

        if (isset($_POST['delete'])) {
            $db->Query('DELETE FROM {pre}news WHERE `newsid`=?',
                (int) $_POST['delete']);
            SessionRedirect($this->_newsAdminUrl(null, array(), false));
            exit();
        }

        if (isset($_POST['add'])) {
            if (isset($_POST['all_groups'])) {
                $groups = '*';
            } else {
                $groups = implode(',', is_array($_POST['groups'] ?? null) ? $_POST['groups'] : array());
            }
            $db->Query('INSERT INTO {pre}news(`title`,`date`,`loggedin`,`groups`,`text`) VALUES(?,?,?,?,?)',
                $_POST['title'] ?? '',
                time(),
                $_POST['loggedin'] ?? 'nli',
                $groups,
                $_POST['text'] ?? '');
            SessionRedirect($this->_newsAdminUrl(null, array(), false));
            exit();
        }

        if (($_REQUEST['do'] ?? '') === 'edit' && isset($_POST['save']) && isset($_REQUEST['id'])) {
            if (isset($_POST['all_groups'])) {
                $groups = '*';
            } else {
                $groups = implode(',', is_array($_POST['groups'] ?? null) ? $_POST['groups'] : array());
            }
            $db->Query('UPDATE {pre}news SET `title`=?,`loggedin`=?,`groups`=?,`text`=? WHERE `newsid`=?',
                $_POST['title'] ?? '',
                $_POST['loggedin'] ?? 'nli',
                $groups,
                $_POST['text'] ?? '',
                (int) $_REQUEST['id']);
            SessionRedirect($this->_newsAdminUrl(null, array(), false));
            exit();
        }
    }

    public function AdminHandler()
    {
        global $tpl, $bm_prefs, $lang_admin, $db;

        $this->_newsNormalizeRequest();
        $this->_newsRedirectLegacyGet();

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $this->_newsHandlePost();
        }

        $action = $_REQUEST['action'] ?? 'news';

        $tabs = [
            0 => [
                'title' => $lang_admin['news_news'],
                'link' => $this->_newsTabLink(),
                'icon' => '../plugins/templates/images/news_add.png',
                'active' => $action === 'news',
            ],
        ];

        $tpl->assign('tabs', $tabs);
        $tpl->assign('newsPlugin', $this->internal_name);
        $tpl->assign('pageURL', $this->_newsAdminUrl(null, array(), true));

        if ($action == 'news') {
            if (!isset($_REQUEST['do'])) {
                $news = array();
                $res = $db->Query('SELECT `newsid`,`title`,`date`,`loggedin` FROM {pre}news ORDER BY `newsid` DESC');
                while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
                    $row['editUrl'] = $this->_newsAdminUrl('edit', array('id' => $row['newsid']), true);
                    $news[$row['newsid']] = $row;
                }
                $res->Free();

                $tpl->assign('usertpldir', B1GMAIL_REL.'templates/'.$bm_prefs['template'].'/');
                $tpl->assign('groups', BMGroup::GetSimpleGroupList());
                $tpl->assign('news', $news);
                $tpl->assign('page', $this->_templatePath('news.admin.tpl'));
            } elseif ($_REQUEST['do'] == 'edit'
                && isset($_REQUEST['id'])) {
                $news = array();
                $res = $db->Query('SELECT `newsid`,`title`,`text`,`loggedin`,`groups` FROM {pre}news WHERE `newsid`=?',
                    (int) $_REQUEST['id']);
                if ($res->RowCount() != 1) {
                    exit();
                }
                $news = $res->FetchArray();
                $res->Free();

                $groups = BMGroup::GetSimpleGroupList();
                if ($news['groups'] != '*') {
                    $newsGroups = explode(',', $news['groups']);

                    foreach ($groups as $key => $val) {
                        if (in_array($val['id'], $newsGroups)) {
                            $groups[$key]['checked'] = true;
                        }
                    }
                }

                $tpl->assign('pageURL', $this->_newsAdminUrl('edit', array('id' => $news['newsid']), true));
                $tpl->assign('usertpldir', B1GMAIL_REL.'templates/'.$bm_prefs['template'].'/');
                $tpl->assign('groups', $groups);
                $tpl->assign('news', $news);
                $tpl->assign('page', $this->_templatePath('news.admin.edit.tpl'));
            }
        }
    }

    public function FileHandler($file, $action)
    {
        global $tpl, $groupRow;

        if ($file == 'index.php' && $action == 'newsPlugin') {
            $news = $this->_getNews(false);

            $tpl->assign('news', $news);
            $tpl->assign('page', $this->_templatePath('news.notloggedin.tpl'));
            $tpl->display('nli/index.tpl');

            exit();
        } elseif ($file == 'start.php' && $action == 'newsPlugin') {
            if (isset($_REQUEST['do']) && $_REQUEST['do'] == 'showNews' && isset($_REQUEST['id'])) {
                $news = $this->_getNews(true, $groupRow['id']);

                if (isset($news[$_REQUEST['id']])) {
                    $tpl->assign('news', $news[$_REQUEST['id']]);
                    $tpl->display($this->_templatePath('news.show.tpl'));
                    exit();
                }
            }
        }
    }

    public function getUserPages($loggedin)
    {
        global $lang_user;

        if ($loggedin) {
            return [];
        }

        if (count($this->_getNews(false)) < 1) {
            return [];
        }

        return [
            'news' => [
                'faIcon' => 'fa-newspaper-o',
                'text' => $lang_user['news_news'],
                'link' => PublicNavUrl('index.php?action=newsPlugin'),
            ],
        ];
    }

    public function _getNews($loggedin, $groupID = 0, $sortField = 'date', $sortDirection = 'DESC')
    {
        global $db;

        $result = [];
        $res = $db->Query('SELECT `newsid`,`date`,`title`,`text` FROM {pre}news WHERE (`loggedin`=? OR `loggedin`=?) AND (`loggedin`=? OR `groups`=? OR `groups`=? OR `groups` LIKE ? OR `groups` LIKE ? OR `groups` LIKE ?) ORDER BY `'.$sortField.'` '.$sortDirection,
            $loggedin ? 'li' : 'nli',
            'both',
            'nli',
            '*',
            $groupID,
            $groupID.',%',
            '%,'.$groupID.',%',
            '%,'.$groupID);
        while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
            $result[$row['newsid']] = $row;
        }
        $res->Free();

        return $result;
    }
}

/**
 * News widget.
 */
class NewsWidget extends BMPlugin
{
    public function __construct()
    {
        // plugin info
        $this->type = BMPLUGIN_WIDGET;
        $this->name = 'News widget';
        $this->author = 'b1gMail Project';
        $this->mail = 'info@b1gmail.org';
        $this->version = '1.8';
        $this->widgetTemplate = 'widget.news.tpl';
        $this->widgetTitle = 'News';
        $this->update_url = 'https://service.b1gmail.org/plugin_updates/';
        $this->website = 'https://www.b1gmail.org/';
    }

    public function isWidgetSuitable($for)
    {
        return $for == BMWIDGET_START
                || $for == BMWIDGET_ORGANIZER;
    }

    public function renderWidget()
    {
        global $groupRow, $tpl;
        $tpl->assign('bmwidget_news_news', (new NewsPlugin())->_getNews(true, $groupRow['id']));

        return true;
    }
}

/*
 * register plugin + widget
 */
$plugins->registerPlugin('NewsPlugin');
$plugins->registerPlugin('NewsWidget');
