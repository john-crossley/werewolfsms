<?php
require 'vendor/autoload.php';

define('ROOT', str_replace('\\', '/', dirname(__FILE__)) . '/');

use Werewolfsms as W;

$app = new \Slim\Slim;

$app->get('/', function() {

    // $clockwork = new \Clockwork\Clockwork(W\System::getApiKey(ROOT . 'api.json'));

    try {
        $person = new Werewolfsms\Person(W\Person::VILLAGER);
    } catch (\Exception $e) {
        die($e->getMessage());
    }

    var_dump($person);

});

$app->run();
