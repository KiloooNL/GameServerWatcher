<?php
header("Content-type: image/png");
/**
 * GAMESERVERWATCHER
 * 	coded by Ben Weidenhofer
 * Published under the open-source GNU GPLv3 licence.
 *
 * GitHub repo: https://github.com/KiloooNL/GameServerWatcher
 *
 * img.class.php
 *
 * This PHP class file is the classes for banner generation
 */
//if(!DEBUG_ENABLED) {
//}
require_once("../../config/config.php");
require_once("img.config.php");

class bannerImage {
    var $red = 0;
    var $green = 0;
    var $blue = 0;
    var $bannerImage;
    var $mapImage;
    var $mapSX;
    var $mapSY;

    var $masterFont = FONT_STYLE;
    var $masterFontSize = FONT_SIZE;
    var $masterColor;
    var $masterShadowColor;

    var $svVars;
    var $svName;
    var $svIP;
    var $svPort;
    var $svMap;
    var $svPlayers;
    var $svStatus;
    var $svRank;

    /**
     * createBanner
     *  - Set variables, get all parsed $_GET strings, then process the banner
     */
    function createBanner() {
        if(file_exists(ROOT_DIR . "/images/banner/css/css_banner.png")) {
            debug("Using banner image: " . ROOT_DIR . "/images/banner/css/css_banner.png");
            $this->bannerImage = imagecreatefrompng(ROOT_DIR."/images/banner/css/css_banner.png");
        } else {
            debug("No banner image found!");
        }

        if(isset($_GET['svName'])) {
            $this->svName = $_GET['svName'];
        }
        if(isset($_GET['svAddress'])) {
            $this->svIP = $_GET['svAddress'];
        }
        if(isset($_GET['svPort'])) {
            $this->svPort = $_GET['svPort'];
        }
        if(isset($_GET['svMap'])) {
            $this->svMap = $_GET['svMap'];
        }
        if(isset($_GET['svPlayers'])) {
            $this->svPlayers = $_GET['svPlayers'];
        }
        if(isset($_GET['svStatus'])) {
            $this->svStatus = $_GET['svStatus'];
        }
        if(isset($_GET['svRank'])) {
            $this->svRank = $_GET['svRank'];
        }

        $this->svVars = array($this->svName, $this->svIP, $this->svPort, $this->svMap, $this->svPlayers, $this->svStatus, $this->svRank);

        // Replace '' in each array item, and show each svVar if debugging enabled
        for($i = 0; $i < count($this->svVars); $i++) {
            if(DEBUG_ENABLED && isset($this->svVars[$i])) {
                debug($this->svVars[$i]);
            }

            $this->svVars[$i] = str_replace("'", "", $this->svVars[$i]);
        }

        // Set vars if server is offline
        if($this->svVars[5] != "Online") {
            $this->svVars[4] = 0; // Set players to 0
            $this->svVars[5] = "Offline";
            debug("Server is offline. Set svPlayers to 0");
        }

        // Process banner
        $this->processBanner();
    }

    function processBanner() {
        $this->bannerFont('white');

        // Now all the font settings are configured, draw text, chart and map image
        $this->drawText();
        $this->playerChart();
        $this->drawMap();

        // Kaboom! Destroy it!
        $this->destroyBanner($this->bannerImage);
    }

    function bannerFont($color) {
        // Init vars
        debug("Font: " . $this->masterFont);
        debug("Font size: " . $this->masterFontSize);

        // Set master color
        debug("Setting masterColor to " . $color);
        $this->masterColor = $this->allocateColor($color);

        debug("Setting masterShadowColor to black.");
        $this->masterShadowColor = $this->allocateColor('black');
    }

    function allocateColor($color) {
        $this->red = $this->green = $this->blue = 0;

        switch(strtolower($color)) {
            case 'white':
                $this->red = $this->green = $this->blue = 255;
                break;
            case 'red':
                $this->red = 255;
                break;
            case 'green':
                $this->green = 255;
                break;
            case 'blue':
                $this->blue = 255;
                break;
            case 'black':
                $this->red = $this->green = $this->blue = 0;
                break;
            case 'grey':
                $this-> red = $this->green = $this->blue = 128;
        }

        // Allocate the color
        if(isset($this->red) && isset($this->blue) && isset($this->green)) {
            debug("Allocating colors...");
            $allocateColor = imagecolorallocate($this->bannerImage, $this->red, $this->green, $this->blue);
        } else {
            debug("No color allocated! Using default color 255, 255, 255 (White)");
            $allocateColor = imagecolorallocate($this->bannerImage, 255, 255, 255);
        }

        debug("Allocated colors ($this->red, $this->green, $this->blue) to $this->bannerImage.");
        return $allocateColor;
    }

    /**
     * Draw the shadow around text.
     */
    function drawShadow($x, $y, $i) {
        imagettftext($this->bannerImage, $this->masterFontSize, 0, $x, $y, $this->masterShadowColor, $this->masterFont, $this->svVars[$i]);
    }

