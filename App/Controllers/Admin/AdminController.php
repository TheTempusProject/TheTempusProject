<?php
/**
 * Controllers/Admin/AdminController.php
 *
 * This is the main admin controller.
 *
 * @version 3.0
 *
 * @author  Joey Kimsey <JoeyKimsey@thetempusproject.com>
 *
 * @link    https://TheTempusProject.com
 *
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */

namespace TheTempusProject\Controllers;

use TempusProjectCore\Core\Controller;
use TempusProjectCore\Core\Installer;
use TempusProjectCore\Core\Template;
use TempusProjectCore\Classes\Config;
use TempusProjectCore\Classes\Debug;
use TempusProjectCore\Classes\Input;
use TempusProjectCore\Classes\Email;
use TempusProjectCore\Classes\Issue;
use TempusProjectCore\Classes\Check;
use TempusProjectCore\Classes\Image;
use TempusProjectCore\Classes\Log;

class AdminController extends Controller
{
    protected static $blog;
    protected static $bugreport;
    protected static $comment;
    protected static $feedback;
    protected static $group;
    protected static $log;
    protected static $message;
    protected static $session;
    protected static $subscribe;
    protected static $tracking;
    protected static $user;

    public function __construct()
    {
        Debug::log('Controller Constructing: ' . get_class($this));
        self::$template->noFollow();
        self::$template->noIndex();
        self::$session = $this->model('sessions');
        if (!self::$isLoggedIn) {
            Issue::notice('You must be logged in to view this page.');
            exit();
        }
        if (!self::$isAdmin) {
            Issue::error('You do not have permission to view this page.');
            exit();
        }
        self::$template->setTemplate('admin');
    }

    public function __destruct()
    {
        Debug::log('Controller Destructing: ' . get_class($this));
        self::$session->updatePage(self::$title);
        $this->build();
        Debug::closeAllGroups();
    }
}