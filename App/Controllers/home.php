<?php
/**
 * Controllers/home.php
 *
 * This is the home controller.
 *
 * @version 3.0
 *
 * @author  Joey Kimsey <JoeyKimsey@thetempusproject.com>
 *
 * @link    https://TheTempusProject.com
 *
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */
namespace TheTempusProject\Controllers;

use TempusProjectCore\Core\Controller;
use TempusProjectCore\Core\Template;
use TempusProjectCore\Functions\Routes;
use TempusProjectCore\Classes\Redirect;
use TempusProjectCore\Classes\Session;
use TempusProjectCore\Classes\Debug;
use TempusProjectCore\Classes\Input;
use TempusProjectCore\Classes\Email;
use TempusProjectCore\Classes\Check;
use TempusProjectCore\Classes\Issue;
use TempusProjectCore\Classes\Log;
use TempusProjectCore\Classes\DB;
use Abraham\TwitterOAuth\TwitterOAuth;

class Home extends Controller
{
    protected static $session;
    protected static $subscribe;
    protected static $feedback;
    protected static $recaptcha;
    protected static $bugreport;
    protected static $user;
    protected static $blog;

    public function __construct()
    {
        Debug::log('Controller Constructing: ' . get_class($this));
        self::$session = $this->model('sessions');
        self::$subscribe = $this->model('subscribe');
        self::$recaptcha = $this->model('recaptcha');
        self::$blog = $this->model('blog');
        self::$feedback = $this->model('feedback');
        self::$bugreport = $this->model('bugreport');
        self::$user = $this->model('user');
    }
    public function __destruct()
    {
        Debug::log('Controller Destructing: ' . get_class($this));
        self::$session->updatePage(self::$title);
        $this->build();
        Debug::closeAllGroups();
    }

