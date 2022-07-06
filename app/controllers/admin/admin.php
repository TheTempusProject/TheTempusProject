<?php
/**
 * Controllers/Admin/Admin.php
 *
 * This is the Admin Log controller.
 *
 * @version 1.0
 * @author  Joey Kimsey <JoeyKimsey@thetempusproject.com>
 * @link    https://TheTempusProject.com
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */

namespace TheTempusProject\Controllers;

use TempusProjectCore\Classes\Debug;
use TempusProjectCore\Core\Template;

class Admin extends AdminController
{
    public function index($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        Views::view('admin.logAdminList', self::$log->adminList());
        exit();
    }
    public function viewLog($data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        Views::view('admin.logAdmin', self::$log->get($data));
        exit();
    }
}
