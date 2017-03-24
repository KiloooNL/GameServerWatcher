<?PHP
/**
 * GAMESERVERWATCHER
 * 	coded by Ben Weidenhofer
 * Published under the open-source GNU GPLv3 licence.
 *
 * GitHub repo: https://github.com/KiloooNL/GameServerWatcher
 *
 * class.quake3.cfg.php
 *
 * This PHP file is a script for scraping Quake 3 server information in real time.
 * This is based on the GameQ Query Class by Tom Buskens
 * For more information, visit: GameQ - game server query class (http://gameq.sf.net)
 *
 * For a full list of supported servers, see this file
 */

$quake3_games = array(
  'aa'        =>  'America\'s Army: Operations/1717/spy',
  'avp2'      =>  'Alien vs Predator 2/27888/spy',
  'bf1942'    =>  'Battlefield 1942/23000/spy/spy1',
  'breed'     =>  'Breed/7649/breed',
  'cod'       =>  'Call of Duty/28960/q3/mohq3',
  'ccren'     =>  'Command & Conquer: Renegade/25300/spy',
  'cjack'     =>  'Contract J.A.C.K./27888/spy',
  'dai'       =>  'Daikatana/27992/q3/qw',
  'dx'        =>  'Deus Ex/7791/spy/spy1',
  'dev'       =>  'Devastation/7778/ut2003',
  'drakan'    =>  'Drakan: Order of the Flame/27046/spy',
  'fc'        =>  'FarCry/49001/fc',
  'fl'        =>  'Freelancer/2302/fl',                                         /* default port? - no playerlisting */
  'giants'    =>  'Giants: Citizen Kabuto/8911/spy',
  'gobs'      =>  'Global Operations/28672/spy',
  'gore'      =>  'Gore/27778/spy',
  'gr'        =>  'Ghost Recon/2348/gr',
  'halo'      =>  'Halo: Combat Evolved/2302/spy',
  'hl'        =>  'HalfLife/27015/hl',
  'hw2'       =>  'Homeworld 2/6500/hw2/spy1',                                  /* tested on Demo v0.99 may not work on retail */
  'hx2'       =>  'Hexen 2/26900/hx2',                                          /* no playerlisting */
  'igi2'      =>  'IGI 2/26001/spy',
  'jk2'       =>  'Jedi Knight 2: Jedi Outcast/28070/q3',
  'jkja'      =>  'Jedi Knight: Jedi Academy/29070/q3',
  'kp'        =>  'Kingpin: Life of Crime/31510/q3/qw',
  'mohaa'     =>  'Medal of Honor/12300/spy',                                   /* normal moh, using gamespy */
  'mohq3'     =>  'Medal of Honor/12203/q3/mohq3',                              /* moh using q3 protocol */
  'mta'       =>  'Multi Theft Auto: Vice City/2126/mta',                       /* query port = game port + 123 */
  'nf'        =>  'Nitro Family/25601/spy',
  'nwn'       =>  'Neverwinter Nights/5121/spy',
  'nolf'      =>  'No One Lives Forever/27888/spy',                             /* default port? */
  'nolf2'     =>  'No One Lives Forever 2/27890/spy',                           /* default port? */
  'of'        =>  'Operation Flashpoint/6073/spy',                              /* default port? */
  'p2'        =>  'Postal 2/7778/spy',
  'pk'        =>  'Painkiller/3455/pk',
  'qw'        =>  'QuakeWorld/27500/qw',
  'q2'        =>  'Quake 2/27910/q3/qw',
  'q3'        =>  'Quake 3 Arena/27960/q3',
  'rf'        =>  'Red Faction/7755/rf',
  'ron'       =>  'Rise of nations/6501/spy/spy1',
  'r6'        =>  'Rainbow Six/2346/spy/spy2',
  'rtcw'      =>  'Return to Castle Wolfenstein/27960/q3',
  'rs'        =>  'Rogue Spear/2346/spy/spy1',
  'rune'      =>  'Rune/7778/spy',
  'rvs'       =>  'RavenShield/8777/rvs',
  'sav'       =>  'Savage: The Battle For Newerth/11235/sav',
  'shogo'     =>  'Shogo: Armored Division/27888/spy',
  'sin'       =>  'SIN/22450/q3/qw',
  'sof'       =>  'Soldier of Fortune/28910/q3/qw',
  'sof2'      =>  'Soldier of Fortune 2: Double Helix/20100/q3',
  'spy'       =>  'Generic Gamespy Server/99999/spy',
  'ss'        =>  'Serious Sam/25601/spy',
  'ss2'       =>  'Serious Sam: The Second Encounter/25601/spy',
  'starsiege' =>  'Starsiege/29001/starsiege',
  'mstarsiege'=>  'Starsiege Master/29000/mstarsiege',                          /* Starsiege Master Server */
  'stvef'     =>  'Star Trek Voyager: Elite Force/27960/q3',
  'stvef2'    =>  'Star Trek Voyager: Elite Force 2/29253/q3',
  'tribes'    =>  'Starsiege: Tribes/28001/tribes',                             /* no playerlisting yet */
  'tops'      =>  'Tactical Operations/7778/spy',
  'tf'        =>  'Team Factor/57778/spy',
  'thps'      =>  'Tony Hawk\'s Pro Skater 3 or 4/6500/spy/spy1',
  'tribes2'   =>  'Tribes 2/28001/tribes2',
  'tron2'     =>  'Tron 2.0/27888/spy',
  'u'         =>  'Unreal/7778/spy/u',
  'u2xmp'     =>  'Unreal 2 XMP/7778/u2xmp',
  'ut'        =>  'Unreal Tournament/7778/spy',
  'ut2003'    =>  'Unreal Tournament 2003/7778/ut2003',
  'ut2004'    =>  'Unreal Tournament 2004/7778/ut2003',
  'v8'        =>  'V8 Supercar Challenge/16700/spy',                            /* default port? */
  'vc'        =>  'Vietcong/15425/spy'
);

