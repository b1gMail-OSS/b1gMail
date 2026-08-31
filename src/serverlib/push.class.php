<?php
/*
 * b1gMail – Web Push (PWA install + plugin-extensible push delivery).
 */

if (!defined('B1GMAIL_INIT')) {
    die('Directly calling this file is not supported');
}

include_once B1GMAIL_DIR.'serverlib/push.vapid.php';
include_once B1GMAIL_DIR.'serverlib/push.web.php';

class BMPush
{
    const AREA_USER = 'user';
    const AREA_ADMIN = 'admin';

    const TYPE_MAIL = 'core.mail';
    const TYPE_MAIL_FILTER = 'core.mail.filter';
    const TYPE_CALENDAR = 'core.calendar';
    const TYPE_BIRTHDAY = 'core.birthday';
    const TYPE_TASK = 'core.task';
    const TYPE_WEBDISK = 'core.webdisk';

    private static $schemaChecked = false;

    /**
     * Ensure DB table and prefs columns exist.
     */
    public static function ensureSchema()
    {
        global $db;

        if (self::$schemaChecked) {
            return;
        }
        self::$schemaChecked = true;

        global $mysql;

        $table = $mysql['prefix'].'push_subscriptions';
        $res = $db->Query('SHOW TABLES LIKE ?', $table);
        if ($res->RowCount() == 0) {
            $db->Query(
                'CREATE TABLE `'.$table.'` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `area` enum(\'user\',\'admin\') NOT NULL DEFAULT \'user\',
                    `userid` int(11) NOT NULL DEFAULT 0,
                    `adminid` int(11) NOT NULL DEFAULT 0,
                    `endpoint` varchar(768) NOT NULL,
                    `p256dh` varchar(255) NOT NULL,
                    `auth` varchar(255) NOT NULL,
                    `user_agent` varchar(255) NOT NULL DEFAULT \'\',
                    `created` int(11) NOT NULL DEFAULT 0,
                    PRIMARY KEY (`id`),
                    KEY `area_user` (`area`,`userid`),
                    KEY `area_admin` (`area`,`adminid`),
                    KEY `endpoint` (`endpoint`(191))
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
            );
        }
        $res->Free();

        $prefsTable = $mysql['prefix'].'prefs';
        $columns = [
            'push_enabled' => "enum('yes','no') NOT NULL DEFAULT 'no'",
            'push_vapid_public' => "text NOT NULL DEFAULT ''",
            'push_vapid_private' => "text NOT NULL DEFAULT ''",
            'push_vapid_subject' => "varchar(255) NOT NULL DEFAULT ''",
        ];
        foreach ($columns as $col => $def) {
            $res = $db->Query('SHOW COLUMNS FROM `'.$prefsTable.'` LIKE ?', $col);
            if ($res->RowCount() == 0) {
                $db->Query('ALTER TABLE `'.$prefsTable.'` ADD `'.$col.'` '.$def);
            }
            $res->Free();
        }
    }

    /**
     * @return bool OpenSSL can generate EC keys for VAPID
     */
    public static function canGenerateKeys()
    {
        return function_exists('openssl_pkey_new')
            && defined('OPENSSL_KEYTYPE_EC')
            && function_exists('openssl_pkey_derive');
    }

    public static function hasVapidKeys()
    {
        return self::loadVapidCredentials() !== false;
    }

    public static function isEnabled()
    {
        global $bm_prefs;

        self::ensureSchema();
        ReadConfig();

        return isset($bm_prefs['push_enabled']) && $bm_prefs['push_enabled'] == 'yes'
            && self::hasVapidKeys();
    }

    public static function getPublicKey()
    {
        global $bm_prefs;

        ReadConfig();

        return isset($bm_prefs['push_vapid_public']) ? trim($bm_prefs['push_vapid_public']) : '';
    }

    /**
     * Load VAPID keys from DB (always fresh). Fixes public/private mismatch in prefs.
     *
     * @return array{private:string,public:string,subject:string}|false
     */
    public static function loadVapidCredentials()
    {
        global $bm_prefs, $db;

        self::ensureSchema();
        ReadConfig();

        $privatePem = BMPushVapid::normalizePrivateKeyPem(
            isset($bm_prefs['push_vapid_private']) ? $bm_prefs['push_vapid_private'] : ''
        );
        if ($privatePem === '' || openssl_pkey_get_private($privatePem) === false) {
            return false;
        }

        $publicBytes = BMPushVapid::getApplicationServerKeyBytes(
            isset($bm_prefs['push_vapid_public']) ? $bm_prefs['push_vapid_public'] : '',
            $privatePem
        );
        if ($publicBytes === false || strlen($publicBytes) !== 65) {
            return false;
        }

        $derived = BMPushVapid::publicKeyFromPrivatePem($privatePem);
        $storedB64 = trim(isset($bm_prefs['push_vapid_public']) ? $bm_prefs['push_vapid_public'] : '');
        $storedBytes = $storedB64 !== '' ? BMPushVapid::base64UrlDecode($storedB64) : '';
        if ($derived !== false && strlen($storedBytes) === 65 && $storedBytes !== $derived) {
            $fixedB64 = BMPushVapid::base64UrlEncode($derived);
            $db->Query('UPDATE {pre}prefs SET push_vapid_public=?', $fixedB64);
            ReadConfig();
            PutLog(
                'Web Push: VAPID public key in prefs did not match private key and was corrected – users should re-enable push once',
                PRIO_WARNING,
                __FILE__,
                __LINE__
            );
        }

        $subject = !empty($bm_prefs['push_vapid_subject'])
            ? trim($bm_prefs['push_vapid_subject'])
            : 'mailto:noreply@localhost';

        return [
            'private' => $privatePem,
            'public' => $publicBytes,
            'subject' => $subject,
        ];
    }

