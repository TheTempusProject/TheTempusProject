<?php
/**
 * install.php
 *
 * This is the install controller for the application.
 * After completion: YOU SHOULD DELETE THIS FILE.
 *
 * @version 3.0
 * @author  Joey Kimsey <Joey@thetempusproject.com>
 * @link    https://TheTempusProject.com
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */
namespace TheTempusProject\Controllers;

require_once 'bin/autoload.php';
require_once 'bin/tempus_project.php';

use TheTempusProject\TheTempusProject;
use TheTempusProject\Classes\{
    Controller, Installer, Forms
};
use TempusProjectCore\Classes\{
    Config, CustomException, Email
};
use TempusProjectCore\Functions\{
    Redirect, Session, Check, Cookie, Debug, Input, Image, Hash, Routes
};
use TempusProjectCore\Template\{
    Issues, Views, Components
};
use TempusProjectCore\Template;

class Install extends Controller {
    private $installer;

    public function __construct() {
        parent::__construct();
        self::$title = 'TTP Installer';
        self::$pageDescription = 'This is the install script for the tempus project.';
        $this->installer = new Installer;
        Template::noIndex();
        Template::noFollow();
        Components::set('menu-Welcome', 'disabled');
        Components::set('menu-Terms', 'disabled');
        Components::set('menu-Verify', 'disabled');
        Components::set('menu-Configure', 'disabled');
        Components::set('menu-Htaccess', 'disabled');
        Components::set('menu-Install', 'disabled');
        Components::set('menu-Resources', 'disabled');
        Components::set('menu-User', 'disabled');
        Components::set('menu-Complete', 'disabled');
        Components::set('installer-nav', Views::standardView('nav.installer'));
        if ($this->installer->getStatus() === false) {
            return $this->index();
        }
        if ($this->installer->checkSession() !== false) {
            $location = $this->installer->getStatus();
            return $this->$location();
        }
        Issues::add('notice', 'We cannot verify your current install session. If you recieve this message in error, please delete App/install.json and begin the installation process again.');
    }
    /**
     * All traffic should come through the index page where the proper controller
     * is loaded based on your security hash and the location of the installer you
     * were last on.
     */
    public function index() {
        Components::set('menu-Welcome', 'active');
        if (Forms::Check('installStart')) {
            $this->installer->nextStep('terms', true);
            return;
        }
        Views::view('install.start');
    }
    public function terms() {
        Components::set('menu-Terms', 'active');
        Issues::add('info', 'Please accept the install agreement and review the warnings in order to continue.');
        Components::set('TERMS', Views::standardView('terms'));
        if (!Forms::Check('installAgreement')) {
            Views::view('install.agreement');
            return;
        }
        $this->installer->nextStep('verify', true);
    }
    public function verify() {
        Components::set('menu-Verify', 'active');
        Issues::add('info', 'Please ensure all checks pass in order to continue.');
        if (!Input::exists()) {
            Views::view('install.check');
            return;
        }
        if (!Forms::Check('installCheck')) {
            Issues::add('error', 'There was an error with the Installation.', Check::userErrors());
            Views::view('install.check');
            return;
        }
        $this->installer->nextStep('configure', true);
    }

