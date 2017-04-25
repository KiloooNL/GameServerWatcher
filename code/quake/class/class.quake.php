<?php
/**
 * GAMESERVERWATCHER
 * 	coded by Ben Weidenhofer
 * Published under the open-source GNU GPLv3 licence.
 *
 * GitHub repo: https://github.com/KiloooNL/GameServerWatcher
 *
 * class.quake3.php
 *
 * This PHP file is a script for scraping Quake 3 server information in real time.
 * This is based on the GameQ Query Class by Tom Buskens
 * For more information, visit: GameQ - game server query class (http://gameq.sf.net)
 *
 * For a full list of supported servers, see /class/class.quake3.cfg.php
 */

require_once("../../config/config.php");

class serverStatus {
    var $quake3_games;      // contains the $quake3_games array from class.quake3.cfg.php
    var $quake3_strings;    // contains the $quake3_string array from class.quake3.cfg.php

    var $type_id;           // server type ID (quake 3, ut2004, etc) -- TODO: Maybe change this to gameID
    var $svID;              // current server ID
    var $svName;            // server type name (Quake 3 Arena, Unreal Tournament, etc)
    var $svAddress;         // server address
    var $svQueryPort;       // server query port (Not the game port!)
    var $svQueryType;       // server query type name
    var $svStrings;         // string(s) to send to the server
    var $svTimeout;         // time in ms to listen to incoming data, aka the "ping" for the server
    var $svType;
    var $time;              // average communication time with the server
    var $ping;               // Ping!

    var $aux;               // class with additional functions (class.aux.php)

    // Load the config
    function serverStatus() {
        require_once("class.quake.cfg.php");

        $this->quake3_games = $quake3_games;
        $this->quake3_strings = $quake3_strings;

        if(DEBUG_ENABLED) {
            print_r($this->quake3_games);
            print_r($this->quake3_strings);
        }

        $this->aux = new Aux;
    }

    function getInfo($servers, $timeout = 200, $outputType = 'parsed') {
        $this->svTimeout = $timeout;

        if(!is_array($servers)) {
            debug('Input data is not an array');
        }

        // Process servers
        while(list($this->svID, $server) = each($servers)) {
            // Get config
            if(!$this->getConfig($server)) {
                continue;
            }

            // Communicate with server
            if(($strings = $this->communicate()) !== false) {
                // Check what to do with the returned strings
                switch($outputType) {
                    case 'parsed':
                        $svOutput = $this->parseData($strings);
                        break;
                    case 'raw':
                        $svOutput['strings'] = $strings;
                        break;
                    default:
                        debug('Wrong output type specified');
                }
            } else {
                $svOutput = '';
            }

            // Add some additional info
            $svOutput = $this->customData($svOutput);

            // Put data into output array
            $output[$this->svID] = $svOutput;
        }

        // TODO: broken?
        if(isset($output)) {
            return $output;
        } else {
            return;
        }
    }

    // Get configuration data for the current server
    function getConfig($server) {
        // Clear data from previous servers
        unset($this->svQueryPort);
        unset($this->svStrings);

        // Read server data
        if(!isset($server[0])) {
            debug('Server type not set');
        }

        if(!isset($server[1])) {
            debug('Server address not set', 0);
        }

        $this->type_id   = $server[0];
        $this->svAddress = $server[1];

        // Check if type exists
        if(!isset($this->quake3_games[$this->type_id])) {
            debug('Server type ' . $this->type_id . ' does not exists in the config file.');
            return false;
        }

        // Get data from config
        $cfg_data = explode('/', $this->quake3_games[$this->type_id]);
        $this->svName      = $cfg_data[0];
        $this->svQueryType = $cfg_data[2];

        // Set port
        if(!isset($server[2])) {
            $this->svQueryPort = $cfg_data[1];
        } else {
            $this->svQueryPort = $server[2];
        }

        // Get strings to query server
        if(!isset($cfg_data[3])) {
            $this->svStrings = explode('/', $this->quake3_strings[$this->svQueryType]);
        } else {
            $this->svStrings = explode('/', $this->quake3_strings[$cfg_data[3]]);
        }
        return true;
    }

    function communicate() {
        // Open connection to the server
        if(!($sock = @fsockopen('udp://' . $this->svAddress, $this->svQueryPort))) {
            debug('Could not connect to server');
            return false;
        }
        socket_set_timeout($sock, 0, 1000 * SOCK_TIMEOUT);

        // Send strings to server, receive data
        $string_cnt = count($this->svStrings);

        for($i = 0; $i != $string_cnt; $i++) {
            // Send string
            fwrite($sock, $this->svStrings[$i]);

            // Wait for answer
            $wait = 0;
            while($wait < $this->svTimeout) {
                $string = fread($sock, 4096);
                if(!empty($string)) {
                    $data[] = $string;
                    if(strlen($string) < 4096) {
                        break;
                    }
                } $wait += SOCK_TIMEOUT;
            }
        }

        @fclose($sock);

        // Rough ping
        $this->ping = $wait;
        // $wait = round($wait, 1);

        // Check if any data was returned
        if(empty($data[0])) {
            debug('The server didn\'t return any data');
            return false;
        }
        return $data;
    }

    // Parse data according to the game type
    function parseData($data) {
        // Include the parse file
        $parse_file = Q3_INC_PATH.INC_PREFIX.$this->svQueryType.INC_POSTFIX;
        if(!is_readable(($parse_file))) {
            debug('Could not read file "' . $parse_file . '".', 0);
        }

        if(file_exists($parse_file)) {
            require($parse_file);

        } else {
            debug('File ' . $parse_file . ' does not exist.', 0);
        }
        // return $output;
        return $data;
    }

    // Adds some general server info to the output
    function customData(&$data) {
        $custom['address']    = $this->svAddress;
        $custom['query_port'] = $this->svQueryPort;
        $custom['id']         = $this->type_id;
        $custom['type']       = $this->svType;
        $custom['name']       = $this->svName;
        $custom['ping']       = $this->ping;

        $data['custom'] = $custom;
        return $data;
    }
}