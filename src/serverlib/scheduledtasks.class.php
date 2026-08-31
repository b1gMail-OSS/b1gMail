<?php
/*
 * b1gMail – geplante Wartungsaufgaben (Core)
 */

class BMScheduledTasks {

    private function template($name) {
        return B1GMAIL_DIR . 'admin/templates/maintenance.scheduler.' . $name;
    }

    /** Language key for a stored task id (e.g. tccrn.db_optimize → sched.task.db_optimize). */
    public static function taskLangKey($taskId) {
        if (is_string($taskId) && strncmp($taskId, 'tccrn.', 6) === 0) {
            return 'sched.task.' . substr($taskId, 6);
        }

        return $taskId;
    }

    /** Localized label for a stored task id. */
    public static function taskLangLabel($taskId) {
        global $lang_admin;
        $key = self::taskLangKey($taskId);

        return isset($lang_admin[$key]) ? $lang_admin[$key] : $taskId;
    }

    /** Task types for the admin dropdown (stored id => label). */
    private static function adminTaskTypeOptions() {
        global $lang_admin;
        $tasks = array();
        foreach ($lang_admin as $k => $v) {
            if (strncmp($k, 'sched.task.', 11) === 0) {
                $tasks['tccrn.' . substr($k, 11)] = $v;
            }
        }
        asort($tasks);

        return $tasks;
    }

    /**
     * Create core tables from database.struct.json if missing (e.g. before DB sync in admin).
     */
    public static function ensureDatabaseTables() {
        global $db, $mysql;

        static $ensured = false;
        if ($ensured) {
            return;
        }

        $tasksTable = $mysql['prefix'] . 'scheduled_tasks';
        $res = $db->Query('SHOW TABLES LIKE ?', $tasksTable);
        if ($res->RowCount() > 0) {
            $res->Free();
            $ensured = true;
            return;
        }
        $res->Free();

        include B1GMAIL_DIR . 'serverlib/database.struct.php';
        $allStruct = json_decode($databaseStructure, true);
        if (!is_array($allStruct)) {
            return;
        }

        $sync = array();
        $prefix = $mysql['prefix'];
        foreach (array('scheduled_tasks', 'scheduled_tasks_config') as $suffix) {
            $jsonKey = 'bm60_' . $suffix;
            if (isset($allStruct[$jsonKey])) {
                $sync[$prefix . $suffix] = $allStruct[$jsonKey];
            }
        }
        if (count($sync) > 0) {
            SyncDBStruct($sync);
            PutLog('Created scheduled_tasks database tables', PRIO_NOTE, __FILE__, __LINE__);
        }

        $ensured = true;
    }

    public static function migrateLegacyPluginData() {
        global $db, $mysql;

        self::ensureDatabaseTables();

        $prefix = $mysql['prefix'];
        $oldCron = $prefix . 'tccrn_plugin_cron';
        $oldSettings = $prefix . 'tccrn_plugin_settings';

        $res = $db->Query('SHOW TABLES LIKE ?', $oldCron);
        $hasOld = $res->RowCount() > 0;
        $res->Free();

        $res = $db->Query('SELECT COUNT(*) FROM {pre}scheduled_tasks');
        list($newCount) = $res->FetchArray(MYSQLI_NUM);
        $res->Free();

        if (!$hasOld) {
            $res = $db->Query('SELECT COUNT(*) FROM {pre}scheduled_tasks_config');
            list($cfgCount) = $res->FetchArray(MYSQLI_NUM);
            $res->Free();
            if ($cfgCount < 1) {
                $db->Query('INSERT INTO {pre}scheduled_tasks_config (`id`, `loglevel`) VALUES (1, 6)');
            }
            return;
        }

        if ($newCount > 0) {
            $db->Query('DROP TABLE IF EXISTS `' . $oldCron . '`');
            $db->Query('DROP TABLE IF EXISTS `' . $oldSettings . '`');
            PutLog('Dropped legacy CleverCron tables', PRIO_NOTE, __FILE__, __LINE__);
            return;
        }

        $db->Query(
            'INSERT INTO {pre}scheduled_tasks (`taskid`, `active`, `task`, `status`, `lastcall`, `nextcall`, `crondata`, `taskdata`, `log`) '
            . 'SELECT `cronid`, `active`, `task`, `status`, `lastcall`, `nextcall`, `crondata`, `taskdata`, `log` FROM `' . $oldCron . '`'
        );

        $res = $db->Query('SHOW TABLES LIKE ?', $oldSettings);
        if ($res->RowCount() > 0) {
            $res2 = $db->Query('SELECT `loglevel` FROM `' . $oldSettings . '` LIMIT 1');
            if ($res2->RowCount() > 0) {
                list($loglevel) = $res2->FetchArray(MYSQLI_NUM);
                $db->Query('INSERT INTO {pre}scheduled_tasks_config (`id`, `loglevel`) VALUES (1, ?) ON DUPLICATE KEY UPDATE `loglevel`=?', (int) $loglevel, (int) $loglevel);
            } else {
                $db->Query('INSERT INTO {pre}scheduled_tasks_config (`id`, `loglevel`) VALUES (1, 6)');
            }
            $res2->Free();
            $db->Query('DROP TABLE IF EXISTS `' . $oldSettings . '`');
        } else {
            $db->Query('INSERT INTO {pre}scheduled_tasks_config (`id`, `loglevel`) VALUES (1, 6)');
        }
        $res->Free();

        $db->Query('DROP TABLE IF EXISTS `' . $oldCron . '`');
        PutLog('Migrated CleverCron plugin data to core scheduled_tasks tables', PRIO_NOTE, __FILE__, __LINE__);
    }

