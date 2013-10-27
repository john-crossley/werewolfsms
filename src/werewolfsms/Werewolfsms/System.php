<?php

namespace Werewolfsms;

class System {

    public static function getApiKey($pathToFile)
    {
        $json = json_decode(file_get_contents($pathToFile));
        return $json->api_key;
    }

    public static function getArray($pathToFile)
    {
        $json = json_decode(file_get_contents($pathToFile));
        return $json;
    }

}
