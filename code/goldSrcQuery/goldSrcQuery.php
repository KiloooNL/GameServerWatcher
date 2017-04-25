<?php
/**
 * GAMESERVERWATCHER
 * 	coded by Ben Weidenhofer
 * Published under the open-source GNU GPLv3 licence.
 *
 * GitHub repo: https://github.com/KiloooNL/GameServerWatcher
 *
 * goldSrcQuery.php
 *
 * This PHP file is a script for scraping Half-Life (Valve) server information in real time.
 *
 */

require_once("../../config/config.php");

// Check if server IP & port are already parsed
if(isset($_GET['serverIP'])) {
    $serverIP = $_GET['serverIP'];

    if(isset($_GET['serverPort'])) {
        $serverPort = $_GET['serverPort'];
    }
} else {
    $serverIP = "192.168.1.147";
    $serverPort = 27016;    // SRCDS Default Port is: 27015.
}

/******
 * microtime_float()
 * see: http://php.net/manual/en/function.microtime.php
 */
function microtime_float() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

function getFloat32($chars) {
    $bin = '';
    for($i = 0; $i <=3; $i++) {
        $bin = str_pad(decbin(ord(substr($chars, $i, 1))), 8, '0', STR_PAD_LEFT).$bin;
    }

    $exponent = bindec(substr($bin, 1, 8));
    $exponent = ($exponent) ? $exponent - 127 : $exponent;

    if($exponent) {
        $float = bindec('1'.substr($bin, 9, $exponent));
        $dec = bindec(substr($bin, 9 + $exponent));
        $time = "$float.$dec";
        return number_format($time / 60, 2);
    } else {
        return 0.0;
    }
}

class goldSrcQuery {
    // Initialize variables
    var $_arr = array();
    var $_ip = "";
    var $_port = 0;
    var $_isconnected = 0;
    var $_players = array();
    var $_rules = array();
    var $_errorcode = ERROR_NOERROR;
    var $_seed = "Server status for server (%s:%d)";
    var $_socket;

    // Constructor
    function goldSrcQuery($serverIP, $serverPort) {
        $this->_ip = $serverIP;
        $this->_port = $serverPort;
        debug($this->_seed);
        $this->_seed = "\x0a\x3c\x21\x2d\x2d\x20\x20\x20\x20\x20\x20\x20\x53\x65\x72\x76\x65\x72\x20\x6d\x61\x64\x51\x75\x65\x72\x79\x20\x43"
            ."\x6c\x61\x73\x73\x20\x20\x20\x20\x20\x20\x20\x2d\x2d\x3e\x0a\x3c\x21\x2d\x2d\x20\x20\x20\x20\x43\x6f\x70\x79\x72\x69"
            ."\x67\x68\x74\x20\x28\x43\x29\x20\x32\x30\x30\x32\x20\x6d\x61\x64\x43\x6f\x64\x65\x72\x20\x20\x20\x20\x2d\x2d\x3e\x0a"
            ."\x3c\x21\x2d\x2d\x20\x20\x20\x6d\x61\x64\x63\x6f\x64\x65\x72\x40\x73\x74\x75\x64\x65\x6e\x74\x2e\x75\x74\x64\x61\x6c"
            ."\x6c\x61\x73\x2e\x65\x64\x75\x20\x20\x20\x2d\x2d\x3e\x0a\x3c\x21\x2d\x2d\x20\x68\x74\x74\x70\x3a\x2f\x2f\x77\x77\x77"
            ."\x2e\x75\x74\x64\x61\x6c\x6c\x61\x73\x2e\x65\x64\x75\x2f\x7e\x6d\x61\x64\x63\x6f\x64\x65\x72\x20\x2d\x2d\x3e\x0a\x0a";
        $this->_arr = array_pad($this->_arr, 21, 0);

        $this->_socket = fsockopen("udp://" . $this->_ip, $this->_port, $errno, $errstr, 3);

        if(is_resource($this->_socket)) {
            debug("Connecting to " . $this->_ip . ":" . $this->_port);
            stream_set_timeout($this->_socket, 5);
        } else {
            debug("Could not connect to " . $this->_socket . ":" . $this->_port);
        }
        if($tmp = $this->_sockState()) {
            // echo $tmp;
            if(!$this->_socket) {
                debug("Error #$errno: $errstr");
                // exit;
            }

            debug("[Initialized]");
            // $this->_brand_seed();
            return !(!$this->_socket);
        }
    }

