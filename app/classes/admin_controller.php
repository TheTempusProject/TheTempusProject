<?php
/**
 * app/classes/admin_controller.php
 *
 * This is the main admin controller. Every other admin controller should 
 * ecxtend this class.
 *
 * @version 3.0
 * @author  Joey Kimsey <JoeyKimsey@thetempusproject.com>
 * @link    https://TheTempusProject.com
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */
namespace TheTempusProject\Classes;

use TempusProjectCore\Functions\Debug;
use TempusProjectCore\Template;

class AdminController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Debug::log('Controller Constructing: ' . get_class($this));
        Template::noFollow();
        Template::noIndex();
        Template::setTemplate('admin');
    }

    public function __destruct()
    {
        Debug::log('Controller Destructing: ' . get_class($this));
        Debug::closeAllGroups();
    }
}
