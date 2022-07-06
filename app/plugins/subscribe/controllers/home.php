<?php
/**
 * controllers/home.php
 *
 * This is the home controller for the XXXXX plugin.
 *
 * @package  Subscribe
 * @version  3.0
 * @author   Joey Kimsey <JoeyKimsey@thetempusproject.com>
 * @link     https://TheTempusProject.com
 * @license  https://opensource.org/licenses/MIT [MIT LICENSE]
 */
namespace TheTempusProject\Controllers;

use TempusProjectCore\Core\Controller;
use TempusProjectCore\Classes\Redirect;
use TempusProjectCore\Classes\Session;
use TempusProjectCore\Classes\Debug;
use TempusProjectCore\Classes\Input;
use TempusProjectCore\Classes\Email;
use TempusProjectCore\Classes\Check;
use TempusProjectCore\Template\Issues;
use TempusProjectCore\Template\Views;

class Home extends Controller
{
    protected static $session;
    protected static $subscribe;

    public function __construct()
    {
        Debug::log('Controller Constructing: ' . get_class($this));
        self::$session = $this->model('sessions');
        self::$subscribe = $this->model('subscribe');
    }

    public function __destruct()
    {
        Debug::log('Controller Destructing: ' . get_class($this));
        self::$session->updatePage(self::$title);
        $this->build();
        Debug::closeAllGroups();
    }
    
    public function subscribe()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Subscribe - {SITENAME}';
        self::$pageDescription = 'We are always publishing great content and keeping our members up to date. If you would like to join our list, you can subscribe here.';
        if (!Input::exists('email')) {
            Views::view('subscribe');
            exit();
        }
        if (!Forms::check('subscribe')) {
            Issues::add('error', 'There was an error with your form.', Check::userErrors());
            Views::view('subscribe');
            exit();
        }
        if (!self::$subscribe->add(Input::post('email'))) {
            Issues::add('error', 'There was an error with your request, please try again.');
            Views::view('subscribe');
            exit();
        }
        $data = self::$subscribe->get(Input::post('email'));
        Email::send(Input::post('email'), 'subscribe', $data->confirmationCode, ['template' => true]);
        Issues::add('success', 'You have successfully been subscribed to our mailing list.');
        exit();
    }

    public function unsubscribe($email = null, $code = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = '{SITENAME}';
        self::$pageDescription = '';
        if (!empty($email) && !empty($code)) {
            if (self::$subscribe->unsubscribe($email, $code)) {
                Issues::add('success', 'You have been successfully unsubscribed from receiving further mailings.');
                exit();
            }
            Issues::add('error', 'There was an error with your request.');
            Views::view('unsubscribe');
            exit();
        }
        if (!Input::exists('submit')) {
            Views::view('unsubscribe');
            exit();
        }
        if (!Forms::check('unsubscribe')) {
            Issues::add('error', 'There was an error with your request.', Check::userErrors());
            Views::view('unsubscribe');
            exit();
        }
        $data = self::$subscribe->get(Input::post('email'));
        if (empty($data)) {
            Issues::add('notice', 'There was an error with your request.');
            Views::view('unsubscribe');
            exit();
        }
        Email::send(Input::post('email'), 'unsubInstructions', $data->confirmationCode, ['template' => true]);
        Session::flash('success', 'An email with instructions on how to unsubscribe has been sent to your email.');
        Redirect::to('home/index');
    }
}
