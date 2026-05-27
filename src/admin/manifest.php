<?php
/*
 * b1gMail – Web App Manifest (admin area).
 */

include '../serverlib/admin.inc.php';

header('Content-Type: application/manifest+json; charset=utf-8');

$start = $bm_prefs['selfurl'].'admin/welcome.php';
$iconBase = $bm_prefs['selfurl'].'pwa-icon.php';

$manifest = [
    'id' => $bm_prefs['selfurl'].'admin/',
    'name' => $bm_prefs['titel'].' – Admin',
    'short_name' => 'b1gMail ACP',
    'description' => 'b1gMail Admin Control Panel',
    'start_url' => $start,
    'scope' => $bm_prefs['selfurl'].'admin/',
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
