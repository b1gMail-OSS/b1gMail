<?php
/*
 * b1gMail – Web App Manifest (customer area).
 */

require './serverlib/init.inc.php';

header('Content-Type: application/manifest+json; charset=utf-8');

$start = $bm_prefs['selfurl'].'index.php';
$iconBase = $bm_prefs['selfurl'].'pwa-icon.php';

$manifest = [
    'id' => $bm_prefs['selfurl'],
    'name' => $bm_prefs['titel'],
    'short_name' => mb_substr($bm_prefs['titel'], 0, 12),
    'description' => $bm_prefs['titel'],
    'start_url' => $start,
    'scope' => $bm_prefs['selfurl'],
    'display' => 'standalone',
    'orientation' => 'any',
    'background_color' => '#1e293b',
    'theme_color' => '#066fd1',
    'icons' => [
        [
            'src' => $iconBase.'?size=192',
            'sizes' => '192x192',
            'type' => 'image/png',
            'purpose' => 'any',
        ],
        [
            'src' => $iconBase.'?size=512',
            'sizes' => '512x512',
            'type' => 'image/png',
            'purpose' => 'any',
        ],
    ],
];

echo json_encode($manifest, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
