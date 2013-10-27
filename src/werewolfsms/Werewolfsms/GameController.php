<?php

namespace Werewolfsms;

function same_person($a, $b)
{
    return $a->getMobileNumber() == $b->getMobileNumber();
}

class GameController
{
    const NIGHT_WOLF = "NIGHT_WOLF";
    const DAY_DISCUSS = "DAY_DISCUSS";
    const DAY_NOMINATED = "DAY_NOMINATED";
    const DAY_ARG1 = "DAY_ARG1";
    const DAY_ARG2 = "DAY_ARG2";
    const DAY_DEFEND = "DAY_DEFEND";
    const DAY_VOTE = "DAY_VOTE";

    public function __construct($storage)
    {
        $this->storage = $storage;
    }

    private function resetVotes()
    {
        $this->votes = [];
        foreach ($this->getLivingPeople() as $person)
        {
            $this->votes[$person->getMobileNumber()] = null;
        }
    }

    public function Vote($who, $guilty)
    {
        $yes = 0;
        $no = 0;
        $this->votes[$who] = $guilty;
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
        $this->voteResult($this->accused, $yes > $no, $this->votes);
        $this->enterPhase(self::NIGHT_WOLF);
    }

    public function nominate($who, $accused)
    {
        if (!is_null($this->$accused))
        {
            throw new Exception($this->accused->friendlyName() . " has already been nominateed");
        }
        $this->accused = $accused;
        $this->nominator = $who;
        $this->enterPhase(self::DAY_NOMINATED);
    }

    public function second($who, $accused)
    {
        if (is_null($this->$accused))
        {
            throw new \Exception($who->friendlyName() . " has not been nominated");
        }
        if (!is_null($this->seconder))
        {
            throw new \Exception($this->accused->friendlyName() . " has already been seconded");
        }
        $this->seconder = $who;
        $this->enterPhase(self::DAY_ARG1);
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

    protected function getLivingPeople()
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

    public function argument($who)
    {
        if ($this->phase == self::DAY_ARG1 && same_person($who, $this->nominator))
        {
            $this->enterPhase;
        }
        throw \Exception("Whu?");
    }

    public function enterPhase($newphase)
    {
        assert($newphase != $this->phase);
        switch ($newphase)
        {
        case self::NIGHT_WOLF:
            foreach ($this->getLivingPeople() as $person)
            {
                $person->sleep();
            }
            break;

        case self::DAY_DISCUSS:
            foreach ($this->getLivingPeople() as $person)
            {
                if ($person->consciousness() == Person::AWAKE)
                    continue;
                $person->wake($this->killed);
            }
            break;

        case self::DAY_NOMINATED:
            $this->resetVotes();
            foreach ($this->getLivingPeople() as $person)
            {
                if (same_person($person, $this->accused)
                    || same_person($person, $this->nominator))
                {
                    $person->askForSeconder($this->accused);
                }
            }
            break;

        case self::DAY_ARG1:
            $this->nominator->askForArgument(Person::NOMINATE);
            break;

        case self::DAY_ARG2:
            $this->nominator->askForArgument(Person::SECOND);
            break;

        case self::DAY_DEFEND:
            $this->nominator->askForArgument(Person::DEFEND);
            break;

        case self::DAY_VOTE:
            foreach ($this->getLivingPeople() as $person)
            {
                $person->askForVote($this->accused);
            }
            break;

        default:
            abort();
        }
    }

    public function toPerson($val)
    {
        if (is_null($val))
        {
            return null;
        }
        return $this->people[$val];
    }

    public function fromJSON($json)
    {
        $ar = json_decode($json, true);
        $this->people = $this->storage->getAllPeople();
        $this->phase = $this->$ar["phase"];
        $this->moninator = $this->toPerson($ar["nominator"]);
        $this->seconder = $this->toPerson($ar["seconder"]);
        $this->accused = $this->toPerson($ar["accused"]);
        $this->votes = $ar["votes"];
    }

    public function toJSON()
    {
        $ar = array(
            "phase" => $this->phase,
            "nominator" => $this->fromPerson($this->nominator),
            "seconder" => $this->fromPerson($this->seconder),
            "accused" => $this->fromPerson($this->accused),
            "votes" => $this->votes
        );
        return json_encode($ar);
    }

    public function StartGame()
    {
        $this->phase = "WOLF_NIGHT";
    }
}