    /**
     * Generate and store VAPID keys in prefs.
     */
    public static function generateVapidKeys($subject = '')
    {
        global $db, $bm_prefs;

        self::ensureSchema();

        if (!self::canGenerateKeys()) {
            return false;
        }

        $pair = BMPushVapid::generateKeyPair();
        if ($pair === false) {
            return false;
        }

        if ($subject == '') {
            $host = 'localhost';
            if (isset($bm_prefs['selfurl'])) {
                $parsedHost = parse_url($bm_prefs['selfurl'], PHP_URL_HOST);
                if (is_string($parsedHost) && $parsedHost != '') {
                    $host = $parsedHost;
                }
            }
            $subject = 'mailto:admin@'.$host;
        }

        $db->Query(
            'UPDATE {pre}prefs SET push_vapid_public=?, push_vapid_private=?, push_vapid_subject=?',
            $pair['public'],
            $pair['private'],
            $subject
        );
        ReadConfig();

        return !empty($bm_prefs['push_vapid_public']);
    }

    /**
     * Register a browser push subscription.
     *
     * @param string $area user|admin
     * @param int    $targetId userid or adminid
     * @param array  $subscription JSON keys endpoint, keys.p256dh, keys.auth
     */
    public static function subscribe($area, $targetId, $subscription)
    {
        global $db;

        self::ensureSchema();

        if (!self::isEnabled() || !is_array($subscription)) {
            return false;
        }

        $endpoint = isset($subscription['endpoint']) ? trim($subscription['endpoint']) : '';
        $p256dh = isset($subscription['keys']['p256dh']) ? trim($subscription['keys']['p256dh']) : '';
        $auth = isset($subscription['keys']['auth']) ? trim($subscription['keys']['auth']) : '';

        if ($endpoint == '' || $p256dh == '' || $auth == '') {
            return false;
        }

        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 255) : '';

        $db->Query(
            'DELETE FROM {pre}push_subscriptions WHERE `area`=? AND `endpoint`=?',
            $area,
            $endpoint
        );

        $db->Query(
            'INSERT INTO {pre}push_subscriptions(`area`,`userid`,`adminid`,`endpoint`,`p256dh`,`auth`,`user_agent`,`created`) VALUES(?,?,?,?,?,?,?,?)',
            $area,
            $area == self::AREA_USER ? (int) $targetId : 0,
            $area == self::AREA_ADMIN ? (int) $targetId : 0,
            $endpoint,
            $p256dh,
            $auth,
            $ua,
            time()
        );

        self::pruneSubscriptions($area, $targetId, 5);

        ModuleFunction('OnPushSubscribe', [$area, $targetId, $subscription]);

