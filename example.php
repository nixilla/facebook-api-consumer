<?php

// make sure that you run `composer install` before executing this script
// run it with php ./example.php

require_once './vendor/autoload.php';

$client = new Buzz\Browser(new Buzz\Client\Curl());
$consumer = new Facebook\Consumer($client);

// get it form here: https://developers.facebook.com/tools/explorer
$access_token = 'YOUR_ACCESS_TOKEN';

$consumer->setAccessToken($access_token);

try {
    $user = $consumer->call('/me/feed');
    print_r($user);
}
catch (Exception $e)
{
    printf('%s: %s', get_class($e), $e->getMessage());
    echo "\n";
}
