<?php
/**
 * index.php
 *
 * Using the .htaccess rerouting: all traffic should be directed to/through index.php.
 * In this file we initiate all models we will need, authenticate sessions, set
 * template objects, and call appload to initialize the appropriate controller/method.
 *
 * @version 3.0
 * @author  Joey Kimsey <Joey@thetempusproject.com>
 * @link    https://TheTempusProject.com
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */
namespace TheTempusProject;

require_once 'bin/autoload.php';

use TempusProjectCore\App;
use TempusProjectCore\Classes\ {
    Config, Database as DB
};
use TempusProjectCore\Functions\ {
    Routes, Debug, Input, Redirect, Session, Cookie
};
use TempusProjectCore\Template\ {
    Components, Filters, Issues, Views
};
use TheTempusProject\Classes\{
    Permissions, Preferences
};
use TheTempusProject\Models\ {
    Message, Sessions, User, Group
};

class TheTempusProject extends App {
    public static $activeGroup = array();
    public static $activePerms = array();
    public static $activePrefs = array();
    public static $activeUser;
    public static $isLoggedIn = false;
    public static $isMember = false;
    public static $isAdmin = false;
    public static $isMod = false;
    private $initialized = false;

    /**
     * The constructor takes care of everything that we will need before
     * finally calling appload to instantiate the appropriate controller/method.
     *
     * @param string $urlDirected - A custom url for initiating the app
     */
    public function __construct() {
        //initialize the parent app
        parent::__construct();
        Debug::info('Requested URL: ' . $this->getCurrentUrl());

        // load our own config
        self::$activeConfig->load( CONFIG_DIRECTORY . 'config.json' );

        // instead of htaccess it should check which server type and use that method
        $htaccessPath = APP_ROOT_DIRECTORY.'.htaccess'; // for apache
        $installPath = APP_ROOT_DIRECTORY.'install.php';
        if (!file_exists($htaccessPath) && !file_exists($installPath)) {
            echo file_get_contents( ERRORS_DIRECTORY.'533.html');
            exit();
        }

        // set default ppermissions and preferences
        self::$activePerms = new Permissions( CONFIG_DIRECTORY . 'permissions.json' );
        self::$activePrefs = new Preferences( CONFIG_DIRECTORY . 'preferences.json' );

        // Authenticate our session
        $this->authenticate();

        // // Populate some of the Template Data
        Components::set('SITENAME', Config::get('main/name'));
        Components::set('AUTHOR', '<meta name="author" content="'.Config::get('main/name').'">');
        Components::set('CURRENT_URL', $this->getCurrentUrl());
        Components::set('ROOT_URL', 'http://192.168.0.200:8080/');
        Filters::add('member', '#{MEMBER}(.*?){/MEMBER}#is', (self::$isMember ? '$1' : ''), true);
        Filters::add('mod', '#{MOD}(.*?){/MOD}#is', (self::$isMod ? '$1' : ''), true);
        Filters::add('admin', '#{ADMIN}(.*?){/ADMIN}#is', (self::$isAdmin ? '$1' : ''), true);

        // load the message template data as part of the template
        $messages = new Message;
        Components::set('MESSAGE_COUNT', $messages->unreadCount());
        if ($messages->unreadCount() > 0) {
            $messageBadge = Views::standardView('message.badge');
        } else {
            $messageBadge = '';
        }
        Components::set('MBADGE', $messageBadge);
        if (self::$isLoggedIn) {
            Components::set('RECENT_MESSAGES', Views::standardView('message.recent', $messages->getInbox(5)));
        } else {
            Components::set('RECENT_MESSAGES', '');
        }
        
        // notify admins if the installer is still around
        if (self::$isAdmin) {
            if (file_exists(APP_ROOT_DIRECTORY . "install.php")) {
                if (Debug::status()) {
                    Debug::warn("You have not removed the installer yet.");
                } else {
                    Issues::add('error',"You have not removed the installer. This is a security risk that should be corrected immediately.");
                }
            }
        }
        Debug::gend();
    }

