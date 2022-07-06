<?php
/**
 * app/classes/controller.php
 *
 * This is the main TempusProject controller.
 *
 * @version 3.0
 * @author  Joey Kimsey <JoeyKimsey@thetempusproject.com>
 * @link    https://TheTempusProject.com
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */
namespace TheTempusProject\Classes;

use TheTempusProject\Models\ {
    User, Sessions
};
use TempusProjectCore\Template\Components;
use TheTempusProject\TheTempusProject as App;
use TempusProjectCore\Controller as TPCController;
use TempusProjectCore\Functions\Debug;
use TempusProjectCore\Template;

class Controller extends TPCController
{
    protected static $user;
    protected static $session;

    public function __construct()
    {
        parent::__construct();
        self::$session = new Sessions;
        self::$user = new User;
        Template::setTemplate('default');
    }
    
    public function __destruct()
    {
        Components::set('TITLE', self::$title);
        Components::set('PAGE_DESCRIPTION', self::$pageDescription);
        parent::__destruct();
        self::$session->updatePage(self::$title); // maybe
        Debug::closeAllGroups();
    }

    public function setActiveUser( ) {

    }
}