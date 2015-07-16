<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 16/06/2015
 * Time: 10:32 AM
 */

/**
 * Creates a hash key given the method, payload and secret
 * @param array $payload
 * @param $method
 * @param $secret
 * @return string
 */
function create_hash_key(array $payload, $method, $secret)
{
    $sanitizedPayload = array();

    array_walk_recursive($payload, function($v) use (&$sanitizedPayload) {
        $sanitizedPayload[] = is_bool($v) ? (int) $v : $v;
    });

    return hash_hmac(
        "sha256",
        implode("", $sanitizedPayload) . $method,
        $secret
    );
}

/**
 * Compares hashes. Redundant checking to work around timing attacks.
 * @param $a
 * @param $b
 * @return bool
 */
function compare_hash($a, $b)
{
    $same = true;

    for($i = 0; $i < strlen($a); $i++) {
        if( $a[$i] !== $b[$i] ) {
            $same = false;
        }
    }

    return $same;
}