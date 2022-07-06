<?php
/**
 * Controllers/register.php
 *
 * This is the register controller.
 *
 * @version 3.0
 * @author  Joey Kimsey <JoeyKimsey@thetempusproject.com>
 * @link    https://TheTempusProject.com
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */
namespace TheTempusProject\Controllers;

use TheTempusProject\Classes\Controller;
use TheTempusProject\Classes\Forms;
use TempusProjectCore\Functions\Debug;
use TempusProjectCore\Functions\Session;
use TempusProjectCore\Functions\Cookie;
use TempusProjectCore\Functions\Input;
use TempusProjectCore\Classes\Email;
use TempusProjectCore\Template\Issues;
use TempusProjectCore\Template\Components;
use TempusProjectCore\Functions\Check;
use TempusProjectCore\Template\Views;
use TempusProjectCore\Template;
use TempusProjectCore\Functions\Code;
use TempusProjectCore\Functions\Hash;
use TempusProjectCore\Functions\Redirect;
use TheTempusProject\TheTempusProject as App;

class Register extends Controller
{
    protected static $recaptcha;

    public function index() {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Register';
        Components::set('TERMS', Views::standardView('terms'));
        if (App::$isLoggedIn) {
            Issues::add('notice', 'You are currently logged in.');
            exit();
        }
        if (!Input::exists()) {
            Views::view('register');
            exit();
        }
        if (!Forms::check('register')) {
            Issues::add('error', 'There was an error with your registration.', Check::userErrors());
            Views::view('register');
            exit();
        }
        if (!self::$recaptcha->verify(Input::post('g-recaptcha-response'))) {
            Issues::add('error', 'There was an error with your login.', self::$recaptcha->getErrors());
            Views::view('login');
            exit();
        }
        $code = Code::genConfirmation();
        self::$user->create([
            'username' => Input::post('username'),
            'password' => Hash::make(Input::post('password')),
            'email' => Input::post('email'),
            'registered' => time(),
            'confirmationCode' => $code,
            'terms' => 1,
        ]);
        Email::send(Input::post('email'), 'confirmation', $code, ['template' => true]);
        Session::flash('success', 'Thank you for registering! Please check your email to confirm your account.');
        Redirect::to('home/index');
    }

    /**
     * @todo  Come back and separate this into multiple forms because this is gross.
     */
    public function recover()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Recover Account - {SITENAME}';
        Template::noIndex();
        if (!Input::exists()) {
            Views::view('forgot');
            exit();
        }
        if (Check::email(Input::post('entry')) && self::$user->findByEmail(Input::post('entry'))) {
            $userData = self::$user->data();
            Email::send($userData->email, 'forgotUsername', $userData->username, ['template' => true]);
            Issues::add('notice', 'Your Username has been sent to your registered email address.');
            Redirect::to('home/index');
        } elseif (self::$user->get(Input::post('entry'))) {
            self::$user->newCode(self::$user->data()->ID);
            self::$user->get(Input::post('entry'));
            $userData = self::$user->data();
            Email::send($userData->email, 'forgotPassword', $userData->confirmationCode, ['template' => true]);
            Issues::add('notice', 'Details for resetting your password have been sent to your registered email address');
            Redirect::to('home/index');
        }
        Issues::add('error', 'User not found.');
        Views::view('forgot');
        exit();
    }

    public function confirm($code = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Confirm Email';
        if (!isset($code) && !Input::exists('confirmationCode')) {
            Views::view('email.confirmation');
            exit();
        }
        if (Forms::check('emailConfirmation')) {
            $code = Input::post('confirmationCode');
        }
        if (!self::$user->confirm($code)) {
            Issues::add('error', 'There was an error confirming your account, please try again.');
            Views::view('email.confirmation');
            exit();
        }
        Session::flash('success', 'You have successfully confirmed your email address.');
        Redirect::to('home/index');
    }

    public function resend()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Resend Confirmation';
        if (!App::$isLoggedIn) {
            Issues::add('notice', 'Please log in to resend your confirmation email.');
            exit();
        }
        if (App::$activeUser->data()->confirmed == '1') {
            Issues::add('notice', 'Your account has already been confirmed.');
            exit();
        }
        if (!Forms::check('confirmationResend')) {
            Views::view('email.confirmationResend');
            exit();
        }
        Email::send(App::$activeUser->data()->email, 'confirmation', App::$activeUser->data()->confirmationCode, ['template' => true]);
        Session::flash('success', 'Your confirmation email has been sent to the email for your account.');
        Redirect::to('home/index');
    }

    public function reset($code = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Password Reset';
        if (!isset($code) && !Input::exists('resetCode')) {
            Issues::add('error', 'No reset code provided.');
            Views::view('password_reset_code');
            exit();
        }
        if (Input::exists('resetCode')) {
            if (Forms::check('password_reset_code')) {
                $code = Input::post('resetCode');
            }
        }
        if (!self::$user->checkCode($code)) {
            Issues::add('error', 'There was an error with your reset code. Please try again.');
            Views::view('password_reset_code');
            exit();
        }
        Components::set('resetCode', $code);
        if (!Input::exists()) {
            Views::view('password_reset');
            exit();
        }
        if (!Forms::check('passwordReset')) {
            Issues::add('error', 'There was an error with your request.', Check::userErrors());
            Views::view('password_reset');
            exit();
        }
        self::$user->changePassword($code, Input::post('password'));
        Email::send(self::$user->data()->email, 'passwordChange', null, ['template' => true]);
        Session::flash('success', 'Your Password has been changed, please use your new password to log in.');
        Redirect::to('home/login');
    }
}
