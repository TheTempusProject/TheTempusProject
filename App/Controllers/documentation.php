<?php
/**
 * Controllers/documentation.php
 *
 * This is the documentation controller.
 *
 * @version 1.0
 *
 * @author  Joey Kimsey <JoeyKimsey@thetempusproject.com>
 *
 * @link    https://TheTempusProject.com
 *
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */

namespace TheTempusProject\Controllers;

use TempusProjectCore\Core\Controller as Controller;
use TempusProjectCore\Classes\Debug as Debug;

class Documentation extends Controller
{
    protected static $session;
    public function __construct()
    {
        Debug::log('Controller Constructing: ' . get_class($this));
        self::$session = $this->model('sessions');
    }
    public function __destruct()
    {
        Debug::log('Controller Destructing: ' . get_class($this));
        self::$session->updatePage(self::$title);
        $this->build();
        Debug::closeAllGroups();
    }
    public function index()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'The Tempus Project Documentation';
        self::$pageDescription = 'This is the home for all the documentation for The Tempus Project from installation to modification.';
        $this->view('documentation.documentation');
        exit();
    }
    public function installation()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Installation - Tempus Project Documentation';
        self::$pageDescription = 'This guide will walk you through installing The Tempus Project and getting it running.';
        $this->view('documentation.installation');
        exit();
    }
    public function gettingstarted()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Getting Started - Tempus Project Documentation';
        self::$pageDescription = 'This guide will show you how to set up your own private web server for developing with PHP and The Tempus Project.';
        $this->view('documentation.gettingstarted');
        exit();
    }
}