    public function index()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = '{SITENAME}';
        self::$pageDescription = 'The Tempus Project is a web application designed for rapid protoyping. Focussed on developers and designed to be quick and easy to install and modify.';
        /** @var TWITTER STUFF
        */
        $twitter_customer_key           = 'LQaXOANU96tK63xSMpUTzovvY';
        $twitter_customer_secret        = 'muiWEmlLBQuyyaO5dPakj2XiZrgKXdyxoU4CJCDzMJh0IZ0IVu';
        $twitter_access_token           = '3147098962-Quh94uHXwp1TZDkqwkVZxn5lzVo7rFDhlugD7sB';
        $twitter_access_token_secret    = 'w8S7qr8qU6FKn9ob8Uhs1X1BRtpCbTAwaNPv5JN34mbAk';
        $connection = new TwitterOAuth($twitter_customer_key, $twitter_customer_secret, $twitter_access_token, $twitter_access_token_secret);
        $my_tweets = $connection->get('statuses/user_timeline', array('screen_name' => 'projecttempus', 'count' => 5));
        $test = $my_tweets[0]->user->profile_image_url_https;
        self::$template->set('profile_image_url_https', $test);
        self::$template->set('tweets', self::$template->standardView('tweets', $my_tweets));
        self::$template->set('POSTS', Template::standardView('blog.recentWidget', self::$blog->recent(3)));
        $this->view('index');
        exit();
    }
    public function beta()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Beta - {SITENAME}';
        self::$pageDescription = 'The Tempus Project is now in open BETA. Here you can sign up to be part of the beta testing mailing list and get exclusive members access here on the site.';
        $this->view('index');
        exit();
    }
    public function downloads()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Downloads - {SITENAME}';
        self::$pageDescription = 'Here you will find all of the available downloads and versions for the tempus project..';
        $this->view('downloads');
        exit();
    }
    public function crashcourse()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Crash Course Sign-up - {SITENAME}';
        self::$pageDescription = 'On this page you can sign up for the tempus project crash course.';
        if (!Input::exists('email')) {
            $this->view('formCrashcourse');
            exit();
        }
        if (!Check::form('crashCourse')) {
            Issue::error('There was an error with your form.', Check::userErrors());
            $this->view('formCrashcourse');
            exit();
        }
        self::$db->insert('crash', [
                'name' => Input::post('name'),
                'email' => Input::post('email'),
                'os' => Input::post('os'),
                'xp' => Input::post('experience'),
                'goals' => Input::post('goals'),
                'info' => Input::post('info'),
                ]);
        Session::flash('success', 'Your Application has been received.');
        Redirect::to('home/index');
    }
    public function subscribe()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Subscribe - {SITENAME}';
        self::$pageDescription = 'We are always publishing great content and keeping our members up to date. If you would like to join our list, you can subscribe here.';
        if (!Input::exists('email')) {
            $this->view('subscribe');
            exit();
        }
        if (!Check::form('subscribe')) {
            Issue::error('There was an error with your form.', Check::userErrors());
            $this->view('subscribe');
            exit();
        }
        if (!self::$subscribe->add(Input::post('email'))) {
            Issue::error('There was an error with your request, please try again.');
            $this->view('subscribe');
            exit();
        }
        $data = self::$subscribe->get(Input::post('email'));
        Email::send(Input::post('email'), 'subscribe', $data->confirmationCode, ['template' => true]);
        Issue::success('You have successfully been subscribed to our mailing list.');
        exit();
    }

    public function unsubscribe($email = null, $code = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = '{SITENAME}';
        self::$pageDescription = '';
        if (!empty($email) && !empty($code)) {
            if (self::$subscribe->unsubscribe($email, $code)) {
                Issue::success('You have been successfully unsubscribed from receiving further mailings.');
                exit();
            }
            Issue::error('There was an error with your request.');
            $this->view('unsubscribe');
            exit();
        }
        if (!Input::exists('submit')) {
            $this->view('unsubscribe');
            exit();
        }
        if (!Check::form('unsubscribe')) {
            Issue::error('There was an error with your request.', Check::userErrors());
            $this->view('unsubscribe');
            exit();
        }
        $data = self::$subscribe->get(Input::post('email'));
        if (empty($data)) {
            Issue::notice('There was an error with your request.');
            $this->view('unsubscribe');
            exit();
        }
        Email::send(Input::post('email'), 'unsubInstructions', $data->confirmationCode, ['template' => true]);
        Session::flash('success', 'An email with instructions on how to unsubscribe has been sent to your email.');
        Redirect::to('home/index');
    }

    public function feedback()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Feedback - {SITENAME}';
        self::$pageDescription = 'At {SITENAME}, we value our users\' input. You can provide any feedback or suggestions using this form.';
        if (!Input::exists()) {
            $this->view('feedback');
            exit();
        }
        if (!Check::form('feedback')) {
            Issue::error('There was an error with your form, please check your submission and try again.', Check::userErrors());
            $this->view('feedback');
            exit();
        }
        self::$feedback->create(Input::post('name'), Input::post('feedbackEmail'), Input::post('entry'));
        Session::flash('success', 'Thank you! Your feedback has been received.');
        Redirect::to('home/index');
    }

    public function bugreport()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Bug Report - {SITENAME}';
        self::$pageDescription = 'On this page you can submit a bug report for the site.';
        if (!self::$isLoggedIn) {
            Issue::notice('You must be logged in to report bugs.');
            exit();
        }
        if (!Input::exists()) {
            $this->view('bugreport');
            exit();
        }
        if (!Check::form('bugreport')) {
            Issue::error('There was an error with your report.', Check::userErrors());
            $this->view('bugreport');
            exit();
        }
        self::$bugreport->create(self::$activeUser->ID, Input::post('url'), Input::post('ourl'), Input::post('repeat'), Input::post('entry'));
        Session::flash('success', 'Your Bug Report has been received. We may contact you for more information at the email address you provided.');
        Redirect::to('home/index');
    }

    public function profile($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'User Profile - {SITENAME}';
        self::$pageDescription = 'User Profiles for {SITENAME}';
        if (!self::$isLoggedIn) {
            Issue::notice('You must be logged in to view this page.');
            exit();
        }
        $user = self::$user->get($data);
        if (!$user) {
            Issue::notice("No user found.");
            exit();
        }
        self::$title = $user->username . '\'s Profile - {SITENAME}';
        self::$pageDescription = 'User Profile for ' . $user->username . ' - {SITENAME}';
        $this->view('user', $user);
    }

    public function login()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Portal - {SITENAME}';
        self::$pageDescription = 'Please log in to use {SITENAME} member features.';
        if (self::$isLoggedIn) {
            Issue::notice('You are already logged in. Please <a href="' . Routes::getAddress() . 'home/logout">click here</a> to log out.');
            exit();
        }
        if (!Input::exists()) {
            $this->view('login');
            exit();
        }
        if (!Check::form('login')) {
            Issue::error('There was an error with your login.', Check::userErrors());
            $this->view('login');
            exit();
        }
        if (!self::$recaptcha->verify(Input::post('g-recaptcha-response'))) {
            Issue::error('There was an error with your login.', self::$recaptcha->getErrors());
            $this->view('login');
            exit();
        }
        if (!self::$user->logIn(Input::post('username'), Input::post('password'), Input::post('remember'))) {
            Issue::error('Username or password was incorrect.');
            $this->view('login');
            exit();
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
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Log Out - {SITENAME}';
        self::$template->noIndex();
        if (!self::$isLoggedIn) {
            Issue::notice('You are not logged in.');
            exit();
        }
        self::$user->logOut();
        Session::flash('success', 'You have been logged out.');
        Redirect::to('home/index');
    }

    public function terms()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Terms and Conditions - {SITENAME}';
        self::$pageDescription = '{SITENAME} Terms and Conditions of use. Please use {SITENAME} safely.';
        $this->view('termsPage');
        exit();
    }
}