    public function authenticate() {
        $user = New User;
        $group = New Group;
        $sessions = New Sessions;
        if (!$sessions->checkSession(Session::get('SessionID')) &&
            !$sessions->checkCookie(Cookie::get('RememberToken'), true)) {
            Debug::info('Sessions->authenticate - Could not authenticate cookie or session');
            return false;
        }
        self::$isLoggedIn = true;
        self::$activeUser = $user->get(self::$activeSession->userID);
        self::$activeGroup = $group->findById(self::$activeUser->userGroup);
        self::$activePrefs = json_decode(self::$activeUser->prefs);
        self::$isAdmin = self::$activeGroup->adminAccess;
        self::$isMod = self::$activeGroup->modAccess;
        self::$isMember = self::$activeGroup->memberAccess;
        return true;
    }

    public function printDebug() {
        $autoloading = '<tr><td>Vendor Autoloaded: </td><td><code>'.var_export(VENDOR_AUTOLOADED,true).'</code></td></tr>';
        if (VENDOR_AUTOLOADED === false) {
            $autoloading .= '<tr><td>Core Autoloaded: </td><td><code>'.var_export(defined('TEMPUS_CORE_AUTOLOADED'),true).'</code></td></tr>';
            $autoloading .= '<tr><td>Project Autoloaded: </td><td><code>'.var_export(defined('TEMPUS_PROJECT_AUTOLOADED'),true).'</code></td></tr>';
            $autoloading .= '<tr><td>Debugger Autoloaded: </td><td><code>'.var_export(defined('TEMPUS_DEBUGGER_AUTOLOADED'),true).'</code></td></tr>';
        }
        $autoloading .= '<tr><td>Core Constants Loaded: </td><td><code>'.var_export(TEMPUS_CORE_CONSTANTS_LOADED,true).'</code></td></tr>';
        $autoloading .= '<tr><td>Project Constants Loaded: </td><td><code>'.var_export(TEMPUS_PROJECT_CONSTANTS_LOADED,true).'</code></td></tr>';
        $autoloading .= '<tr><td>Debugger Constants Loaded: </td><td><code>'.var_export(TEMPUS_DEBUGGER_CONSTANTS_LOADED,true).'</code></td></tr>';
        echo '<div style="margin: 0 auto; padding-bottom: 25px; background: #eee; width: 1000px;">';
        echo '<h1 style="text-align: center;">Tempus Project Debugging Info</h1>';
        echo '<table style="margin: 0 auto; padding-bottom: 25px; background: #eee; width: 950px;">';
        echo '<tr><td style="text-align: center; padding-top: 25px; padding-bottom: 10px;" colspan="2"><h2>App Data</h2></td></tr>';
        echo '<tr><td>Controller: </td><td><code>'.var_export(self::$controllerName,true).'</code><br></td></tr>';
        echo '<tr><td>Method: </td><td><code>'.var_export(self::$methodName,true).'</code><br></td></tr>';
        echo '<tr><td style="text-align: center; padding-top: 25px; padding-bottom: 10px;" colspan="2"><h2>Authentication</h2></td></tr>';
        echo '<tr><td>isLoggedIn: </td><td><code>'.var_export(self::$isLoggedIn,true).'</code><br></td></tr>';
        echo '<tr><td>isAdmin: </td><td><code>'.var_export(self::$isAdmin,true).'</code><br></td></tr>';
        echo '<tr><td>isMod: </td><td><code>'.var_export(self::$isMod,true).'</code><br></td></tr>';
        echo '<tr><td>isMember: </td><td><code>'.var_export(self::$isMember,true).'</code><br></td></tr>';
        echo '<tr><td style="text-align: center; padding-top: 25px; padding-bottom: 10px;" colspan="2"><h2>User Data</h2></td></tr>';
        echo '<tr><td>activeUser: </td><td><pre>'.var_export(self::$activeUser,true).'</pre></td></tr>';
        echo '<tr><td>activeGroup: </td><td><pre>'.var_export(self::$activeGroup,true).'</pre></td></tr>';
        echo '<tr><td>activePerms: </td><td><pre>'.var_export(self::$activePerms,true).'</pre></td></tr>';
        echo '<tr><td>activePrefs: </td><td><pre>'.var_export(self::$activePrefs,true).'</pre></td></tr>';
        echo '<tr><td style="text-align: center; padding-top: 25px; padding-bottom: 10px;" colspan="2"><h2>Autoloading</h2></td></tr>';
        echo $autoloading;
        echo '</table></div>';
        parent::printDebug();
    }
}