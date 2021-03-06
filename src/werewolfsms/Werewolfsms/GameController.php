<?php

namespace Werewolfsms;

class GameController
{
    const PRE_GAME = "PRE_GAME";
    const NIGHT_WOLF = "NIGHT_WOLF";
    const DAY_DISCUSS = "DAY_DISCUSS";
    const DAY_NOMINATED = "DAY_NOMINATED";
    const DAY_VOTE = "DAY_VOTE";
    const END_GAME = "END_GAME";

    private $storage  = null;
    private $victim = null;
    private $people = [];
    private $phase = self::PRE_GAME;
    private $nominator = null;
    private $seconder = null;
    private $accused = null;
    private $votes = [];
    private $wolfVotes = [];
    private $smsObject = null;
    private $endGame = null;

    public function __construct($storage, $smsObject)
    {
        $this->storage = $storage;
        $this->smsObject = $smsObject;
    }

    private function resetVotes()
    {
        $this->votes = [];
        foreach ($this->getLivingPeople() as $person)
        {
            $this->votes[$person->getMobileNumber()] = null;
        }
    }

    public function voteWolf($who, $victim)
    {
        $wnum = $who->getMobileNumber();
        if (!array_key_exists($wnum, $this->wolfVotes))
            throw new \Exception("You are not a wolf");
        $this->wolfVotes[$who->getMobileNumber()] = $victim;
        $agree = true;
        foreach ($this->wolfVotes as $other)
        {
            if (is_null($other))
            {
                return;
            }
            if (!$other->isMe($victim))
            {
                $agree = false;
            }
        }
        if ($agree)
        {
            $this->wolfVotes = [];
            $this->victim = $victim;
            $this->enterPhase(self::DAY_DISCUSS);
        }
        else
        {
            foreach ($this->getLivingWolves() as $person)
            {
                $person->askForKill(true);
            }
        }
    }

    public function voteLynch($who, $guilty)
    {
        $yes = 0;
        $no = 0;
        $this->votes[$who->getMobileNumber()] = $guilty;
        foreach ($this->votes as $vote)
        {
            if (is_null($vote))
                return;
            if ($vote)
            {
                $yes += 1;
            }
            else
            {
                $no += 1;
            }
        }
        if ($yes == $no && mt_rand(0, 1) == 0)
        {
            $yes += 1;
        }
        $dead = $yes > $no;
        foreach ($this->getLivingPeople() as $person)
        {
            $person->voteResult($this->accused, $dead, $this->votes);
        }
        $this->votes = [];
        if ($this->checkEndGame()) {
            $this->enterPhase(self::END_GAME);
            return;
        }
        if ($dead) {
            $this->enterPhase(self::NIGHT_WOLF);
        }
        else
        {
            $this->enterPhase(self::DAY_DISCUSS);
        }
    }

    public function nominate($who, $accused)
    {
        if (!is_null($this->accused))
        {
            throw new \Exception($this->accused->getName() . " has already been nominateed");
        }
        $this->accused = $accused;
        $this->nominator = $who;
        $this->enterPhase(self::DAY_NOMINATED);
    }

    public function second($who)
    {
        if (is_null($this->accused))
        {
            throw new \Exception("Nobody has been nominated");
        }
        if (!is_null($this->seconder))
        {
            throw new \Exception("We already have a second");
        }
        $this->seconder = $who;
        $this->enterPhase(self::DAY_VOTE);
    }

    /* Person API???
    Sleep
    Wake
    AskForSeconder(Person)
    AskForVote(Person)
    Kill
    *Nominated
    *Argue
     */

    public function getAllPeople()
    {
        return $this->people;
    }

    public function getLivingPeople()
    {
        $alivePeople = [];
        foreach ($this->people as $person)
        {
            if ($person->isAlive())
            {
                $alivePeople[] = $person;
            }
        }
        return $alivePeople;
    }

    public function getLivingWolves()
    {
        $wolves = [];
        foreach ($this->people as $person)
        {
            if ($person->isAlive() && $person->getRole() == Person::WEREWOLF)
            {
                $wolves[] = $person;
            }
        }
        return $wolves;
    }

    private function checkEndGame()
    {
        $wolfCount = 0;
        $villagerCount = 0;
        foreach ($this->getLivingPeople() as $person) {
            if ($person->getRole() == Person::WEREWOLF) {
                $wolfCount++;
            } else {
                $villagerCount++;
            }
        }
        if ($wolfCount != 0 && $villagerCount != 0 ) {
            return false;
        }
        if ($wolfCount == 0) {
            $this->endGame = Person::VILLAGER;
        } else {
            $this->endGame = Person::WEREWOLF;
        }
        return true;
    }

