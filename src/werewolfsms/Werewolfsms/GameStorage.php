<?php
/**
 * Created by JetBrains PhpStorm.
 * User: martyn
 * Date: 26/10/13
 * Time: 21:44
 * To change this template use File | Settings | File Templates.
 */

namespace Werewolfsms;

class GameStorage {

    private $mongoDbConnection = null;
    private $mongoDatabase = null;
    private $peopleCollection = null;
    private $gameCollection = null;
    private $allPeopleArray = null;
    private $clockworkObject = null;
    private $currentGame = null;


    public function __construct(\Clockwork\Clockwork $clockworkObject)
    {
        $this->mongoDbConnection = new \MongoClient;
        $this->mongoDatabase = $this->mongoDbConnection->compaxion;
        $this->gameCollection = $this->mongoDatabase->game;
        $this->peopleCollection = $this->mongoDatabase->people;
        $this->clockworkObject = $clockworkObject;
    }

    public function getAllPeople()
    {
        /* This call (getGame()) will call this function (getAllPeople), but once in this function
          will use the cached version it creates and will therefore not be infinite. */
        $currentGame = $this->getGame();
        if (is_null($this->allPeopleArray)) {
            $allPlayers = $this->playerCollection->find();
            foreach ($allPlayers as $aPerson) {
                $aPersonObject = new Person($this->clockworkObject,$currentGame);
                $aPersonObject->fromJSON($aPerson);
                $this->allPeopleArray[$aPerson->getMobileNumber()] = $aPerson;
            }
            return $this->allPeopleArray;
        }
    }

    public function getGame()
    {
        if (is_null($this->currentGame)) {
            $currentGame = new GameController($this);
            $currentGameJson = $this->gameCollection->findOne();
            $this->currentGame = $currentGame;
            $currentGame->fromJSON($currentGameJson);
        }
        return $this->currentGame;
    }

    public function saveGame() {
        $currentGame = $this->getGame();
        $gameAsJSON = $currentGame->toJason();
        $this->gameCollection->findAndModify(array(),array('$set'=>$gameAsJSON),null,array('update'=>true,'new'=>false));
    }

    public function savePeople() {
        foreach ($this->allPeopleArray as $person) {
            $this->peopleCollection->findAndModify(
                array('mobileNumber'=>$person->getMobileNumber()),
                array('$set'=>$person->toJSON()),
                null,
                array('update'=>true,'upsert'=>true,'new'=>false)
            );
        }
    }

    public function saveEverything()
    {
        $this->saveGame();
        $this->savePeople();
    }

}