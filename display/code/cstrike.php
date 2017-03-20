<?php
/**
 * GAMESERVERWATCHER
 * 	coded by Ben Weidenhofer
 * Published under the open-source GNU GPLv3 licence.
 *
 * GitHub repo: https://github.com/KiloooNL/GameServerWatcher
 *
 * cstrike.php
 *
 * This PHP file is a script for scraping Source Engine (OrangeBox+) server information in real time.
 *
 */

// Server IP / Port
define("serverIP", "127.0.0.1");
define("serverPort", "27015");



function sourceQuery($serverIP) {
    $info = array(
        "name"        => "",
        "map"         => "",
        "dir"         => "",
        "description" => "",
        "version"     => "",);

    $result = "";

    $cut = explode(":", $serverIP);
    $svStats = "";
    $svAddress = $cut[0];
    $svPort = $cut[1];
    $svCommand = "\377\377\377\377TSource Engine Query\0";
    $svSocket = fsockopen("udp://".$svAddress, $svPort, $errno, $errstr, 3);
    fwrite($svSocket, $svCommand);

    $junkHead = fread($svSocket, 4);
    $checkStatus = socket_get_status($svSocket);

    if($checkStatus["unread_bytes"] == 0) {
        return 0;
    }

}