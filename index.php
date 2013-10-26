<?php
require 'vendor/autoload.php';

define('ROOT', str_replace('\\', '/', dirname(__FILE__)) . '/');

use Werewolfsms as W;

$app = new Slim\Slim;

$app->get('/register', function() use ($app) {
    return $app->render('index.php', array(
        'title' => 'This is the title'
    ));
});

$app->post('/register', function() use ($app) {
    var_dump($_POST);
});

$app->get('/', function() {

    // Clockwork sms object
    $clockwork = new \Clockwork\Clockwork(
        W\System::getApiKey(ROOT . 'api.json'),
        array('from' => 'WEREWOLFSMS')
    );

    try {

        // Create a new instance of a person
        $person = new W\Person($clockwork);
        $person->setName("John Crossley");
        $person->setMobileNumber('07598935460');



        var_dump($person->kill(W\Person::KILL_BY_LYNCH));


    } catch (\Exception $e) {
        die($e->getMessage());
    }

});

//$app->get('admin/', function() {
//    $app->render('index.php', array('title' => 'Nicks Page'));
//});

$app->run();
