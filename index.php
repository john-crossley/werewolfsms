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

     // $clockwork = new \Clockwork\Clockwork(W\System::getApiKey(ROOT . 'api.json'), array('from' => 'WerewolfSMS'));

    try {

        // $john = new W\Person(W\Person::VILLAGER, 'John Crossley', '07598935460', $clockwork);

        // $nick = new W\Person(W\Person::VILLAGER, 'Nicholas Mitchell', '07765150512', $clockwork);

        // $person3 = new W\Person(W\Person::WEREWOLF, "Villager Three");

        // var_dump( $nick->askForVote( $john ) );

        $nick = new W\Person($clockwork, new $game);
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

$app->get('/people', function() use ($clockwork) {
    $storage = new W\GameStorage($clockwork);
    echo json_encode($storage->getAllPeople());
});

$app->get('/people/alive', function() use ($clockwork) {
    $storage = new W\GameStorage($clockwork);
    $game = $storage->getGame();
    $players = $game->getLivingPeople();
    echo json_encode($players);
});

$app->run();