    public function configure() {
        Components::set('menu-Configure', 'active');
        Components::set('TIMEZONELIST', Views::standardView('tz_dropdown'));
        Issues::add('info', 'Configure your new installation.');
        if (!Input::exists()) {
            Views::view('install.configure');
            return;
        }
        if (!Forms::Check('installConfigure')) {
            Issues::add('error', 'There was an error with your form.', Check::userErrors());
            Views::view('install.configure');
            return;
        }
        if (Input::exists('logo') && Image::upload('logo', 'System')) {
            $logo = 'Uploads/Images/System/' . Image::last();
        } else {
            $logo = 'Images/logo.png';
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
                'name' => 'logo',
                'value' => $logo
            ],
            [
                'category' => 'main',
                'name' => 'timezone',
                'value' => Input::postNull('timezone')
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
                'name' => 'dbPrefix',
                'value' => Input::postNull('dbPrefix')
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
        if (!TheTempusProject::$activeConfig->generate(CONFIG_DIRECTORY . 'config.json', $mods)) {
            Issues::add('error', 'Config file already exists so the installer has been halted. If there was an error with installation, please delete App/config.php manually and try again. The installer should automatically bring you back to this step.');
            return;
        }
        $this->installer->nextStep('routing', true);
    }

    public function routing() {
        Components::set('menu-Htaccess', 'active');
        Issues::add('info', 'Configure page routing.');
        if (!Input::exists()) {
            Views::view('install.routing');
            return;
        }
        if (!Forms::Check('installhtaccess')) {
            Issues::add('error', 'There was an error with your form.', Check::userErrors());
            Views::view('install.routing');
            return;
        }
        if ( Check::isNginx() ) {
            if (!testRouting()) {
                Issues::add('notice', 'There appears to be an issue with your configuration. Certain urls are not being routed as expected.');
                Views::view('install.routing');
                return;
            }
        }
        if ( Check::isApache() ) {
            if (!testRouting()) {
                $this->installer->checkHtaccess(true);
            }
        }
        $this->installer->nextStep('install', true);
    }

    public function install() {
        Components::set('menu-Install', 'active');
        Issues::add('info', 'Installing models');
        $models = $this->installer->getModelVersionList('App/Models');
        if (!Input::exists()) {
            Views::view('install.models', $models);
            return;
        }
        if (!Forms::Check('installModels')) {
            Issues::add('error', 'There was an error with your form.', Check::userErrors());
            Views::view('install.models', $models);
            return;
        }
        $error = false;
        $models = Input::post('M_');
        foreach ($models as $model) {
            if (!$this->installer->installModel($model, '', ['installResources' => false])) {
                $error = true;
            }
        }
        if ($error) {
            Issues::add('error', 'There was an error with the Installation.', $this->installer->getErrors());
            return;
        }
        $this->installer->nextStep('resources', true);
    }

    public function resources() {
        Components::set('menu-Resources', 'active');
        Issues::add('info', 'Generate and install resources.');
        if (!Input::exists()) {
            Views::view('install.resources');
            return;
        }
        if (!Forms::Check('installResources')) {
            Issues::add('error', 'There was an error with your form.', Check::userErrors());
            Views::view('install.resources');
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
        $models = $this->installer->getModelList('App/Models');
        foreach ($models as $model) {
            if (!$this->installer->installModel($model, 'App/Models', $flags)) {
                $error = true;
            }
        }
        if ($error) {
            Issues::add('error', 'There was an error with the Installation.', $this->installer->getErrors());
            return;
        }
        $this->installer->nextStep('user', true);
    }

    public function user() {
        Components::set('menu-User', 'active');
        Issues::add('info', 'Please register your administrator account.');
        if (!Input::exists()) {
            Views::view('install.adminUser');
            return;
        }
        if (!Forms::Check('installAdminUser')) {
            Issues::add('error', 'There was an error with your form.', Check::userErrors());
            Views::view('install.adminUser');
            return;
        }
        self::$user = $this->model('user');
        if (!self::$user->create([
            'username' => Input::post('newUsername'),
            'password' => Hash::make(Input::post('userPassword')),
            'email' => Input::post('userEmail'),
            'lastLogin' => time(),
            'registered' => time(),
            'confirmed' => 1,
            'terms' => 1,
            'userGroup' => 1,
        ])) {
            Issues::add('error', 'There was an error creating the admin user.');
            return;
        }
        $this->installer->nextStep('complete', true);
    }

    public function complete() {
        Components::set('menu-Complete', 'active');
        Issues::add('success', 'The Tempus Project has been installed successfully.');
        Email::send(Input::post('email'), 'install', null, ['template' => true]);
        Views::view('install.complete');
        $this->installer->nextStep('delete');
    }

    public function delete() {
        Issues::add('notice', 'Installation has been completed. Updates and installation can be managed in the admin panel under Installed. Please delete this file.');
    }
}

$app = new TheTempusProject();
new Install;
// $app->printDebug();