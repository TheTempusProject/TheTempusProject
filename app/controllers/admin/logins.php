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

class Logins extends AdminController
{
    public function viewLogin($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        Views::view('admin.logLogin', self::$log->get($data));
        exit();
    }
    public function delete($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        if (Input::exists('L_')) {
            $data = Input::post('L_');
        }
        if (self::$log->delete((array) $data)) {
            Issues::add('success', 'login log deleted');
        } else {
            Issues::add('error', 'There was an error with your request.');
        }
        $this->index();
    }
    public function clear($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$log->clear('login');
        Issues::add('success', 'Login Logs Cleared');
        $this->index();
    }
    public function index($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        Views::view('admin.logLoginList', self::$log->loginList());
        exit();
    }
}
