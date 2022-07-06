<?php

use TempusProjectCore\Functions\Routes;
function findTemplatePackageCore( $dir ) {
	$tttttt = str_replace( '\\', DIRECTORY_SEPARATOR, 'vendor\thetempusproject\tempusprojectcore' );
	$tttttt = str_replace( '/', DIRECTORY_SEPARATOR, $tttttt );
	$aotoloader_location = rtrim( $dir, DIRECTORY_SEPARATOR ) .
		DIRECTORY_SEPARATOR .
		$tttttt .
		DIRECTORY_SEPARATOR;
	return $aotoloader_location;
}

function findTemplateDebugger( $dir ) {
	$tttttt = str_replace( '\\', DIRECTORY_SEPARATOR, 'vendor\thetempusproject\tempusprojectcore' );
	$tttttt = str_replace( '/', DIRECTORY_SEPARATOR, $tttttt );
	$aotoloader_location = rtrim( $dir, DIRECTORY_SEPARATOR ) .
		DIRECTORY_SEPARATOR .
		$tttttt .
		DIRECTORY_SEPARATOR;
	return $aotoloader_location;
}

// $mods = apache_get_modules();
// if (!in_array('mod_rewrite', $mods)) {
//     echo file_get_contents('app/views/errors/rewrite.php');
//     exit();
// }





    // public function getComposerJson()
    // {
        // FIND ANOTHER WAY
    //     $docLocation = Routes::getLocation('composerJson');
    //     if ($docLocation->error) {
    //         Debug::error('No install json found.');
    //         return false;
    //     }
    //     return json_decode(file_get_contents($docLocation->fullPath), true);
    // }

    // public function getComposerLock()
    // {
        // FIND ANOTHER WAY
    //     $docLocation = Routes::getLocation('composerLock');
    //     if ($docLocation->error) {
    //         Debug::error('No install json found.');
    //         return false;
    //     }
    //     return json_decode(file_get_contents($docLocation->fullPath), true);
    // }

function getJson() {
    $location = CONFIG_DIRECTORY . 'install.json';
    if (file_exists($location)) {
        $content = file_get_contents($location);
        $json = json_decode($content, true);
    } else {
        $json = array();
    }
    return $json;
}

function testRouting() {
    // echo file_get_contents('http://192.168.0.200:8080/images/favicon.ico');
    return false;
}




