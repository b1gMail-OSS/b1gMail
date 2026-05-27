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
        BMPush::ensureAdminPushPrefsColumn();
        $types = [];
        if (is_array($json) && !empty($json['types']) && is_array($json['types'])) {
            $types = $json['types'];
        } else {
            foreach (array_keys(BMPush::getPushTypes(BMPush::AREA_ADMIN)) as $t) {
                $types[$t] = true;
            }
        }
        global $db;
        $db->Query(
            'UPDATE {pre}admins SET push_prefs=? WHERE adminid=?',
            serialize(['enabled' => true, 'types' => $types]),
            $adminRow['adminid']
        );
    }
    echo json_encode(['ok' => (bool) $ok]);
    exit;
}

if ($action == 'unsubscribe') {
    $endpoint = isset($json['endpoint']) ? $json['endpoint'] : '';
    $ok = BMPush::unsubscribe(BMPush::AREA_ADMIN, $adminRow['adminid'], $endpoint);
    global $db;
    $db->Query(
        'UPDATE {pre}admins SET push_prefs=? WHERE adminid=?',
        serialize(['enabled' => false, 'types' => []]),
        $adminRow['adminid']
    );
    echo json_encode(['ok' => (bool) $ok]);
    exit;
}

echo json_encode(['ok' => false, 'error' => 'unknown_action']);