    public static function onCron() {
        self::migrateLegacyPluginData();
        $instance = new BMScheduledTasks();
        $instance->runCron();
    }

    public static function adminPageUrl($do = 'start', array $extra = array()) {
        $params = array('action' => 'scheduler', 'do' => $do);
        foreach ($extra as $key => $value) {
            if ($value !== '' && $value !== null) {
                $params[$key] = $value;
            }
        }
        if (function_exists('AdminSessionUrl')) {
            return AdminSessionUrl('maintenance.php', $params, false);
        }
        $url = 'maintenance.php?action=scheduler&do=' . rawurlencode((string) $do);
        foreach ($extra as $key => $value) {
            $url .= '&' . rawurlencode((string) $key) . '=' . rawurlencode((string) $value);
        }
        return SessionUrl($url);
    }

    /**
     * Admin UI under Tools » Maintenance » Scheduled tasks.
     */
    public function adminHandler() {
        global $tpl, $lang_admin;

        self::migrateLegacyPluginData();

        $tpl->addCSSFile('admin', '../plugins/css/scheduledtasks.css?ver=' . B1GMAIL_VERSION);
        $tpl->addJSFile('admin', '../plugins/js/scheduledtasks.js?ver=' . B1GMAIL_VERSION);

        static $countdownRegistered = false;
        if (!$countdownRegistered) {
            $tpl->registerPlugin('function', 'tccrn_countdown', 'ScheduledTasksTemplateCountdown');
            $countdownRegistered = true;
        }

        $tpl->assign('tccrn_admin_script', 'maintenance.php');
        $tpl->assign('tccrn_admin_action', 'scheduler');
        $tpl->assign('tccrn_nav_tpl', $this->template('nav.tpl'));

        $do = $this->_adminResolveDo();
        $tpl->assign('scheduler_do', $do);
        $tpl->assign('pageURL', self::adminPageUrl('start'));

        switch ($do) {
            case 'settings':
                $this->_adminSettings();
                break;
            case 'task':
                $this->_adminTask();
                break;
            default:
                $this->_adminStart();
        }
    }

    /**
     * Admin page slug (do); legacy action= and do=delete|switch|execute supported.
     *
     * @return string
     */
    private function _adminResolveDo() {
        $do = isset($_REQUEST['do']) ? (string) $_REQUEST['do'] : '';
        if ($do === '') {
            return 'start';
        }
        if (in_array($do, array('delete', 'switch', 'execute'), true)) {
            $_REQUEST['op'] = $do;

            return 'start';
        }

        return $do;
    }

    /**
     * POST actions on the overview (switch / delete / execute).
     */
    private function _adminHandleStartOp() {
        global $db;

        $op = isset($_REQUEST['op']) ? (string) $_REQUEST['op'] : '';
        if ($op === '' || !in_array($op, array('delete', 'switch', 'execute'), true)) {
            return;
        }
        if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        CsrfEnforceOnPost();

        $id = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;
        if ($id <= 0) {
            return;
        }

        if ($op === 'delete') {
            $db->Query('DELETE FROM {pre}scheduled_tasks WHERE taskid = ?', $id);
        } elseif ($op === 'switch') {
            $active = isset($_REQUEST['active']) ? (int) $_REQUEST['active'] : 0;
            $res = $db->Query('SELECT crondata FROM {pre}scheduled_tasks WHERE taskid = ?', $id);
            if ($res->RowCount()) {
                list($crondata) = $res->FetchArray(MYSQLI_NUM);
                $crondata = unserialize($crondata);
                $db->Query(
                    'UPDATE {pre}scheduled_tasks SET active = ?, nextcall = ? WHERE taskid = ?',
                    $active == 1 ? 0 : 1,
                    $active == 1 ? 0 : $this->_getNextCall($crondata),
                    $id
                );
            }
            $res->Free();
        } elseif ($op === 'execute') {
            $res = $db->Query('SELECT * FROM {pre}scheduled_tasks WHERE taskid = ?', $id);
            if ($res->RowCount()) {
                $task = $res->FetchArray();
                $this->_executeTask($task, true);
            }
            $res->Free();
        }
    }

