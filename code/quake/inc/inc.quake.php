<?php
/**
 * GAMESERVERWATCHER
 * 	coded by Ben Weidenhofer
 * Published under the open-source GNU GPLv3 licence.
 *
 * GitHub repo: https://github.com/KiloooNL/GameServerWatcher
 *
 * inc.quake3.php
 *
 * This PHP file is a script for scraping Quake 3 server information in real time.
 * This is based on the GameQ Query Class by Tom Buskens
 * For more information, visit: GameQ - game server query class (http://gameq.sf.net)
 *
 * For a full list of supported servers, see /class/class.quake3.cfg.php
 */

$data = $data[0];

// Parse variables
$length = strlen($data);

for($i = 0; $data{$i} != '\\'; $i++);
for($j = $i; $j < $length && $data{$j} != chr(10); $j++);

$output = $this->aux->spyString(substr($data, $i, $j - $i));

// Parse players
$numPlayers = 0;

for($i = $j + 1; $i < $length; $i++) {
    $x = $i;

    for($i; $data{$i} != chr(10); $i++);

    // Get name, score and ping
    preg_match("/^((.?\d+)\x20)?(\d+).*\x20\"(.*)\"$/", substr($data, $x, $i - $x), $match);

    if(!empty($match[2]) || ($match[2] == 0)) {
        $player['score'] = $match[2];
    }

    $player['ping'] = $match[3];
    $player['name'] = $match[4];

    // Put players into main array
    $output['players'][$numPlayers++] = $player;
}

$output['num_players'] = $numPlayers;

// Sort players
$this->aux->sortPlayers($output, 'quake');
?>