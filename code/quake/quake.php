<?php
/**
 * GAMESERVERWATCHER
 * 	coded by Ben Weidenhofer
 * Published under the open-source GNU GPLv3 licence.
 *
 * GitHub repo: https://github.com/KiloooNL/GameServerWatcher
 *
 * quake3.php
 *
 * This PHP file is a script for scraping Quake 3 server information in real time.
 * This is based on the GameQ Query Class by Tom Buskens
 * For more information, visit: GameQ - game server query class (http://gameq.sf.net)
 *
 * For a full list of supported servers, see /class/class.quake3.cfg.php
 */

require_once("../../config/config.php");

// Get all the scripts
require_once("class/class.quake.php");
require_once("config/quakeStrings.php");
require_once("config/quake.config.php");

$query = new serverStatus;

$data = $query->getInfo($servers);
$svStatus = '';

// TODO: Clean this up, big time.
// Go through all the servers
foreach($servers as $serverID => $values) {
        // Grab the array
        $thisServer = $data[$serverID];
        // $svGameType = quakeGameType($thisServer['g_gametype']);
        $svMap      = $thisServer['mapname'];
        $svName     = $thisServer['sv_hostname'];
        $svPlayers  = count($thisServer['players']);
        $svMax      = $thisServer['sv_maxclients'];
}