<?php
/**
 * app/controllers/home.php
 *
 * This is the home or 'index' controller.
 *
 * @version 3.0
 * @author  Joey Kimsey <JoeyKimsey@thetempusproject.com>
 * @link    https://TheTempusProject.com
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */
namespace TheTempusProject\Controllers;

use TheTempusProject\Classes\Controller;
use TempusProjectCore\Functions\Routes;
use TempusProjectCore\Functions\Redirect;
use TempusProjectCore\Functions\Session;
use TempusProjectCore\Functions\Debug;
use TempusProjectCore\Functions\Input;
use TempusProjectCore\Functions\Check;
use TempusProjectCore\Template\Issues;
use TempusProjectCore\Template\Components;
use TempusProjectCore\Template\Views;
use TempusProjectCore\Template;
use TheTempusProject\TheTempusProject as App;

class Home extends Controller
{
    public function index()
    {
        self::$title = '{SITENAME}';
        self::$pageDescription = 'This is the homepage of your new Tempus Project Installation. Thank you for installing. find more info at http://www.thetempusproject.com';
        // Components::set('POSTS', Views::standardView('blog.recentWidget', self::$blog->recent(3)));
        Views::view('index');
    }

    public function profile($data = null)
    {
        self::$title = 'User Profile - {SITENAME}';
        self::$pageDescription = 'User Profiles for {SITENAME}';
        if (!App::$isLoggedIn) {
            Issues::add('notice', 'You must be logged in to view this page.');
            return;
        }
        $user = self::$user->get($data);
        if (!$user) {
            Issues::add('notice', "No user found.");
            return;
        }
        self::$title = $user->username . '\'s Profile - {SITENAME}';
        self::$pageDescription = 'User Profile for ' . $user->username . ' - {SITENAME}';
        Views::view('user', $user);
    }

    public function login()
    {
        self::$title = 'Portal - {SITENAME}';
        self::$pageDescription = 'Please log in to use {SITENAME} member features.';
        if (App::$isLoggedIn) {
            Issues::add('notice', 'You are already logged in. Please <a href="' . Routes::getAddress() . 'home/logout">click here</a> to log out.');
            return;
        }
        if (!Input::exists()) {
            Views::view('login');
            return;
        }
        if (!Forms::check('login')) {
            Issues::add('error', 'There was an error with your login.', Check::userErrors());
            Views::view('login');
            return;
        }
        // self::$recaptcha = $this->model('recaptcha');
        // if (!self::$recaptcha->verify(Input::post('g-recaptcha-response'))) {
        //     Issues::add('error', 'There was an error with your login.', self::$recaptcha->getErrors());
        //     Views::view('login');
        //     return;
        // }
        if (!self::$user->logIn(Input::post('username'), Input::post('password'), Input::post('remember'))) {
            Issues::add('error', 'Username or password was incorrect.');
            Views::view('login');
            return;
        }
        Session::flash('success', 'You have been logged in.');
        if (Input::exists('rurl')) {
            Redirect::to(Input::post('rurl'));
        } else {
            Redirect::to('home/index');
        }
    }

    public function logout()
    {
        self::$title = 'Log Out - {SITENAME}';
        Template::noIndex();
        if (!App::$isLoggedIn) {
            Issues::add('notice', 'You are not logged in.');
            return;
        }
        self::$user->logOut();
        Session::flash('success', 'You have been logged out.');
        Redirect::to('home/index');
    }

    public function terms()
    {
        self::$title = 'Terms and Conditions - {SITENAME}';
        self::$pageDescription = '{SITENAME} Terms and Conditions of use. Please use {SITENAME} safely.';
        Views::view('terms_page');
    }
}
