<?php

namespace Werewolfsms;

static function same_person($a, $b)
{
    return $a->uniqueID() == $b->uniqueID();
}

class GameController {
    public function Vote($who, $guilty)
    {
	$this->votes[$who] = $guilty
    }
    # Return true if nomination was accepted
    public function Nominate($who, $accused)
    {
	if (is_null($this->accused))
	{
	  return false;
	}
	$this->accused = accused;
	$this->enterPhase($this->DAY_NOMINATED);
	return true;
    }
    # Return true if seconding was accepted
    public function Second($who, $accused)

    /*
    enum game_phases {
      NIGHT_WOLF
      DAY_DISCUSS
      DAY_NOMINATED
      DAY_ARG1
      DAY_ARG2
      DAY_DEFEND
      DAY_VOTE
    }
    Sleep
    Wake
    AskForSeconder(Person)
    AskForVote(Person)
    Kill
    *Nominated
    *Argue
     */

    protected livePeople()
    {
	$alivePeople = [];
	foreach ($this->people as $person)
	{
	    if ($person.isAlive())
	    {
		$alivePeople[] = $person;
	    }
	}
	return $alivePeople;
    }

    public function enterPhase($newphase)
    {
        assert($newphase != $this->phase);
        switch ($newphase)
        {
        case "NIGHT_WOLF":
            foreach ($this->livePeople() as $person)
            {
                $person->sleep();
            }
            break;

        case "DAY_DISCUSS":
            foreach ($this->livePeople() as $person)
            {
		if ($person->consciousness() == Person::AWAKE)
		    continue;
                $person->wake($this->killed);
            }
            break;

        case "DAY_NOMINATED":
            foreach ($this->livePeople() as $person)
            {
                if (same_person($person, $this->accused)
                    || same_person($person, $this->nominator))
                {
                    $person->askForSeconder($this->accused);
                }
            }
            break;

        case "DAY_ARG1":
            $this->nominator->argue(Person::NOMINATE);
            break;

        case "DAY_ARG2":
            $this->nominator->argue(Person::SECOND);
            break;

        case "DAY_DEFEND":
            $this->nominator->argue(Person::DEFEND);
            break;

	case "DAY_VOTE":
            foreach ($this->livePeople() as $person)
            {
		$person->vote($this->accused);
            }
	    break;

        default:
            abort();
        }
    }
    public function Serialise()
    {
	maybe_person(nominater);
	maybe_person(seconder);
	maybe_person(accused);
	game_phase(phase);
    }

    public function StartGame()
    {
        $this->phase = "WOLF_NIGHT";
    }
}
