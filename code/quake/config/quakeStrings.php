<?php
/**
 * GAMESERVERWATCHER
 * 	coded by Ben Weidenhofer
 * Published under the open-source GNU GPLv3 licence.
 *
 * GitHub repo: https://github.com/KiloooNL/GameServerWatcher
 *
 * quakeStrings.php
 *
 * This PHP file is a script for scraping Quake 3 server information in real time.
 * This is based on the GameQ Query Class by Tom Buskens
 * For more information, visit: GameQ - game server query class (http://gameq.sf.net)
 *
 * For a full list of supported servers, see this file
 */

/** TODO: Replace @deprecated HTML tags */

function quakeColorize($str) {
    $enableColor = 1;
    $i = 0;
    $ascii = 0;
    $strLength = strlen($str);

    if($strLength < 1) {
        return " ";
    }

    if($enableColor) {
        $processedStr = "<font color='#000000'>";
    }

    for($i = 0; $i < $strLength - 1; $i++) {
        if($str[$i] == "^" && $str[$i + 1] != "^") {
            $ascii = ord($str[$i + 1]);

            if($enableColor) {
                /************************************************************
                 * Thanks to K9-Moonshine for the OSP RGB color code filter
                 ************************************************************
                 * if F,f,B,b,N
                 */
                if ($ascii == 70 || $ascii == 102 || $ascii == 66 || $ascii == 98 || $ascii == 78)
                {
                    // ignore these codes
                    $i++;
                    continue;
                }

                $processedStr .= "</font>";

                /**
                 * if ^X
                 * Note: ideally, this would set the background color like it does in Q3.
                 */
                if(($ascii == 88 || $ascii == 120) && strlen($str) - $i > 6) {
                    $processedStr .= "<font color='#" . substr($str, $i + 2, 6) . "'>";
                    $i += 7;
                    continue;
                }

                switch($ascii % 8) {
                    case 0:
                        $processedStr .= "<font color='#555555'>";
                        break;
                    case 1:
                        $processedStr .= "<font color='#FF0000'>";
                        break;
                    case 2:
                        $processedStr .= "<font color='#00FF00'>";
                        break;
                    case 3:
                        $processedStr .= "<font color='#FFFF00'>";
                        break;
                    case 4:
                        $processedStr .= "<font color='#4444FF'>";
                        break;
                    case 5:
                        $processedStr .= "<font color='#00FFFF'>";
                        break;
                    case 6:
                        $processedStr .= "<font color='#FF00FF'>";
                        break;
                    case 7:
                        $processedStr .= "<font color='#FFFFFF'>";
                        break;
                }
            } $i++;
        } else {
            $processedStr .= $str[$i];
        }
    }

    if($enableColor) {
        $processedStr .= "</font>";
    }

    return $processedStr;
}

function quakeGameType($inString) {
    $outString = str_replace("0", "Free-For-All DM", $inString);
    $outString = str_replace("1", "Tournament 1-on-1", $outString);
    $outString = str_replace("2", "Single-Player", $outString);
    $outString = str_replace("3", "Team Deathmatch", $outString);
    $outString = str_replace("4", "Capture the Flag", $outString);

    return $outString;
}