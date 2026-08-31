<?php
/*
 * b1gMail – Web Push subscription API (admin area).
 */

include '../serverlib/admin.inc.php';
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

RequestPrivileges(PRIVILEGES_ADMIN);

$input = file_get_contents('php://input');
$json = $input ? json_decode($input, true) : [];

if ($action == 'subscribe') {
    $sub = is_array($json) && isset($json['subscription']) ? $json['subscription'] : $json;
    $ok = BMPush::subscribe(BMPush::AREA_ADMIN, $adminRow['adminid'], $sub);
    if ($ok) {
        if (is_array($json) && !empty($json['types']) && is_array($json['types'])) {
            BMPush::setAdminPushPrefs($adminRow['adminid'], [
                'enabled' => true,
                'types' => $json['types'],
            ]);
        } else {
            BMPush::enableAdminPushTypes($adminRow['adminid'], true);
        }
    }
    echo json_encode(['ok' => (bool) $ok]);
    exit;
}

if ($action == 'sync') {
    BMPush::enableAdminPushTypes($adminRow['adminid'], true);
    echo json_encode(['ok' => true]);
    exit;
}

if ($action == 'unsubscribe') {
    $endpoint = isset($json['endpoint']) ? $json['endpoint'] : '';
    $ok = BMPush::unsubscribe(BMPush::AREA_ADMIN, $adminRow['adminid'], $endpoint);
    BMPush::setAdminPushPrefs($adminRow['adminid'], ['enabled' => false, 'types' => []]);
    echo json_encode(['ok' => (bool) $ok]);
    exit;
}

if ($action == 'status') {
    $prefs = BMPush::getAdminPushPrefs($adminRow['adminid']);
    echo json_encode([
        'ok' => true,
        'prefsEnabled' => !empty($prefs['enabled']),
        'subscriptions' => BMPush::countSubscriptions(BMPush::AREA_ADMIN, $adminRow['adminid']),
    ]);
    exit;
}

echo json_encode(['ok' => false, 'error' => 'unknown_action']);
