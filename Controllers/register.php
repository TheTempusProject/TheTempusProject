<?php
/**
 * Controllers/Register.php.
 *
 * This is the Register controller.
 *
 * @version 0.9
 *
 * @author  Joey Kimsey <joeyk4816@gmail.com>
 *
 * @link    https://github.com/JoeyK4816/TheTempusProject
 *
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */

namespace TheTempusProject\Controllers;

use TempusProjectCore\Core\Controller as Controller;
use TempusProjectCore\Classes\Debug as Debug;
use TempusProjectCore\Classes\Config as Config;
use TempusProjectCore\Classes\DB as DB;
use TempusProjectCore\Classes\Session as Session;
use TempusProjectCore\Classes\Cookie as Cookie;
use TempusProjectCore\Classes\Input as Input;
use TempusProjectCore\Classes\Email as Email;
use TempusProjectCore\Classes\Issue as Issue;
use TempusProjectCore\Classes\Check as Check;
use TempusProjectCore\Classes\Code as Code;
use TempusProjectCore\Classes\Hash as Hash;
use TempusProjectCore\Classes\Token as Token;
use TempusProjectCore\Classes\Redirect as Redirect;

class Register extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Debug::group("Controller: " . get_class($this), 1);
        Self::$_template->noIndex();
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
        Self::$_title = 'Register';
        Self::$_template->set('TERMS', Self::$_template->standard_view('terms'));
        if (Self::$_is_logged_in) {
            Issue::error('You are already logged in!');
            exit();
        }
        if (!Input::exists()) {
            $this->view('register');
            exit();
        }
        if (Check::form('register')) {
            $Ccode = Code::new_confirmation();
            Self::$_user->create(array(
                    'username' => Input::post('username'),
                    'password' => Hash::make(Input::post('password')),
                    'email' => Input::post('email'),
                    'registered' => time(),
                    'confirmation_code' => $Ccode,
                    'terms' => 1,
                ));
            Self::$_user = $this->model('user', Input::post('user'));
            Email::send(Input::post('email'), 'confirmation', $Ccode, array('template' => true));
            Session::flash('success', 'Thank you for registering! Please check your email to confirm your account.');
            Redirect::to('home');
            exit();
        }
        Issue::error(Check::errors());
        $this->view('register');
    }
    public function confirm($code = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        Self::$_title = 'Confirm Email';
        if (!isset($code)) {
            Issue::notice('No confirmation code provided.');
            exit();
        }
        if (!Self::$_user->confirm($code)) {
            Issue::error('There was an error confirming your account, please try again.');
            exit();
        }
        Issue::success('Thanks for confirming!');
        exit();
    }
    public function resend()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        Self::$_title = 'Resend Confirmation';
        if (!Self::$_is_logged_in) {
            Issue::notice('Please log in to resend your confirmation email.');
            exit();
        }
        if (Self::$_user->data()->confirmed == '0') {
            Email::send(Self::$_user->data()->email, 'confirmation', Self::$_user->data()->confirmation_code, array('template' => true));
            Issue::notice('Your confirmation email has been resent to the email from our records.');
            exit();
        } else {
            Issue::error('Your account has already been confirmed.');
            exit();
        }
    }
    public function reset($code = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        Self::$_title = 'Password Reset';
        if (!isset($code)) {
            Issue::error('No reset code provided.');
            exit();
        }
        if (!Input::exists()) {
            if (Self::$_user->check_code($code)) {
                $this->view('password.reset');
            } else {
                Issue::error('There was an error with your request.');
            }
            exit();
        }
        if (!Token::check(Input::post('token'))) {
            Issue::error('There was an error with your request.');
            exit();
        }
        if (Check::password(Input::post('password'), Input::post('password2'))) {
            Self::$_user->change_password($code, Input::post('password'));
            Email::send(Self::$_user->data()->email, 'password_change', null, array('template' => true));
            Issue::success('Your Password has been changed, please use your new password to <a href="{BASE}home/login">log in</a>.');
            exit();
        } else {
            Issue::error('Invalid Password.');
            $this->view('password.reset');
            exit();
        }    
    }
}
