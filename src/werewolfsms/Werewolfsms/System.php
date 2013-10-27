<?php

namespace Werewolfsms;

class System {

    public static function getApiKey($pathToFile)
    {
        $json = json_decode(file_get_contents($pathToFile));
        return $json->api_key;
    }

    public static function getConnectionString($pathToConfigFile)
    {
        $json = json_decode(file_get_contents($pathToConfigFile),true);
        if (!array_key_exists('host',$json)) { $json['host'] = ''; }
        if (!array_key_exists('username',$json)) { $json['username'] = ''; }
        if (!array_key_exists('password',$json)) { $json['password'] = ''; }
        if (!array_key_exists('port',$json)) { $json['port'] = ''; }
        $serverstring = 'mongodb://';
        if (!empty($json['username'])) {
            if (!empty($json['password'])) {
                $serverstring .= $json['username'].':'.$json['password'].'@';
            } else {
                $serverstring .= $json['username'].'@';
            }
        }
        if (!empty($json['host'])) {
            $serverstring .= $json['host'];
        } else {
            $serverstring .= 'localhost';
        }
        if (!empty($json['port'])) {
            $serverstring .= ':'.$json['port'];
        }
        return $serverstring;
    }

}
