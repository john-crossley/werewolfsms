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

    private $consciousness,
            $alive,
            $smsObject,
            $mobileNumber,
            $id;


    /**
     * The Person constructor - Is called when its the start of a new game.
     *
     * @param \Clockwork\Clockwork $smsObject - Requires the SMS object.
     */
    public function __construct(\Clockwork\Clockwork $smsObject)
    {
        // Set some default values on the person.
        $this->consciousness = self::AWAKE;
        $this->alive = true;
        $this->smsObject = $smsObject;
    }

    /**
     * Pass in a person state as a JSON object.
     *
     * @param $json The person state
     */
    public function initialise($json, $smsObject)
    {
        $this->smsObject = $smsObject;
        $person = json_decode($json);
        $this->consciousness = $person->consciousness;
        $this->alive = $person->alive;
        $this->id = $person->_id;
    }

    public function setRole($role)
    {
        if (self::VILLAGER === $role || self::WEREWOLF === $role) {
            // We can set the role.
            $this->role = $role;
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
        return $this->name;
    }

    /**
     * Set the name of the village.
     * @param $name String The name of the villager
     */
    public function setName($name)
    {
        $this->name = strip_tags($name);
    }

    /**
     * Gets the consciousness (state) of the person object.
     * @return string The consciousness of the person object.
     */
    public function getConsciousness()
    {
        return $this->consciousness;
    }

    public function setConsciousness($consciousness)
    {
        if (self::AWAKE === $consciousness || self::ASLEEP === $consciousness) {
            // We can set the role.
            $this->consciousness = $consciousness;
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
        $this->mobileNumber = $mobileNumber;
    }

    /**
     * Get the mobile number of the person.
     * @return The persons phone number
     */
    public function getMobileNumber()
    {
        return $this->mobileNumber;
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
    private function contactPerson(Person $person, $message)
    {
        try {
            return $this->smsObject->send(array(
                'to' => $person->mobileNumber,
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
        $this->alive = false;
        return $this->contactPerson($this, $message);
    }

    /**
     * Checks to see if the person is alive or not.
     * @return bool True or False depending on the person.
     */
    public function isAlive()
    {
        return ($this->alive) ? true : false;
    }

    /**
     * @return string
     */
    public function methodOfDeath()
    {
        return $this->deathBy;
    }

}
