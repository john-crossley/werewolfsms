<?php

namespace Werewolfsms;

use Symfony\Component\Config\Definition\Exception\Exception;

class Person {

    const VILLAGER = 'villager';
    const WEREWOLF = 'werewolf';

    const AWAKE = 'awake';
    const ASLEEP = 'asleep';

    public $role; // Werewolf/Villager
    public $consciousness; // Awake/Asleep

    public function sleep() {}
    public function wake() {}

    public function kill($howToKill) {}

    // Send out an sms - Such and such a person has been voted do
    // we have a seconder.
    public function askForSeconders(Person $person) {
        // return true; or false;
    }
    public function askForVote(Person $person) {
        // return true; or false;
    }


    public function __construct($role)
    {

        // Ensure the role exists.
        if ($role === static::VILLAGER || $role === static::WEREWOLF) {
            $this->role = $role;
        } else {
            throw new \Exception("Invalid role has been supplied for the person object.");
        }

        // Default consciousness
        $this->consciousness = self::AWAKE;
    }

}
