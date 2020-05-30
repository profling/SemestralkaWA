<?php

require_once 'vendor/autoload.php';
$fb = new Facebook\Facebook([
    'app_id' => '549183225958832', //TODO doplňte app_id a app_secret podle údajů z registrace na Facebooku
    'app_secret' => '5a85f50e7850166f70c1332b375e44f5',
    'default_graph_version' => 'v4.0',
]);
