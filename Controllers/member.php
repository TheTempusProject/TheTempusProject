<?php
/**
 * Controllers/member.php.
 *
 * This is the Members controller.
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
use TempusProjectCore\Classes\Issue as Issue;

class member extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Debug::group("Controller: " . get_class($this), 1);
        Self::$_template->noIndex();
        if (!Self::$_is_member) {
            Issue::error('You do not have permission to view this page.');
            exit();
        }
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
        Self::$_title = 'Members Area';
        $this->view('member');
        exit();
    }
}
