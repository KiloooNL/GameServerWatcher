<?php
/**
 * GAMESERVERWATCHER
 * 	coded by Ben Weidenhofer
 * Published under the open-source GNU GPLv3 licence.
 *
 * GitHub repo: https://github.com/KiloooNL/GameServerWatcher
 *
 * half-life.php
 *
 * This PHP file is a script for scraping Half-Life server information in real time.
 *
 */


/** TODO:
 * For now, these variables are changed in this file.
 * in the future, it should be changed so that they are accessed and edited via /config/config.php
 *
 *
 */

$serverIP = "127.0.0.1";
$serverPort = 27016;    // SRCDS Default Port is: 27015.

$fp = fsockopen($serverIP, $serverPort, $errno, $errstr, 5);
stream_set_timeout($fp, 5);

// Let's begin.

define("ERROR_NOERROR", 0);
define("ERROR_NOSERVER", -1);
define("ERROR_INSOCKET", -2);

// Debugging:
define("DEBUG", 0);
function debug($string) {
    if(defined('DEBUG')) {
        echo "<!-- [DEBUG]: " . $string . " -->\n";
    }
}

/******
 * microtime_float()
 * see: http://php.net/manual/en/function.microtime.php
 */
function microtime_float() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
    echo $sec;
}

function getFloat32($fourchars) {
    $bin = '';
    for($i = 0; $i <=3; $i++) {
        $bin = str_pad(decbin(ord(substr($fourchars, $i, 1))), 8, '0', STR_PAD_LEFT).$bin;
    }

    $exponent = bindec(substr($bin, 1, 8));
    $exponent = ($exponent)? $exponent - 127 : $exponent;

    if(exponent) {
        $float = bindec('1'.substr($bin, 9, $exponent));
        $dec = bindec(substr($bin, 9 + $exponent));
        $time = "$float.$dec";
        return number_format($time / 60, 2);
    } else {
        return 0.0;
    }
}

class serverStatus {
    // Initialize variables
    var $_arr = array();
    var $_ip = "";
    var $_port = 0;
    var $_isconnected = 0;
    var $_players = array();
    var $_rules = array();  // TODO: we may not need this, but init it for now.
    var $_errorcode = ERROR_NOERROR;
    var $_seed = "Server status for server (%s:%d)";
    var $_socket;

    // Constructor
    function serverStatus($serverIP, $serverPort) {
        $this->_ip = $serverIP;
        $this->_port = $serverPort;
        $this->_seed = ""; // TODO: update seed
        $this->_arr = array_pad($this->_arr, 21, 0);
        $this->_socket = fsockopen("udp://" . $this->_ip, $this->_port, $errno, $errstr, 3);
        socket_set_timeout($this->_socket, 1, 0);

        if($tmp = $this->_sockstate()) {
            // echo $tmp;
            if(!$this->_socket) {
                echo "Error #" . $errno . ": " . $errstr;
                // exit;
            }

            debug("[Initialized]");
            // $this->_brand_seed();
            return !(!$this->_socket);
        }
    }

    // Set error code
    function setError($code) {
        debug("[Setting error code (" . $code . ")]<br/>\n");
        $this->_errorcode = $code;
    }

    // Obtains a ping value to the server
    function _ping() {
        debug("[Getting Ping");
        if($tmp = $this->_sockstate()) {
            // echo $tmp;
            debug("[Error in socket]");
            $this->seterror(ERROR_INSOCKET);
            return -1; // Error in socket
        } else {
            $tmp = "";
            $start = microtime_float()*1000;
            $this->_send("ping".chr(0)); // Todo: original code was $this->_send("ÿÿÿÿping".chr(0)); ... was this an encoding error? Check.

            while(strlen($tmp) < 4 && (microtime_float()*1000 - $start) < 1000) {
                $tmp = $this->_getmore();
            }

            if(strlen($tmp) >=4 && substr($tmp, 4, 1) == 'j') {
                $end = getmicrotime()*1000;
                if($end < $start) {
                    echo $end . '\n' . $start;
                    return ($end - $start); // ($end - $start >= 0 ? ($end - $start) : -1; // Will be numeric ping
                } else {
                    $this->setError(ERROR_NOSERVER);
                    debug("[Error: No ping from the server]");
                    return -1; // Server unresponsive
                }
            }
            return 0;
        }
    }

    // Populates details array
    function getDetails() {
        debug("[Getting details]");
        if($tmp = $this->_sockstate()) {
            // echo $tmp;
            $this->setError(ERROR_INSOCKET);
            return -1;
        } else {
            $this->_send("details".chr(0));
            $buffer = $this->_getmore();
            /**
             *  echo $buffer;
             *  for($i = 0; $i < strlen($buffer); i++) {
             *      echo '[' . ord(substr($buffer, $i)) . ']';
             *  } exit;
             */

            $tmp = substr($buffer, 0, 5);
            $buffer = substr($buffer, 5);
            $text = "";
            $count = 0;
            $arr = array();

            do {
                $tmp = substr($buffer, 0,1 );
                $buffer = substr($buffer, 1);
                if(!ord($tmp)) {
                    $this->_arr[$count++] = $text;
                    $text = "";
                } else {
                    $text.=$tmp;
                }
            } while($count < 5);

            for($i = 0; $i < 6; $i++, $count++) {
                $tmp=substr($buffer, 0, 1);
                $buffer = substr($buffer, 1);
                if($count == 8 || $count == 9) {
                    $this->_arr[$count] = $tmp;
                } else {
                    $this->_arr[$count] = ord($tmp);
                }
            } // $count = 12

            if($this->_arr[$count - 1]) { // if ismod
                do {
                    $tmp = substr($buffer, 0, 1);
                    $buffer = substr($buffer, 1);
                    $this->_arr[$count] = "";
                    if (ord($tmp != 0)) {
                        $this->_arr[$count] .= $tmp; // mod website [12]
                    }
                } while(ord($tmp) != 0);
                $count++;

                do {
                    $tmp = substr($buffer, 0, 1);
                    $buffer = substr($buffer, 1);

                    $this->_arr[$count] = "";
                    if(ord($tmp) != 0) {
                        $this->_arr[$count] .= $tmp; // mod FTP [13]
                    }
                } while(ord($tmp != 0));
                $count++; // [14] = unused

                $this->_arr[$count++] = ord(substr($buffer, 0, 1));
                $buffer = substr($buffer, 1);
                $tmp = substr($buffer, 0, 4);
                $buffer = substr($buffer, 4);

                for($j = 0; $j < 4; $j++) {
                    $this->_arr[$count++] += (pow(256, $j) * ord(substr($tmp, $j, 1))); // [15] Version
                } $count++;

                $tmp = substr($buffer, 0, 4);
                $buffer = substr($buffer, 4);

                for($j = 0; $j < 4; $j++) {
                    $this->_arr[$count++] += (pow(256, $j) * ord(substr($tmp, $j, 1))); // [16] Size
                } $count++;

                // [17] Server only
                $this->_arr[$count++] = ord(substr(($buffer, 0, 1));
                $buffer = substr($buffer, 1);

                // [18] Custom client.dll
                $this->_arr[$count++] = ord(substr(($buffer, 0, 1));
                $buffer = substr($buffer, 1);

                // [19] Secure.
                $this->_arr[$count++] = ord(substr(($buffer, 0, 1));
                $buffer = substr($buffer, 1);
            } else {
                for($i = 0; $i < 8; $i++) {
                    $this->_arr[$count] = round($this->_ping(), 1);
                    return 0;
                }
            }
        }
    }

    // Sets players array
    function getPlayers() {
        debug("[Getting players]");
        //
    }


}