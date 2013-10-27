<?php
/**
 * Created by JetBrains PhpStorm.
 * User: martyn
 * Date: 27/10/13
 * Time: 06:07
 * To change this template use File | Settings | File Templates.
 */

namespace Werewolfsms;


class SMSReceipt {

    function processSMS($app, GameController $game) {

        $to = $app->request->post('to');
        $from = $app->request->post('from');
        $fromPerson = $game->toPerson($from);
        $content = $app->request->post('content');

        list($keyword,$restOfContent) = explode(' ',$content,2);

        switch (strtolower($keyword)) {
            case 'vote' :
                list($intent, $spuriouscontent) = explode(' ',$restOfContent,2);
                $game->voteLynch($fromPerson,(strtolower($intent) != "save"));
                break;
            case 'kill' :
                $nominee = $game->getPersonByName($restOfContent);
                $game->voteWolf($fromPerson, $nominee);
                break;
            case 'nominate' :
                $nominee = $game->getPersonByName($restOfContent);
                $game->nominate($fromPerson, $nominee);
            case 'second' :
                $game->second($fromPerson);
                break;
            case 'register' :
                list($firstName, $spuriouscontent) = explode(' ',$restOfContent,2);
                $game->registerPerson($from,$firstName);
                break;
            case 'start' :
                $game->startGame();
                break;
        }
    }

}