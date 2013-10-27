<?php

namespace Werewolfsms;

use Clockwork\ClockworkException;
use Symfony\Component\Config\Definition\Exception\Exception;

class Person
{

    // Deaths
    const KILL_BY_LYNCH = 'kill_by_lynch';
    const KILL_BY_WEREWOLF = 'kill_by_werewolf';

    // Role
    const VILLAGER = 'villager';
    const WEREWOLF = 'werewolf';

    // Consciousness
    const AWAKE = 'awake';
    const ASLEEP = 'asleep';

    // Reasonings
    const NOMINATE = 'nominate';
    const SECOND = 'second';
    const DEFEND = 'defend';

    // Nominate, second or defend

    // Stores the actual state
    // of the person object.
    public $personState,
        $gameState;

    /**
     * The Person constructor - Is called when its the start of a new game.
     *
     * @param \Clockwork\Clockwork $smsObject - Requires the SMS object.
     */
    public function __construct(\Clockwork\Clockwork $smsObject, $gameState)
    {
        $this->personState = array();

        // Set some state values
        $this->personState['alive'] = true;
        $this->personState['role'] = null;
        $this->personState['name'] = null;
        $this->personState['mobileNumber'] = null;

        // Set the sms object
        $this->smsObject = $smsObject;

        // Game state
        $this->gameState = $gameState;
    }

    /**
     * Pass in a person state as a JSON object.
     *
     * @param $json The person state
     */
    public function initialise($json, $smsObject)
    {
//        $this->smsObject = $smsObject;
//        $person = json_decode($json);
//        $this->consciousness = $person->consciousness;
//        $this->alive = $person->alive;
//        $this->id = $person->_id;
    }

    public function setRole($role)
    {
        if (self::VILLAGER === $role) {
            $this->personState['role'] = $role;
            $this->contactPerson($this->getMobileNumber(),
                "You are a villager, night fall is upon us. Go to sleep!");
        }

        if (self::WEREWOLF === $role) {
            $this->personState['role'] = $role;
        }
    }

    /**
     * Get the name of the villager
     * @return string
     */
    public function getName()
    {
        if (array_key_exists('name', $this->personState)) {
            return $this->personState['name'];
        } else {
            return null;
        }
    }

    /**
     * Set the name of the village.
     * @param $name String The name of the villager
     */
    public function setName($name)
    {
        $this->personState['name'] = strip_tags($name);
        $this->contactPerson($this->getMobileNumber(),
            'Hello ' . $name . ' and welcome to the game! Further instructions will follow...');
    }

    /**
     * Set the mobile number of a person
     *
     * @param $phoneNumber The mobile number
     */
    public function setMobileNumber($mobileNumber)
    {
        // Todo: Validate this mobile number
        $this->personState['mobileNumber'] = $mobileNumber;
    }

    /**
     * Get the mobile number of the person.
     * @return The persons phone number
     */
    public function getMobileNumber()
    {
        if (array_key_exists('mobilenumber', $this->personState)) {
            return $this->personState['mobileNumber'];
        } else {
            return null;
        }
    }

    /**
     * Sets the consciousness of the person
     * object to be awake.
     */
    public function wake(Person $person = null)
    {
        if ($this->isMe($person)) {
            return;
        }
        if (!is_null($person)) {
            $message = "OMG, {$person->getName()} has been found dead! There's blood and guts everywhere. Please discuss on who you think committed this insidious act of violence.";
        } else {
            $message = "It's the dawn of a new day. There is an werewolf in our midst! Discuss who you this this is.";
        }
        return $this->contactPerson($person, $message);
    }

    /**
     * Send a message to a person object.
     *
     * @param Person $person A person object
     * @param $message The message to send to the person
     * @return string Success or failure information.
     */
    private function contactPerson($mobileNumber, $message)
    {
        try {
            return $this->smsObject->send(array(
                'to' => $mobileNumber,
                'message' => $message
            ));
        } catch (ClockworkException $e) {
            return $e->getMessage();
        }
    }

    /**
     * Kills the person object
     * @param $howToKill - How the person should be killed.
     */
    public function kill($howToKill)
    {
        $message = '';
        switch ($howToKill) {
            case self::KILL_BY_LYNCH:
                $this->deathBy = 'Death by Lynching';
                $message = 'The villagers have decided to lynch you!';
                break;
            case self::KILL_BY_WEREWOLF:
                $this->deathBy = 'Death by Werewolf';
                $message = 'You have been killed by a Werewolf!';
                break;
            default:
                $this->deathBy = 'Death by Suicide';
                $message = 'For some reason you have decided to kill yourself!';
                break;
        }
        $this->personState['alive'] = false;
        return $this->contactPerson($this->personState['mobileNumber'], $message);
    }


    public function askForReasoning($typeOfArgument, Person $person)
    {
        switch ($typeOfArgument) {
            case 'nominate':
                $this->contactPerson($this->getMobileNumber(),
                    "Please present your reasons for nominating {$person->getName()}");
                break;
            case 'second':
                $this->contactPerson($this->getMobileNumber(),
                    "Please present your reasons for seconding {$person->getName()}");
                break;
            case self::DEFEND:
                $this->contactPerson($this->getMobileNumber(),
                    "Please defend your yourself against these accusations...");
                break;
        }
    }


    public function voteResult(Person $victim, $wasKilled, Array $people)
    {
        $wantedToLynch = array();
        $didNotWantToLynch = array();

        if (!$wasKilled) {
            $message = 'The Lynching did not pass, discussion will continue...';
        } elseif ($this->isMe($victim)) {
            $victim->kill(self::KILL_BY_LYNCH);
            return;
        } else {

            $message = 'Night fall is here, please go to sleep.';

            $message .= $victim->getName() . ' has been Lynched!' . "\n";
        }

            // If the person was not killed, let everyone know
            // that the lynching did not pass.
            foreach ($people as $mobile => $playerVote) {

                $personData = $this->gameState->toPerson($mobile);
                $currentName = $personData->personState['name'];

                if ($victim->isMe($personData)) {
                    continue;
                }

                if ($playerVote) {
                    array_push($wantedToLynch, $currentName);
                } else {
                    array_push($didNotWantToLynch, $currentName);
                }

            }

            // Build the message
            if (!empty($wantedToLynch)) {
                $message .= implode(', ', $wantedToLynch);
                $message .= ' chose to lynch. ';
            }

            if (!empty($didNotWantToLynch)) {
                $message .= implode(', ', $didNotWantToLynch);
                $message .= ' chose not to lynch. ';
            }

        $this->contactPerson($this->getMobileNumber(), $message);

    }

    /**
     * Checks to see if the person is alive or not.
     * @return bool True or False depending on the person.
     */
    public function isAlive()
    {
        return $this->personState['alive'];
    }

    /**
     * @return string
     */
    public function methodOfDeath()
    {
        return $this->personState['deathBy'];
    }

    /**
     * Create a JSON string from the current person object.
     * @return string a JSON version of the object
     */
    public function toJSON()
    {
        return json_encode($this->personState);
    }

    public function fromJSON($jsonObjectAsString)
    {
        // This will set the state of the person to what it was
        // when the person was saved as JSON.
        $this->personState = json_decode($jsonObjectAsString, true);
        return $this;
    }

    /**
     * @param Person $person
     * @return bool
     */
    public function isMe(Person $person)
    {
        return $person->getMobileNumber() == $this->getMobileNumber();
    }
}
