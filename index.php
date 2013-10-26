<?php
require 'vendor/autoload.php';

define('ROOT', str_replace('\\', '/', dirname(__FILE__)) . '/');

use Werewolfsms as W;

$app = new Slim\Slim;

$clockwork = new \Clockwork\Clockwork(
    W\System::getApiKey(ROOT . 'api.json'),
    array('from' => 'WEREWOLFSMS')
);

$app->get('/register', function() use ($app) {
    return $app->render('index.php', array(
        'title' => 'This is the title'
    ));
});

$app->post('/register', function() use ($app) {
    var_dump($_POST);
});

$app->get('/', function() use ($clockwork) {

    try {

        // Create a new instance of a person
        $john = new W\Person($clockwork);
        $john->setName("John Crossley");
        $john->setMobileNumber('07598935460');

        // var_dump($person->kill(W\Person::KILL_BY_LYNCH));

        echo "<pre>";
        print_r($john);
        echo "</pre>";

        $john->setConsciousness(W\Person::ASLEEP);
        $j = $john->toJSON();


        echo "<pre>";
        print_r($j);
        echo "</pre>";

        echo "<pre>";
        print_r($john->fromJSON($j));
        echo "</pre>";



    } catch (\Exception $e) {
        die($e->getMessage());
    }

});

//$app->get('admin/', function() {
//    $app->render('index.php', array('title' => 'Nicks Page'));
//});

$app->run();
