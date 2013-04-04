<?php

namespace Facebook;

use Facebook\Exception\InvalidEncryptionAlgorithmException;
use Facebook\Exception\InvalidEncryptionSignatureException;

class Utils
{
    const SIGNED_REQUEST_ALGORITHM = 'HMAC-SHA256';

    public static function makeSignedRequest($data, $app_secret)
    {
        if ( ! is_array($data))
            throw new \InvalidArgumentException('makeSignedRequest expects an array. Got: ' . print_r($data, true));

        $data['algorithm'] = self::SIGNED_REQUEST_ALGORITHM;
        $data['issued_at'] = time();
        $json = json_encode($data);
        $b64 = self::base64UrlEncode($json);
        $raw_sig = hash_hmac('sha256', $b64, $app_secret, $raw = true);
        $sig = self::base64UrlEncode($raw_sig);
        return sprintf('%s.%s', $sig, $b64);
    }

    /**
     * Parses a signed_request and validates the signature.
     *
     * @param string $signed_request A signed token
     * @param $app_secret
     * @throws Exception\InvalidEncryptionAlgorithmException
     * @throws \InvalidArgumentException
     * @throws Exception\InvalidEncryptionSignatureException
     * @return array The payload inside it or null if the sig is wrong
     */
    public static function parseSignedRequest($signed_request, $app_secret)
    {
        $input = explode('.', $signed_request, 2);

        if(count($input) < 2)
            throw new \InvalidArgumentException(sprintf('Invalid signed_request format. Expected: "xxx.xxx", got: "%s"', $signed_request));

        list($encoded_sig, $payload) = $input;

        // decode the data
        $sig = self::base64UrlDecode($encoded_sig);
        $data = json_decode(self::base64UrlDecode($payload), true);

        if (strtoupper($data['algorithm']) !== self::SIGNED_REQUEST_ALGORITHM)
            throw new InvalidEncryptionAlgorithmException('Unknown algorithm. Expected ' . self::SIGNED_REQUEST_ALGORITHM);

        // check sig
        $expected_sig = hash_hmac('sha256', $payload, $app_secret, $raw = true);
        if ($sig !== $expected_sig)
            throw new InvalidEncryptionSignatureException('Bad Signed JSON signature!');

        return $data;
    }

    /**
     * Base64 encoding that doesn't need to be urlencode()ed.
     * Exactly the same as base64_encode except it uses
     *   - instead of +
     *   _ instead of /
     *
     * @param string $input string
     * @return string base64Url encoded string
     */
    public static function base64UrlEncode($input)
    {
        $str = strtr(base64_encode($input), '+/', '-_');
        $str = str_replace('=', '', $str);
        return $str;
    }

    /**
     * Base64 encoding that doesn't need to be urlencode()ed.
     * Exactly the same as base64_encode except it uses
     *   - instead of +
     *   _ instead of /
     *   No padded =
     *
     * @param string $input base64UrlEncoded string
     * @return string
     */
    public static function base64UrlDecode($input)
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }
}