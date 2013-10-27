<?php

namespace Werewolfsms;

use Clockwork\ClockworkException;
use Symfony\Component\Config\Definition\Exception\Exception;

class Person {

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
        $this->personState = new \stdClass();

        // Set some state values
        $this->personState->consciousness = self::AWAKE;
        $this->personState->alive = true;
        $this->personState->role = self::VILLAGER;
        $this->personState->name = 'VILLAGER ' . mt_rand(0, 99);

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
        if (self::VILLAGER === $role || self::WEREWOLF === $role) {
            // We can set the role.
            $this->personState->role = $role;
        } else {
            throw new \Exception('Invalid role has been supplied to the person object.');
        }
    }

    /**
     * Get the name of the villager
     * @return string
     */
    public function getName()
    {
        return $this->personState->name;
    }

    /**
     * Set the name of the village.
     * @param $name String The name of the villager
     */
    public function setName($name)
    {
        $this->personState->name = strip_tags($name);
    }

    /**
     * Gets the consciousness (state) of the person object.
     * @return string The consciousness of the person object.
     */
    public function getConsciousness()
    {
        return $this->personState->consciousness;
    }

    public function setConsciousness($consciousness)
    {
        if (self::AWAKE === $consciousness || self::ASLEEP === $consciousness) {
            // We can set the role.
            $this->personState->consciousness = $consciousness;
        } else {
            throw new \Exception('Invalid consciousness has been supplied to the person object.');
        }
    }

    /**
     * Set the mobile number of a person
     *
     * @param $phoneNumber The mobile number
     */
    public function setMobileNumber($mobileNumber)
    {
        // Todo: Validate this mobile number
        $this->personState->mobileNumber = $mobileNumber;
    }

    /**
     * Get the mobile number of the person.
     * @return The persons phone number
     */
    public function getMobileNumber()
    {
        return $this->personState->mobileNumber;
    }

    /**
     * Sets the consciousness of the person
     * object to asleep.
     */
    public function sleep()
    {
        $this->setConsciousness(self::ASLEEP);
    }

    /**
     * Sets the consciousness of the person
     * object to be awake.
     */
    public function wake(Person $person = null)
    {
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
        $this->personState->alive = false;
        return $this->contactPerson($this, $message);
    }


    public function askForReasoning($typeOfArgument, Person $person)
    {
        switch($typeOfArgument) {
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


    public function voteResult(Person $person, $wasKilled, Array $people)
    {
        $message = '';
        $wantedToLynch = array();
        $didNotWatchToLynch = array();
        $contactNumbers = array();

        foreach ($people as $mobile => $votedLynch) {

            // If the person was not killed, let everyone know
            // that the lynching did not pass.
            if (!$wasKilled) {
                $message = 'The Lynching did not pass, discussion will continue...';
                $this->contactPerson($mobile, $message);
                continue;
            }

            $currentName = $this->gameState->toPerson($mobile)->personState->name;

            if ($currentName == $person->getName()) continue;

            if ($votedLynch) {
                array_push($wantedToLynch, $currentName);
            } else {
                array_push($didNotWatchToLynch, $currentName);
            }

            $contactNumbers[] = $mobile;

        }

        $message = $person->getName() . ' has been Lynched!' . "\n";

        // Build the message
        if (!empty($wantedToLynch)) {
            $message .= implode(', ', $wantedToLynch);
            $message .= ' chose to lynch. ';
        }

        if (!empty($didNotWatchToLynch)) {
            $message .= implode(', ', $didNotWatchToLynch);
            $message .= ' chose not to lynch. ';
        }

         $this->bulkSendSms($contactNumbers, $message);

    }

    private function bulkSendSms(array $contactNumbers, $message)
    {
        foreach ($contactNumbers as $number)
        {
            $this->contactPerson($number, $message);
        }
        return true;
    }

    /**
     * Checks to see if the person is alive or not.
     * @return bool True or False depending on the person.
     */
    public function isAlive()
    {
        return ($this->personState->alive) ? true : false;
    }

    /**
     * @return string
     */
    public function methodOfDeath()
    {
        return $this->personState->deathBy;
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
        $this->personState = json_decode($jsonObjectAsString);
        return $this;
    }
}
