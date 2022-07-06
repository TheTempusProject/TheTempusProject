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
use TempusProjectCore\Template\Issues;
use TempusProjectCore\Classes\Input;
use TempusProjectCore\Classes\Code;
use TempusProjectCore\Core\Template;
use TheTempusProject\Controllers\AdminController;

require_once 'AdminController.php';

class Logs extends AdminController
{

    public function index($data = null)
    {   
        Views::view('admin.logErrorList', self::$log->errorList());
        Views::view('admin.logAdminList', self::$log->adminList());
        Views::view('admin.logLoginList', self::$log->loginList());
        exit();
    }

    public function delete($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        if (self::$log->delete($data) === true) {
            Issues::add('success', 'Log Deleted');
        } else {
            Issues::add('error', 'There was an error with your request.');
        }
        $this->index();
    }

    public function viewLog($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        $logData = self::$log->getLog($data);
        if ($logData !== false) {
            Views::view('admin.log', self::$log->getLog($data));
            exit();
        }
        Issues::add('error', 'Log not found.');
        $this->index();
    }
}
