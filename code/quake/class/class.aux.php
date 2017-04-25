<?php
/**
* GAMESERVERWATCHER
* 	coded by Ben Weidenhofer
* Published under the open-source GNU GPLv3 licence.
*
* GitHub repo: https://github.com/KiloooNL/GameServerWatcher
*
* class.aux.php
*
* This PHP file is a script for scraping Quake 3 server information in real time.
* This is based on the GameQ Query Class by Tom Buskens
* For more information, visit: GameQ - game server query class (http://gameq.sf.net)
*
* For a full list of supported servers, see this file
*/

require_once("../../config/config.php");
require_once("class.quake.php");

class Aux {
    function spyString($string, $del = '\\', $start = 2) {
        // Cut string into pieces according to delimiter
        $pieces = explode($del, $string);
        $cnt = count($pieces, COUNT_RECURSIVE);

        for($i = $start; $i < $cnt; $i += 2) {
            $result[$pieces[$i - 1]] = $pieces[$i];
        }
        return $result;
    }

    function savageString($string) {
        // Cut string into pieces
        $pieces = explode('ÿ', $string);
        $cnt = count($pieces, COUNT_RECURSIVE);

        for($i = 1; $i < $cnt; $i++) {
            $smpieces = explode('ş', $pieces[$i]);
            $result[$smpieces[0]] = $smpieces[1];
        }
        return $result;
    }

    function HLString($string, &$i) {
        $begin = $i;
        $length = strlen($string);

        for($i; ($i < $length) && ($string{$i} != chr(0)); $i++);
        $result = substr($string, $begin, $i - $begin);
        $i++;
        return $result;
    }

    function tribesString($string, &$i, $count_index = true) {
        $length = ord($string{$i});

        if($count_index) {
            $result = substr($string, ++$i, $length - 1);
        } else {
            $result = substr($string, ++$i, $length);
        }

        $i += $length;
        return $result;
    }

    /**
     * Unreal 2 XMP strings sometimes have color coding.
     * See: http://unreal.student.utwente.nl/UT2003-queryspec.html for more details.
     */
    function unreal2String($string, &$i, $count_index = true) {
        if(substr($string, $i + 1, 4) == "\x5e\x00\x23\x00") {
            // Color coded string
            $length = ord($string{$i}) - 128;
            $length *= 2;
        } else {
            // Normal (tribes) string
            $length = ord($string{$i});
        }

        if($count_index) {
            $result = substr($string, ++$i, $length - 1);
        } else {
            $result = substr($string, ++$i, $length);
        }

        $i += $length;
        return $result;
    }

    function ghostReconString($string, &$i) {
        $substr = substr($string, $i, 4);

        if(strlen($substr) < 4) {
            return 0;
        }

        $length = current(unpack("V", $substr));
        $i += 4;
        $j = 0;

        while($j < $length && $string($i + $j) != chr(0)) {
            $j++; // Check for first "\x00" in the string
        }

        $result = substr($string, $i, min($j, $length - 1));
        $i += $length;
        return $result;
    }

    function unsignedLong($string, &$i) {
        $substr = substr($string, $i, 4);

        if(strlen($substr) < 4) {
            return 0;
        }

        $result = current(unpack("V", $substr));
        $i += 4;
        return $result;
    }

    function parseBitFlag($flag, $data) {
        $bit = 1;

        foreach($data as $elt) {
            if($flag & $bit) {
                $output[$elt] = 1;
            } else {
                $output[$elt] = 0;
            }
            $bit *= 2;
        }
        return $output;
    }

    /**
     * Sorts players by score, puts player data for ALL gametypes
     * into $data['players'][$i], clears any other player data.
     * This breaks compatibility with version < 0.2.5
     */
    function sortPlayers(&$data, $type = 'spy') {
        // Possible variables to sort players by
        $sortvars = array('score', 'frags', 'kills', 'honor');
        $cnt = count($sortvars);

        switch($type) {
            // Gamespy style players
            case 'spy':
                /**
                 * Put all data with key <name>_<postfix> into an array
                 * $player[<postfix>][<name>]
                 */
                foreach($data as $key => $val) {
                    // Fix for BF1942
                    if(preg_match("/^(.+)_(\d\d?)$/", $key, $match) && $match[1] != 'teamname') {
                        $players_u[$match[2]][$match[1]] = $data[$key];
                        unset($data[$key]);
                    }
                }

                // Check if a sortvar can be found
                for($i = 0; $i != $cnt; $i++) {
                    if(isset($players_u[0][$sortvars[$i]])) {
                        $sortvar = $sortvars[$i];
                        break;
                    }
                }

                // If no sortvar is found, return players unsorted
                if(!isset($sortvar)) {
                    if(isset($players_u)) {
                        $data['players'] = $players_u;
                        return true;
                    }
                }

                // Re-index players so they can be sorted more easily
                foreach($players_u as $key => $val) {
                    $players[] = $players_u[$key];
                }
                break;

            // Quake style players
            case 'quake':
                // Check if a sortvar can be found
                for($i = 0; $i != $cnt; $i++) {
                    if(isset($data['players'][0][$sortvars[$i]])) {
                        $sortvar = $sortvars[$i];
                        break;
                    }
                }

                // If no sortvar is found, return players unsorted
                if(!isset($sortvar)) {
                    return true;
                }

                $players = $data['players'];
                break;
        }

        // Sort
        $cnt = count($players);

        for($i = 0; $i != $cnt; $i++) {
            $a = $i;
            $b = $cnt - 1;

            while($a != $b) {
                if($players[$a][$sortvar] > $players[$b][$sortvar]) {
                    $b--;
                } else {
                    $a++;
                }
            }

            $h = $players[$i];
            $players[$i] = $players[$a];
            $players[$a] = $h;
        }

        // Put playerdata back into the array
        $data['players'] = $players;
    }

}