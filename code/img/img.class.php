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
header("Content-type: image/png");

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
    }

    function fontColor($color) {
        $this->fontStyle();
        $this->fontSize();

        // Set the color
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
        debug("Allocating color...");
        $this->allocateColor($this->bannerImage, $red, $green, $blue);

        // Set master font
        debug("Setting masterColor to: " . $color);
        $this->masterColor = $color;

        if(!isset($this->masterShadowColor)) {
            debug("Setting masterShadow color to black.");
            $this->masterShadowColor = imagecolorallocate($this->bannerImage, 0, 0, 0);
        }
    }

    function fontStyle() {
        $font = ROOT_DIR . "/display/trebuc.ttf";
        debug("Font: " . $font);
        $this->masterFont = $font;
    }

    function fontSize() {
        $size = 9;
        debug("Font size: " . $size);
        $this->masterFontSize = $size;
    }

    function allocateColor($bannerImage, $red, $green, $blue) {
        imagecolorallocate($bannerImage, $red, $green, $blue);
        debug("Allocated colors ($red, $green, $blue) to $bannerImage.");

        $this->drawText($this->bannerImage, $this->masterFont, $this->masterFontSize, $this->masterShadowColor, $this->masterColor);
        $this->playerChart();
        $this->drawMap();
    }

    /**
     * Draw the text and shadow around text.
     */
    function drawText($image, $font, $fontSize, $shadowColor, $textColor) {
        $svVars = $this->svVars;
        $color = $this->masterShadowColor;

        // TODO: change to class level cvar
        $statusColor = imagecolorallocate($this->bannerImage, 45, 151, 56);

        debug("Drawing shadows... ");
        /***
         * First, we draw the shadows for each array item
         */
        // Left
        imagettftext($image, $fontSize, 0, 115, 27, $shadowColor, $font, $svVars[0]);
        imagettftext($image, $fontSize, 0, 114, 57, $shadowColor, $font, $svVars[1]);
        imagettftext($image, $fontSize, 0, 221, 57, $shadowColor, $font, $svVars[2]);
        imagettftext($image, $fontSize, 0, 274, 87, $shadowColor, $font, $svVars[3]);
        imagettftext($image, $fontSize, 0, 147, 87, $shadowColor, $font, $svVars[4]);
        imagettftext($image, $fontSize, 0, 275, 57, $shadowColor, $font, $svVars[5]);
        imagettftext($image, $fontSize, 0, 212, 87, $shadowColor, $font, $svVars[6]);

        // Top
        imagettftext($image, $fontSize, 0, 117, 27, $shadowColor, $font, $svVars[0]);
        imagettftext($image, $fontSize, 0, 116, 57, $shadowColor, $font, $svVars[1]);
        imagettftext($image, $fontSize, 0, 222, 57, $shadowColor, $font, $svVars[2]);
        imagettftext($image, $fontSize, 0, 276, 87, $shadowColor, $font, $svVars[3]);
        imagettftext($image, $fontSize, 0, 149, 87, $shadowColor, $font, $svVars[4]);
        imagettftext($image, $fontSize, 0, 277, 57, $shadowColor, $font, $svVars[5]);
        imagettftext($image, $fontSize, 0, 223, 87, $shadowColor, $font, $svVars[6]);

        // Bottom
        imagettftext($image, $fontSize, 0, 115, 29, $shadowColor, $font, $svVars[0]);
        imagettftext($image, $fontSize, 0, 114, 59, $shadowColor, $font, $svVars[1]);
        imagettftext($image, $fontSize, 0, 220, 59, $shadowColor, $font, $svVars[2]);
        imagettftext($image, $fontSize, 0, 274, 89, $shadowColor, $font, $svVars[3]);
        imagettftext($image, $fontSize, 0, 147, 89, $shadowColor, $font, $svVars[4]);
        imagettftext($image, $fontSize, 0, 275, 59, $shadowColor, $font, $svVars[5]);
        imagettftext($image, $fontSize, 0, 212, 89, $shadowColor, $font, $svVars[6]);

        // Right
        imagettftext($image, $fontSize, 0, 117, 29, $shadowColor, $font, $svVars[0]);
        imagettftext($image, $fontSize, 0, 116, 59, $shadowColor, $font, $svVars[1]);
        imagettftext($image, $fontSize, 0, 222, 59, $shadowColor, $font, $svVars[2]);
        imagettftext($image, $fontSize, 0, 276, 89, $shadowColor, $font, $svVars[3]);
        imagettftext($image, $fontSize, 0, 149, 89, $shadowColor, $font, $svVars[4]);
        imagettftext($image, $fontSize, 0, 277, 59, $shadowColor, $font, $svVars[5]);
        imagettftext($image, $fontSize, 0, 223, 89, $shadowColor, $font, $svVars[6]);

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
        } else {
            // Use the "No image found" picture
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
$myBanner->fontColor("white");