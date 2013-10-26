<?php
require 'vendor/autoload.php';

define('ROOT', str_replace('\\', '/', dirname(__FILE__)) . '/');

use Werewolfsms as W;

$app = new Slim\Slim;

$app->get('/', function() {

    // $clockwork = new \Clockwork\Clockwork(W\System::getApiKey(ROOT . 'api.json'));

    try {
        $person = new W\Person(W\Person::VILLAGER);
    } catch (\Exception $e) {
        die($e->getMessage());
    }

    // var_dump($person);
    // $person->kill(W\Person::LYNCH);

    $person->sleep();


    echo "<pre>";
    var_dump($person);
    echo "</pre>";

    echo "<pre>";
    var_dump($person->isAlive());
    echo "</pre>";

    echo "<pre>";
    var_dump($person);
    echo "</pre>";

    $person->kill(W\Person::KILL_BY_LYNCH);

    echo "<pre>";
    var_dump($person->isAlive());
    echo "</pre>";

    echo $person->methodOfDeath();

    echo "<pre>";
    var_dump($person);
    echo "</pre>";

});

$app->run();
