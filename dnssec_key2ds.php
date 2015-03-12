<?php

/**
 * Calculate ds-rdata from dnskey-rdata
 * For additional information please refer to RFC 5910: http://www.ietf.org/rfc/rfc5910.txt
 * 
 * @param string owner, the coanonical name of the owner (e.g. example.com.)
 * @param int flags, the flags of the dnskey (only 256 or 257)
 * @param int protocol, the protocol of the dnskey (only 3)
 * @param int algoritm, the algorithm of the dnskey (only 3, 5, 6, 7, 8, 10, 12, 13 or 14)
 * @param string publickey, the full publickey base64 encoded (care, no spaces allowed)
 * 
 * @return array, on success
 *   Array (
 *     [owner] => $owner
 *     [keytag] => $keytag
 *     [algorithm] => $algorithm
 *     [digest] => Array (
 *       [] => Array (
 *         [type] => 1
 *         [hash] => $digest_sha1
 *       ),
 *       [] => Array (
 *         [type] => 2
 *         [hash] => $digest_sha256
 *       )
 *     )
 *   )
 * @return int < 0, on failure
 *   -1, unsupported owner
 *   -2, unsupported flags
 *   -3, unsupported protocol
 *   -4, unsupported algorithm
 *   -5, unsupported publickey
 */
function dnssec_key2ds($owner, $flags, $protocol, $algorithm, $publickey) {
	// define paramenter check variants
	$regex_owner = '/^[a-z0-9\-]+\.[a-z]+\.$/';
	$allowed_flags = array(256, 257);
	$allowed_protocol = array(3);
	$allowed_algorithm = array(3,5,6,7,8,10,12,13,14);
	$regex_publickey = '/^(?:[A-Za-z0-9+\/]{4})*(?:[A-Za-z0-9+\/]{2}==|[A-Za-z0-9+\/]{3}=|[A-Za-z0-9+\/]{4})$/';
	
	// do parameter checks and break if failed
	if(!preg_match($regex_owner, $owner)) return -1;
	if(!in_array($flags, $allowed_flags)) return -2;
	if(!in_array($protocol, $allowed_protocol)) return -3;
	if(!in_array($algorithm, $allowed_algorithm)) return -4;
	if(!preg_match($regex_publickey, $publickey)) return -5;
	
	// calculate hex of parameters
	$owner_hex = '';
	$parts = explode(".", substr($owner, 0, -1));
	foreach ($parts as $part) {
		$len = dechex(strlen($part));
		$owner_hex .= str_repeat('0', 2 - strlen($len)).$len;
		$part = str_split($part);
		for ($i = 0; $i < count($part); $i++) {
			$byte = strtoupper(dechex(ord($part[$i])));
			$byte = str_repeat('0', 2 - strlen($byte)).$byte;
			$owner_hex .= $byte;
		}
	}
	$owner_hex .= '00';
	$flags_hex = sprintf("%04d", dechex($flags));
	$protocol_hex = sprintf("%02d", dechex($protocol));
	$algorithm_hex = sprintf("%02d", dechex($algorithm));
	$publickey_hex = bin2hex(base64_decode($publickey));
	
	// calculate keytag using algorithm defined in rfc
	$string = hex2bin($flags_hex.$protocol_hex.$algorithm_hex.$publickey_hex);
	$sum = 0;
	for($i = 0; $i < strlen($string); $i++) {
		$b = ord($string[$i]);
		$sum += ($i & 1) ? $b : $b << 8;
	}
	$keytag = 0xffff & ($sum + ($sum >> 16));
	
	// calculate digest using rfc specified hashing algorithms
	$string = hex2bin($owner_hex.$flags_hex.$protocol_hex.$algorithm_hex.$publickey_hex);
	$digest_sha1 = strtoupper(sha1($string));
	$digest_sha256 = strtoupper(hash('sha256', $string));
	
	// return results and also copied parameters
	return array(
		//'debug' => array($owner_hex, $flags_hex, $protocol_hex, $algorithm_hex, $publickey_hex),
		'owner' => $owner,
		'keytag' => $keytag,
		'algorithm' => $algorithm,
		'digest' => array(
			array(
				'type' => 1,
				'hash' => $digest_sha1
			),
			array(
				'type' => 2,
				'hash' => $digest_sha256
			)
		)
	);
}

/**
 * hex2bin compatibility function (missing in PHP < 5.4)
 */
if (!function_exists('hex2bin')) {
    function hex2bin($str) {
        $sbin = "";
        $len = strlen($str);
        for ($i=0; $i<$len; $i+=2 ) {
            $sbin .= pack("H*", substr($str, $i, 2));
        }
        return $sbin;
    }
}
