<?php
/**
 * controllers/usercp.php
 *
 * This is the userCP controller.
 *
 * @version 3.0
 * @author  Joey Kimsey <JoeyKimsey@thetempusproject.com>
 * @link    https://TheTempusProject.com
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 * 

 * @package  Subscribe
 * @version  3.0
 * @author   Joey Kimsey <JoeyKimsey@thetempusproject.com>
 * @link     https://TheTempusProject.com
 * @license  https://opensource.org/licenses/MIT [MIT LICENSE]
 */
namespace TheTempusProject\Controllers;

use TempusProjectCore\Core\Controller;
use TempusProjectCore\Template\Issues;
use TheTempusProject\TheTempusProject as App;
use TempusProjectCore\Classes\{
    Code, Debug, Check, Input, Email, Image, Hash
};

class Controller extends Controller
{
    protected static $user;
    protected static $session;

    public function __construct()
    {
        Debug::log('Controller Constructing: ' . get_class($this));
        self::$session = $this->model('sessions');
        self::$user = $this->model('user');
    }
    
    public function __destruct()
    {
        Debug::log('Controller Destructing: ' . get_class($this));
        self::$session->updatePage(self::$title); // maybe
        $this->build();
        Debug::closeAllGroups();
    }
============================================================
    public function __construct()
    {
        Debug::log('Controller Constructing: ' . get_class($this));
        self::$session = $this->model('sessions');
        self::$user = $this->model('user');
        self::$subscribe = $this->model('subscribe');
    }
    public function __construct()
    {
        Debug::log('Controller Constructing: ' . get_class($this));
        Pagination::activePageSelect('navigation.usercp', null, true);
        Template::noIndex();
        if (!App::$isLoggedIn) {
            Issues::add('notice','You must be logged in to view this page!');
            exit();
        }
        self::$session = $this->model('sessions');
        self::$user = $this->model('user');
        self::$message = $this->model('message');
    }
    public function __construct()
    {
        Debug::log('Controller Constructing: ' . get_class($this));
        Template::noFollow();
        Template::noIndex();
        Template::setTemplate('rest');
    }
    public function __construct()
    {
        Debug::log('Controller Constructing: ' . get_class($this));
        Template::noIndex();
        self::$session = $this->model('sessions');
        self::$recaptcha = $this->model('recaptcha');
        self::$user = $this->model('user');
    }
    public function __construct()
    {
        Debug::log('Controller Constructing: ' . get_class($this));
        Template::noIndex();
        if (!$this->isMember) {
            Issues::add('error','You do not have permission to view this page.');
            exit();
        }
        $this->session = $this->model('sessions');
    }
    public function __construct()
    {
        Debug::log('Controller Constructing: ' . get_class($this));
        self::$session = $this->model('sessions');
    }



    ADMIN
    ADMIN
    ADMIN


    public function __construct($data = null)
    {
        parent::__construct();
        Debug::log('Controller Constructing: ' . get_class($this));

        Template::noFollow();
        Template::noIndex();

        if (!App::$isLoggedIn) {
            Issues::add('notice','You must be logged in to view this page.');
            exit();
        }
        if (!self::$isAdmin) {
            Issues::add('error','You do not have permission to view this page.');
            exit();
        }

        self::$title = 'Admin - Users';
        self::$title = 'Admin - Settings';
        self::$title = 'Admin - Logs';
        self::$title = 'Admin - Login Logs';
        self::$title = 'Admin - Installed';
        self::$title = 'Admin - Home';
        self::$title = 'Admin - Groups';
        self::$title = 'Admin - Errors';
        self::$title = 'Admin - Dependencies';
        self::$title = 'Admin - Contact';
        self::$title = 'Admin - Admin Logs';

        if (Input::post('submit') == 'delete') {
            $sub = 'delete';
        }
        if (Input::post('submit') == 'edit') {
            $sub = 'edit';
        }

        self::$log = $this->model('log');
        self::$user = $this->model('user');
        self::$blog = $this->model('blog');
        self::$bugreport = $this->model('bugreport');
        self::$feedback = $this->model('feedback');
        self::$message = $this->model('message');
        self::$tracking = $this->model('track');
        self::$subscribe = $this->model('subscribe');
        self::$session = $this->model('session');
        self::$comment = $this->model('comment');
        self::$group = $this->model('group');
        $this->installer = new Installer;

        Filters::add('logMenu', "#<ul id=\"log-menu\" class=\"collapse\">#is", "<ul id=\"log-menu\" class=\"\">", true);

        Template::setTemplate('admin');


    ADMIN
    ADMIN
