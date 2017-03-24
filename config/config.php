<?php
/**
 * GAMESERVERWATCHER
 * 	coded by Ben Weidenhofer
 * Published under the open-source GNU GPLv3 licence.
 *
 * GitHub repo: https://github.com/KiloooNL/GameServerWatcher
 *
 * config.php
 */

########################################
# Global IP for the following:
#   : All game servers (it is recommended game servers are on a different IP to reduce lag under heavy load.
#   : FTP
########################################
define("GLOBAL_IP", "127.0.0.1");

########################################
# Database settings
#	: MySQL + Site-Wide DB settings.
#	: change $db_ip if different from localhost.
########################################
define("DB_IP", "localhost");
define("DB_USER", "root");
define("DB_PASS", "password");

########################################
# Server directories
#	: include trailing slash
#	: leave blank for default
#
#   STATS_DIR: the directory you will want to display server statistics
#   CONFIG_DIR: the directory for this file.
########################################
require_once("../../rootDir.php");
define("STATS_DIR", "/stats/");
define("CONFIG_DIR","/config");

########################################
# Error logging & debugging info
#   : DEBUG_ENABLED: Enables debugging. 0 = Off, 1 = On
#   : ERROR_LOGGING_ENABLED: Enables error logging. 0 = Off, 1 = On
########################################
require_once(ROOT_DIR . "/code/error.php");

define('DEBUB_ENABLED', 0);
define('ERROR_LOGGING_ENABLED', 1);

/************************************************
 *  WARNING!
 *
 * DO NOT EDIT CODE BELOW THIS LINE
 * UNLESS YOU KNOW WHAT YOU'RE DOING
 ***********************************************/

/************************
 * Page load time diagnostic function
 * not used right now, but implemented for future use
 *
 */
function pageLoadTime() {
    $time = microtime();
    $time = explode(' ', $time);
    $time = $time[1] + $time[0];
    $start = time; // start
}

###################################
#
# Quake 3 Servers Config - TODO: clean this up
#
###################################
define('Q3_ROOT', ROOT_DIR . "/code/quake/");           // Quake 3 scripts root folder
define('Q3_INC_PATH', Q3_ROOT . "inc/");   // Path to inc. files
define('INC_PREFIX', 'inc');                    // Prefix for .inc files
define('INC_POSTFIX', '.php');                  // Postfix for .inc files
define('SOCK_TIMEOUT', '10');                   // Socket timeout in ms - TODO: Make this a global config var for ALL game server queries

require_once(Q3_ROOT . "class/class.aux.php");
?>