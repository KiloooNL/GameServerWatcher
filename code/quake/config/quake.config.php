<?php
/**
 * GAMESERVERWATCHER
 * 	coded by Ben Weidenhofer
 * Published under the open-source GNU GPLv3 licence.
 *
 * GitHub repo: https://github.com/KiloooNL/GameServerWatcher
 *
 * quake.config.php
 *
 * This PHP file is a script for scraping Quake 3 server information in real time.
 * This is based on the GameQ Query Class by Tom Buskens
 * For more information, visit: GameQ - game server query class (http://gameq.sf.net)
 *
 * For a full list of supported servers, see this file
 */

$servers['quake3'] = array('q3', "216.86.155.163", "27960");
/** The following are the variables returned by a Q3A server so you can do what you want with them

    [sv_punkbuster]
 	[g_maxGameClients]
	[bot_minplayers]
	[g_gametype]
	[capturelimit]
	[sv_maxPing]
	[sv_minPing]
	[sv_hostname]
	[sv_maxRate]
	[sv_floodProtect]
	[dmflags]
	[fraglimit]
	[timelimit]
	[sv_maxclients]
	[version]
	[protocol]
	[mapname]
	[sv_privateClients]
	[sv_allowDownload]
	[web site]
	[URL]
	[CPU]
	[gamename]
	[g_needpass]
	[players]
    */