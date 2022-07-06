<?php
/**
 * app/ontrollers/member.php
 *
 * This is the members controller.
 *
 * @version 3.0
 * @author  Joey Kimsey <JoeyKimsey@thetempusproject.com>
 * @link    https://TheTempusProject.com
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */
namespace TheTempusProject\Controllers;

use TheTempusProject\Classes\Controller;
use TempusProjectCore\Functions\Debug;
use TempusProjectCore\Template\Views;

class Member extends Controller
{
    public function index()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Members Area';
        Views::view('member');
        return;
    }
}