    var $_config = null;

    function _getConfig() {
        global $db;
        if ($this->_config == null) {
            self::ensureDatabaseTables();
            $res = $db->Query('SELECT * FROM {pre}scheduled_tasks_config WHERE id = 1');
            if ($res->RowCount() < 1) {
                $db->Query('INSERT INTO {pre}scheduled_tasks_config (`id`, `loglevel`) VALUES (1, 6)');
                $res->Free();
                $res = $db->Query('SELECT * FROM {pre}scheduled_tasks_config WHERE id = 1');
            }
            $config = $res->FetchArray();
            $res->Free();
            $this->_config = $config;
        }
        return $this->_config;
    }

    function _adminSettings() {
        global $tpl, $db;
        if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
            CsrfEnforceOnPost();
            $logLevel = 0;
            if (isset($_REQUEST['loglevel']) && is_array($_REQUEST['loglevel'])) {
                foreach ($_REQUEST['loglevel'] as $val) {
                    $logLevel |= $val;
                }
            }
            $db->Query('UPDATE {pre}scheduled_tasks_config SET loglevel = ? WHERE id = 1', $logLevel);
            $this->_config = null;
        }
        $tpl->assign('page', $this->template('settings.tpl'));
        $tpl->assign('tccrn_prefs', $this->_getConfig());
    }

    function _taskDb($op, $tables) {
        global $db;
        foreach ($tables as $table) {
            $db->Query($op . $table);
        }
    }

    function _task_db_optimize($taskData) {
        $this->_taskDb('OPTIMIZE TABLE ', $taskData['table']);
    }

    function _task_db_repair($taskData) {
        $this->_taskDb('REPAIR TABLE ', $taskData['table']);
    }

    function _task_db_struct($taskData) {
        // read default structure
        $databaseStructure = '';
        include (B1GMAIL_DIR . '/serverlib/database.struct.php');
        $databaseStructure = json_decode($databaseStructure, true);
        $myDatabaseStructure = array();
        foreach ($databaseStructure as $table => $value) {
            if (array_search($table, $taskData['table']) !== false) {
                $myDatabaseStructure[$table] = $value;
            }
        }
        SyncDBStruct($myDatabaseStructure);
    }

    function _task_fs_cleanup($taskData, $log) {
        CleanupTempFiles();
        if ($log) {
            PutLog('Cleaned up temp files.', PRIO_NOTE, __FILE__, __LINE__);
        }
    }

    function _task_cc_cleanup($taskData, $log) {
        global $bm_prefs, $cacheManager;
        if ($bm_prefs['cache_type'] == CACHE_B1GMAIL || $bm_prefs['cache_type'] == CACHE_MEMCACHE) {
            $cacheManager->CleanUp(true);
        }
        if ($log) {
            PutLog('Cleaned up cache.', PRIO_NOTE, __FILE__, __LINE__);
        }
    }

    function _taskUsCondition($taskData) {
        $condition = array();
        if (empty($taskData['groups'])) {
            return 'WHERE 0 = 1';
        }
        $timeDiff = time() - max(1, $taskData['days']) * TIME_ONE_DAY;
        $condition[] = sprintf('(lastlogin<%d AND last_notify<%d AND last_pop3<%d AND last_imap<%d AND reg_date<%d)', $timeDiff, $timeDiff, $timeDiff, $timeDiff, $timeDiff);
        $condition[] = '(gruppe IN (' . implode(',', array_values($taskData['groups'])) . '))';
        $condition = 'WHERE ' . implode(' AND ', $condition);
        return $condition;
    }

    function _task_us_lock($taskData, $log) {
        global $db;
        $condition = $this->_taskUsCondition($taskData);
        $db->Query('UPDATE {pre}users SET gesperrt=? ' . $condition, 'yes');
        if ($log) {
            PutLog('Locked inactive users from group(s) ' . implode(',', array_values($taskData['groups'])) . ': ' . $db->AffectedRows(), PRIO_NOTE, __FILE__, __LINE__);
        }
    }

    function _task_us_move($taskData, $log) {
        global $db;
        $condition = $this->_taskUsCondition($taskData);
        $db->Query('UPDATE {pre}users SET gruppe=? ' . $condition, $taskData['moveGroup']);
        if ($log) {
            PutLog('Moved inactive users from group(s) ' . implode(',', array_values($taskData['groups'])) . ' to group ' . $taskData['moveGroup'] . ': ' . $db->AffectedRows(), PRIO_NOTE, __FILE__, __LINE__);
        }
    }

    function _task_us_delete($taskData, $log, $mode = false) {
        global $db;
        $condition = $this->_taskUsCondition($taskData);
        if($mode == 'delete_not_active') {
            $condition .= ' AND gesperrt = "locked"';
        } else if($mode == 'delete_no_login') {
            $condition .= ' AND lastlogin = 0 AND last_pop3 = 0 AND last_imap = 0';
        }
        $adjective = ($mode ? substr($mode, 7) : 'inactive');
        if(!empty($taskData['realdel'])) {
            $res = $db->Query('SELECT `id` FROM {pre}users ' . $condition);
            $i = 0;
            while (($row = $res->FetchArray(MYSQLI_NUM)) != false) {
                $i++;
                DeleteUser($row[0]);
            }
            if ($log) {
                PutLog('Deleted ' . $adjective . ' users from group(s) ' . implode(',', array_values($taskData['groups'])) . ': ' . $i, PRIO_NOTE, __FILE__, __LINE__);
            }
        } else {
            $db->Query('UPDATE {pre}users SET gesperrt=? ' . $condition, 'delete');
            if ($log) {
                PutLog('Marked ' . $adjective . ' users for deletion from group(s) ' . implode(',', array_values($taskData['groups'])) . ': ' . $db->AffectedRows(), PRIO_NOTE, __FILE__, __LINE__);
            }
        }
    }

    function _task_us_na_delete($taskData, $log) {
        $this->_task_us_delete($taskData, $log, 'delete_not_active');
    }

    function _task_us_nl_delete($taskData, $log) {
        $this->_task_us_delete($taskData, $log, 'delete_no_login');
    }

    function _task_tr_delete($taskData, $log, $spamFolder = false) {
        global $db;
        // load class, if needed
        if(!class_exists('BMMailbox'))
            include(B1GMAIL_DIR . 'serverlib/mailbox.class.php');
        if(!class_exists('BMUser'))
            include(B1GMAIL_DIR . 'serverlib/user.class.php');
        $mails = $mailSizes = 0;
        $res = $db->Query('SELECT id,email FROM {pre}users WHERE gruppe IN(' . implode(',', array_values($taskData['groups'])) . ')');
        while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
            $user = _new('BMUser', array($row['id']));
            /* @var $mailbox BMMailbox */
            $mailbox = _new('BMMailbox', array($row['id'],
                $row['email'],
                $user));

            if($spamFolder) {
                $trashMails = $mailbox->GetMailList(FOLDER_SPAM);
            } else {
                $trashMails = $mailbox->GetMailList(FOLDER_TRASH);
            }
            foreach ($trashMails as $mailID => $mail) {
                if ((!isset($taskData['daysOnly']) || $mail['timestamp'] < time() - max(1, $taskData['days']) * TIME_ONE_DAY) && (!isset($taskData['sizesOnly']) || $mail['size'] > max(1, $taskData['size']) * 1024)) {
                    // delete
                    $mailbox->DeleteMail($mailID);

                    // stats
                    $mails++;
                    $mailSizes += $mail['size'];
                }
            }
            unset($mailbox);
            unset($user);
        }
        if ($log) {
            PutLog('Cleaned up ' . ($spamFolder ? 'spam folders' : 'trashes') . '; Deleted ' . $mails . ' mails (' . round($mailSizes / 1024 / 1024, 2) . ' MB)', PRIO_NOTE, __FILE__, __LINE__);
        }
        $res->Free();
    }

    function _task_tr_sp_delete($taskData, $log) {
        $this->_task_tr_delete($taskData, $log, true);
    }

    function _taskOpenLogfile($fileName) {
        if (PHPNumVersion() >= 430 && function_exists('gzopen')) {
            $fp = fopen('compress.bzip2://' . $fileName . '.bz2', 'w+');
        }
        if (!isset($fp) || !$fp) {
            $fp = fopen($fileName, 'w+');
        }
        return $fp;
    }

    function _task_lg_archive($taskData, $log) {
        global $db;
        $date = time();
        if (isset($taskData['keepDays']) && isset($taskData['days'])) {
            $date -= TIME_ONE_DAY * $taskData['days'];
        }
        if ($log) {
            PutLog('Archiving log entries before ' . date('r', $date), PRIO_NOTE, __FILE__, __LINE__);
        }
        if(function_exists('ArchiveLogs')) {
            PutLog('Seems to be an up to date version of b1gMail: will use ArchiveLogs()', PRIO_DEBUG, __FILE__, __LINE__);
            $archive = !empty($taskData['save']);
            $archivedLogEntryCount = -1;
            if(!ArchiveLogs($date, $archive, $archivedLogEntryCount)) {
                PutLog('Failed to create a new log archive file. The archiving procedure has been aborted.', PRIO_ERROR, __FILE__, __LINE__);
            } else {
                if($log) {
                    PutLog('Archived ' . $archivedLogEntryCount . ' log entries', PRIO_NOTE, __FILE__, __LINE__);
                }
            }
        } else {
            PutLog('This is not an up to date version of b1gMail: will use my own mechanisms', PRIO_DEBUG, __FILE__, __LINE__);
            if (!empty($taskData['save'])) {
                $fileName = B1GMAIL_DIR . 'logs/b1gMailLog-' . time() . '.log';
                $fp = $this->_taskOpenLogfile($fileName);
                if (!$fp) {
                    PutLog('Could not open ' . $fileName . ' for writing!', PRIO_ERROR, __FILE__, __LINE__);
                    return;
                }
                fwrite($fp, '#' . "\n");
                fwrite($fp, '# b1gMail ' . B1GMAIL_VERSION . "\n");
                fwrite($fp, '# Log file' . "\n");
                fwrite($fp, '#' . "\n");
                fwrite($fp, '# To: ' . date('r', $date) . "\n");
                fwrite($fp, '# Generated: ' . date('r') . "\n");
                fwrite($fp, '#' . "\n");
                fwrite($fp, "\n");
                $res = $db->Query('SELECT prio,eintrag,zeitstempel FROM {pre}logs WHERE zeitstempel<' . $date . ' ORDER BY id ASC');
                while ($row = $res->FetchArray()) {
                    fwrite($fp, sprintf('%s [%d]: %s' . "\n", date('r', $row['zeitstempel']), $row['prio'], $row['eintrag']));
                }
                if ($log) {
                    PutLog('Exported ' . ((int) $res->RowCount()) . ' log entries to ' . $fileName, PRIO_NOTE, __FILE__, __LINE__);
                }
                $res->Free();
                fclose($fp);
            }
            $db->Query('DELETE FROM {pre}logs WHERE zeitstempel<' . $date);
        }
    }

    function _task_lg_bs_archive($taskData, $log) {
        global $db;
        $date = time();
        if (isset($taskData['keepDays']) && isset($taskData['days'])) {
            $date -= TIME_ONE_DAY * $taskData['days'];
        }
        if (!empty($taskData['save'])) {
            $fileName = B1GMAIL_DIR . 'logs/b1gMailServerLog-' . time() . '.log';
            $fp = $this->_taskOpenLogfile($fileName);
            if (!$fp) {
                PutLog('Could not open ' . $fileName . ' for writing!', PRIO_ERROR, __FILE__, __LINE__);
                return;
            }
            fwrite($fp, '#' . "\n");
            fwrite($fp, '# b1gMailServer ' . "\n");
            fwrite($fp, '# Log file' . "\n");
            fwrite($fp, '#' . "\n");
            fwrite($fp, '# To: ' . date('r', $date) . "\n");
            fwrite($fp, '# Generated: ' . date('r') . "\n");
            fwrite($fp, '#' . "\n");
            fwrite($fp, "\n");
            $res = $db->Query('SELECT iComponent,iSeverity,iDate,szEntry FROM {pre}bms_logs WHERE iDate<' . $date . ' ORDER BY id ASC');
            $componentNames = array(BMS_CMP_CORE => 'Core',
                BMS_CMP_HTTP => 'HTTP',
                BMS_CMP_IMAP => 'IMAP',
                BMS_CMP_MSGQUEUE => 'MSGQueue',
                BMS_CMP_POP3 => 'POP3',
                BMS_CMP_SMTP => 'SMTP');
            while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
                fwrite($fp, sprintf('[%s] %s [%d]: %s' . "\n", $componentNames[$row['iComponent']], date('r', $row['iDate']), $row['iSeverity'], trim($row['szEntry'])));
            }
            if ($log) {
                PutLog('Exported ' . ((int) $res->RowCount()) . ' log entries (b1gMailServer) to ' . $fileName, PRIO_NOTE, __FILE__, __LINE__);
            }
            $res->Free();
            fclose($fp);
        }
        if ($log) {
            PutLog('Deleting log entries (b1gMailServer) before ' . date('r', $date), PRIO_NOTE, __FILE__, __LINE__);
        }
        $db->Query('DELETE FROM {pre}bms_logs WHERE iDate<' . $date);
    }

    function _task_se_delete($taskData, $log) {
        $dir = B1GMAIL_DIR . 'temp/session/';
        $limit = time() - ($taskData['days'] * TIME_ONE_DAY);
        $deleted = 0;
        if (is_dir($dir)) {
            if (($dh = opendir($dir)) != false) {
                while ($file = readdir($dh)) {
                    if (substr($file, 0, 5) != 'sess_') {
                        continue;
                    }
                    $path = $dir . $file;
                    if (fileatime($path) < $limit && filectime($path) < $limit && filemtime($path) < $limit) {
                        if (@unlink($path)) {
                            $deleted++;
                        }
                    }
                }
                closedir($dh);
            }
        }
        if ($log) {
            PutLog('Deleted ' . $deleted . ' session(s).', PRIO_NOTE, __FILE__, __LINE__);
        }
    }

    function _task_st_reset($taskData, $log) {
        global $db;
        if (empty($taskData) || empty($taskData['days'])) {
            $db->Query('TRUNCATE TABLE {pre}stats');
            if ($log) {
                PutLog('Statistics reseted', PRIO_NOTE, __FILE__, __LINE__);
            }
        } else {
            $limit = time() - ($taskData['days'] * TIME_ONE_DAY);
            $d = date('j', $limit);
            $m = date('n', $limit);
            $y = date('Y', $limit);
            $db->Query('DELETE FROM {pre}stats WHERE (d < ' . $d . ' AND m = ' . $m . ' AND y = ' . $y . ') OR (m < ' . $m . ' AND y = ' . $y . ') OR (y < ' . $y . ')');
            if ($log) {
                PutLog('Deleted statistic entries before ' . date('r', $limit), PRIO_NOTE, __FILE__, __LINE__);
            }
        }
    }

    function _executeTask($task, $force = false) {
        global $db;
        $config = $this->_getConfig();
        $task['taskdata'] = unserialize($task['taskdata']);
        $task['crondata'] = unserialize($task['crondata']);
        if ($task['active']) {
            $start = microtime(true);
            if (($config['loglevel'] & 8) != 0) {
                PutLog('Executing cron ' . $task['taskid'] . ': Task = ' . $task['task'] . '; Nextcall = ' . date('r', $task['nextcall']) . '; Lastcall = ' . date('r', $task['lastcall']) . '; Status = ' . $task['status'], PRIO_DEBUG, __FILE__, __LINE__);
            }
            if (!$force && $task['status'] == 'started') {
                if($task['lastcall'] + 60 < time()) {
                    if(($config['loglevel'] & 4) != 0) {
                        PutLog('Cron ' . $task['taskid'] . ' (' . $task['task'] . ') didn\'t finish correctly... Lastcall = ' . date('r', $task['lastcall']), PRIO_ERROR, __FILE__, __LINE__);
                    }
                } else {
                    return;
                }
            }
            if ($task['nextcall'] && (($delay = time() - $task['nextcall']) > 600) && ($config['loglevel'] & 2) != 0) {
                PutLog('Cron ' . $task['taskid'] . ' (' . $task['task'] . ') executed with a delay of ' . $delay . 's...', PRIO_WARNING, __FILE__, __LINE__);
            }
            $db->Query('UPDATE {pre}scheduled_tasks SET status = ?, lastcall = ? WHERE taskid = ?', 'started', time(), $task['taskid']);
            $f = '_task_' . substr($task['task'], 6);
            $this->$f($task['taskdata'], $task['log']);
            $time = microtime(true) - $start;
            if ($time > 20 && ($config['loglevel'] & 2) != 0) {
                PutLog('Executing cron ' . $task['taskid'] . ' (' . $task['task'] . ') took a long time (' . $time . 's)!', PRIO_WARNING, __FILE__, __LINE__);
            } elseif ($time > 5 && ($config['loglevel'] & 1) != 0) {
                PutLog('Executing cron ' . $task['taskid'] . ' (' . $task['task'] . ') took a long time (' . $time . 's)!', PRIO_NOTE, __FILE__, __LINE__);
            } elseif (($config['loglevel'] & 8) != 0) {
                PutLog('Executing cron ' . $task['taskid'] . ' (' . $task['task'] . ') took ' . $time . 's.');
            }
        }
        $db->Query('UPDATE {pre}scheduled_tasks SET status = ?, nextcall = ? WHERE taskid = ?', 'finished', $this->_getNextCall($task['crondata']), $task['taskid']);
    }

    function runCron() {
        global $db;
        $config = $this->_getConfig();
        $res = $db->Query('SELECT * FROM {pre}scheduled_tasks WHERE nextcall <= ? AND nextcall != 0 AND active = 1 ORDER BY RAND() LIMIT 5', time());
        if (($c = $res->RowCount()) != 0) {
            if (($config['loglevel'] & 8) != 0) {
                PutLog($c . ' cron(s) found...', PRIO_DEBUG, __FILE__, __LINE__);
            }
        }
        while ($task = $res->FetchArray()) {
            $this->_executeTask($task);
        }
        if ($c && ($config['loglevel'] & 8) != 0) {
            PutLog('All done!', PRIO_DEBUG, __FILE__, __LINE__);
        }
        $res->Free();

        $res = $db->Query('SELECT taskid, crondata FROM {pre}scheduled_tasks WHERE nextcall <= ? AND nextcall != 0 AND active = 0', time());
        while ($task = $res->FetchArray()) {
            $db->Query('UPDATE {pre}scheduled_tasks SET status = ?, nextcall = ? WHERE taskid = ?', 'finished', $this->_getNextCall($task['crondata']), $task['taskid']);
        }
        $res->Free();
    }

    public static function getNotices() {
        global $db, $lang_admin;

        self::ensureDatabaseTables();

        $res = $db->Query('SELECT COUNT(*) FROM {pre}scheduled_tasks WHERE status = ? AND lastcall < ?', 'started', time() - 30);
        $count = $res->FetchArray(MYSQLI_NUM);
        $res->Free();
        $count = $count[0];
        if ($count > 0) {
            return array(
                array('type' => 'error',
                    'text' => sprintf($lang_admin['sched.task_failed_notice'], $count),
                    'link' => self::adminPageUrl('start')));
        } else {
            return array();
        }
    }

    function _adminStart() {
        global $tpl, $db, $currentLanguage;

        $this->_adminHandleStartOp();

        $tpl->assign('notices', $this->getNotices());

        $tpl->assign('page', $this->template('start.tpl'));
        $tpl->assign('groups', BMGroup::GetSimpleGroupList());
        $tpl->assign('tccrn_tasks', $this->_getTasks());
    }

    function _getTasks() {
        global $db;
        $res = $db->Query('SELECT taskid, task, nextcall, taskdata, status, active, lastcall FROM {pre}scheduled_tasks ORDER BY active DESC, nextcall ASC');
        $tasks = array();
        while ($task = $res->FetchArray()) {
            $task['taskdata'] = unserialize($task['taskdata']);
            $task['task_label'] = self::taskLangLabel($task['task']);
            $tasks[$task['taskid']] = $task;
        }
        return $tasks;
    }

    var $_taskData = array('db_optimize' => 'db',
        'db_repair' => 'db',
        'db_struct' => 'db',
        'us_lock' => 'user',
        'us_move' => 'userGroup',
        'us_delete' => 'user',
        'us_na_delete' => 'user',
        'us_nl_delete' => 'user',
        'tr_delete' => 'trash',
        'tr_sp_delete' => 'trash',
        'lg_archive' => 'log',
        'lg_bs_archive' => 'log',
        'se_delete' => 'session',
        'st_reset' => 'stats');

    function _loadTaskData($task) {
        global $db, $mysql, $tpl;
        $task = substr($task, 6);
        if (isset($this->_taskData[$task])) {
            if (substr($task, 0, 2) == 'db') {
                $res = $db->Query('SHOW TABLES');
                $myTables = array();
                while ($row = $res->FetchArray(MYSQLI_NUM)) {
                    if (substr($row[0], 0, strlen($mysql['prefix'])) == $mysql['prefix']) {
                        $myTables[] = $row[0];
                    }
                }
                $res->Free();
                $tpl->assign('tccrn_tables', $myTables);
            } elseif (substr($task, 0, 2) == 'us') {
                $tpl->assign('groups', BMGroup::GetSimpleGroupList());
            } elseif (substr($task, 0, 2) == 'tr') {
                $tpl->assign('groups', BMGroup::GetSimpleGroupList());
            }
            $tpl->assign('tccrn_task_data', $this->template('taskdata.' . $this->_taskData[$task] . '.tpl'));
            if ($this->_taskData[$task] === 'userGroup') {
                $tpl->assign('tccrn_task_data_user', $this->template('taskdata.user.tpl'));
            }
        }
    }

    function _adminRedirect($do = 'start') {
        SessionRedirect(self::adminPageUrl($do));
    }

    function _getNextCall($cronData) {
        if(!isset($cronData['month']) || !isset($cronData['day']) || !isset($cronData['weekday']) || !isset($cronData['hour']) || !isset($cronData['minute'])) {
            return 0;
        }
        $aYear = date('y');
        $nTime = 0;
        $time = time();
        for ($year = $aYear; $year <= $aYear + 10; $year++) {
            for ($month = 1; $month <= 12; $month++) {
                if (mktime(23, 59, 59, $month, 31, $year) < $time || array_search($month, $cronData['month']) === false) {
                    continue;
                }
                for ($day = 1; $day <= 31; $day++) {
                    if (mktime(23, 59, 59, $month, $day, $year) < $time || array_search($day, $cronData['day']) === false) {
                        continue;
                    }
                    $w = date('w', mktime(0, 0, 0, $month, $day, $year));
                    if ($w == 0) {
                        $w = 7;
                    }
                    --$w;
                    if (array_search($w, $cronData['weekday']) === false) {
                        continue;
                    }
                    for ($hour = 0; $hour <= 23; $hour++) {
                        if (mktime($hour, 59, 59, $month, $day, $year) < $time || array_search($hour, $cronData['hour']) === false) {
                            continue;
                        }
                        for ($minute = 0; $minute <= 59; $minute++) {
                            if (mktime($hour, $minute, 0, $month, $day, $year) <= $time || array_search($minute, $cronData['minute']) === false) {
                                continue;
                            }
                            $nTime = mktime($hour, $minute, 0, $month, $day, $year);
                            break (5);
                        }
                    }
                }
            }
        }
        return $nTime;
    }

    /**
     * Ensure task form arrays exist (crondata day/hour/… after POST "next").
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function _normalizeTaskFormData($data) {
        if (!is_array($data)) {
            $data = array();
        }
        if (!isset($data['taskdata']) || !is_array($data['taskdata'])) {
            $data['taskdata'] = array();
        }
        if (!isset($data['crondata']) || !is_array($data['crondata'])) {
            $data['crondata'] = array();
        }
        foreach (array('day', 'weekday', 'month', 'hour', 'minute') as $key) {
            if (!isset($data['crondata'][$key]) || !is_array($data['crondata'][$key])) {
                $data['crondata'][$key] = array();
            }
        }

        return $data;
    }

    function _adminTask() {
        global $tpl, $lang_admin, $db;
        $tasks = self::adminTaskTypeOptions();

        if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
            CsrfEnforceOnPost();
            $taskKey = isset($_POST['task']) ? (string) $_POST['task'] : '';
            if ($taskKey === '' || !isset($tasks[$taskKey])) {
                $taskKey = '';
            }
            if (!empty($_POST['next']) && $taskKey !== '') {
                $tpl->assign('tccrn_data', $this->_normalizeTaskFormData($_POST));
                $this->_loadTaskData($taskKey);
            } elseif (!empty($_POST['save']) && $taskKey !== '') {
                if (empty($_POST['taskdata'])) {
                    $_POST['taskdata'] = array();
                }
                if (empty($_POST['crondata'])) {
                    $_POST['crondata'] = array();
                }
                $id = isset($_POST['id']) ? (int) $_POST['id'] : 'NULL';
                if (!empty($_POST['active'])) {
                    $nextCall = $this->_getNextCall($_POST['crondata']);
                } else {
                    $nextCall = 0;
                }
                $db->Query('REPLACE INTO {pre}scheduled_tasks(taskid, active, log, task, nextcall, crondata, taskdata) VALUES (' . $id . ', ?, ?, ?, ?, ?, ?)', !empty($_POST['active']), !empty($_POST['log']), $taskKey, $nextCall, serialize($_POST['crondata']), serialize($_POST['taskdata']));
                $this->_adminRedirect();
            }
        } elseif (isset($_REQUEST['id'])) {
            $res = $db->Query('SELECT * FROM {pre}scheduled_tasks WHERE taskid = ?', (int) $_REQUEST['id']);
            $row = $res->FetchArray();
            $row['taskdata'] = unserialize($row['taskdata']);
            $row['crondata'] = unserialize($row['crondata']);
            $tpl->assign('tccrn_data', $this->_normalizeTaskFormData($row));
            $this->_loadTaskData($row['task']);
        }

        $tccrnData = $tpl->getTemplateVars('tccrn_data');
        $tpl->assign('tccrn_data', $this->_normalizeTaskFormData(
            ($tccrnData === null || $tccrnData === false) ? array() : $tccrnData
        ));

        $tpl->assign('tccrn_tasks', $tasks);
        $tpl->assign('tccrn_schedule_tpl', $this->template('schedule.tpl'));
        $tpl->assign('sched_weekdays_array', $lang_admin['sched.weekdays_array']);
        $tpl->assign('sched_weekdays_short', $lang_admin['sched.weekdays_short']);
        $tpl->assign('page', $this->template('task.tpl'));
    }
}

function ScheduledTasksTemplateCountdown($params, $smarty = null) {
    global $lang_admin;

    $newDate = $params['timestamp'];
    $actDate = time();
    $diffDate = ($newDate - $actDate);
    if ($actDate >= $newDate) {
        return $lang_admin['now'] . '...';
    } elseif ($actDate + 60 > $newDate) {
        return $lang_admin['sched.eta'] . $diffDate . ' ' . $lang_admin['sched.eta_seconds'];
    }

    $days = floor($diffDate / 24 / 60 / 60);
    $diffDate = $diffDate - ($days * 24 * 60 * 60);
    $hours = floor($diffDate / 60 / 60);
    $diffDate = ($diffDate - ($hours * 60 * 60));
    $minutes = floor($diffDate / 60);

    $etaUnit = array(
        'days' => array('sched.eta_1day', 'sched.eta_days'),
        'hours' => array('sched.eta_1hour', 'sched.eta_hours'),
        'minutes' => array('sched.eta_1minute', 'sched.eta_minutes'),
    );

    $string = $lang_admin['sched.eta'];
    $res = array('days' => $days, 'hours' => $hours, 'minutes' => $minutes);
    foreach (array_keys($res) as $var) {
        if ($$var == 0) {
            unset($res[$var]);
        }
    }
    $i = count($res);
    foreach ($res as $k => $v) {
        --$i;
        if ($v == 1) {
            $string .= $lang_admin[$etaUnit[$k][0]];
        } else {
            $string .= $v . ' ' . $lang_admin[$etaUnit[$k][1]];
        }
        if ($i != 0) {
            $string .= ', ';
        }
    }

    return $string;
}

?>