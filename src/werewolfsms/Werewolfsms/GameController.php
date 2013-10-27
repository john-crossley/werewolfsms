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

    public function voteWolf($who, $victim)
    {
        $wnum = $who->getMobileNumber();
        if (array_key_exists($wnum, $this->wolfVotes))
            throw Exception("You are not a wolf")
        $this->wolfVotes[$who->getMobileNumber()] = $victim;
        $agree = true;
        foreach ($this->wolfVotes as $other)
        {
            if (is_null($other))
            {
                return;
            }
            if (!same_person($other, $victim))
            {
                agree = false;
            }
        }
        if (agree)
        {
            $this->wolfVotes = [];
            $this->victim = $victim;
            $this->enterPhase(self::DAY_DISCUSS);
        }
        else
        {
            foreach ($this->getLivingPeople as $person)
            {
                if ($person->role != Person::WEREWOLF)
                    continue;
                $person->askForKill(true);
            }
        }
    }

    public function voteLynch($who, $guilty)
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
        $dead = $yes > $no;
        for ($this->getLivingPeople() as $person)
        {
            $person->voteResult($this->accused, $dead, $this->votes);
        }
        $this->votes = [];
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

    public function argument($who)
    {
        if ($this->phase == self::DAY_ARG1 && same_person($who, $this->nominator))
        {
            $this->enterPhase(self::DAY_ARG2);
        }
        else if ($this->phase == self::DAY_ARG2 && same_person($who, $this->seconder))
        {
            $this->enterPhase(self::DAY_DEFEND);
        }
        else if ($this->phase == self::DAY_DEFEND && same_person($who, $this->seconder))
        {
            $this->enterPhase(self::DAY_VOTE);
        }
        else
        {
            throw new Exception("Whu?");
        }
    }

    public function enterPhase($newphase)
    {
        assert($newphase != $this->phase);
        switch ($newphase)
        {
        case self::NIGHT_WOLF:
            $this->wolfVotes = []
            foreach ($this->getLivingPeople() as $person)
            {
                if ($person->role != Person::WEREWOLF)
                    continue;
                $person->askForKill(false);
                $this->wolfVotes[$person->getMobileNumber()] = null;
            }
            break;

        case self::DAY_DISCUSS:
            if (is_null($this->victim))
            {
                foreach ($this->getLivingPeople() as $person)
                {
                    if ($person->consciousness() == Person::AWAKE)
                        continue;
                    $person->wake($this->victim);
                }
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
        // $this->phase = $this->$ar["phase"];
        $this->moninator = $this->toPerson($ar["nominator"]);
        $this->seconder = $this->toPerson($ar["seconder"]);
        $this->accused = $this->toPerson($ar["accused"]);
        $this->votes = $ar["votes"];
        $this->wolfVotes = [];
        foreach ($ar["wolfVotes"] as $wnum => $victim)
        {
            $this->wolfVotes[$wnum] = $this->toPerson($victim);
        }
    }

    public function toJSON()
    {
        $wolfVotes = [];
        foreach ($this->wolfVotes as $wnum, $victim)
        {
            $wolfVotes[$wnum] = $victim->getMobileNumber();
        }
        $ar = array(
            "phase" => $this->phase,
            "nominator" => $this->fromPerson($this->nominator),
            "seconder" => $this->fromPerson($this->seconder),
            "accused" => $this->fromPerson($this->accused),
            "votes" => $this->votes,
            "wolfVotes" => $wolfVotes
        );
        return json_encode($ar);
    }

    public function StartGame()
    {
        $this->phase = "WOLF_NIGHT";
    }
}
