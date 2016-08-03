<?php
require(__DIR__ . '/vendor/autoload.php');

use \GuzzleHttp\Client;

$client = new Client();
$res = $client->request('GET', 'https://api.github.com/user', [
    'auth' => ['despected@gmail.com', 'Wareb0s5']
]);

var_dump($res);