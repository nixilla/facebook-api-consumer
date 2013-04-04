<?php

// make sure that you run `composer install` before executing this script
// run it with php ./example.php

require_once './vendor/autoload.php';

$key = 'YOUR_FACEBOOK_APP_ID';
$secret = 'YOUR_FACEBOOK_APP_SECRET';

$client = new Buzz\Browser(new Buzz\Client\Curl());
$consumer = new Facebook\Consumer($client, $key, $secret);

// get it form here: https://developers.facebook.com/tools/explorer
$access_token = 'YOUR_ACCESS_TOKEN';

$consumer->setAccessToken($access_token);

try {
    $user = $consumer->getUser();
    printf('Found user %s', $user);
    printf("\n%s\n\n",'===================================');
    print_r($user->toArray());
}
catch (Exception $e)
{
    printf('%s: %s', get_class($e), $e->getMessage());
    echo "\n";
}
