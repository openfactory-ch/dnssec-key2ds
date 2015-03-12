# php-dnssec-key2ds
A simple lightweight opensource function for calculating ds-rdata from dnskey-rdata according to RFC 5910: http://www.ietf.org/rfc/rfc5910.txt

It calculates the keytag with the digest type 1 (SHA1) and digest type 2 (SHA256) at the same time.

## Requirements

* php5
 
## Usage

*Documentation"

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

	}

*Example*

	<?php

	require('dnssec_key2ds.php');

	$result = dnssec_key2ds(
		'example.com.',
		257, 
		3, 
		5, 
		'AwEAAd8j5QLFybVOOacGMph0nBgRjk1mRBjWUtORLaT29ix2cWbfviVsMT+ywC3L1wu21mzfjai9c3h7Fwu7nNDQqGd//6u7r3K0qIllSiOO2N6NXfc1cyuwJD72zVCWxHxigZnzZOEA2ad2JJmCL4+bCh5qfovv6i1fJKECIZJZ9UfgOltJhjwmrjzakIPZR81V7XX90BuaymCrN28nNwPJM40='
	);

	print_r($result);
	
*Returns*

	Array
	(
		[owner] => example.com.
		[keytag] => 56206
		[algorithm] => 5
		[digest] => Array
			(
				[0] => Array
					(
						[type] => 1
						[hash] => E19153792416F34E1591B59E9909EF70F07B8F4D
					)

				[1] => Array
					(
						[type] => 2
						[hash] => BF3876D00BB9C99D719A7697CB7ACBFE19E8CE1A8720DEF38EEC7BD55D5B5E41
					)

			)

	)

## Contribute

Feel free to report bugs and features in the issues section or send me pull requests directly.
