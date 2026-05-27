<?php
/*
 * b1gMail – Web Push subscription API (customer area).
 */

require './serverlib/init.inc.php';
include B1GMAIL_DIR.'serverlib/push.class.php';

header('Content-Type: application/json; charset=utf-8');

if (!BMPush::isEnabled()) {
    echo json_encode(['ok' => false, 'error' => 'disabled']);
    exit;
}

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

if ($action == 'vapidPublicKey') {
    echo json_encode(['ok' => true, 'publicKey' => BMPush::getPublicKey()]);
    exit;
}

RequestPrivileges(PRIVILEGES_USER);

$input = file_get_contents('php://input');
$json = $input ? json_decode($input, true) : [];

if ($action == 'subscribe') {
    $sub = is_array($json) && isset($json['subscription']) ? $json['subscription'] : $json;
    $ok = BMPush::subscribe(BMPush::AREA_USER, $thisUser->_id, $sub);
    if ($ok && is_array($json) && !empty($json['types']) && is_array($json['types'])) {
        BMPush::setUserPushPrefs($thisUser->_id, [
            'enabled' => true,
            'types' => $json['types'],
        ]);
    } elseif ($ok) {
        $existing = BMPush::getUserPushPrefs($thisUser->_id);
        $types = [];
        foreach (array_keys(BMPush::getPushTypes(BMPush::AREA_USER)) as $t) {
            if (isset($existing['types']) && is_array($existing['types']) && array_key_exists($t, $existing['types'])) {
                $types[$t] = !empty($existing['types'][$t]);
            } else {
                $types[$t] = true;
            }
        }
        BMPush::setUserPushPrefs($thisUser->_id, ['enabled' => true, 'types' => $types]);
    }
    echo json_encode(['ok' => (bool) $ok]);
    exit;
}

if ($action == 'unsubscribe') {
    $endpoint = isset($json['endpoint']) ? $json['endpoint'] : '';
    $ok = BMPush::unsubscribe(BMPush::AREA_USER, $thisUser->_id, $endpoint);
    BMPush::setUserPushPrefs($thisUser->_id, ['enabled' => false, 'types' => []]);
    echo json_encode(['ok' => (bool) $ok]);
    exit;
}

if ($action == 'pushTypes') {
    echo json_encode([
        'ok' => true,
        'types' => BMPush::getPushTypes(BMPush::AREA_USER),
        'prefs' => BMPush::getUserPushPrefs($thisUser->_id),
    ]);
    exit;
}

if ($action == 'status') {
    $prefs = BMPush::getUserPushPrefs($thisUser->_id);
    echo json_encode([
        'ok' => true,
        'prefsEnabled' => !empty($prefs['enabled']),
        'promptDismissed' => !empty($prefs['promptDismissed']),
        'subscriptions' => BMPush::countSubscriptions(BMPush::AREA_USER, $thisUser->_id),
    ]);
    exit;
}

if ($action == 'dismissPrompt') {
    BMPush::dismissPushPrompt($thisUser->_id);
    echo json_encode(['ok' => true]);
    exit;
}

echo json_encode(['ok' => false, 'error' => 'unknown_action']);
