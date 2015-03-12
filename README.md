# php-dnssec-key2ds
A simple lightweight opensource function for calculating ds-rdata from dnskey-rdata according to RFC 5910: http://www.ietf.org/rfc/rfc5910.txt

It calculates the keytag with the digest type 1 (SHA1) and digest type 2 (SHA256) at the same time.

## Requirements

* php5
 
## Usage

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
	
*returns*

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
