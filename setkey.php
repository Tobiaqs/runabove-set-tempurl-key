<?php
/**
 * @author _Tobias
 * This script sets a temporary URL key for a RunAbove account.
 */

// == Configuration starts here. ==

$username = '<Your RunAbove username>';
$password = '<Your RunAbove password>';

$key = strtoupper(md5(uniqid()));

// Uncomment this to manually enter a key.
// $key = 'yourkey';

// You can find this in the RunAbove control panel (Expert Mode),
// under Access & Security -> API Access -> Object Store.
$storageEndpoint = 'https://storage.sbg-1.runabove.io/v1/AUTH_tenant';

// == Configuration ends here. ==

// Get tenant ID from $storageEndpoint.
$tenant = substr($storageEndpoint, strrpos($storageEndpoint, '_')+1);

$options = array(
	'http' => array(
		'header' => "Content-type: application/json\r\n",
		'method' => 'POST',
		'content' => json_encode(array(
			'auth' => array(
				'passwordCredentials' => array(
					'username' => $username,
					'password' => $password
				),
				'tenantId' => $tenant
			)
		)),
	),
);

$result = @file_get_contents('https://auth.runabove.io/v2.0/tokens', false, stream_context_create($options));
if($result === false)
	die("Obtaining access token failed.");

$result = json_decode($result, true);

$options = array(
	'http' => array(
		'header' => array(
			'X-Auth-Token: ' . $result['access']['token']['id'],
			'X-Account-Meta-Temp-URL-Key: ' . $key
			),
		'method' => 'POST'
	),
);

$result = @file_get_contents($storageEndpoint, false, stream_context_create($options));

if($result === false)
	die("Setting temporary URL key failed.");

echo "Your key has been set to:\n{$key}";