        return true;
    }

    /**
     * Keep only the newest N push subscriptions per user/admin.
     */
    public static function pruneSubscriptions($area, $targetId, $max = 5)
    {
        global $db;

        $max = max(1, (int) $max);
        $res = $db->Query(
            'SELECT id FROM {pre}push_subscriptions WHERE `area`=? AND '
            .($area == self::AREA_USER ? '`userid`=?' : '`adminid`=?')
            .' ORDER BY `created` DESC',
            $area,
            (int) $targetId
        );
        $ids = [];
        while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
            $ids[] = (int) $row['id'];
        }
        $res->Free();

        if (count($ids) <= $max) {
            return;
        }

        foreach (array_slice($ids, $max) as $oldId) {
            $db->Query('DELETE FROM {pre}push_subscriptions WHERE `id`=?', $oldId);
        }
    }

    public static function unsubscribe($area, $targetId, $endpoint)
    {
        global $db;

        self::ensureSchema();

        $db->Query(
            'DELETE FROM {pre}push_subscriptions WHERE `area`=? AND `endpoint`=? AND '
            .($area == self::AREA_USER ? '`userid`=?' : '`adminid`=?'),
            $area,
            $endpoint,
            (int) $targetId
        );

        ModuleFunction('OnPushUnsubscribe', [$area, $targetId, $endpoint]);

        return true;
    }

    /**
     * Pause push delivery by removing server subscriptions (e.g. on logout).
     * Browser subscription and user prefs stay intact for re-activation on login.
     */
    public static function unsubscribeAll($area, $targetId)
    {
        global $db;

        self::ensureSchema();

        $targetId = (int) $targetId;
        if ($targetId <= 0) {
            return false;
        }

        $db->Query(
            'DELETE FROM {pre}push_subscriptions WHERE `area`=? AND '
            .($area == self::AREA_USER ? '`userid`=?' : '`adminid`=?'),
            $area,
            $targetId
        );

        ModuleFunction('OnPushUnsubscribeAll', [$area, $targetId]);

        return true;
    }

    /**
     * Send push to one user/admin (all subscriptions).
     *
     * @param array $message keys: area, targetId, type, title, body, url, icon, tag
     *
     * @return array sent, failed, removed
     */
    private static function logPushResult($targetId, $type, $sent, $failed, $subscriptions, $lastError = '', $removed = 0)
    {
        PutLog(sprintf(
            'Web Push: user %d type %s – sent=%d failed=%d removed=%d subscriptions=%d%s',
            $targetId,
            $type,
            $sent,
            $failed,
            $removed,
            $subscriptions,
            $lastError != '' ? ' last='.$lastError : ''
        ), PRIO_NOTE, __FILE__, __LINE__);
    }

    public static function send($message)
    {
        global $bm_prefs, $db;

        ReadConfig();

        $area = isset($message['area']) ? $message['area'] : self::AREA_USER;
        $targetId = isset($message['targetId']) ? (int) $message['targetId'] : 0;
        $type = isset($message['type']) ? $message['type'] : 'core.generic';

        if (!self::isEnabled()) {
            if ($targetId > 0) {
                self::logPushResult($targetId, $type, 0, 0, 0, 'push_disabled');
            }

            return ['sent' => 0, 'failed' => 0, 'removed' => 0, 'reason' => 'push_disabled'];
        }

        $vapid = self::loadVapidCredentials();
        if ($vapid === false) {
            self::logPushResult($targetId, $type, 0, 0, 0, 'vapid_invalid');

            return ['sent' => 0, 'failed' => 0, 'removed' => 0, 'reason' => 'vapid_invalid'];
        }

        if ($targetId <= 0) {
            return ['sent' => 0, 'failed' => 0, 'removed' => 0, 'reason' => 'invalid_target'];
        }

        $skipPrefCheck = !empty($message['skipPrefCheck']);

        if (!$skipPrefCheck && $area == self::AREA_USER && !self::userAllowsType($targetId, $type)) {
            $prefs = self::getUserPushPrefs($targetId);
            $detail = 'prefs_blocked';
            if (!self::userHasPushDelivery($targetId, $prefs)) {
                $detail = 'prefs_blocked_not_enabled';
            } elseif (isset($prefs['types'][$type]) && empty($prefs['types'][$type])) {
                $detail = 'prefs_blocked_type_off';
            }
            self::logPushResult($targetId, $type, 0, 0, self::countSubscriptions($area, $targetId), $detail);

            return ['sent' => 0, 'failed' => 0, 'removed' => 0, 'reason' => 'prefs_blocked'];
        }

        if (!$skipPrefCheck && $area == self::AREA_ADMIN && !self::adminAllowsType($targetId, $type)) {
            self::logPushResult($targetId, $type, 0, 0, self::countSubscriptions($area, $targetId), 'prefs_blocked');

            return ['sent' => 0, 'failed' => 0, 'removed' => 0, 'reason' => 'prefs_blocked'];
        }

        $pushSessionState = $area == self::AREA_USER
            ? SessionUserGetPushSessionState($targetId)
            : ($area == self::AREA_ADMIN ? SessionAdminGetPushSessionState($targetId) : 'none');

        if ($pushSessionState === 'none') {
            self::logPushResult($targetId, $type, 0, 0, self::countSubscriptions($area, $targetId), 'no_active_session');

            return ['sent' => 0, 'failed' => 0, 'removed' => 0, 'reason' => 'no_active_session'];
        }

        $payload = [
            'title' => isset($message['title']) ? self::plainText($message['title']) : '',
            'body' => isset($message['body']) ? self::plainText($message['body']) : '',
            'icon' => isset($message['icon']) ? $message['icon'] : '',
            'tag' => isset($message['tag']) && $message['tag'] != ''
                ? $message['tag']
                : self::pushNotificationTag($type),
            'url' => isset($message['url']) ? $message['url'] : '',
            'type' => $type,
        ];

        if ($pushSessionState === 'locked') {
            self::applyLockedScreenPushPrivacy($payload, $type, $area);
        }

        $abort = false;
        ModuleFunction('OnBeforePushSend', [&$payload, &$message, &$abort]);
        if ($abort) {
            self::logPushResult($targetId, $type, 0, 0, self::countSubscriptions($area, $targetId), 'plugin_abort');

            return ['sent' => 0, 'failed' => 0, 'removed' => 0, 'reason' => 'plugin_abort'];
        }

        // Relative click URL – Service Worker resolves host (beta vs production) from registration scope
        if ($area == self::AREA_USER) {
            $payload['url'] = self::appendSid($payload['url'], $targetId);
        } elseif ($area == self::AREA_ADMIN) {
            $payload['url'] = self::appendAdminSid($payload['url'], $targetId);
        }

        if ($payload['icon'] == '' && !empty($bm_prefs['selfurl'])) {
            $payload['icon'] = 'pwa-icon.php?size=192';
        }
        if ($payload['icon'] != '' && !preg_match('#^https?://#i', $payload['icon'])) {
            $payload['icon'] = self::absoluteUrl($payload['icon'], $area);
        }

        $json = json_encode($payload, JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            self::logPushResult($targetId, $type, 0, 0, self::countSubscriptions($area, $targetId), 'json_encode_failed');

            return ['sent' => 0, 'failed' => 0, 'removed' => 0, 'reason' => 'json_encode_failed'];
        }

        $privatePem = $vapid['private'];
        $publicUncompressed = $vapid['public'];
        $subject = $vapid['subject'];

        $res = $db->Query(
            'SELECT id,endpoint,p256dh,auth FROM {pre}push_subscriptions WHERE `area`=? AND '
            .($area == self::AREA_USER ? '`userid`=?' : '`adminid`=?'),
            $area,
            $targetId
        );

        $subscriptionCount = $res->RowCount();
        $sent = $failed = $removed = 0;
        $lastError = '';

        if ($subscriptionCount == 0) {
            $res->Free();
            self::logPushResult($targetId, $type, 0, 0, 0, 'no_subscription_reenable_push');

            return ['sent' => 0, 'failed' => 0, 'removed' => 0, 'reason' => 'no_subscription'];
        }

        while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
            $sub = [
                'endpoint' => $row['endpoint'],
                'p256dh' => $row['p256dh'],
                'auth' => $row['auth'],
            ];
            $result = BMPushWeb::send($sub, $json, $privatePem, $publicUncompressed, $subject);

            if ($result['ok']) {
                ++$sent;
            } else {
                ++$failed;
                $lastError = isset($result['error']) ? $result['error'] : 'delivery_failed';
                if (!empty($result['status'])) {
                    $lastError .= '_'.$result['status'];
                }
                if (in_array($result['status'], [401, 403, 404, 410], true)) {
                    PutLog(sprintf(
                        'Web Push: user %d subscription id=%d removed after HTTP %d (re-enable push in settings)',
                        $targetId,
                        $row['id'],
                        $result['status']
                    ), PRIO_NOTE, __FILE__, __LINE__);
                    $db->Query('DELETE FROM {pre}push_subscriptions WHERE `id`=?', $row['id']);
                    ++$removed;
                }
            }
        }
        $res->Free();

        ModuleFunction('OnAfterPushSend', [$payload, $message, ['sent' => $sent, 'failed' => $failed, 'removed' => $removed]]);

        if ($sent == 0) {
            self::logPushResult(
                $targetId,
                $type,
                $sent,
                $failed,
                $subscriptionCount,
                $lastError != '' ? $lastError : (isset($message['reason']) ? $message['reason'] : 'none'),
                $removed
            );
        } else {
            self::logPushResult($targetId, $type, $sent, $failed, $subscriptionCount, 'ok', $removed);
        }

        $out = ['sent' => $sent, 'failed' => $failed, 'removed' => $removed, 'subscriptions' => $subscriptionCount];
        if ($sent == 0 && $failed > 0) {
            $out['reason'] = 'delivery_failed';
            if ($lastError != '') {
                $out['lastError'] = $lastError;
            }
        }

        return $out;
    }

    /**
     * Count stored push subscriptions for a user/admin.
     */
    public static function countSubscriptions($area, $targetId)
    {
        global $db;

        self::ensureSchema();

        $res = $db->Query(
            'SELECT COUNT(*) AS c FROM {pre}push_subscriptions WHERE `area`=? AND '
            .($area == self::AREA_USER ? '`userid`=?' : '`adminid`=?'),
            $area,
            (int) $targetId
        );
        $row = $res->FetchArray(MYSQLI_ASSOC);
        $res->Free();

        return isset($row['c']) ? (int) $row['c'] : 0;
    }

    /**
     * Web Push when a mail is stored (mailbox ReceiveMail) – independent of notify_email / PostNotification.
     *
     * @param BMUser $user
     * @param BMMail $mail
     * @param int    $folderId
     * @param bool   $filterNotify FILTER_ACTIONFLAG_NOTIFY
     * @param int    $unreadCount  for notify_newemail text
     *
     * @return array
     */
    public static function sendNewMailPush($user, $mail, $folderId, $filterNotify = false, $unreadCount = 1)
    {
        global $bm_prefs, $lang_custom;

        $mailId = is_object($mail) && isset($mail->id) ? (int) $mail->id : 0;

        PutLog(sprintf(
            'Web Push: user %d mailid=%d receive handler (filter_notify=%d, spam=%d)',
            is_object($user) ? $user->_id : 0,
            $mailId,
            $filterNotify ? 1 : 0,
            (is_object($mail) && ($mail->flags & FLAG_SPAM) != 0) ? 1 : 0
        ), PRIO_NOTE, __FILE__, __LINE__);

        if (!is_object($user) || !is_object($mail)) {
            return ['sent' => 0, 'failed' => 0, 'removed' => 0, 'reason' => 'invalid_args'];
        }

        if (!self::isEnabled()) {
            PutLog(sprintf(
                'Web Push: user %d mailid=%d skipped (disabled in ACP or no VAPID)',
                $user->_id,
                $mailId
            ), PRIO_NOTE, __FILE__, __LINE__);

            return ['sent' => 0, 'failed' => 0, 'removed' => 0, 'reason' => 'push_disabled'];
        }

        if (($mail->flags & FLAG_SPAM) != 0) {
            PutLog(sprintf(
                'Web Push: user %d mailid=%d skipped (spam)',
                $user->_id,
                $mailId
            ), PRIO_NOTE, __FILE__, __LINE__);

            return ['sent' => 0, 'failed' => 0, 'removed' => 0, 'reason' => 'spam'];
        }

        $type = $filterNotify ? self::TYPE_MAIL_FILTER : self::TYPE_MAIL;

        if (!self::userHasPushDelivery($user->_id)) {
            PutLog(sprintf(
                'Web Push: user %d mailid=%d skipped (no subscription / push not enabled)',
                $user->_id,
                $mailId
            ), PRIO_NOTE, __FILE__, __LINE__);

            return ['sent' => 0, 'failed' => 0, 'removed' => 0, 'reason' => 'no_delivery'];
        }

        if (!self::userAllowsType($user->_id, $type)) {
            PutLog(sprintf(
                'Web Push: user %d mailid=%d skipped (type %s off in prefs)',
                $user->_id,
                $mailId,
                $type
            ), PRIO_NOTE, __FILE__, __LINE__);

            return ['sent' => 0, 'failed' => 0, 'removed' => 0, 'reason' => 'prefs_blocked'];
        }

        if ($filterNotify) {
            $phrase = isset($lang_custom['notify_email']) ? $lang_custom['notify_email'] : '%s: %s';
            $params = [
                HTMLFormat(DecodeSingleEMail(ExtractMailAddress($mail->GetHeaderValue('from')))),
                HTMLFormat($mail->GetHeaderValue('subject')),
            ];
            $url = 'email.read.php?id='.$mailId.'&';
        } else {
            $unreadCount = max(1, (int) $unreadCount);
            if ($unreadCount <= 1) {
                $phrase = isset($lang_custom['notify_email']) ? $lang_custom['notify_email'] : '%s: %s';
                $params = [
                    HTMLFormat(DecodeSingleEMail(ExtractMailAddress($mail->GetHeaderValue('from')))),
                    HTMLFormat($mail->GetHeaderValue('subject')),
                ];
            } else {
                $phrase = isset($lang_custom['notify_newemail']) ? $lang_custom['notify_newemail'] : '%d: %s';
                $params = [
                    $unreadCount,
                    HTMLFormat($mail->GetHeaderValue('subject')).', ...',
                ];
            }
            $url = 'email.php?folder='.(int) $folderId.'&';
        }

        $body = @vsprintf($phrase, $params);
        if ($body === false) {
            $body = $phrase;
        }
        $body = self::plainText($body);

        return self::send([
            'area' => self::AREA_USER,
            'targetId' => $user->_id,
            'type' => $type,
            'title' => self::pushTitleForType($type),
            'body' => $body,
            'url' => $url,
            'icon' => 'pwa-icon.php?size=192',
            'tag' => self::pushNotificationTag($type),
        ]);
    }

    /**
     * Test push – ignores per-type prefs, only requires stored subscription(s).
     */
    public static function sendTestPush($userId)
    {
        global $bm_prefs, $lang_user;

        $title = isset($bm_prefs['titel']) ? $bm_prefs['titel'] : 'b1gMail';
        $body = isset($lang_user['push_test_body']) ? $lang_user['push_test_body'] : 'Test';

        return self::send([
            'area' => self::AREA_USER,
            'targetId' => (int) $userId,
            'type' => self::TYPE_MAIL,
            'skipPrefCheck' => true,
            'title' => $title,
            'body' => $body,
            'url' => 'start.php',
            'tag' => 'push-test',
        ]);
    }

    /**
     * Map PostNotification to Web Push (mail, calendar, …).
     */
    /**
     * @return array sent, failed, removed, …
     */
    public static function sendFromNotification($user, $notificationId, $textPhrase, $textParams, $link, $icon, $flags, $class)
    {
        global $bm_prefs, $lang_custom, $tpl;

        if (!is_object($user)) {
            return ['sent' => 0, 'failed' => 0, 'removed' => 0, 'reason' => 'no_user'];
        }

        if (!self::isEnabled()) {
            PutLog(sprintf(
                'Web Push: user %d class=%s skipped (Web Push off or VAPID missing in ACP)',
                $user->_id,
                $class
            ), PRIO_NOTE, __FILE__, __LINE__);

            return ['sent' => 0, 'failed' => 0, 'removed' => 0, 'reason' => 'push_disabled'];
        }

        $type = self::classToType($class);
        if ($type === false) {
            PutLog(sprintf(
                'Web Push: user %d class=%s skipped (unknown notification class)',
                $user->_id,
                $class
            ), PRIO_NOTE, __FILE__, __LINE__);

            return ['sent' => 0, 'failed' => 0, 'removed' => 0, 'reason' => 'unknown_class'];
        }

        if (($flags & NOTIFICATION_FLAG_USELANG) != 0) {
            $phrase = isset($lang_custom[$textPhrase]) ? $lang_custom[$textPhrase] : $textPhrase;
        } else {
            $phrase = $textPhrase;
        }

        $params = [];
        if (is_string($textParams) && $textParams != '') {
            $params = ExplodeOutsideOfQuotation($textParams, ',');
        }

        if (count($params)) {
            $body = @vsprintf($phrase, $params);
            if ($body === false) {
                $body = $phrase;
            }
        } else {
            $body = $phrase;
        }
        $body = self::plainText($body);

        $url = self::pushUrlForNotification($class, $link, $flags);

        return self::send([
            'area' => self::AREA_USER,
            'targetId' => $user->_id,
            'type' => $type,
            'title' => self::pushTitleForType($type),
            'body' => $body,
            'url' => $url,
            'icon' => 'pwa-icon.php?size=192',
            'tag' => self::pushNotificationTag($type),
        ]);
    }

    public static function getCorePushTypes($area)
    {
        global $lang_user;

        if ($area == self::AREA_ADMIN) {
            return [];
        }

        return [
            self::TYPE_MAIL => $lang_user['push_type_mail'],
            self::TYPE_MAIL_FILTER => $lang_user['push_type_mail_filter'],
            self::TYPE_CALENDAR => $lang_user['push_type_calendar'],
            self::TYPE_BIRTHDAY => $lang_user['push_type_birthday'],
            self::TYPE_TASK => $lang_user['push_type_task'],
            self::TYPE_WEBDISK => $lang_user['push_type_webdisk'],
        ];
    }

    /**
     * All push types for prefs UI (core + plugins).
     */
    public static function getPushTypes($area)
    {
        $types = self::getCorePushTypes($area);
        ModuleFunction('GetPushTypes', [&$types, $area]);

        return $types;
    }

    /**
     * Fix legacy keys (core_mail) and default missing types to enabled.
     */
    public static function normalizeUserPushPrefs($prefs, $area = self::AREA_USER)
    {
        if (!is_array($prefs)) {
            $prefs = [];
        }

        if (!isset($prefs['types']) || !is_array($prefs['types'])) {
            return $prefs;
        }

        $normalized = [];
        foreach (self::getPushTypes($area) as $typeKey => $label) {
            $postKey = str_replace('.', '_', $typeKey);
            if (array_key_exists($typeKey, $prefs['types'])) {
                $normalized[$typeKey] = !empty($prefs['types'][$typeKey]);
            } elseif (array_key_exists($postKey, $prefs['types'])) {
                $normalized[$typeKey] = !empty($prefs['types'][$postKey]);
            }
        }
        $prefs['types'] = $normalized;

        return $prefs;
    }

    /**
     * Booleans for prefs UI (Smarty "default:true" treats false as empty).
     */
    public static function getPushTypePrefsForUi($userId, $area = self::AREA_USER)
    {
        $prefs = self::getUserPushPrefs($userId);
        $ui = [];
        $hasSaved = isset($prefs['types']) && is_array($prefs['types']) && count($prefs['types']) > 0;

        foreach (self::getPushTypes($area) as $typeKey => $label) {
            if ($hasSaved && array_key_exists($typeKey, $prefs['types'])) {
                $ui[$typeKey] = !empty($prefs['types'][$typeKey]);
            } else {
                $ui[$typeKey] = true;
            }
        }

        return $ui;
    }

    public static function isPushPromptDismissed($userId)
    {
        $prefs = self::getUserPushPrefs($userId);

        return !empty($prefs['promptDismissed']);
    }

    public static function dismissPushPrompt($userId)
    {
        self::setUserPushPrefs($userId, ['promptDismissed' => true]);
    }

    public static function getUserPushPrefs($userId)
    {
        $user = _new('BMUser', [(int) $userId]);
        $raw = $user->GetPref('pushTypes');
        $prefs = @unserialize($raw);
        if (!is_array($prefs)) {
            $prefs = [];
        }

        return self::normalizeUserPushPrefs($prefs);
    }

    public static function setUserPushPrefs($userId, $prefs)
    {
        $user = _new('BMUser', [(int) $userId]);
        $existing = self::getUserPushPrefs($userId);
        if (!is_array($prefs)) {
            $prefs = [];
        }
        $merged = array_merge($existing, $prefs);
        $merged = self::normalizeUserPushPrefs($merged);
        $user->SetPref('pushTypes', serialize($merged));
    }

    /**
     * PHP converts dots in POST field names to underscores (push_type[core.mail] → core_mail).
     */
    public static function pushTypesFromPost($postTypes, $area = self::AREA_USER)
    {
        if (!is_array($postTypes)) {
            $postTypes = [];
        }

        $types = [];
        foreach (self::getPushTypes($area) as $typeKey => $label) {
            $postKey = str_replace('.', '_', $typeKey);
            $types[$typeKey] = !empty($postTypes[$typeKey]) || !empty($postTypes[$postKey]);
        }

        return $types;
    }

    public static function userHasPushDelivery($userId, $prefs = null)
    {
        if ($prefs === null) {
            $prefs = self::getUserPushPrefs($userId);
        }
        if (!empty($prefs['enabled'])) {
            return true;
        }

        return self::countSubscriptions(self::AREA_USER, (int) $userId) > 0;
    }

    public static function userAllowsType($userId, $type)
    {
        $prefs = self::getUserPushPrefs($userId);
        if (!self::userHasPushDelivery($userId, $prefs)) {
            return false;
        }
        if (!isset($prefs['types']) || !is_array($prefs['types']) || count($prefs['types']) === 0) {
            return true;
        }
        if (!array_key_exists($type, $prefs['types'])) {
            return true;
        }

        return !empty($prefs['types'][$type]);
    }

    public static function userNotifyEmailEnabled($userId)
    {
        global $db;

        $res = $db->Query('SELECT notify_email FROM {pre}users WHERE `id`=?', (int) $userId);
        if ($res->RowCount() != 1) {
            $res->Free();

            return false;
        }
        list($notifyEmail) = $res->FetchArray(MYSQLI_NUM);
        $res->Free();

        return $notifyEmail == 'yes';
    }

    public static function getAdminPushPrefs($adminId)
    {
        global $db;

        self::ensureAdminPushPrefsColumn();

        $prefs = [];
        $res = $db->Query('SELECT push_prefs FROM {pre}admins WHERE `adminid`=?', (int) $adminId);
        if ($res->RowCount() == 1) {
            list($raw) = $res->FetchArray(MYSQLI_NUM);
            $decoded = @unserialize($raw);
            if (is_array($decoded)) {
                $prefs = $decoded;
            }
        }
        $res->Free();

        return self::normalizeAdminPushPrefs($prefs);
    }

    /**
     * Merge admin push prefs; new plugin types default to enabled.
     */
    public static function normalizeAdminPushPrefs($prefs)
    {
        if (!is_array($prefs)) {
            $prefs = [];
        }

        if (!isset($prefs['types']) || !is_array($prefs['types'])) {
            return $prefs;
        }

        $normalized = [];
        foreach (self::getPushTypes(self::AREA_ADMIN) as $typeKey => $label) {
            $postKey = str_replace('.', '_', $typeKey);
            if (array_key_exists($typeKey, $prefs['types'])) {
                $normalized[$typeKey] = !empty($prefs['types'][$typeKey]);
            } elseif (array_key_exists($postKey, $prefs['types'])) {
                $normalized[$typeKey] = !empty($prefs['types'][$postKey]);
            } else {
                $normalized[$typeKey] = true;
            }
        }
        $prefs['types'] = $normalized;

        return $prefs;
    }

    public static function setAdminPushPrefs($adminId, $prefs)
    {
        global $db;

        self::ensureAdminPushPrefsColumn();

        $existing = self::getAdminPushPrefs($adminId);
        if (!is_array($prefs)) {
            $prefs = [];
        }
        $merged = array_merge($existing, $prefs);
        $merged = self::normalizeAdminPushPrefs($merged);

        $db->Query(
            'UPDATE {pre}admins SET push_prefs=? WHERE adminid=?',
            serialize($merged),
            (int) $adminId
        );
    }

    /**
     * Enable all admin push types (e.g. after browser subscribe / sync).
     */
    public static function enableAdminPushTypes($adminId, $enabledFlag = true)
    {
        $types = [];
        foreach (array_keys(self::getPushTypes(self::AREA_ADMIN)) as $t) {
            $types[$t] = true;
        }

        self::setAdminPushPrefs($adminId, [
            'enabled' => $enabledFlag,
            'types' => $types,
        ]);
    }

    public static function adminHasPushDelivery($adminId, $prefs = null)
    {
        if ($prefs === null) {
            $prefs = self::getAdminPushPrefs($adminId);
        }
        if (!empty($prefs['enabled'])) {
            return true;
        }

        return self::countSubscriptions(self::AREA_ADMIN, (int) $adminId) > 0;
    }

    public static function adminAllowsType($adminId, $type)
    {
        $prefs = self::getAdminPushPrefs($adminId);
        if (!self::adminHasPushDelivery($adminId, $prefs)) {
            return false;
        }
        if (!isset($prefs['types']) || !is_array($prefs['types']) || count($prefs['types']) === 0) {
            return true;
        }
        if (!array_key_exists($type, $prefs['types'])) {
            return true;
        }

        return !empty($prefs['types'][$type]);
    }

    public static function ensureAdminPushPrefsColumn()
    {
        global $db;

        global $mysql;

        $table = $mysql['prefix'].'admins';
        $res = $db->Query('SHOW COLUMNS FROM `'.$table.'` LIKE ?', 'push_prefs');
        if ($res->RowCount() == 0) {
            $db->Query('ALTER TABLE `'.$table.'` ADD `push_prefs` text NOT NULL');
        }
        $res->Free();
    }

    /**
     * Click target for Web Push (not in-app JS links like showCalendarDate(...)).
     */
    private static function pushUrlForNotification($class, $link, $flags)
    {
        switch ($class) {
            case '::dateReminder':
                if (preg_match('/showCalendarDate\s*\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/', $link, $m)) {
                    return sprintf(
                        'organizer.calendar.php?date=%d&',
                        (int) $m[2]
                    );
                }

                return 'organizer.calendar.php?';

            case '::taskReminder':
                if (preg_match('/(?:editTask[^&]*&|^[^?]*[?&])id=(\d+)/', $link, $m)) {
                    return 'organizer.todo.php?action=editTask&id='.(int) $m[1].'&';
                }

                return 'organizer.todo.php?';

            case '::notifyBirthday':
            case '::birthdayReminder':
                if (preg_match('/id=(\d+)/', $link, $m)) {
                    return 'organizer.addressbook.php?action=editContact&id='.(int) $m[1].'&';
                }

                return 'organizer.addressbook.php?';

            default:
                if (($flags & NOTIFICATION_FLAG_JSLINK) != 0) {
                    return 'start.php';
                }

                return $link != '' ? $link : 'start.php';
        }
    }

    /**
     * Stable OS notification tag per push type (mail / calendar / task do not replace each other).
     */
    private static function pushNotificationTag($type)
    {
        return 'bm-push-'.str_replace('.', '-', $type);
    }

    /**
     * Generic notification text when session is locked (no sender/subject on lock screen).
     */
    private static function genericPushBodyForType($type, $area = self::AREA_USER)
    {
        global $lang_user, $lang_admin;

        $lang = ($area == self::AREA_ADMIN && !empty($lang_admin['push_privacy_body'])) ? $lang_admin : $lang_user;
        $map = [
            self::TYPE_MAIL => 'push_privacy_body_mail',
            self::TYPE_MAIL_FILTER => 'push_privacy_body_mail_filter',
            self::TYPE_CALENDAR => 'push_privacy_body_calendar',
            self::TYPE_BIRTHDAY => 'push_privacy_body_birthday',
            self::TYPE_TASK => 'push_privacy_body_task',
            self::TYPE_WEBDISK => 'push_privacy_body_webdisk',
        ];

        if (isset($map[$type]) && !empty($lang[$map[$type]])) {
            return self::plainText($lang[$map[$type]]);
        }

        return self::plainText(!empty($lang['push_privacy_body']) ? $lang['push_privacy_body'] : 'New notification');
    }

    /**
     * Strip sensitive details from push payload (idle lock / UI locked).
     *
     * @param array  $payload
     * @param string $type
     * @param string $area
     */
    private static function applyLockedScreenPushPrivacy(&$payload, $type, $area)
    {
        global $bm_prefs;

        $payload['title'] = self::plainText(!empty($bm_prefs['titel']) ? $bm_prefs['titel'] : 'b1gMail');
        $payload['body'] = self::genericPushBodyForType($type, $area);
        $payload['url'] = ($area == self::AREA_ADMIN) ? 'index.php' : 'start.php';
        $payload['tag'] = self::pushNotificationTag($type).'-private';
    }

    /**
     * Distinct notification title per category (not only service name).
     */
    private static function pushTitleForType($type, $area = self::AREA_USER)
    {
        global $bm_prefs;

        $types = self::getPushTypes($area);
        if (isset($types[$type]) && $types[$type] != '') {
            return self::plainText($types[$type]);
        }

        return self::plainText($bm_prefs['titel']);
    }

    private static function classToType($class)
    {
        switch ($class) {
            case '::newEMail':
                return self::TYPE_MAIL;
            case '::notifyEMail':
                return self::TYPE_MAIL_FILTER;
            case '::dateReminder':
                return self::TYPE_CALENDAR;
            case '::notifyBirthday':
                return self::TYPE_BIRTHDAY;
            case '::taskReminder':
                return self::TYPE_TASK;
            default:
                if (strpos($class, '::webdiskShareExpired') === 0 || strpos($class, '::webdiskShareDownload') === 0) {
                    return self::TYPE_WEBDISK;
                }
                if (strpos($class, '::') === 0) {
                    return 'core.'.substr($class, 2);
                }

                return false;
        }
    }

    private static function plainText($html)
    {
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES, 'UTF-8');
        if (function_exists('mb_convert_encoding')) {
            $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        }
        $text = trim(preg_replace('/\s+/u', ' ', $text));
        if (strlen($text) > 240) {
            $text = substr($text, 0, 237).'...';
        }

        return $text;
    }

    private static function absoluteUrl($url, $area)
    {
        global $bm_prefs;

        if ($url == '' || preg_match('#^https?://#i', $url)) {
            return $url;
        }

        $base = $bm_prefs['selfurl'];
        if ($area == self::AREA_ADMIN) {
            $base .= 'admin/';
        }

        return $base.ltrim($url, '/');
    }

    /**
     * Newest active PHP session for user (SMTP/cron have no $thisUser session).
     */
    private static function findUserSessionId($userId)
    {
        return SessionFindActivePushSessionId((int) $userId, false);
    }

    /**
     * Newest active PHP session for admin (push from LI/cron has no admin session).
     */
    private static function findAdminSessionId($adminId)
    {
        return SessionFindActivePushSessionId((int) $adminId, true);
    }

    private static function appendSid($url, $userId)
    {
        if ($url == '' || strpos($url, 'sid=') !== false) {
            return $url;
        }

        $sid = '';

        global $thisUser;
        if (isset($thisUser) && is_object($thisUser) && $thisUser->_id == $userId && session_id() != '') {
            $sid = session_id();
        }
        if ($sid == '') {
            $sid = self::findUserSessionId($userId);
        }
        if ($sid == '') {
            return $url;
        }

        $sep = strpos($url, '?') !== false ? '&' : '?';

        return $url.$sep.'sid='.rawurlencode($sid);
    }

    private static function appendAdminSid($url, $adminId)
    {
        if ($url == '' || strpos($url, 'sid=') !== false) {
            return $url;
        }

        $sid = '';
        global $adminRow;
        if (isset($adminRow) && is_array($adminRow) && (int) ($adminRow['adminid'] ?? 0) === (int) $adminId && session_id() != '') {
            $sid = session_id();
        }
        if ($sid == '') {
            $sid = self::findAdminSessionId($adminId);
        }
        if ($sid == '') {
            return $url;
        }

        $sep = strpos($url, '?') !== false ? '&' : '?';

        return $url.$sep.'sid='.rawurlencode($sid);
    }
}
