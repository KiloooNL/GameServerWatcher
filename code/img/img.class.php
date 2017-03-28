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


class imgBanner {
    var $red = 0;
    var $green = 0;
    var $blue = 0;
    var $bannerImage = "banner.jpg";

    /**
     * Font colors
     *
     *  0, 0, 60      = Dark blue
     *  0, 0, 0       = Black
     *  255, 255, 255 = White
     */
    function fontColor($color) {
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
        $this->allocateColor($this->bannerImage, $red, $green, $blue);
    }

    function fontStyle($font) {
        return $font;
    }

    function fontSize($size) {
        return $size;
    }

    function allocateColor($bannerImage, $red, $green, $blue) {
        // $rgbColor = array($red, $green, $blue);
        // $rgbColor = implode(", ", $rgbColor);

        imagecolorallocate($bannerImage, $red, $green, $blue);
    }
}

$myBanner = new imgBanner();
$myBanner->fontColor("white");