<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Callback for learn360 repository.
 *
 * 
 * @package   repository_learn360
 * @author    Pramod Ubbala (AGS -> Infobase) 
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2019 Infobase
 * @copyright based on work by Pramod Ubbala
 */
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->dirroot . '/repository/lib.php');

$json = required_param('tlelinks', PARAM_RAW);
require_login();
parse_str($json, $info);
$url = $json;
$fileShortPlusSource = $url;
$filename =  $info['TitleName'];
if (isset($info['TitleName'])) {
$filename  = s(clean_param($info['TitleName'], PARAM_FILE)) . '.mp4';
}

$thumbnail = $info['ThumbnailUrl'];
if (isset($info['ThumbnailUrl'])) {
    $thumbnail = s(clean_param($info['ThumbnailUrl'], PARAM_URL));
}

$source = base64_encode(json_encode(array('url'=>$url,'filename'=>$filename)));
$sourcekey = sha1($source . repository::get_secret_key() . sesskey());;

$js =<<<EOD
<html>
<head>
   <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <script type="text/javascript">
    window.onload = function() {
        var resource = {};
        resource.title = "$filename";      
        resource.source = "$source";
        resource.thumbnail = '$thumbnail';
        resource.author = "";
        resource.license = "";
		resource.sourcekey = "$sourcekey"
        parent.M.core_filepicker.select_file(resource);
    }
    </script>
</head>
<body><noscript></noscript></body>
</html>
EOD;

header('Content-Type: text/html; charset=utf-8');
die($js);
