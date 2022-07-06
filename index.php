<?php
/**
 * index.php
 *
 * Using the .htaccess rerouting: all traffic should be directed to/through index.php.
 * In this file we initiate all models we will need, authenticate sessions, set
 * template objects, and call appload to initialize the appropriate controller/method.
 *
 * @version 3.0
 * @author  Joey Kimsey <Joey@thetempusproject.com>
 * @link    https://TheTempusProject.com
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */
namespace TheTempusProject;

require_once 'bin/autoload.php';
require_once 'bin/tempus_project.php';

use TheTempusProject\TheTempusProject;
use TempusProjectCore\Functions\ {
    Debug, Input, Redirect
};

/**
 * Instantiate a new instance of our application.
 *
 * You can add to the conditional for any pages that you want to have
 * access to outside of the typical .htaccess redirect method.
 */
$app = new TheTempusProject();

// tracking should somehow be included in the plugin for it

if (Input::exists('tracking')) {
    switch (Input::get('tracking')) {
        case 'pixel':
            // need to add something that will actually record the data here
            $app->setUrl('tracking/pixel');
            Redirect::to('images/pixel.png');
            break;
        default:
            $app->setUrl('tracking/index');
            break;
    }
} elseif (Input::exists('error')) {
    switch (Input::get('error')) {
        case 'image404':
            Debug::error('Image not found');
            Redirect::to('images/imageNotFound.png');
            break;
        case 'upload404':
            Debug::error('Upload Error');
            $app->setUrl('error/upload404');
            break;
        case '404':
            // unreachable, its going to url=404.html need better detection
            Debug::error('MISSING Error');
            $app->setUrl('error/404');
            break;
        default:
            $app->setUrl('error/' . Input::get('error'));
            break;
    }
} elseif (stripos($_SERVER['REQUEST_URI'], 'install.php')) {
    $app->setUrl('install/index');
}
// testRouting();
$app->load();
// $app->printDebug();
Debug::gend();

// if app isn't installed and install.php is there, point to install.php
// if app isn't installed and install.php is gone, there is a fatal error

// public static $location = null;
// public static $base = null;

// will not work unless inside a controller
// self::$session->updatePage(self::$title);

// this should be moved
// if (!self::$isLoggedIn) {
//     Issues::add('notice','You must be logged in to view this page.');
//     exit();
// }
// if (!self::$isAdmin) {
//     Issues::add('error','You do not have permission to view this page.');
//     exit();
// }
// self::$log->admin("Deleted " . self::$tableName . ": $instance");
// self::$log->admin("Cleared " . self::$tableName);