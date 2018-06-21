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

class Admin extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        Debug::log('Controller Constructing: ' . get_class($this));
        self::$title = 'Admin - Admin Logs';
        self::$log = $this->model('log');
        Template::addFilter('logMenu', "#<ul id=\"log-menu\" class=\"collapse\">#is", "<ul id=\"log-menu\" class=\"\">", true);
    }
    public function index($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        $this->view('admin.logAdminList', self::$log->adminList());
        exit();
    }
    public function viewLog($data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        $this->view('admin.logAdmin', self::$log->get($data));
        exit();
    }
}
