<?php
/**
 * app/controllers/admin/home.php
 *
 * This is the admin index controller.
 *
 * @version 3.0
 * @author  Joey Kimsey <JoeyKimsey@thetempusproject.com>
 * @link    https://TheTempusProject.com
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */

namespace TheTempusProject\Controllers;

use TempusProjectCore\Classes\Debug;
use TempusProjectCore\Classes\Input;
use TempusProjectCore\Classes\Code;
use TempusProjectCore\Core\Template;

class Home extends AdminController
{
    public function __destruct()
    {
        Debug::log('Controller Destructing: ' . get_class($this));
        self::$session->updatePage(self::$title);
        $this->build();
        Debug::closeAllGroups();
    }
    public function index()
    {
        $users = Views::standardView('admin.dashUsers', self::$user->recent(5));
        $comments = Views::standardView('admin.dashComments', self::$comment->recent('all', 5));
        $posts = Views::standardView('admin.dashPosts', self::$blog->recent(5));
        Components::set('userDash', $users);
        Components::set('blogDash', $posts);
        Components::set('commentDash', $comments);
        Views::view('admin.dash');
    }
}
