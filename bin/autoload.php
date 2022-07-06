<?php
/**
 * bin/autoload.php
 *
 * Handles the initial setup like autoloading, basic functions, constants, etc.
 *
 * @version 3.0
 * @author  Joey Kimsey <Joey@thetempusproject.com>
 * @link    https://TheTempusProject.com
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */
namespace TheTempusProject;

use TempusProjectCore\Classes\Autoloader;

// REMOVE
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
// REMOVE

session_start();
$autoloader_set = false;

// Basic Constants needed to load the other constants
if ( ! defined('APP_SPACE') ) {
    define('APP_SPACE', __NAMESPACE__);
}
if ( ! defined('APP_ROOT_DIRECTORY') ) {
    define('APP_ROOT_DIRECTORY', dirname(__DIR__) . DIRECTORY_SEPARATOR);
}
if ( ! defined('CONFIG_DIRECTORY') ) {
    define('CONFIG_DIRECTORY', APP_ROOT_DIRECTORY . 'app' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR);
}

// Tempus Project Constants
if (! defined('TEMPUS_PROJECT_CONSTANTS_LOADED')) {
    require_once CONFIG_DIRECTORY . 'constants.php';
}
// Core Constants
if (! defined('TEMPUS_CORE_CONSTANTS_LOADED')) {
    if (defined('TPC_ROOT_DIRECTORY')) {
        require_once TPC_ROOT_DIRECTORY.'config'.DIRECTORY_SEPARATOR. 'constants.php';
    }
}
// Debugger Constants
if (! defined('TEMPUS_DEBUGGER_CONSTANTS_LOADED')) {
    if (defined('TD_ROOT_DIRECTORY')) {
        require_once TD_ROOT_DIRECTORY . 'constants.php';
    }
}

// Requires our common functions
require_once FUNCTIONS_DIRECTORY . 'common.php';

// find the autoloader composer > custom
if ( file_exists( VENDOR_DIRECTORY . 'autoload.php' ) ) {
    require_once VENDOR_DIRECTORY . 'autoload.php';
    define('VENDOR_AUTOLOADED', true);
} else {
    define('VENDOR_AUTOLOADED', false);
    // Core Autoloader
    if (! defined('TEMPUS_CORE_AUTOLOADED')) {
        if (defined('TPC_ROOT_DIRECTORY')) {
            require_once TPC_ROOT_DIRECTORY.'bin'.DIRECTORY_SEPARATOR .'autoload.php';
        }
    }
    // Debugger Autoloader
    if (! defined('TEMPUS_DEBUGGER_AUTOLOADED')) {
        if (defined('TD_ROOT_DIRECTORY')) {
            require_once TD_ROOT_DIRECTORY . 'autoload.php';
        }
    }
    // App Autoloader
    if ( file_exists( APP_DIRECTORY . 'autoload.php' ) ) {
        require_once APP_DIRECTORY . 'autoload.php';
    }
}

// cant find autoloader
if ( !VENDOR_AUTOLOADED && !defined('TEMPUS_PROJECT_AUTOLOADED') ) {
    echo file_get_contents( ERRORS_DIRECTORY . 'autoload.php' );
    exit;
}
