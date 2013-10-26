<?php
require 'vendor/autoload.php';

define('ROOT', str_replace('\\', '/', dirname(__FILE__)) . '/');

use Werewolfsms as W;

$app = new Slim\Slim;

$app->get('/join-game', function() use ($app) {
    return $app->render('register', array(
        'title' => 'Join Game '
    ));
});

$app->post('/join-game', function() use ($app) {

    var_dump($_POST);

});

$app->get('/', function() use ($app) {

    return $app->render('index.php',array(
        'title'=>'Were Wolve SMS'
    ));

});

$app->get('/godmode',function(){
    
    return $app->render('godmode.php'{
        'title'=>'God Mode'
    });

});


$app->get('/', function() {

     // $clockwork = new \Clockwork\Clockwork(W\System::getApiKey(ROOT . 'api.json'), array('from' => 'WerewolfSMS'));

    try {

        // $john = new W\Person(W\Person::VILLAGER, 'John Crossley', '07598935460', $clockwork);

        // $nick = new W\Person(W\Person::VILLAGER, 'Nicholas Mitchell', '07765150512', $clockwork);

        // $person3 = new W\Person(W\Person::WEREWOLF, "Villager Three");

        // var_dump( $nick->askForVote( $john ) );

        // $john->wake($john);

        $john->kill(W\Person::KILL_BY_WEREWOLF);

//        echo "<pre>";
//        var_dump( serialize($john) );
//        echo "</pre>";


    } catch (\Exception $e) {
        die($e->getMessage());
    }

});

//$app->get('admin/', function() {
//    $app->render('index.php', array('title' => 'Nicks Page'));
//});

$app->run();
