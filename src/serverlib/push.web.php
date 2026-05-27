<?php
/*
 * b1gMail – Web Push payload encryption (aes128gcm) and delivery.
 */

if (!defined('B1GMAIL_INIT')) {
    die('Directly calling this file is not supported');
}

include_once B1GMAIL_DIR.'serverlib/push.vapid.php';

class BMPushWeb
{
    /**
     * Encrypt and POST a JSON payload to a push subscription endpoint.
     *
     * @param array  $subscription keys: endpoint, p256dh, auth
     * @param string $payloadJson  UTF-8 JSON string
     * @param string $vapidPrivatePem
     * @param string $vapidPublicUncompressed 65-byte key
     * @param string $vapidSubject mailto: or https:// URI
     *
     * @return array{ok:bool,status:int,error:string}
     */
    public static function send($subscription, $payloadJson, $vapidPrivatePem, $vapidPublicUncompressed, $vapidSubject)
    {
        $endpoint = $subscription['endpoint'];
        $userPublicKey = BMPushVapid::base64UrlDecode($subscription['p256dh']);
        $userAuth = BMPushVapid::base64UrlDecode($subscription['auth']);

        if (strlen($userPublicKey) !== 65 || strlen($userAuth) < 16) {
            return ['ok' => false, 'status' => 0, 'error' => 'invalid_subscription_keys'];
        }

        $encrypted = self::encryptPayload($payloadJson, $userPublicKey, $userAuth);
        if ($encrypted === false) {
            return ['ok' => false, 'status' => 0, 'error' => 'encrypt_failed'];
        }

        $authHeader = BMPushVapid::getAuthorizationHeader(
            $vapidPrivatePem,
            $vapidPublicUncompressed,
            $endpoint,
            $vapidSubject
        );
        if ($authHeader === false) {
            return ['ok' => false, 'status' => 0, 'error' => 'vapid_failed'];
        }

        return self::httpPost($endpoint, $encrypted, $authHeader);
    }

    /**
     * @return string|false binary body
     */
    /**
     * RFC 8188 / RFC 8291: last record ends with delimiter 0x02 (after optional zero padding).
     */
    private static function padPayload($payload)
    {
        return $payload."\x02";
    }

    public static function encryptPayload($payload, $userPublicKey, $userAuth)
    {
        $payload = self::padPayload($payload);

        $localKey = openssl_pkey_new([
            'private_key_type' => OPENSSL_KEYTYPE_EC,
            'curve_name' => 'prime256v1',
        ]);
        if ($localKey === false) {
            return false;
        }

        $localDetails = openssl_pkey_get_details($localKey);
        if ($localDetails === false || !isset($localDetails['ec']['x'], $localDetails['ec']['y'])) {
            return false;
        }

        $localPublicKey = "\x04".$localDetails['ec']['x'].$localDetails['ec']['y'];

        $userPem = self::publicKeyToPem($userPublicKey);
        $userKey = openssl_pkey_get_public($userPem);
        if ($userKey === false) {
            return false;
        }

        if (!function_exists('openssl_pkey_derive')) {
            return false;
        }

        $sharedSecret = openssl_pkey_derive($userKey, $localKey, 256);
        if ($sharedSecret === false) {
            return false;
        }

        $salt = random_bytes(16);

        $ikmInfo = "WebPush: info\x00".$userPublicKey.$localPublicKey;
        $ikm = self::hkdf($userAuth, $sharedSecret, $ikmInfo, 32);

        $cek = self::hkdf($salt, $ikm, "Content-Encoding: aes128gcm\x00", 16);
        $nonce = self::hkdf($salt, $ikm, "Content-Encoding: nonce\x00", 12);

        $tag = '';
        $ciphertext = openssl_encrypt(
            $payload,
            'aes-128-gcm',
            $cek,
            OPENSSL_RAW_DATA,
            $nonce,
            $tag,
            '',
            16
        );
        if ($ciphertext === false) {
            return false;
        }

        $recordSize = pack('N', 4096);

        return $salt.$recordSize.chr(strlen($localPublicKey)).$localPublicKey.$ciphertext.$tag;
    }

    private static function hkdf($salt, $ikm, $info, $length)
    {
        $prk = hash_hmac('sha256', $ikm, $salt, true);
        $t = '';
        $last = '';
        for ($i = 1; strlen($t) < $length; ++$i) {
            $last = hash_hmac('sha256', $last.$info.chr($i), $prk, true);
            $t .= $last;
        }

        return substr($t, 0, $length);
    }

    private static function publicKeyToPem($rawKey)
    {
        $der = "\x30\x59\x30\x13\x06\x07\x2a\x86\x48\xce\x3d\x02\x01\x06\x08\x2a\x86\x48\xce\x3d\x03\x01\x07\x03\x42\x00".$rawKey;

        return "-----BEGIN PUBLIC KEY-----\n"
            .chunk_split(base64_encode($der), 64)
            ."-----END PUBLIC KEY-----";
    }

    /**
     * @return array{ok:bool,status:int,error:string}
     */
    private static function httpPost($url, $body, $authHeader)
    {
        $headers = [
            'Content-Type: application/octet-stream',
            'Content-Encoding: aes128gcm',
            'TTL: 86400',
            'Authorization: '.$authHeader,
        ];

        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 15,
            ]);
            $responseBody = curl_exec($ch);
            $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $ok = $status >= 200 && $status < 300;
            $error = $ok ? '' : 'http_'.$status;
            if (!$ok && is_string($responseBody) && $responseBody !== '') {
                $hint = preg_replace('/\s+/u', ' ', trim($responseBody));
                if (strlen($hint) > 120) {
                    $hint = substr($hint, 0, 120);
                }
                $error .= ':'.str_replace(':', '_', $hint);
            }

            return ['ok' => $ok, 'status' => $status, 'error' => $error, 'body' => is_string($responseBody) ? $responseBody : ''];
        }

        $opts = [
            'http' => [
                'method' => 'POST',
                'header' => implode("\r\n", $headers)."\r\n",
                'content' => $body,
                'timeout' => 15,
                'ignore_errors' => true,
            ],
        ];
        $ctx = stream_context_create($opts);
        $response = @file_get_contents($url, false, $ctx);
        $status = 0;
        if (isset($http_response_header[0]) && preg_match('/\d{3}/', $http_response_header[0], $m)) {
            $status = (int) $m[0];
        }
        $ok = $status >= 200 && $status < 300;

        return [
            'ok' => $ok,
            'status' => $status,
            'error' => $ok ? '' : ($response === false ? 'http_failed' : 'http_'.$status),
        ];
    }
}
