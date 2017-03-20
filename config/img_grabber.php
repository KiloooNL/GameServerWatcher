<?php
/**
 * GAMESERVERWATCHER
 * 	coded by Ben Weidenhofer
 * Published under the open-source GNU GPLv3 licence.
 *
 * GitHub repo: https://github.com/KiloooNL/GameServerWatcher
 *
 * img_grabber.php
 *
 * This PHP file is a script that scrapes gametracker.com and grabs
 * JPG images of map overviews in 160x120 resolution to display on the server's banner
 *
 *
*/

// Alter php.ini settings as timeouts are possible, so we want to extend timeout limit and remove errors.
// It is handy to enable startup errors for debugging.
ini_set('display_startup_errors', 1);
ini_set('max_execution_time', 300);


/**
 * Classes:
 * 	getMapImages: Scrapes for the map image based on the classes function called.
 *
 * getMapImages Functions:
 * 	css:           scrape Counter-Strike :Source images.
 *  quake3:        scrap Quake 3 images.
 *  hl1:           scrape Half-Life images.
 *  saveImage:     saves the image.
 *
 * Arrays:
 *  So far the arrays are as follows:
 * 	$cssMaps    = Counter-Strike: Source
 *  $quakeMaps  = Quake 3
 *
 * Each array contains a list of maps, with continually growing data.
 *
 * TODO:
 * 1 - In the future it would be nice to scrape this array from a text file for easier editing,
 * Or pull a list of published maps from a website that may have this info already.
 *
 * 2 - At the moment when we call a function from getMapImages, it will save the scraped image to the root folder of img_grabber.php
 * This should be changed so it saves to the games /image/mapimg/$game/ folder instead. There should later be a variable defined in config/config.php
 * to set the default dir for images as well.
 *
 * 2 - UPDATE: there is now a saveImage() function that saves the image to a folder based on the $imgDirectory string, per each function.
 *             still needs implementing into config.php
 */

class getMapImages {
	// Base URL. The game's name in short form goes at the end and will be declared in each function.
	public $baseURL = "http://image.www.gametracker.com/images/maps/160x120/";

	// Counter-Strike Source
	public $cssDir = "css/";

	// Quake 3
	public $quakeDir = "q3/";

	// Half-Life
	public $hlDir = "hl1";

	public function hl1() {
	}

	public function css() {
        $imgDirectory = "mapimg/css";

		$cssMaps = array("aim_47th_v2.jpg",
		"css_mc_origin.jpg",
		"dr3w_gg_paintabll_forrest.jpg",
		"geek_pacman.jpg",
		"gg_ag_tex2_red.jpg",
		"gg_aim_47th_v2.jpg",
		"gg_aim_ag_texture_jungle.jpg",
		"gg_aim_pistol_3floor_v3.jpg",
		"gg_ar_glass_fortress2a.jpg",
		"gg_autumn.jpg",
		"gg_cirular.jpg",
		"gg_csbase2.jpg",
		"gg_deagle5.jpg",
		"gg_deagle_phobia.jpg",
		"gg_desertstorm_v2.jpg",
		"gg_dglknife_v4.jpg",
		"gg_eddland.jpg",
		"gg_factory.jpg",
		"gg_fight_yard.jpg",
		"gg_floatworld.jpg",
		"gg_fortrush_bm.jpg",
		"gg_fy_area_762a.jpg",
		"gg_fy_battlefield.jpg",
		"gg_fy_brickworld_final.jpg",
		"gg_fy_funtimes.jpg",
		"gg_fy_snow.jpg",
		"gg_fy_stoneworld_obe.jpg",
		"gg_gardenworld.jpg",
		"gg_iceland_ip_1.jpg",
		"gg_knas_bunker_css.jpg",
		"gg_lego_arena.jpg",
		"gg_neonarena_ii.jpg",
		"gg_platforms_v2_b1.jpg",
		"gg_playground_b.jpg",
		"gg_schranz_texture.jpg",
		"gg_scoutzknivez_hc.jpg",
		"gg_simpsons_h1.jpg",
		"gg_small.jpg",
		"gg_strategicthrust.jpg",
		"gg_supadeth_b3.jpg",
		"gg_texture_nitro.jpg",
		"gg_trs_aim_churches.jpg",
		"gg_ultradeth_b1.jpg",
		"gg_wmd_conduit.jpg",
		"gg_wmd_missile.jpg",
		"gg_wmd_temporal.jpg",
		"gg_x34.jpg",
		"he_canyon-fight_v2.jpg",
		"skullz_sandstorm.jpg");

        foreach($cssMaps as $mapImg) {
            $url = $this->baseURL . $this->quakeDir . $mapImg;
            $data = file_get_contents($url);
            $handle = fopen(basename($url), 'w');
            $this->saveImage($handle, $data, $imgDirectory); // Save image
        }
	}

	public function quake3() {
        $imgDirectory = "mapimg/quake3";

		$quakeMaps = array("q3ctf1.jpg",
			"q3ctf2.jpg",
			"q3ctf3.jpg",
			"q3ctf4.jpg",
			"q3dm0.jpg",
			"q3dm1.jpg",
			"q3dm10.jpg",
			"q3dm11.jpg",
			"q3dm12.jpg",
			"q3dm13.jpg",
			"q3dm14.jpg",
			"q3dm15.jpg",
			"q3dm16.jpg",
			"q3dm17.jpg",
			"q3dm18.jpg",
			"q3dm19.jpg",
			"q3dm2.jpg",
			"q3dm3.jpg",
			"q3dm4.jpg",
			"q3dm5.jpg",
			"q3dm6.jpg",
			"q3dm7.jpg",
			"q3dm8.jpg",
			"q3dm9.jpg",
			"q3tourney1.jpg",
			"q3tourney2.jpg",
			"q3tourney3.jpg",
			"q3tourney4.jpg",
			"q3tourney5.jpg",
			"q3tourney6.jpg");

        foreach($quakeMaps as $mapImg) {
            $url = $this->baseURL . $this->quakeDir . $mapImg;
            $data = file_get_contents($url);
            $handle = fopen(basename($url), 'w');
            $this->saveImage($handle, $data, $imgDirectory); // Save image
        }
	}

    public function saveImage($filename, $filecontent, $folderPath) {
        // Check if folder exists, if not create it.
        if(strlen($filename > 0)) {
            if (!file_exists($folderPath)) {
                mkdir($folderPath);
            }

            $file = @fopen($folderPath . DIRECTORY_SEPARATOR . $filename, "W");
            if ($file != false) {
                fwrite($file, $filecontent);
                fclose($file);

                return 1; // Image saved
            }
            // An error occurred trying to save the image.
            return "<tr><td><br>An error occurred trying to save the file. Check folder \"$folderPath\" for write permissions.<br></td></tr>";
        }
        // Bad file name
        return "<tr><td><br>An error occurred with the filename: $filename. The image was not saved.<br></td></tr>";
    }
}

// Creating an instance for debugging.
$grabImages = new getMapImages();
$grabImages->css();
$grabImages->hl1();
$grabImages->quake3();

?>
