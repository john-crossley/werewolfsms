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

    public function __get($property)
    {
        return isset($this->$property) ? $this->$property : false;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    /**
     * The Person constructor - Is called when its the start of a new game.
     *
     * @param \Clockwork\Clockwork $smsObject - Requires the SMS object.
     */
    public function __construct(\Clockwork\Clockwork $smsObject)
    {
        $this->smsObject = $smsObject;
    }

    /**
     * The initialise method, loads the persons current state.
     *
     * @param $jsonObject The person as a JSON object
     */
    public function initialise($jsonObject)
    {
        // Some random magic
    }

//    public function __construct($role, $name, $phoneNumber, $smsObject)
//    {
//        // Ensure the role exists.
//        if (self::VILLAGER === $role || self::WEREWOLF === $role) {
//            $this->role = $role;
//        } else {
//            throw new \Exception("Invalid role has been supplied for the person object.");
//        }
//
//        // Default consciousness
//        $this->consciousness = static::AWAKE;
//        // Person is alive by default
//        $this->alive = true;
//
//        $this->name = $name;
//
//        // Store the game state
//        $this->smsObject = $smsObject;
//
//        $this->phoneNumber = $phoneNumber;
//    }

    /**
     * Get the name of the villager
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * Set the name of the village.
     * @param $name String The name of the villager
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    // Send out an sms - Such and such a person has been voted do
    // we have a seconder.
    public function askForSeconders(Person $person)
    {

    }

    /**
     * Gets the consciousness (state) of the person object.
     * @return string The consciousness of the person object.
     */
    public function consciousness()
    {
        return $this->consciousness;
    }

    /**
     * Sets the consciousness of the person
     * object to asleep.
     */
    public function sleep()
    {
        $this->consciousness = static::ASLEEP;
    }

    /**
     * Sets the consciousness of the person
     * object to be awake.
     */
    public function wake(Person $person = null)
    {
        if (!is_null($person)) {
            $message = "OMG, {$person->name} has been found dead! There's blood and guts everywhere. Please discuss on who you think committed this insidious act of violence.";
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
                'to' => $person->phoneNumber,
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
