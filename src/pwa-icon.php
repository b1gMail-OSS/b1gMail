<?php
/*
 * b1gMail – PWA / Push icon in correct dimensions (192, 512).
 */

if(!defined('B1GMAIL_INIT'))
	require './serverlib/init.inc.php';

$size = isset($_REQUEST['size']) ? (int) $_REQUEST['size'] : 192;
if (!in_array($size, [192, 512], true)) {
    $size = 192;
}

if (!function_exists('imagecreatetruecolor')) {
    header('HTTP/1.1 503 Service Unavailable');
    exit;
}

/**
 * @return string|false absolute path
 */
function PwaIconFindSource()
{
    global $bm_prefs;

    $candidates = [
        B1GMAIL_DIR.'admin/templates/images/favicon-256x256.png',
        B1GMAIL_DIR.'templates/'.$bm_prefs['template'].'/images/logo.png',
        B1GMAIL_DIR.'res/favicon.png',
    ];

    foreach ($candidates as $path) {
        if (is_file($path) && is_readable($path)) {
            $info = @getimagesize($path);
            if ($info && $info[0] > 0 && $info[1] > 0) {
                return $path;
            }
        }
    }

    return false;
}

/**
 * @return resource|false
 */
function PwaIconLoadImage($path)
{
    $info = @getimagesize($path);
    if (!$info) {
        return false;
    }

    switch ($info[2]) {
        case IMAGETYPE_PNG:
            return @imagecreatefrompng($path);
        case IMAGETYPE_JPEG:
            return @imagecreatefromjpeg($path);
        case IMAGETYPE_GIF:
            return @imagecreatefromgif($path);
        case IMAGETYPE_WEBP:
            if (function_exists('imagecreatefromwebp')) {
                return @imagecreatefromwebp($path);
            }

            return false;
        default:
            return false;
    }
}

$sourcePath = PwaIconFindSource();
$src = $sourcePath ? PwaIconLoadImage($sourcePath) : false;

$dst = imagecreatetruecolor($size, $size);
imagealphablending($dst, false);
imagesavealpha($dst, true);
$transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
imagefilledrectangle($dst, 0, 0, $size, $size, $transparent);

if ($src) {
    $sw = imagesx($src);
    $sh = imagesy($src);
    $scale = min($size / $sw, $size / $sh);
    $dw = (int) round($sw * $scale);
    $dh = (int) round($sh * $scale);
    $dx = (int) round(($size - $dw) / 2);
    $dy = (int) round(($size - $dh) / 2);
    imagecopyresampled($dst, $src, $dx, $dy, 0, 0, $dw, $dh, $sw, $sh);
    imagedestroy($src);
} else {
    $bg = imagecolorallocate($dst, 6, 111, 209);
    imagefilledrectangle($dst, 0, 0, $size, $size, $bg);
    $fg = imagecolorallocate($dst, 255, 255, 255);
    $letter = 'M';
    $font = 5;
    $tw = imagefontwidth($font) * strlen($letter);
    $th = imagefontheight($font);
    imagestring($dst, $font, (int) (($size - $tw) / 2), (int) (($size - $th) / 2), $letter, $fg);
}

header('Content-Type: image/png');
header('Cache-Control: public, max-age=604800');
header('Expires: '.gmdate('D, d M Y H:i:s', time() + 604800).' GMT');

imagepng($dst);
imagedestroy($dst);
