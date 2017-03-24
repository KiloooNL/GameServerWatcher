<?php
/**
 * GAMESERVERWATCHER
 * 	coded by Ben Weidenhofer
 * Published under the open-source GNU GPLv3 licence.
 *
 * GitHub repo: https://github.com/KiloooNL/GameServerWatcher
 *
 * error.php
 *
 * This PHP file is a script for error & debug logging for all other scripts
 * 
 * TODO: This needs to be globalized and require_once'd in all PHP scripts, or by config.php
 *
 */
 
 // Enable debugging / error logging?
 define('DEBUB_ENABLED', 0);
 define('ERROR_LOGGING_ENABLED', 1);
 require_once("../../config/config.php");
 
 // Error codes
define("ERROR_NOERROR", 0);
define("ERROR_NOSERVER", -1);
define("ERROR_INSOCKET", -2);
define("ERROR_TIMEOUT", -3);

function debug($string) {
    if(defined('DEBUG_ENABLED')) {
        echo "<!-- [DEBUG]: " . $string . " -->\n";
    }
}

function error($string, $errCode) {
    if(defined('ERROR_LOGGING_ENABLED')) {
        echo "[ERROR] [Error Code: " . $errCode . "] " . $string . "\n";    
    }
}
 ?>