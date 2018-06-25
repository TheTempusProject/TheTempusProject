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

use TempusProjectCore\Classes\Debug;
use TempusProjectCore\Classes\Issue;
use TempusProjectCore\Classes\Input;
use TheTempusProject\Controllers\AdminController;

require_once 'AdminController.php';

class BugReports extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        Debug::log('Controller Constructing: ' . get_class($this));
        self::$bugreport = $this->model('bugreport');
        self::$title = 'Admin - Bug Reports';
    }
    public function index($data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        $this->view('admin.bugreportList', self::$bugreport->listReports());
        exit();
    }
    public function viewReport($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        $reportData = self::$bugreport->get($data);
        if ($reportData !== false) {
            $this->view('admin.bugreport', $reportData);
            exit();
        }
        Issue::error('Report not found.');
        $this->index();
    }
    public function delete($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        if (Input::exists('submit')) {
            $data = Input::post('BR_');
        }
        if (self::$bugreport->delete((array) $data)) {
            Issue::success('Bug Report Deleted');
        } else {
            Issue::error('There was an error with your request.');
        }
        $this->index();
    }
    public function clear($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$bugreport->clear();
        $this->index();
    }
}
