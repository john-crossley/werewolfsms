<?php
require 'vendor/autoload.php';

define('ROOT', str_replace('\\', '/', dirname(__FILE__)) . '/');

use Werewolfsms as W;

$app = new Slim\Slim;

$app->get('/', function() {

     $clockwork = new \Clockwork\Clockwork(W\System::getApiKey(ROOT . 'api.json'), array('from' => 'WerewolfSMS'));

    try {

        $john = new W\Person(W\Person::VILLAGER, 'John Crossley', '07598935460', $clockwork);

        $nick = new W\Person(W\Person::VILLAGER, 'Nicholas Mitchell', '07765150512', $clockwork);

        // $person3 = new W\Person(W\Person::WEREWOLF, "Villager Three");

        // var_dump( $nick->askForVote( $john ) );


    } catch (\Exception $e) {
        die($e->getMessage());
    }

});

$app->get('/people', function() {
    $game = new W\GameController();
    $game->loadState($jsonObject);
    $players = $game->getCurrentPlayers();
    echo json_encode($players);
});

$app->get('/people/alive', function() {
    $game = new W\GameController();
    $game->loadState($jsonObject);
    $players = $game->getAlivePlayers();
    echo json_encode($players);
});

$app->run();
