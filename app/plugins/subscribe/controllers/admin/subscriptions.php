<?php
/**
 * Controllers/Admin/Subscriptions.php
 *
 * This is the Subscriptions controller.
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
use TheTempusProject\Controllers\AdminController;
use TempusProjectCore\Template\Views;

class Subscriptions extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        Debug::log('Controller Constructing: ' . get_class($this));
        self::$title = 'Admin - Email Subscribers';
        self::$subscribe = $this->model('subscribe');
    }

    public function delete($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        if (Input::exists('submit')) {
            $data = Input::post('S_');
        }
        if (self::$subscribe->remove((array) $data)) {
            Issues::add('success', 'Subscriber removed.');
            $this->index();
        }
        Issues::add('error', 'There was an error with your request, please try again.');
        $this->index();
    }

    public function add($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        if (!Forms::check('subscribe')) {
            Issues::add('error', 'There was an error with your request.', Check::userErrors());
            $this->index();
        }
        if (!self::$subscribe->add(Input::post('email'))) {
            Issues::add('error', 'There was an error with your request, please try again.');
            $this->index();
        }
        Issues::add('success', 'Subscriber added.');
        $this->index();
    }

    public function index($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        Views::view('admin.subscribersList', self::$subscribe->listSubscribers());
        exit();
    }
}
