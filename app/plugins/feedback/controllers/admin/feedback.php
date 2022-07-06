<?php
/**
 * app/controllers/admin/feedback.php
 *
 * This is the feedback admin controller.
 *
 * @version 3.0
 * @author  Joey Kimsey <JoeyKimsey@thetempusproject.com>
 * @link    https://TheTempusProject.com
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */
namespace TheTempusProject\Controllers;

use TempusProjectCore\Core\Controller;
use TempusProjectCore\Classes\Debug;
use TempusProjectCore\Template\Issues;
use TempusProjectCore\Classes\Input;
use TempusProjectCore\Classes\Hash;
use TempusProjectCore\Classes\Code;
use TheTempusProject\Controllers\AdminController;
use TempusProjectCore\Template\Views;

require_once 'AdminController.php';

class Feedback extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        Debug::log('Controller Constructing: ' . get_class($this));
        self::$title = 'Admin - Feedback';
        self::$feedback = $this->model('feedback');
    }
    public function view($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        Views::view('admin.feedback', self::$feedback->get($data));
        exit();
    }
    public function delete($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        if (Input::exists('submit')) {
            $data = Input::post('F_');
        }
        if (self::$feedback->delete((array) $data)) {
            Issues::add('success', 'feedback deleted');
        } else {
            Issues::add('error', 'There was an error with your request.');
        }
        $this->index();
    }
    public function clear($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$feedback->clear();
        $this->index();
    }
    public function index($data = null)
    {
        Views::view('admin.feedbackList', self::$feedback->getList());
        exit();
    }
}
