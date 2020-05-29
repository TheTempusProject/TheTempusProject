<?php
/**
 * Controllers/wyr.php
 *
 * This is the would you rather controller.
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

use TempusProjectCore\Core\Controller as Controller;
use TempusProjectCore\Classes\Debug as Debug;
use TempusProjectCore\Classes\Issue as Issue;
use TempusProjectCore\Classes\Input as Input;
use TempusProjectCore\Classes\Hash as Hash;
use TempusProjectCore\Classes\Code as Code;

class Wyr extends Controller
{
    public function __construct()
    {
        self::$template->noIndex();
        if (!self::$isMember) {
            Issue::error('You do not have permission to view this page.');
            exit();
        }
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
        self::$title = 'Would You rather?';
        $this->view('wyr.select');
        exit();
    }

    public function createdeck()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Would You rather?';
        $this->view('wyr.create.deck');
        exit();
    }

    public function createcard()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Would You rather?';
        $this->view('wyr.create.card');
        exit();
    }

    public function viewdecks()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Would You rather?';
        $this->view('wyr.decks');
        exit();
    }

    public function viewcards()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Would You rather?';
        $this->view('wyr.card');
        exit();
    }

    public function play()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Would You rather?';
        $this->view('wyr');
        exit();
    }
}
