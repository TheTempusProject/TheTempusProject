<?php
/**
 * install.php
 *
 * This is the install controller for the application. All traffic should be funneled
 * After completion: YOU SHOULD DELETE THIS FILE.
 *
 * @version 1.0
 *
 * @author  Joey Kimsey <JoeyKimsey@thetempusproject.com>
 *
 * @link    https://TheTempusProject.com
 *
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */

namespace TheTempusProject\Controllers;

require_once 'index.php';

use TempusProjectCore\Core\Controller as Controller;
use TempusProjectCore\Core\Installer as Installer;
use TempusProjectCore\Functions\Docroot as Docroot;
use TempusProjectCore\Classes\Redirect as Redirect;
use TempusProjectCore\Classes\Session as Session;
use TempusProjectCore\Classes\Config as Config;
use TempusProjectCore\Classes\Cookie as Cookie;
use TempusProjectCore\Classes\Debug as Debug;
use TempusProjectCore\Classes\Check as Check;
use TempusProjectCore\Classes\Input as Input;
use TempusProjectCore\Classes\Email as Email;
use TempusProjectCore\Classes\Issue as Issue;
use TempusProjectCore\Classes\Hash as Hash;

class Install extends Controller
{
    private static $installer = null;

    public function __construct()
    {
        self::$template->noIndex();
        self::$template->noFollow();
        Debug::group("Controller: " . get_class($this), 1);
        self::$pageDescription = 'This is the install script for the tempus project.';
        self::$title = 'TTP Installer';
        self::$installer = new Installer;
        self::$template->set('menu-Welcome', 'disabled');
        self::$template->set('menu-Terms', 'disabled');
        self::$template->set('menu-Verify', 'disabled');
        self::$template->set('menu-Configure', 'disabled');
        self::$template->set('menu-Htaccess', 'disabled');
        self::$template->set('menu-Install', 'disabled');
        self::$template->set('menu-Resources', 'disabled');
        self::$template->set('menu-User', 'disabled');
        self::$template->set('menu-Complete', 'disabled');
        if (self::$installer->getStatus() !== false) {
            if (self::$installer->checkSession() !== false) {
                $location = self::$installer->getStatus();
                $this->$location();
                exit();
            } else {
                Issue::notice('We cannot verify your current install session. If you recieve this message in error, please delete App/install.json and begin the installation process again.');
            }
        }
        $this->index();
        exit;
    }

    public function __destruct()
    {
        Debug::log('Controller Destructing: '.get_class($this));
        Debug::gend();
        $this->build();
    }

    /**
     * All traffic should come through the index page where the proper controller
     * is loaded based on your security hash and the location of the installer you
     * were last on.
     */
    public function index()
    {
        self::$template->set('menu-Welcome', 'active');
        self::$template->set('installer-nav', self::$template->standardView('navigation.installer'));
        Debug::log("Controller initiated: " . __METHOD__ . '.');
        if (Check::form('installStart')) {
            self::$installer->nextStep('terms');
            return;
        }
        $this->view('install.start');
    }
    
    public function terms()
    {
        self::$template->set('menu-Terms', 'active');
        self::$template->set('installer-nav', self::$template->standardView('navigation.installer'));
        Issue::info('Please accept the install agreement and review the warnings in order to continue.');
        self::$template->set('TERMS', self::$template->standardView('terms'));
        if (!Check::form('installAgreement')) {
            $this->view('install.agreement');
            return;
        }
        self::$installer->nextStep('verify');
    }

    public function verify()
    {
        self::$template->set('menu-Verify', 'active');
        self::$template->set('installer-nav', self::$template->standardView('navigation.installer'));
        Issue::info('Please ensure all checks pass in order to continue.');
        if (!Input::exists()) {
            $this->view('install.check');
            return;
        }
        if (!Check::form('installCheck')) {
            Issue::error('There was an error with the Installation.', Check::userErrors());
            $this->view('install.check');
            return;
        }
        self::$installer->nextStep('configure');
    }

