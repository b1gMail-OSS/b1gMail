<?php
/*
 * b1gMail – Web Push VAPID (EC P-256 / ES256), no external dependencies.
 */

if (!defined('B1GMAIL_INIT')) {
    die('Directly calling this file is not supported');
}

class BMPushVapid
{
    /**
     * Generate a new VAPID key pair.
     *
     * @return array{public:string,private:string,publicKeyUncompressed:string}
     */
    public static function generateKeyPair()
    {
        $key = openssl_pkey_new([
            'private_key_type' => OPENSSL_KEYTYPE_EC,
            'curve_name' => 'prime256v1',
        ]);
        if ($key === false) {
            return false;
        }

        $details = openssl_pkey_get_details($key);
        if ($details === false || !isset($details['ec']['x'], $details['ec']['y'])) {
            return false;
        }

        $uncompressed = "\x04".$details['ec']['x'].$details['ec']['y'];

        return [
            'public' => self::base64UrlEncode($uncompressed),
            'private' => self::exportPrivatePem($key),
            'publicKeyUncompressed' => $uncompressed,
        ];
    }

    /**
     * Build VAPID Authorization header value for a push endpoint.
     *
     * @param string $privateKeyPem PEM-encoded EC private key
     * @param string $publicKeyUncompressed 65-byte uncompressed public key (0x04…)
     * @param string $endpoint Push subscription endpoint URL
     * @param string $subject mailto: or https:// contact URI
     */
    public static function normalizePrivateKeyPem($pem)
    {
        $pem = trim((string) $pem);
        if ($pem === '') {
            return '';
        }
        if (strpos($pem, '\\n') !== false) {
            $pem = str_replace('\\n', "\n", $pem);
        }

        return $pem;
    }

    /**
     * 65-byte uncompressed P-256 public key for VAPID (must match client subscription key).
     *
     * @return string|false
     */
    public static function getApplicationServerKeyBytes($publicB64, $privatePem)
    {
        $publicB64 = trim((string) $publicB64);
        $privatePem = self::normalizePrivateKeyPem($privatePem);
        $derived = $privatePem !== '' ? self::publicKeyFromPrivatePem($privatePem) : false;

        if ($publicB64 !== '') {
            $stored = self::base64UrlDecode($publicB64);
            if (strlen($stored) === 65) {
                if ($derived !== false && $stored !== $derived) {
                    return $derived;
                }

                return $stored;
            }
        }

        return $derived;
    }

    public static function getAuthorizationHeader($privateKeyPem, $publicKeyUncompressed, $endpoint, $subject)
    {
        $parsed = parse_url($endpoint);
        if (!isset($parsed['scheme'], $parsed['host'])) {
            return false;
        }

        $audience = $parsed['scheme'].'://'.$parsed['host'];
        if (isset($parsed['port']) && (($parsed['scheme'] === 'https' && $parsed['port'] != 443)
            || ($parsed['scheme'] === 'http' && $parsed['port'] != 80))) {
            $audience .= ':'.$parsed['port'];
        }

        $subject = trim((string) $subject);
        if ($subject === '' || (strpos($subject, 'mailto:') !== 0 && strpos($subject, 'https://') !== 0)) {
            $subject = 'mailto:noreply@localhost';
        }

        $now = time();
        $header = self::base64UrlEncode(json_encode(['typ' => 'JWT', 'alg' => 'ES256']));
        $payload = self::base64UrlEncode(json_encode([
            'aud' => $audience,
            'iat' => $now,
            'exp' => $now + 12 * 3600,
            'sub' => $subject,
        ]));
        $data = $header.'.'.$payload;

        $privateKeyPem = self::normalizePrivateKeyPem($privateKeyPem);
        $pkey = openssl_pkey_get_private($privateKeyPem);
        if ($pkey === false) {
            return false;
        }

        $derSig = '';
        if (!openssl_sign($data, $derSig, $pkey, OPENSSL_ALGO_SHA256)) {
            return false;
        }

        $rawSig = self::derSignatureToRaw($derSig);
        if ($rawSig === false) {
            return false;
        }

        $jwt = $data.'.'.self::base64UrlEncode($rawSig);
        $publicKey = self::base64UrlEncode($publicKeyUncompressed);

        return 'vapid t='.$jwt.', k='.$publicKey;
    }

    public static function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function base64UrlDecode($data)
    {
        $pad = 4 - (strlen($data) % 4);
        if ($pad < 4) {
            $data .= str_repeat('=', $pad);
        }

        return base64_decode(strtr($data, '-_', '+/'), true);
    }

    public static function publicKeyFromPrivatePem($privateKeyPem)
    {
        $pkey = openssl_pkey_get_private($privateKeyPem);
        if ($pkey === false) {
            return false;
        }
        $details = openssl_pkey_get_details($pkey);
        if ($details === false || !isset($details['ec']['x'], $details['ec']['y'])) {
            return false;
        }

        return "\x04".$details['ec']['x'].$details['ec']['y'];
    }

    private static function exportPrivatePem($key)
    {
        $pem = '';
        if (!openssl_pkey_export($key, $pem)) {
            return false;
        }

        return $pem;
    }

    /**
     * Convert ASN.1 DER ECDSA signature to raw R||S (64 bytes for P-256).
     */
    private static function derSignatureToRaw($der)
    {
        $pos = 0;
        if (strlen($der) < 8 || ord($der[$pos++]) !== 0x30) {
            return false;
        }
        self::readAsn1Length($der, $pos);

        if (ord($der[$pos++]) !== 0x02) {
            return false;
        }
        $rLen = self::readAsn1Length($der, $pos);
        $r = substr($der, $pos, $rLen);
        $pos += $rLen;

        if (ord($der[$pos++]) !== 0x02) {
            return false;
        }
        $sLen = self::readAsn1Length($der, $pos);
        $s = substr($der, $pos, $sLen);

        $r = ltrim($r, "\x00");
        $s = ltrim($s, "\x00");
        $r = str_pad($r, 32, "\x00", STR_PAD_LEFT);
        $s = str_pad($s, 32, "\x00", STR_PAD_LEFT);

        if (strlen($r) !== 32 || strlen($s) !== 32) {
            return false;
        }

        return $r.$s;
    }

    private static function readAsn1Length($der, &$pos)
    {
        $len = ord($der[$pos++]);
        if ($len & 0x80) {
            $n = $len & 0x7f;
            $len = 0;
            for ($i = 0; $i < $n; ++$i) {
                $len = ($len << 8) | ord($der[$pos++]);
            }
        }

        return $len;
    }
}
