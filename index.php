<?php
require 'vendor/autoload.php';

define('ROOT', str_replace('\\', '/', dirname(__FILE__)) . '/');

use Werewolfsms as W;

$app = new Slim\Slim;

$clockwork = new \Clockwork\Clockwork(
    W\System::getApiKey(ROOT . 'api.json'),
    array('from' => 'WEREWOLFSMS')
);

$app->get('/join-game', function() use ($app) {
    return $app->render('joinnow.php', array(
        'title' => 'Join Game '
    ));
});

$app->get('/learn-more',function()  use ($app){

    return $app->render('learnmore.php',array(
        'title'=>'Learn More!'
    ));

});



$app->get('/', function() use ($clockwork) {

    $storage = new W\GameStorage($clockwork);
    $game = $storage->getGame();

    try {

        $nick = new W\Person($clockwork, $game);
        $nick->setName("Nicholas Mitchell");
        $nick->setMobileNumber('07765150512');

        $john = new W\Person($clockwork, $game);
        $john->setName("John Crossley");
        $john->setMobileNumber('07598935460');


        // This means nick was killed
        $john->voteResult($nick, true, array(
            '07598935460' => true,
            // '07765150512' => true,
           '00000000000' => false,
           '07777777777' => true
        ));


    } catch (\Exception $e) {
        die($e->getMessage());
    }

});

$app->get('/people', function() use ($clockwork) {
    $storage = new W\GameStorage($clockwork);
    foreach ($storage->getAllPeople() as $person) {
        $jsonPeople[$person->getMobileNumber()] = json_decode($person->toJSON());
    }
    echo json_encode($jsonPeople);
});

$app->get('/people/alive', function() use ($clockwork) {
    $storage = new W\GameStorage($clockwork);
    $game = $storage->getGame();
    $players = $game->getLivingPeople();
    echo json_encode($players);
});

$app->get('/cron', function () {
    /*$storage = new W\GameStorage($clockwork);
    $game = $storage->getGame();
    $game->tick();*/
    $mongoDbConnection = new \MongoClient;
    $mongoDatabase = $mongoDbConnection->werewolfsms;
    $aCollection = $mongoDatabase->cron;
    $doc = $aCollection->findOne();
    if ($doc == null) {
        $doc['last_run'] = date('c');
        $aCollection->insert($doc);
    }
    $doc = $aCollection->findOne();
    $doc['last_run'] = date('c');
    unset($doc['_id']);
    $aCollection->findAndModify(null,$doc);
    var_dump($doc);
});

$app->get('/lastcron', function() {
    $mongoDbConnection = new \MongoClient;
    $mongoDatabase = $mongoDbConnection->werewolfsms;
    $aCollection = $mongoDatabase->cron;
    var_dump($aCollection->findOne());
});

$app->run();
