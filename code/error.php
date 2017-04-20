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

require_once("../../config/config.php");
 
// Error codes
define("ERROR_NOERROR", 0);
define("ERROR_NOSERVER", -1);
define("ERROR_INSOCKET", -2);
define("ERROR_TIMEOUT", -3);

function debug($string) {
    if(DEBUG_ENABLED) {
        echo "<!-- [DEBUG]: " . $string . " -->\n";
    }
}

function error($string, $errCode) {
    if(ERROR_LOGGING_ENABLED) {
        echo "[ERROR] [Error Code: " . $errCode . "] " . $string . "\n";    
    }
}

// Catch the error: Fatal error: Maximum execution time of XX seconds exceeded
register_shutdown_function(function() {
    $error = error_get_last();

    if ($error['type'] === E_ERROR && strpos($error['message'], 'Maximum execution time of') === 0) {
        /**
         * TODO: Make this much neater.
         */
        $ip = "IP";
        $port = "port";

        if(isset($serverIP)) {
            $ip = "IP of '$serverIP' ";
        }

        if(isset($serverPort)) {
            $port = "port of '$port'";
        }

        echo "The server did not respond. Please check if the server is online, and that the $ip & $port specified are correct.";
    }
});