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
use TheTempusProject\Controllers\AdminController;

require_once 'AdminController.php';

class BugReports extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        Debug::log('Controller Constructing: ' . get_class($this));
        self::$blog = $this->model('blog');
        self::$bugreport = $this->model('bugreport');
        self::$comment = $this->model('comment');
        self::$feedback = $this->model('feedback');
        self::$group = $this->model('group');
        self::$log = $this->model('log');
        self::$message = $this->model('message');
        self::$subscribe = $this->model('subscribe');
        self::$tracking = $this->model('track');
        self::$user = $this->model('user');
    }
    public function index($data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        self::$title = 'Admin - Bug Reports';
        $this->view('admin.bugreportList', self::$bugreport->listReports());
        exit();
    }
    public function viewReport($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Admin - Bug Reports';
        $reportData = self::$bugreport->get($data);
        if ($reportData !== false) {
            $this->view('admin.bugreport', $reportData);
            exit();
        }
        Issue::error('Report not found.');
        $this->view('admin.bugreportList', self::$bugreport->listReports());
        exit();
    }
    public function delete($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Admin - Bug Reports';
        if (Input::exists('submit')) {
            $data = Input::post('BR_');
        }
        if (self::$bugreport->delete((array) $data)) {
            Issue::success('Bug Report Deleted');
        } else {
            Issue::error('There was an error with your request.');
        }
        $this->view('admin.bugreportList', self::$bugreport->listReports());
        exit();
    }
    public function clear($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Admin - Bug Reports';
        self::$bugreport->clear();
        $this->view('admin.bugreportList', self::$bugreport->listReports());
        exit();
    }
}
