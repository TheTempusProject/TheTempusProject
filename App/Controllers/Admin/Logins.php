<?php
/**
 * Controllers/Admin/.php
 *
 * This is the xxxxxx controller.
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

use TempusProjectCore\Core\Controller;
use TempusProjectCore\Classes\Debug;
use TempusProjectCore\Classes\Issue;
use TempusProjectCore\Classes\Input;
use TempusProjectCore\Classes\Hash;
use TempusProjectCore\Classes\Code;
use TempusProjectCore\Core\Template;
use TheTempusProject\Controllers\AdminController;

require_once 'AdminController.php';

class Logins extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        Debug::log('Controller Constructing: ' . get_class($this));
        self::$title = 'Admin - Login Logs';
        self::$log = $this->model('log');
        Template::addFilter('logMenu', "#<ul id=\"log-menu\" class=\"collapse\">#is", "<ul id=\"log-menu\" class=\"\">", true);
    }
    public function viewLogin($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        $this->view('admin.logLogin', self::$log->get($data));
        exit();
    }
    public function delete($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        if (Input::exists('L_')) {
            $data = Input::post('L_');
        }
        if (self::$log->delete((array) $data)) {
            Issue::success('login log deleted');
        } else {
            Issue::error('There was an error with your request.');
        }
        $this->index();
    }
    public function clear($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$log->clear('login');
        Issue::success('Login Logs Cleared');
        $this->index();
    }
    public function index($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        $this->view('admin.logLoginList', self::$log->loginList());
        exit();
    }
}
