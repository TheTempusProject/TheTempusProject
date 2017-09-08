<?php
/**
 * Controllers/Home.php.
 *
 * This is the Home controller.
 *
 * @version 1.0
 *
 * @author  Joey Kimsey <joeyk4816@gmail.com>
 *
 * @link    https://github.com/JoeyK4816/TheTempusProject
 *
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html [GNU GENERAL PUBLIC LICENSE]
 */

namespace TheTempusProject\Controllers;

use TempusProjectCore\Core\Controller as Controller;
use TempusProjectCore\Classes\Debug as Debug;
use TempusProjectCore\Classes\Config as Config;
use TempusProjectCore\Classes\DB as DB;
use TempusProjectCore\Classes\Session as Session;
use TempusProjectCore\Classes\Cookie as Cookie;
use TempusProjectCore\Classes\Log as Log;
use TempusProjectCore\Classes\Input as Input;
use TempusProjectCore\Classes\Email as Email;
use TempusProjectCore\Classes\Check as Check;
use TempusProjectCore\Classes\Issue as Issue;
use TempusProjectCore\Classes\Redirect as Redirect;

class home extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Debug::group("Controller: " . get_class($this), 1);
    }
    public function __destruct()
    {
        Debug::log('Controller Destructing: '.get_class($this));
        Self::$_session->update_page(Self::$_title);
        Debug::gend();
        $this->build();
    }
    public function index()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        Self::$_page_description = 'This is the homepage of your new Tempus Project Installation. Thank you for installing. find more info at http://www.thetempusproject.com';
        Self::$_title = '{SITENAME}';
        $this->view('index');
        exit();
    }
    public function subscribe()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        Self::$_title = 'Subscribe - {SITENAME}';
        Self::$_page_description = 'We are always publishing great content and keeping our members up to date. If you would like to join our list, you can subscribe here.';
        if (!Input::exists('email')) {
            $this->view('subscribe');
            exit();
        }
        if (!Check::form('subscribe')) {
            Issue::notice('There was an error with your request.');
            $this->view('subscribe');
            exit();
        }
        if (!Self::$_subscribe->add(Input::post('email'))) {
            Issue::error('There was an error with your request, please try again.');
            $this->view('subscribe');
            exit();
        }
        Issue::success('You have successfully been subscribed to our mailing list.');
        $data = Self::$_subscribe->get(Input::post('email'));
        Email::send(Input::post('email'), 'subscribe', $data->confirmation_code, array('template' => true));
        exit();
    }
    public function unsubscribe($email = null, $code = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        Self::$_title = '{SITENAME}';
        Self::$_page_description = '';
        if (!empty($email) && !empty($code)) {
            if (Self::$_subscribe->unsubscribe($email, $code)) {
                Issue::success('You have been successfully unsubscribed from receiving further mailings.');
                Email::send($email, 'unsubscribe', null, array('template' => true));
                exit();
            }
        }
        if (Input::exists('submit')) {
            if (Check::form('unsubscribe')) {
                $data = Self::$_subscribe->get(Input::post('email'));
                if (empty($data)) {
                    Issue::notice('There was an error with your request.');
                    exit();
                }
                Email::send(Input::post('email'), 'unsubInstructions', $data->confirmation_code, array('template' => true));
                Issue::success('An email with instructions on how to unsubscribe has been sent to your email.');
            }
        }
        $this->view('unsubscribe');
        exit();
    }
    public function forgot()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        Self::$_title = 'Recover Account - {SITENAME}';
        Self::$_template->noIndex();
        if (!Input::exists()) {
            $this->view('forgot');
            exit();
        }
        $type = (Check::email(Input::post('entry'))) ? 'email' : 'username';
        switch($type) {
            case 'username':
                if (Self::$_user->get(Input::post('entry'))) {
                    Self::$_user->new_code(Self::$_user->data()->ID);
                    Self::$_user->get(Input::post('entry'));
                    $data = Self::$_user->data();
                    Email::send($data->email, 'forgot_password', $data->confirmation_code, array('template' => true));
                    Issue::notice('Details for resetting your password have been sent to your registered email address');
                } else {
                    Issue::error('User not found.');
                }
            break;
            case 'email':
                if (Self::$_user->find_by_email(Input::post('entry'))) {
                    $data = Self::$_user->data();
                    Email::send($data->email, 'forgot_username', $data->username, array('template' => true));
                    Issue::notice('Your Username has been sent to your registered email address.');
                } else {
                    Issue::error('User not found.');
                }
            break;
        }
        exit();
    }
    public function feedback()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        Self::$_title = 'Feedback - {SITENAME}';
        Self::$_page_description = 'At {SITENAME}, we value our users\' input. You can provide any feedback or suggestions using this form.'; 
        if (!Input::exists()) {
            $this->view('feedback');
            exit();
        }
        if (Check::form('feedback')) {
            Self::$_log->feedback(Input::post('name'), Input::post('email'), Input::post('entry'));
            Issue::success('Your feedback has been received.');
            exit();
        }
        Issue::notice('There was an error with your form, please check your submission and try again.');
        $this->view('feedback');
        exit();
    }
    public function bugreport()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        Self::$_title = 'Bug Report - {SITENAME}';
        Self::$_page_description = 'On this page you can submit a bug report for the site.';
        if (!Self::$_is_logged_in) {
            Issue::notice('You must be logged in to report bugs.');
            exit();
        }
        if (!Input::exists()) {
            $this->view('bug.report');
            exit();
        }
        if (Check::form('bug_report')) {
            Self::$_log->bug_report(Self::$_active_user->ID, Input::post('url'), Input::post('ourl'), Input::post('repeat'), Input::post('description_'));
            Issue::success('Your Bug Report has been received. We may contact you for more information at the email address you provided..');
            exit();
        }
        Issue::notice('There was an error with your form, please check your submission and try again.');
        $this->view('bug.report');  
        exit();
    }
    public function profile($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        Self::$_title = 'User Profile - {SITENAME}';
        Self::$_page_description = 'User Profiles for {SITENAME}';
        if (!Self::$_is_logged_in) {
            Issue::notice('You must be logged in to view this page.');
            exit();
        }
        $user = Self::$_user->get($data);
        if ($user) {
            Self::$_title = $user->username . '\'s Profile - {SITENAME}';
            Self::$_page_description = 'User Profile for '.$user->username.' - {SITENAME}';
            $this->view('user', $user);
            exit();
        }
        Issue::notice("No user found.");
        exit();
            
    }
    public function login()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        Self::$_title = 'Portal - {SITENAME}';
        Self::$_page_description = 'Please log in to use {SITENAME} member features.';
        if (Self::$_is_logged_in) {
            Issue::notice('You Are currently logged in. Please <a href="'.Config::get('main/base').'home/logout">click here</a> to log out.');
            exit();
        }
        if (!Input::exists()) {
            $this->view('login');
            exit();
        }
        if (Self::$_user->log_in(Input::post('username'), Input::post('password'), Input::post('remember'))) {
            Session::flash('success', 'You have been logged in.');
            Redirect::to('home');
            exit();
        }
        $this->view('login');
        exit();
    }
    public function logout()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        Self::$_title = 'Log Out - {SITENAME}';
        Self::$_template->noIndex();
        if (Self::$_is_logged_in) {
            Self::$_user->log_out();
            Issue::notice('You have been logged out.');
            exit();
        }
        Issue::notice('You are not logged in.');
        exit();
    }
    public function terms()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        Self::$_title = 'Terms and Conditions - {SITENAME}';
        Self::$_page_description = '{SITENAME} Terms and Conditions of use. Please use {SITENAME} safely.';
        $this->view('terms.page');
        exit();
    }
}