$quake3_string = array(
  'breed'     =>  "\xfe\xfd\x00\x3b\x95\xab\x00\x05\x01\x05\x06\x08\x0a\x00\x00",
  'fc'        =>  "\x7f\xff\xff\xffrules/\x7f\xff\xff\xffstatus/\x7f\xff\xff\xffplayers",
  'fl'        =>  "\x00\x02\xf1\x26\x01\x26\xf0\x90\xa6\xf0\x26\x57\x4e\xac\xa0\xec\xf8\x68\xe4\x8d\x21",
  'gr'        =>  "\xc0\xde\xf1\x11\x42\x06\x00\xf5\x03\x00\x78\x30\x63",
  'hx2'       =>  "\x80\x00\x00\x0e\x02HEXENII\x00\x05",
  'hl'        =>  "����details\x00/����players\x00/����rules\x00",
  'mohq3'     =>  "����\x02getstatus\x00",
  'pk'        =>  "\xfe\xfd\x00BLEH\xff\x00\x00/\xfe\xfd\x00BLEH\x00\xff\xff",                              /* seems that you can send any 4 letter word */
  'qw'        =>  "����status\x00",
  'q3'        =>  "����getstatus\x00",
  'rf'        =>  "\x00\x00\x00\x00",
  'rvs'       =>  "REPORT",
  'sav'       =>  "\x9e\x4c\x23\x00\x00\xce\xa3\xf7\x6e\x40",
  'spy'       =>  "\\status\\\\players\\",
  'spy1'      =>  "\\status\\",
  'spy2'      =>  "\\basic\\\\info\\",
  'starsiege' =>  "\x72\x10\x00",
  'mstarsiege'=>  "\x10\x03\xff",
  'tribes'    =>  "b++",
  'tribes2'   =>  "\x0E\x02\x01\x02\x03\x04/\x12\x02\x01\x02\x03\x04",
  'u'         =>  "\\status\\/\\basic\\/\\info\\/\\players\\",
  'ut2003'    =>  "\x79\x00\x00\x00\x00/\x79\x00\x00\x00\x01/\x79\x00\x00\x00\x02",
  'u2xmp'     =>  "\x7E\x00\x00\x00\x00/\x7E\x00\x00\x00\x01/\x7E\x00\x00\x00\x02",
  'mta'       =>  "s"
);
?>