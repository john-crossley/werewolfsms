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

    private $role;
    private $consciousness;
    private $alive;
    private $deathBy;
    private $name;
    private $smsObject;
    private $phoneNumber;


    public function __construct($role, $name, $phoneNumber, $smsObject)
    {
        // Ensure the role exists.
        if (self::VILLAGER === $role || self::WEREWOLF === $role) {
            $this->role = $role;
        } else {
            throw new \Exception("Invalid role has been supplied for the person object.");
        }

        // Default consciousness
        $this->consciousness = static::AWAKE;
        // Person is alive by default
        $this->alive = true;

        $this->name = $name;

        // Store the game state
        $this->smsObject = $smsObject;

        $this->phoneNumber = $phoneNumber;
    }

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

    public function askForVote(Person $person)
    {
        try {
            $sms = $this->smsObject;
            $message = array(
                'to' => $person->phoneNumber,
                'message' => 'I am voting for: ' . $person->name
            );
            return $sms->send($message);
        } catch (ClockworkException $e) {
            return $e->getMessage();
        }
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
    public function wake(Person $person)
    {
        $name = $person->name;

        try {
            $sms = $this->smsObject;
            $message = array(
                'to' => $this->phoneNumber,
                'message' => 'OMFG... It\'s one of the villagers'
            );
            return $sms->send($message);
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
        switch ($howToKill) {
            case self::KILL_BY_LYNCH:
                $this->deathBy = 'Death by Lynching';
                break;
            case self::KILL_BY_WEREWOLF:
                $this->deathBy = 'Death by Werewolf';
                break;
            default:
                $this->deathBy = 'Death by Suicide';
                break;
        }

        $this->alive = false;
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