    // Set error code
    function setError($code) {
        debug("[Setting error code (" . $code . ")]");
        $this->_errorcode = $code;
    }

    // Obtains a ping value to the server
    function _ping() {
        debug("[Getting Ping]");
        if($tmp = $this->_sockState()) {
            // echo $tmp;
            debug("[Error in socket]");
            $this->setError(ERROR_INSOCKET);
            return -1; // Error in socket
        } else {
            $tmp = "";
            $start = microtime_float()*1000;
            $this->_send("ÿÿÿÿping".chr(0));

            while(strlen($tmp) < 4 && (microtime_float()*1000 - $start) < 1000) {
                $tmp = $this->_getMore();
            }

            if(strlen($tmp) >= 4 && substr($tmp, 4, 1) == 'j') {
                $end = microtime_float()*1000;
                if($end < $start) {
                    echo $end . '\n' . $start;
                }
                return ($end - $start); // ($end - $start >= 0 ? ($end - $start) : -1; // Will be numeric ping
            } else {
                $this->setError(ERROR_NOSERVER);
                debug("[Error: No response from the server]");
                return -1; // Server unresponsive
            }
        }
        return 0;
    }

    // Populates details array
    function getDetails() {
        debug("[Getting details]");
        if($tmp = $this->_sockState()) {
            // echo $tmp;
            $this->setError(ERROR_INSOCKET);
            return -1;
        } else {
            $this->_send("ÿÿÿÿdetails".chr(0));
            $buffer = $this->_getMore();

            /*** Useful to show each buffer, but not needed otherwise
            if(DEBUG_ENABLED) {
                debug($buffer);
                for ($i=0; $i < strlen($buffer); $i++)  {
                    debug('['.ord(substr($buffer,$i)).'] ');
                }
            }
            */

            $tmp = substr($buffer, 0, 5);
            $buffer = substr($buffer, 5);
            $text = "";
            $count = 0;
            $arr = array();

            do {
                $tmp = substr($buffer, 0, 1);
                $buffer = substr($buffer, 1);
                if(!ord($tmp)) {
                    $this->_arr[$count++] = $text;
                    $text = "";
                } else {
                    $text .= $tmp;
                }
            } while($count < 5);

            for($i = 0; $i <= 6; $i++, $count++) {
                $tmp = substr($buffer, 0, 1);
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
                        $this->_arr[$count] .= $tmp; // [12] Mod website
                    }
                } while(ord($tmp) != 0);
                $count++;

                do {
                    $tmp = substr($buffer, 0, 1);
                    $buffer = substr($buffer, 1);

                    $this->_arr[$count] = "";
                    if(ord($tmp) != 0) {
                        $this->_arr[$count] .= $tmp; // [13] Mod FTP
                    }
                } while(ord($tmp != 0));
                $count++; // [14] = unused

                $this->_arr[$count++] = ord(substr($buffer, 0, 1));
                $buffer = substr($buffer, 1);
                $tmp = substr($buffer, 0, 4);
                $buffer = substr($buffer, 4);

                for($j = 0; $j < 4; $j++) {
                    $this->_arr[$count] += (pow(256, $j) * ord(substr($tmp, $j, 1))); // [15] Version
                } $count++;

                $tmp = substr($buffer, 0, 4);
                $buffer = substr($buffer, 4);

                for($j = 0; $j < 4; $j++) {
                    $this->_arr[$count] += (pow(256, $j) * ord(substr($tmp, $j, 1))); // [16] Size
                } $count++;

                // [17] Server only
                $this->_arr[$count++] = ord(substr($buffer, 0, 1));
                $buffer = substr($buffer, 1);

                // [18] Custom client.dll
                $this->_arr[$count++] = ord(substr($buffer, 0, 1));
                $buffer = substr($buffer, 1);

                // [19] Secure.
                $this->_arr[$count++] = ord(substr($buffer, 0, 1));
                $buffer = substr($buffer, 1);
            } else {
                for($i = 0; $i < 8; $i++) {
                    $this->_arr[$count++] = "\0";
                }
            }
        }
        $this->_arr[$count] = round($this->_ping(), 1);
        return 0;
    }

    // Sets players array
    function getPlayers() {
        debug("[Getting players]");
        // $fp = fsockopen("udp://" . $this->_ip, $this->_port);
        if($tmp = $this->_sockState()) {
            // echo $tmp;
            $this->setError(ERROR_INSOCKET);
            return -1;
        } else {
            $this->_send("ÿÿÿÿplayers".chr(0));
            $buffer = $this->_getMore();
            $buffer = substr($buffer, 5);
            $count = ord(substr($buffer, 0, 1)); // Number of active players
            debug("[A total of " . $count . " active players were discovered]");
            $buffer = substr($buffer, 1);
            $tfrags = "";
            $ttime = 0;
            $arr = array();

            for($i = 0; $i < $count; $i++) {
                $rfrags = 0.0;
                $rtime = 0;
                $stime = 0;
                $tind = ord(substr($buffer, 0, 1));
                $buffer = substr($buffer, 1);
                $tname = "";

                do {
                    $tmp = substr($buffer, 0, 1);
                    $buffer = substr($buffer, 1);
                    if(ord($tmp !=0)) {
                        $tname .= $tmp;
                    }
                } while(ord($tmp != 0));

                $tfrags = substr($buffer, 0, 4);
                $buffer = substr($buffer, 4);

                for($j = 0; $j < 4; $j++) {
                    $rfrags += (pow(256, $j) * ord(substr($tfrags, $j, 1)));
                }

                if($rfrags > 2147483648) {
                    $rfrags -= 4294967296;
                }

                $tmp = substr($buffer, 0, 4);
                $buffer = substr($buffer, 4);
                $rtime = getFloat32($tmp);
                $arr[$i] = array("Index" => $tind, "Name" => $tname, "Frags" => $rfrags, "Time" => $rtime);
            }
        }
        $this->_players = $arr;
        return 0;
    }

    function getRules() {
        debug("[Getting rules]");
        $multi = 0;
        $cvars = array(); // NOTE: Originally not used.
        if($tmp = $this->_sockState()) {
            $this->setError(ERROR_INSOCKET);
            return -1;
        }

        $this->_send("ÿÿÿÿrules".chr(0));
        $buffer = $this->_getMore();

        if(strlen($buffer) == 0) {
            $buffer = $this->_getMore();
        }

        $tmp = substr($buffer, 0, 5);
        $buffer = substr($buffer, 5);

        if(substr($tmp, 0, 4) == chr(254).chr(255).chr(255).chr(255)) {
            // Now, 5 more bytes to look at
            $multi = 1;
            for($i = 0; $i < 4; $i++) {
                $tmp = substr($buffer, 0, 1);
                $buffer = substr($buffer, 1);
            }
            $tmp = substr($buffer, 0, 5); // ÿÿÿÿE = Rules response
            $buffer = substr($buffer, 5);
        }
        $count = ord(substr($buffer, 0, 1));
        $buffer = substr($buffer, 2); // Number of rules
        $i = 0;
        $svar = "";

        while($i < $count) {
            if(strlen($buffer) == 0 && $multi == 1) {
                $buffer = $this->_getMore();
                $tmp = substr($buffer, 0, 5); // pÿÿÿ_
                $buffer = substr($buffer, 5);
                $buffer = substr($buffer, 4);
            }
            $tmp = substr($buffer, 0, 1);
            $buffer = substr($buffer, 1);
            if(ord($tmp) == 0) {
                $i += 0.5;
                $svar = $svar.$tmp;
            }
            $vars = explode(chr(0), $svar);

            for($i = 0; $i < (int) (count($vars)) -1; $i += 2) {
                $cvars[$vars[$i]] = $vars[$i + 1];
            }

            if(sizeof($cvars) > 0) {
                ksort($cvars);
            }

            $this->_rules = $cvars;
            return 0;
        }
    }

    function _sockState() {
        if(!$this->_socket) {
            return 8;
        }
        $status = socket_get_status($this->_socket);
        $ret = 0;

        switch($status) {
            case 'timed out':
                debug("Socket timed out.<br>\n");
                $ret |= 1;
                break;
            case 'eof':
                debug("Error: Socket was closed by the remote host.<br>\n");
                $ret |= 2;
                break;
            case 'blocked':
                break;
            default:
                break;
        }
        return $ret;
        /** DEPRECATED VERSION - TODO: test the switch method before destroying the below code
        if($status["timed out"]) {
            debug("Socket timed out.<br>\n");
            $ret |= 1;
        }
        if($status["eof"]) {
            debug("Error: Socket was closed by the remote host.<br>\n");
            $ret |= 2;
        }
        if($status["blocked"]) {
            // Enable these at your own risk! Doing so will cause a 'BLOCKED' response
            // debug("Error: Port blocked.<br>\n");
            // exit;
            // $ret |= 4;

        }
        return $ret;
        //return (!$status["timed_out"] && !$status["eof"] && !(!$this->_socket));  */
    }

    function _send($outstr)
    {
        if (!$this->_sockState()) {
            fwrite($this->_socket, $outstr, strlen($outstr));
        } else {
            return "\0";
        }
    }

    function _getMore() {
        if(!$this->_sockState()) {
            $tmp = fread($this->_socket, 1);
            $status = socket_get_status($this->_socket);

            if($status["unread_bytes"] == 0) {
                $status["unread_bytes"] = 1;
            }

            $tmp .= fread($this->_socket, $status["unread_bytes"]);
            return $tmp;
        } else {
            return "\0";
        }
    }

    function _brandSeed() {
        print($this->_seed);
        $this->_seed="\x0a\x3c\x21\x2d\x2d\x20\x20\x20\x20\x20\x20\x20\x53\x65\x72\x76\x65\x72\x20\x6d\x61\x64\x51\x75\x65\x72\x79\x20\x43"
            ."\x6c\x61\x73\x73\x20\x20\x20\x20\x20\x20\x20\x2d\x2d\x3e\x0a\x3c\x21\x2d\x2d\x20\x20\x20\x20\x43\x6f\x70\x79\x72\x69"
            ."\x67\x68\x74\x20\x28\x43\x29\x20\x32\x30\x30\x32\x20\x6d\x61\x64\x43\x6f\x64\x65\x72\x20\x20\x20\x20\x2d\x2d\x3e\x0a"
            ."\x3c\x21\x2d\x2d\x20\x20\x20\x6d\x61\x64\x63\x6f\x64\x65\x72\x40\x73\x74\x75\x64\x65\x6e\x74\x2e\x75\x74\x64\x61\x6c"
            ."\x6c\x61\x73\x2e\x65\x64\x75\x20\x20\x20\x2d\x2d\x3e\x0a\x3c\x21\x2d\x2d\x20\x68\x74\x74\x70\x3a\x2f\x2f\x77\x77\x77"
            ."\x2e\x75\x74\x64\x61\x6c\x6c\x61\x73\x2e\x65\x64\x75\x2f\x7e\x6d\x61\x64\x63\x6f\x64\x65\x72\x20\x2d\x2d\x3e\x0a\x0a";
    }

    // Return the current error code
    function getError() {
        return $this->_errorcode;
    }

    function isUp() {
        /************************************
         * int isup(char * ip, long port);
         * Return values:
         *   0 = No response - probably down
         *   1 = HL Responded - Server is up
         *  -1 = Error in socket
         ************************************/

        if($ret = $this->_sockState()) {
            // echo $ret;
            return -1;
        } else {
            if($ret & 2) {
                return 0;
            }
            $myPing = $this->_ping();
            if($myPing > 0) {
                return $myPing;
            } else {
                return 0;
            }
        }

    }

    function Address  (){ return $this->_arr[ 0]; }     // IP Address
    function Hostname (){ return $this->_arr[ 1]; }     // Hostname / Port
    function Map      (){ return $this->_arr[ 2]; }     // Current map
    function ModName  (){ return $this->_arr[ 3]; }     // Server mod
    function Desc     (){ return $this->_arr[ 4]; }     // Server description
    function Active   (){ return $this->_arr[ 5]; }     // Current players
    function Max      (){ return $this->_arr[ 6]; }     // Max players
    function Protocol (){ return $this->_arr[ 7]; }      // Protocol
    function SvrType  (){ return $this->_arr[ 8]; }      // Server Type
    function SvrOS    (){ return $this->_arr[ 9]; }      // Server OS
    function Pass     (){ return $this->_arr[10]; }      // Server Password
    function IsMod    (){ return $this->_arr[11]; }      // Server is running mod?
    function ModHTTP  (){ return $this->_arr[12]; }      // Mod website
    function ModFTP   (){ return $this->_arr[13]; }      // Mod FTP
    function NotUsed  (){ return $this->_arr[14]; }      // Not used
    function SvrVer   (){ return $this->_arr[15]; }      // Server version
    function SvrSize  (){ return $this->_arr[16]; }      // Server size
    function SvrOnly  (){ return $this->_arr[17]; }      // Server only
    function Custom   (){ return $this->_arr[18]; }      //
    function VACSec   (){ return $this->_arr[19]; }      // VAC Secure?
    function Ping     (){ return $this->_arr[20]; }      // Server ping
    function Players  (){ return $this->_players; }      // Player data
    function Rules    (){ return $this->_rules;   }      // Rules
}

