<?php
/**
 * GAMESERVERWATCHER
 * 	coded by Ben Weidenhofer
 * Published under the open-source GNU GPLv3 licence.
 *
 * GitHub repo: https://github.com/KiloooNL/GameServerWatcher
 *
 * gameClass.php
 *
 * This PHP class file is main class for ALL game engines.
 * Within it, eventually will be every supported game in an array
 * with a short name (EG: "CS:S") and a long name (EG: "Counter-Strike: Source")
 *
 * So that we can go,
 * serverBanner("IP", "Port");
 *
 * findGameEngine($IP, $Port) {
 *      // find game engine by running query
 *      return gameEngine;
 * }
 *
 * findGameEngine("IP", "Port");
 */

function gameNameShortToLong($shortName) {
    strtolower($shortName);

    switch($shortName) {
        case 'hl':
            return 'Half-Life';
        case 'css':
            return 'Counter-Strike: Source';
        case 'q3':
            return 'Quake 3';
        default:
            return 'No game server defined.';
    }
}