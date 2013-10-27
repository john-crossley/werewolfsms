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
        $john = new W\Person($clockwork, $gameState);
        $john->setName("John Crossley");
        $john->setMobileNumber('07598935460');

        $nick = new W\Person($clockwork, new $gameState);
        $nick->setName("Nicholas Mitchell");
        $nick->setMobileNumber('07765150512');

        // This means nick was killed
        $john->voteResult($nick, false, array(
            '07598935460' => false,
            '07765150512' => true
        ));


    } catch (\Exception $e) {
        die($e->getMessage());
    }

});


$app->run();
