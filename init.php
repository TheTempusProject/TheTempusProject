<?php
/**
 * 
 * Core/init.php.
 *
 * This class is the first file loaded. It includes our core App files, 
 * sets our autoloader, sets our exception handler, and checks for login 
 * data.
 *
 * @version 1.0
 *
 * @author  Joey Kimsey <joeyk4816@gmail.com>
 *
 * @link    https://github.com/JoeyK4816/TheTempusProject
 *
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html [GNU GENERAL PUBLIC LICENSE]
 *
 * @todo  rewrite this documentation
 */
namespace TheTempusProject;

session_start();

define('APP_SPACE', __NAMESPACE__);

require_once 'vendor/autoload.php';

set_exception_handler('TempusProjectCore\\Functions\\Handler::Exception_Handler');

use TempusProjectCore\Classes\Debug as Debug;
use TempusProjectCore\Classes\Redirect as Redirect;

/**
 * This will check for the htaccess file since it controls 
 * the display of the site. If not found it will redirect to 
 * our error page for a broken installation.
 */
$fullArray = explode('/', $_SERVER['PHP_SELF']);
array_pop($fullArray);
$docroot = implode('/', $fullArray) . '/';
$htaccess_path = $_SERVER['DOCUMENT_ROOT'].$docroot.'.htaccess';
$install_path = $_SERVER['DOCUMENT_ROOT'].$docroot.'install';
if (!file_exists($htaccess_path) && file_exists($install_path)) {
	Debug::error('HTACCESS file not found, redirecting to error page.');
    Redirect::to(533);
}