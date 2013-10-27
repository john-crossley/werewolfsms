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
        if (!empty($config['username'])) {
            if (!empty($config['password'])) {
                $serverstring .= $config['username'].':'.$config['password'].'@';
            } else {
                $serverstring .= $config['username'].'@';
            }
        }
        if (!empty($config['host'])) {
            $serverstring .= $config['host'];
        } else {
            $serverstring .= 'localhost';
        }
        if (!empty($config['port'])) {
            $serverstring .= ':'.$config['port'];
        }
        return $serverstring;
    }

}
