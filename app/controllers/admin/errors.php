<?php
/**
 * app/controllers/admin/errors.php
 *
 * This is the error logs controller.
 *
 * @version 3.0
 * @author  Joey Kimsey <JoeyKimsey@thetempusproject.com>
 * @link    https://TheTempusProject.com
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */
namespace TheTempusProject\Controllers;

use TempusProjectCore\Classes\Debug;
use TempusProjectCore\Template\Issues;
use TempusProjectCore\Classes\Input;

class Errors extends AdminController
{
    public function viewError($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        Views::view('admin.logError', self::$log->getError($data));
        exit();
    }
    public function delete($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        if (Input::exists('submit')) {
            $data[] = Input::post('E_');
        }
        if (self::$log->delete((array) $data)) {
            Issues::add('success', 'error log deleted');
        } else {
            Issues::add('error', 'There was an error with your request.');
        }
        $this->index();
    }
    public function clear($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$log->clear('error');
        $this->index();
    }
    public function index($data = null)
    {
        Views::view('admin.logErrorList', self::$log->errorList());
        exit();
    }
}
