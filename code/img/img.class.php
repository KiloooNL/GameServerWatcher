<?php
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
if(!DEBUG_ENABLED) {
    header("Content-type: image/png");
}

require_once("../../config/config.php");

class bannerImage {
    var $red = 0;
    var $green = 0;
    var $blue = 0;
    var $bannerImage;
    var $mapImage;
    var $mapSX;
    var $mapSY;

    var $masterFont;
    var $masterFontSize;
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
     * Font colors
     *
     *  0, 0, 60      = Dark blue
     *  0, 0, 0       = Black
     *  255, 255, 255 = White
     */
    function init($svHostname, $svPlayers, $svStatus) {
        $this->bannerImage = imagecreatefrompng(ROOT_DIR."/images/banner/css/css_banner.png");
        debug("Banner image is: " . $this->bannerImage);
        $svVars = array($this->svName, $this->svIP, $this->svPort, $this->svMap, $this->svPlayers, $this->svStatus, $this->svRank);

        // Set vars if server is offline
        if($svStatus != "Online") {
            $svPlayers = '';
            debug("Server is offline.");
        }

        // Process banner
        $this->processBanner();
    }

    function processBanner() {
        $this->bannerFont("white");
    }

    function bannerFont($color) {
        // Init vars
        $font = ROOT_DIR . "/display/trebuc.ttf";
        debug("Font: " . $font);
        $this->masterFont = $font;

        $size = 9;
        debug("Font size: " . $size);
        $this->masterFontSize = $size;

        // Set the color
        // TODO: Add more colors, maybe make it an array as well?
        switch($color) {
            case 'white':
                $red = $green = $blue = 255;
                break;
            case 'grey':
                break;
            case 'black':
                $red = $green = $blue = 0;
                break;
            case 'dark blue':
                $red = $green = 0;
                $blue = 60;
                break;
        }

        // Allocate the color
        if(isset($this->red) && isset($this->blue) && isset($this->green)) {
            debug("Allocating color...");
            $this->allocateColor($this->bannerImage, $red, $green, $blue);
        } else {
            debug("No color allocated! Using default color 255, 255, 255 (White)");
            $this->allocateColor($this->bannerImage, 255, 255, 255);
        }

        // Set master color
        debug("Setting masterColor to: " . $color);
        $this->masterColor = $color;


        if(!isset($this->masterShadowColor)) {
            debug("Setting masterShadow color to black.");
            $this->masterShadowColor = imagecolorallocate($this->bannerImage, 0, 0, 0);
        }

        // Now all the font settings are configured, draw text, chart and map image
        $this->drawText();
        $this->playerChart();
        $this->drawMap();
    }

    function allocateColor($bannerImage, $red, $green, $blue) {
        imagecolorallocate($bannerImage, $red, $green, $blue);
        debug("Allocated colors ($red, $green, $blue) to $bannerImage.");
    }

    /**
     * Draw the shadow around text.
     */
    function drawShadow($x, $y, $i) {
        $svVars = $this->svVars;
        $fontSize = $this->masterFontSize;
        $font = $this->masterFont;
        $shadowColor = $this->masterShadowColor;
        $image = $this->bannerImage;

        debug("Drawing shadows... ");
        imagettftext($image, $fontSize, 0, $x, $y, $shadowColor, $font, $svVars[$i]);
    }

    /**
     * Draw the text
     */
    function drawText() {
        $svVars = $this->svVars;
        $fontSize = $this->masterFontSize;
        $font = $this->masterFont;
        $image = $this->bannerImage;
        $color = $this->masterColor;

        // TODO: change to class level cvar
        $statusColor = imagecolorallocate($this->bannerImage, 45, 151, 56);

        /***
         * First, we draw the shadows for each array item
         */
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
        imagettftext($image, $fontSize, 0, 116, 28, $color, $font, $svVars[0]);
        imagettftext($image, $fontSize, 0, 115, 58, $color, $font, $svVars[1]);
        imagettftext($image, $fontSize, 0, 221, 58, $color, $font, $svVars[2]);
        imagettftext($image, $fontSize, 0, 275, 88, $color, $font, $svVars[3]);
        imagettftext($image, $fontSize, 0, 148, 88, $color, $font, $svVars[4]);
        imagettftext($image, $fontSize, 0, 276, 58, $statusColor, $font, $svVars[5]);
        imagettftext($image, $fontSize, 0, 222, 88, $color, $font, $svVars[6]);
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
            $playerChart = imagecreatefrompng(ROOT_DIR . "/images/player_chart/0.png");
        } else {
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
        if(file_exists(ROOT_DIR . "/images/mapimg/valve/" . $this->svVars[3] . ".png")) {
            $this->mapImage = imagecreatefrompng(ROOT_DIR . "/images/mapimg/valve/" . $this->svVars[3] . ".png");
        }

        // X,Y positioning
        $mapRight = 5;
        $mapBottom = -13;
        $this->mapSX = imagesx($this->mapImage);
        $this->mapSY = imagesx($this->mapImage);

        // Show PIP of map
        imagettftext($this->bannerImage, $this->masterFontSize, 0, 479, 91, $this->masterColor, $this->masterFont, "");

        // Draw it!
        debug("Drawing map using image (" . $this->mapImage . ")... ");
        imagecopy($this->bannerImage, $this->mapImage, imagesx($this->bannerImage) - $this->mapSX - $mapRight, imagesy($this->bannerImage) - $this->mapSY - $mapBottom, 0, 0, imagesx($this->mapImage), imagesy($this->mapImage));

        // Kaboom! Destroy it!
        $this->destroyBanner($this->bannerImage);
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
$myBanner->init("test", "1", "Online");