/**
 * Start displaying the server information, create instances etc.
 */

$gameServer = new goldSrcQuery($serverIP, $serverPort);
$gameServer->getDetails();
$gameServer->getPlayers();

$svAddress    = $gameServer->Address();
$svHostName   = $gameServer->Hostname();
$svMap        = $gameServer->Map();
$svModName    = $gameServer->ModName();
$svDesc       = $gameServer->Desc();
$svPlayers    = $gameServer->Active();
$svMax        = $gameServer->Max();
$svProtocol   = $gameServer->Protocol();
$svSvrType    = $gameServer->SvrType();
$svSvrOS      = $gameServer->SvrOS();
$svPass       = $gameServer->Pass();
$svIsMod      = $gameServer->IsMod();
$svModHTTP    = $gameServer->ModHTTP();
$svModFTP     = $gameServer->ModFTP();
$svNotUsed    = $gameServer->NotUsed();
$svSvrVer     = $gameServer->SvrVer();
$svSvrSize    = $gameServer->SvrSize();
$svSvrOnly    = $gameServer->SvrOnly();
$svCustom     = $gameServer->Custom();
$svSecure     = $gameServer->VACSec();
$svPing       = $gameServer->Ping();
$svPlayerData = $gameServer->Players();
$svRules      = $gameServer->Rules();