    public function enterPhase($newPhase)
    {
        if ($newPhase == $this->phase)
        {
            throw new \Exception("State machine borked");
        }
        switch ($newPhase)
        {
        case self::NIGHT_WOLF:
            $this->wolfVotes = [];
            foreach ($this->getLivingWolves() as $person)
            {
                $person->askForKill(false);
                $this->wolfVotes[$person->getMobileNumber()] = null;
            }
            break;

        case self::DAY_DISCUSS:
            if (!is_null($this->victim))
            {
                foreach ($this->getLivingPeople() as $person)
                {
                    $person->wake($this->victim);
                }
                $this->victim = null;
                if ($this->checkEndGame()) {
                    $this->enterPhase(self::END_GAME);
                    return;
                }
            }
            break;

        case self::DAY_NOMINATED:
            $this->resetVotes();
            foreach ($this->getLivingPeople() as $person)
            {
                if (!($person->isMe($this->accused)
                    || $person->isMe($this->nominator)))
                {
                    $person->askForSeconder($this->accused);
                }
            }
            break;

        case self::DAY_VOTE:
            foreach ($this->getLivingPeople() as $person)
            {
                $person->askForVote($this->accused);
            }
            break;

        case self::END_GAME:
            foreach ($this->getAllPeople() as $person)
            {
                $person->gameEnded($this->endGame);
            }
            break;

        default:
            throw new \Exception("State machine borked");
        }
        $this->phase = $newPhase;
    }

    public function registerPerson($num, $name)
    {
        $person = $this->getPersonByName($name);
        if (!is_null($person))
            return;
        $person = $this->toPerson($num);
        if (!is_null($person))
        {
            $person->setName($name);
            return;
        }
        $person = new Person($this->smsObject, $this);
        $person->setMobileNumber($num);
        $person->setName($name);
        $this->insertPerson($person);
    }

    public function getPersonByName($name)
    {
        foreach ($this->getLivingPeople() as $person)
        {
            if ($person->getName() == $name)
            {
                return $person;
            }
        }
        return null;
    }

    public function toPerson($val)
    {
        if (is_null($val))
        {
            return null;
        }
        if (!array_key_exists($val, $this->people))
        {
            return null;
        }
        return $this->people[$val];
    }

    public function fromPerson($person)
    {
        if (is_null($person))
        {
            return null;
        }
        return $person->getMobileNumber();
    }

    public function tick()
    {
        /* tock */
    }

    public function insertPerson(Person $person)
    {
        $this->people[$person->getMobileNumber()] = $person;
    }

    public function fromJSON($json)
    {
        $ar = json_decode($json, true);
        $this->people = array();
        $this->storage->readPeopleDb();
        $this->phase = System::withDefault($ar, "phase", self::PRE_GAME);
        $this->moninator = $this->toPerson(System::withDefault($ar, "nominator", null));
        $this->seconder = $this->toPerson(System::withDefault($ar, "seconder", null));
        $this->accused = $this->toPerson(System::withDefault($ar, "accused", null));
        $this->victim = $this->toPerson(System::withDefault($ar, "victim", null));
        $this->votes = System::withDefault($ar, "votes", array());
        $this->wolfVotes = [];
        $wolfVotes = System::withDefault($ar, "wolfVotes", []);
        foreach ($wolfVotes as $wnum => $victim)
        {
            $this->wolfVotes[$wnum] = $this->toPerson($victim);
        }
    }

    public function toJSON()
    {
        $wolfVotes = [];
        foreach ($this->wolfVotes as $wnum => $victim)
        {
            $wolfVotes[$wnum] = $this->fromPerson($victim);
        }
        $ar = array(
            "phase" => $this->phase,
            "nominator" => $this->fromPerson($this->nominator),
            "seconder" => $this->fromPerson($this->seconder),
            "accused" => $this->fromPerson($this->accused),
            "victim" => $this->fromPerson($this->victim),
            "votes" => $this->votes,
            "wolfVotes" => $wolfVotes
        );
        return json_encode($ar);
    }

    private function numberOfWolves()
    {
        $villagers = count($this->people);
        if ($villagers < 3) {
            return 1;
        }
        if ($villagers < 12) {
            return 2;
        }
        if ($villagers < 18) {
            return 3;
        }
        return 4;
    }

    public function startGame()
    {
        $wolves = array_rand($this->people, $this->numberOfWolves());
        if (!is_array($wolves)) {
            $wolves = array($wolves);
        }

        foreach ($wolves as $wnum)
        {
            $this->people[$wnum]->setRole(Person::WEREWOLF);
        }
        foreach ($this->getLivingPeople() as $person)
        {
            if (is_null($person->getRole()))
            {
                $person->setRole(Person::VILLAGER);
            }
        }
        $this->enterPhase(self::NIGHT_WOLF);
    }

    public function resetGame()
    {
        $this->fromJSON(json_encode(array()));
        foreach ($this->people as $person) {
            $person->fromJSON(json_encode(array(
                    'name' => $person->getName(),
                    'mobileNumber' => $person->getMobileNumber(),
                    'alive' => true,
                    'role' => null
                )
            ,true));
        }
    }
}