    public function configure()
    {
        self::$template->set('menu-Configure', 'active');
        self::$template->set('installer-nav', self::$template->standardView('navigation.installer'));
        Issue::info('Configure your new installation.');
        if (!Input::exists()) {
            $this->view('install.configure');
            return;
        }
        if (!Check::form('installConfigure')) {
            Issue::error('There was an error with your form.', Check::userErrors());
            $this->view('install.configure');
            return;
        }
        $mods = [
            [
                'category' => 'main',
                'name' => 'name',
                'value' => Input::postNull('siteName')
            ],
            [
                'category' => 'main',
                'name' => 'loginLimit',
                'value' => 5
            ],
            [
                'category' => 'main',
                'name' => 'pageLimit',
                'value' => 50
            ],
            [
                'category' => 'uploads',
                'name' => 'files',
                'value' => true
            ],
            [
                'category' => 'uploads',
                'name' => 'images',
                'value' => true
            ],
            [
                'category' => 'uploads',
                'name' => 'maxFileSize',
                'value' => 5000000
            ],
            [
                'category' => 'uploads',
                'name' => 'maxImageSize',
                'value' => 500000
            ],
            [
                'category' => 'database',
                'name' => 'dbHost',
                'value' => Input::postNull('dbHost')
            ],
            [
                'category' => 'database',
                'name' => 'dbUsername',
                'value' => Input::postNull('dbUsername')
            ],
            [
                'category' => 'database',
                'name' => 'dbPassword',
                'value' => Input::postNull('dbPassword')
            ],
            [
                'category' => 'database',
                'name' => 'dbName',
                'value' => Input::postNull('dbName')
            ],
            [
                'category' => 'database',
                'name' => 'dbEnabled',
                'value' => true
            ],
            [
                'category' => 'database',
                'name' => 'dbMaxQuery',
                'value' => 100
            ]
        ];
        if (!Config::generateConfig($mods)) {
            Issue::error('Config file already exists so the installer has been halted. If there was an error with installation, please delete App/config.php manually and try again. The installer should automatically bring you back to this step.');
            return;
        }
        self::$installer->nextStep('htaccess');
    }

    public function htaccess()
    {
        self::$template->set('menu-Htaccess', 'active');
        self::$template->set('installer-nav', self::$template->standardView('navigation.installer'));
        Issue::info('Modify/Generate the htaccess file.');
        if (!Input::exists()) {
            $this->view('install.htaccess');
            return;
        }
        if (!Check::form('installhtaccess')) {
            Issue::error('There was an error with your form.', Check::userErrors());
            $this->view('install.htaccess');
            return;
        }
        self::$installer->checkHtaccess(true);
        self::$installer->nextStep('install');
    }

    public function install()
    {
        self::$template->set('menu-Install', 'active');
        self::$template->set('installer-nav', self::$template->standardView('navigation.installer'));
        Issue::info('Installing models');
        $models = self::$installer->getModelVersionList('Models');
        if (!Input::exists()) {
            $this->view('install.models', $models);
            return;
        }
        if (!Check::form('installModels')) {
            Issue::error('There was an error with your form.', Check::userErrors());
            $this->view('install.models', $models);
            return;
        }
        $error = false;
        $models = Input::post('M_');
        foreach ($models as $model) {
            if (!self::$installer->installModel('Models', $model, ['installResources' => false])) {
                $error = true;
            }
        }
        if ($error) {
            Issue::error('There was an error with the Installation.', self::$installer->getErrors());
            return;
        }
        self::$installer->nextStep('resources');
    }

    public function resources()
    {
        self::$template->set('menu-Resources', 'active');
        self::$template->set('installer-nav', self::$template->standardView('navigation.installer'));
        Issue::info('Generate and install resources.');
        if (!Input::exists()) {
            $this->view('install.resources');
            return;
        }
        if (!Check::form('installResources')) {
            Issue::error('There was an error with your form.', Check::userErrors());
            $this->view('install.resources');
            return;
        }
        $flags = [
            'installDB' => false,
            'installPermissions' => false,
            'installConfigs' => false,
            'installResources' => true,
            'installPreferences' => false
         ];
        $error = false;
        $models = self::$installer->getModelList('Models');
        foreach ($models as $model) {
            if (!self::$installer->installModel('Models', $model, $flags)) {
                $error = true;
            }
        }
        if ($error) {
            Issue::error('There was an error with the Installation.', self::$installer->getErrors());
            return;
        }
        self::$installer->nextStep('user');
    }

    public function user()
    {
        self::$template->set('menu-User', 'active');
        self::$template->set('installer-nav', self::$template->standardView('navigation.installer'));
        Issue::info('Please register your administrator account.');
        if (!Input::exists()) {
            $this->view('install.adminUser');
            return;
        }
        if (!Check::form('installAdminUser')) {
            Issue::error('There was an error with your form.', Check::userErrors());
            $this->view('install.adminUser');
            return;
        }
        if (!self::$user->create([
            'username' => Input::post('newUsername'),
            'password' => Hash::make(Input::post('password')),
            'email' => Input::post('email'),
            'lastLogin' => time(),
            'registered' => time(),
            'confirmed' => 1,
            'terms' => 1,
            'userGroup' => 1,
        ])) {
            Issue::error('There was an error creating the admin user.');
            return;
        }
        self::$installer->nextStep('complete');
    }

    public function complete()
    {
        self::$template->set('menu-Complete', 'active');
        self::$template->set('installer-nav', self::$template->standardView('navigation.installer'));
        Issue::success('The Tempus Project has been installed successfully.');
        Email::send(Input::post('email'), 'install', null, ['template' => true]);
        $this->view('install.complete');
    }
}
