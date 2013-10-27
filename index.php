<?php
require 'vendor/autoload.php';

define('ROOT', str_replace('\\', '/', dirname(__FILE__)) . '/');

use Werewolfsms as W;

$app = new Slim\Slim;

$clockwork = new \Clockwork\Clockwork(
    W\System::getApiKey(ROOT . 'config.json'),
    array('from' => '447860033041')
);

$app->container->singleton('storage', function () use ($clockwork) {
    return new W\GameStorage($clockwork);
});

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



$app->get('/', function() use ($app,$clockwork) {

    $game = $app->storage->getGame();

    try {

        $nick = new W\Person($clockwork, $game);
        $nick->setMobileNumber('07765150512');
        $nick->setName("Nicholas Mitchell");

        $john = new W\Person($clockwork, $game);
        $john->setMobileNumber('07598935460');
        $john->setName("John Crossley");

        // This means nick was killed
//        $john->voteResult($nick, true, array(
//            '07598935460' => true,
//            // '07765150512' => true,
//           '00000000000' => false,
//           '07777777777' => true
//        ));


    } catch (\Exception $e) {
        die($e->getMessage());
    }

});

$app->get('/people', function() use ($app) {;
    foreach ($app->storage->getAllPeople() as $person) {
        $jsonPeople[$person->getMobileNumber()] = json_decode($person->toJSON());
    }
    echo json_encode($jsonPeople);
});

$app->get('/people/alive', function() use ($app) {
    $game = $app->storage->getGame();
    $players = $game->getLivingPeople();
    echo json_encode($players);
});

$app->get('/cron', function () use ($app)  {
    $game = $app->storage->getGame();
    $game->tick();
});

$app->post('/sms', function() use ($app)  {
    $handler = new W\SMSReceipt();
    $game = $app->storage->getGame();
    $handler->processSMS($app, $game);

});

$app->run();
$app->storage->saveEverything();