    /**
     * Draw the text
     */
    function drawText() {
        /***
         * First, we draw the shadows for each array item
         */
        debug("Drawing shadows... ");
        // Left
        $this->drawShadow(115, 27, 0);
        $this->drawShadow(114, 57, 1);
        $this->drawShadow(221, 57, 2);
        $this->drawShadow(274, 87, 3);
        $this->drawShadow(147, 87, 4);
        $this->drawShadow(275, 57, 5);
        $this->drawShadow(212, 87, 6);

        // Top
        $this->drawShadow(117, 27, 0);
        $this->drawShadow(116, 57, 1);
        $this->drawShadow(222, 57, 2);
        $this->drawShadow(276, 87, 3);
        $this->drawShadow(149, 87, 4);
        $this->drawShadow(277, 57, 5);
        $this->drawShadow(223, 87, 6);

        // Bottom
        $this->drawShadow(115, 29, 0);
        $this->drawShadow(114, 59, 1);
        $this->drawShadow(220, 59, 2);
        $this->drawShadow(274, 89, 3);
        $this->drawShadow(147, 89, 4);
        $this->drawShadow(275, 59, 5);
        $this->drawShadow(212, 89, 6);

        // Right
        $this->drawShadow(117, 29, 0);
        $this->drawShadow(116, 59, 1);
        $this->drawShadow(222, 59, 2);
        $this->drawShadow(276, 89, 3);
        $this->drawShadow(149, 89, 4);
        $this->drawShadow(277, 59, 5);
        $this->drawShadow(223, 89, 6);

        /***
         * Then, we draw the actual text for each array item
         *  - usage is:
         *    imagettftext(image, font size, angle, x, y, font color, font file, text);
         */
        debug("Drawing text... ");
        $color = $this->masterColor;
        $statusColor = $this->allocateColor(SERVER_ONLINE_COLOR);
        imagettftext($this->bannerImage, $this->masterFontSize, 0, 116, 28, $color, $this->masterFont, $this->svVars[0]);
        imagettftext($this->bannerImage, $this->masterFontSize, 0, 115, 58, $color, $this->masterFont, $this->svVars[1]);
        imagettftext($this->bannerImage, $this->masterFontSize, 0, 221, 58, $color, $this->masterFont, $this->svVars[2]);
        imagettftext($this->bannerImage, $this->masterFontSize, 0, 275, 88, $color, $this->masterFont, $this->svVars[3]);
        imagettftext($this->bannerImage, $this->masterFontSize, 0, 148, 88, $color, $this->masterFont, $this->svVars[4]);
        imagettftext($this->bannerImage, $this->masterFontSize, 0, 276, 58, $statusColor, $this->masterFont, $this->svVars[5]);
        imagettftext($this->bannerImage, $this->masterFontSize, 0, 222, 88, $color, $this->masterFont, $this->svVars[6]);
    }

    /**
     * playerChart() is essentially a PIP (Picture in picture)
     *  and it shows a pie chart of the current players over max players
     *
     * EG: 4/12 current players.
     */
    function playerChart() {
        // Get the number of players, and use the pie image for X amount of current players
        if(!isset($this->svVars[4])) {
            debug("Found no active players, using 0.png for playerChart.");
            $playerChart = imagecreatefrompng(ROOT_DIR . "/images/player_chart/0.png");
        } else {
            debug("Found active players, using " . $this->svVars[4] . ".png for playerChart");
            $playerChart = imagecreatefrompng(ROOT_DIR . "/images/player_chart/" . $this->svVars[4] . ".png");
        }

        // Sets the margins for the player chart and gets the height & width of the chart image.
        $margRight = 422;
        $margBottom = 6;
        $sx = imagesx($playerChart); // X pos
        $sy = imagesy($playerChart); // Y pos

        // Copy the player chart onto the banner using the margin offsets and the banner width to calculate positioning of the player chart
        debug("Drawing player chart... ");
        imagecopy($this->bannerImage, $playerChart, imagesx($this->bannerImage) - $sx - $margRight, imagesy($this->bannerImage) - $sy - $margBottom, 0, 0, imagesx($playerChart), imagesy($playerChart));
    }

    function drawMap() {
        $this->mapImage = imagecreatefrompng(ROOT_DIR . "/images/mapimg/NoImage.png");

        // Check if current server map exists in image folder
        // TODO: CHANGE THIS SO EACH FOLDER IS INDEPENDENT TO THE SERVER GAME!
        debug("Searching for " . $this->svVars[3] . " map image...");
        if(file_exists(ROOT_DIR . "/images/mapimg/cstrikesource/" . $this->svVars[3] . ".png")) {
            debug("Map image found, using " . ROOT_DIR . "/images/mapimg/cstrikesource/" . $this->svVars[3] . ".png");
            $this->mapImage = imagecreatefrompng(ROOT_DIR . "/images/mapimg/cstrikesource/" . $this->svVars[3] . ".png");
        } else {
            debug("No map image found for '" . ROOT_DIR . "/images/mapimg/cstrikesource/" . $this->svVars[3] . ".png'. \nRun img_grabber in " . ROOT_DIR . "/code/config/ to try and grab missing maps");
            debug("Because no map image was found, we will use NoImage.png instead");
        }

        // X, Y positioning
        $mapRight = 5;
        $mapBottom = -13;
        $this->mapSX = imagesx($this->mapImage);
        $this->mapSY = imagesx($this->mapImage);

        // Show PIP of map
        imagettftext($this->bannerImage, $this->masterFontSize, 0, 479, 91, $this->allocateColor($this->masterColor), $this->masterFont, "");

        // Draw it!
        debug("Drawing map using image (" . $this->mapImage . ")... ");
        imagecopy($this->bannerImage, $this->mapImage, imagesx($this->bannerImage) - $this->mapSX - $mapRight, imagesy($this->bannerImage) - $this->mapSY - $mapBottom, 0, 0, imagesx($this->mapImage), imagesy($this->mapImage));
    }

    function destroyBanner($image) {
        // Save PNG image and free memory
        debug("Drawing PNG image...");
        imagepng($image);
        debug("Destroying PNG image...");
        imagedestroy($image);
    }
}
$myBanner = new bannerImage();
$myBanner->createBanner();