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
$serverIP = "127.0.0.1";
$serverPort = "27015";

function sourceQuery($serverIP)
{
    /** TODO: Change variables in this file to reflect the same variables in half-life.php
     *        this will make moving between PHP files easier, and make code base neater and more understandable.
     * // Initialize variables
     * var $_arr = array();
     * var $_ip = "";
     * var $_port = 0;
     * var $_isconnected = 0;
     * var $_players = array();
     * var $_rules = array();  // TODO: we may not need this, but init it for now.
     * var $_errorcode = ERROR_NOERROR;
     * var $_seed = "Server status for server (%s:%d)";
     * var $_socket;
     */

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
    $svSocket = fsockopen("udp://" . $svAddress, $svPort, $errno, $errstr, 3);
    fwrite($svSocket, $svCommand);

    $junkHead = fread($svSocket, 4);
    $checkStatus = socket_get_status($svSocket);

    if($checkStatus["unread_bytes"] == 0) {
        return 0;
    }

    $loop = true;
    while($loop) {
        $str = fread($svSocket, 1);
        $svStats .= $str;
        $status = socket_get_status(@$svSocket);
        if ($status["unread_bytes"] == 0) {
            $loop = false;
        }
    }
    fclose($svSocket);

    $x = 0;
    while($x <= strlen($svStats)) {
        $x++;
        $result .= substr($svStats, $x, 1);
    }

    // ord(string $string);
    $result = str_split($result);
    $info['network'] = ord($result[0]);
    $char = 1;

    // TODO: Make this into array and to a foreach loop
    while(ord($result[$char]) != "%00") {
        $info['name'] .= $result[$char];
        $char++;
    }
    $char++;

    while(ord($result[$char]) != "%00") {
        $info['map'] .= $result[$char];
        $char++;
    }
    $char++;

    while(ord($result[$char]) != "%00") {
        $info['dir'] .= $result[$char];
        $char++;
    }
    $char++;

    while(ord($result[$char]) != "%00") {
        $info['description'] .= $result[$char];
        $char++;
    }
    $char++;

    $info['appid'] = ord($result[$char] . $result[($char + 1)]);
    $char += 2;
    $info['players'] = ord($result[$char]);
    $char++;
    $info['max'] = ord($result[$char]);
    $char++;
    $info['bots'] = ord($result[$char]);
    $char++;
    $info['dedicated'] = ord($result[$char]);
    $char++;
    $info['os'] = chr(ord($result[$char]));
    $char++;
    $info['password'] = ord($result[$char]);
    $char++;
    $info['secure'] = ord($result[$char]);
    $char++;

    while(ord($result[$char]) != "%00") {
        $info['version'] .= $result[$char];
        $char++;
    }

    return $info;
}

$query = sourceQuery($serverIP);

/** Don't need to display this info.
 * This is for debugging purposes.
 *
    echo "network: ".$q['network']."<br/>";
    echo "name: ".$q['name']."<br/>";
    echo "map: ".$q['map']."<br/>";
    echo "dir: ".$q['dir']."<br/>";
    echo "desc: ".$q['description']."<br/>";
    echo "id: ".$q['appid']."<br/>";
    echo "players: ".$q['players']."<br/>";
    echo "max: ".$q['max']."<br/>";
    echo "bots: ".$q['bots']."<br/>";
    echo "dedicated: ".$q['dedicated']."<br/>";
    echo "os: ".$q['os']."<br/>";
    echo "password: ".$q['password']."<br/>";
    echo "secure: ".$q['secure']."<br/>";
    echo "version: ".$q['version']."<br/>";
 *
 */

$svStatus = $query['network'];

if(!$svStatus) {
    $svStatus = "Offline";
} else {
    $svStatus = "Online";
}

$svRank = "1st"; // TODO: Scrape this information from gametracker.rs in the future for a true rank.
?>

<img src="csImg.php?svName="<?php echo $query['name']; ?>"&svAddress="<?php echo $serverIP; ?>"&svPort="<?php echo $serverPort; ?>"&svStatus="<?php echo $svStatus; ?>"&svPlayers="<?php echo $query['players']; ?>"&svMax="<?php echo $query['max']; ?>"&svRank="<?php echo $svRank; ?>"&svMap="<?php echo $query['map']; ?>" class="border" width="560" height="95" align="middle" />