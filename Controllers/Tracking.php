<?php
/**
 * Controllers/Tracking.php
 *
 * This is the Tracking controller.
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

use TempusProjectCore\Core\Controller;
use TempusProjectCore\Classes\Debug;
use TempusProjectCore\Classes\Issue;
use TempusProjectCore\Classes\Input;
use TempusProjectCore\Classes\Hash;
use TempusProjectCore\Classes\Code;

class Tracking extends Controller
{
    protected static $tracking;
    protected static $session;

    public function __construct()
    {
        self::$template->noIndex();
        self::$template->noFollow();
        self::$tracking = $this->model('tracking');
        self::$session = $this->model('session');
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
        self::$tracking->track();
    }
    public function pixel()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        if (Input::exists('hash')) {
            $hash = Input::get('hash');
        } else {
            $hash = 'Unknown';
        }
        if (isset($_SERVER['HTTP_REFERER'])) {
            $referer = $_SERVER['HTTP_REFERER'];
        } else {
            $referer = 'Unknown';
        }
        
        self::$tracking->track([
            'referer' => $referer,
            'hash' => $hash,
            'data' => $data,
        ]);
    }
}