if($svMax != 0) {
    $svStatus = "Online";
}
/**
 * NOTE: The commands below are for debugging purposes,
 * or for displaying further info on the page if needed.
 * Change DEBUG_ECHO to '1' in config.php to enable site-wide debugging.
 */

// No idea how this ended up working, so don't touch it.
// Doesn't really make a whole lot of sense.
// TODO: Probably review this.
for($i = 0; $i < 0; $i++) {
    $serverIP = $serverIP[$i++];
}

if(DEBUG_ECHO) {
    debug("Gathering server data...");

    $serverCon = $gameServer->isUp();

    if($serverCon == 0) {
        debug("No server response!");
    } else if($serverCon == -1) {
        debug("Error in socket.");
    } else if($serverCon == 1) {
        debug("Server is up.");
    } ?>
<html>
<head>
    <style>
        .datagrid table { border-collapse: collapse; text-align: left; width: 30%; border: 1px solid #006699;}
        .datagrid {font: normal 12px/150% Arial, Helvetica, sans-serif; background: #fff; overflow: hidden; }
        .datagrid table td, .datagrid table th { padding: 3px 10px; }
        .datagrid table thead th {background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #006699), color-stop(1, #00557F) );  background:-moz-linear-gradient( center top, #006699 5%, #00557F 100% );filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#006699', endColorstr='#00557F');background-color:#006699; color:#ffffff; font-size: 15px; font-weight: bold; border-left: 1px solid #0070A8; }
        .datagrid table thead th:first-child { border: none; }
        .datagrid table tbody td { color: #00496B; border-left: 1px solid #E1EEF4;font-size: 12px;font-weight: normal; }
        .datagrid table tbody .alt td { background: #E1EEF4; color: #00496B; }
        .datagrid table tbody td:first-child { border-left: none; }
        .datagrid table tbody tr:last-child td { border-bottom: none; }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   .datagrid table tfoot ul a:hover { text-decoration: none;border-color: #006699; color: #FFFFFF; background: none; background-color:#00557F;}div.dhtmlx_window_active, div.dhx_modal_cover_dv { position: fixed !important; }
    </style>
</head>
<body>
    <div class="datagrid">
    <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
            <tr>            <td>Server address:</td>            <td><?php echo $svAddress; ?></td></tr>
            <tr class="alt"><td>Server host name:</td>          <td><?php echo $svHostName; ?></td></tr>
            <tr>            <td>Server current map:</td>        <td><?php echo $svMap; ?></td></tr>
            <tr class="alt"><td>Server mod:</td>                <td><?php echo $svModName; ?></td></tr>
            <tr>            <td>Server description:</td>        <td><?php echo $svDesc; ?></td></tr>
            <tr class="alt"><td>Current players:</td>           <td><?php echo $svPlayers; ?></td></tr>
            <tr>            <td>Server max players:</td>        <td><?php echo $svMax; ?></td></tr>
            <tr class="alt"><td>Server protocol:</td>           <td><?php echo $svProtocol; ?></td></tr>
            <tr>            <td>Server type:</td>               <td><?php echo $svSvrType; ?></td></tr>
            <tr class="alt"><td>Server OS:</td>                 <td><?php echo $svSvrOS; ?></td></tr>
            <tr>            <td>Server requires a password:</td><td><?php echo $svPass; ?></td></tr>
            <tr class="alt"><td>Server running a mod:</td>      <td><?php echo $svIsMod; ?></td></tr>
            <tr>            <td>Server HTTP:</td>               <td><?php echo $svModHTTP; ?></td></tr>
            <tr class="alt"><td>Server FTP:</td>                <td><?php echo $svModFTP; ?></td></tr>
            <tr>            <td>Server version:</td>            <td><?php echo $svSvrVer; ?></td></tr>
            <tr class="alt"><td>Server size:</td>               <td><?php echo $svSvrSize; ?></td></tr>
            <tr>            <td>Server only:</td>               <td><?php echo $svSvrOnly; ?></td></tr>
            <tr class="alt"><td>Server VAC Secure:</td>         <td><?php echo $svSecure; ?></td></tr>
            <tr>            <td>Server ping (rounded):</td>     <td><?php echo round($svPing); ?>ms</td></tr>
            <tr class="alt"><td>Server player data:</td>        <td><?php echo print_r($svPlayerData, true); ?></td></tr>
            <tr>            <td>Server rules:</td>              <td><?php print_r($svRules, true) ?></td></tr>
            </tbody>
        </table>
    </div>
<?php
}
?>
</body>
</html>
<img src="../img/img.class.php?svName='<?php echo $svHostName;?>'&svAddress='<?php echo $serverIP ?>'&svPort='<?php echo $serverPort; ?>'&svStatus='<?php echo $svStatus; ?>'&svPlayers='<?php echo $svPlayers;?>'&svMax='<?php echo $svMax; ?>'&svRank='1st'&svMap='<?php echo $svMap;?>'&svShortName=hl" class="border" width="560" height="95" align="middle" />