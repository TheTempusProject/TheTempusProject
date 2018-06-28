<?php
/**
 * Models/model_blog.php.
 *
 * This class is used for the manipulation of the blog database table.
 *
 * @version 1.0
 *
 * @author  Joey Kimsey <joeyk4816@gmail.com>
 *
 * @link    https://github.com/JoeyK4816/TheTempusProject
 *
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */

namespace TheTempusProject\Models;

use TempusProjectCore\Core\Controller as Controller;
use TempusProjectCore\Classes\Debug as Debug;

class Github extends Controller
{
    public function __construct()
    {
        Debug::log('Model Constructed: '.get_class($this));
        Debug::gend();
    }
    public static function sql()
    {
    }
    public static function latestRelease($user, $repo)
    {
    }
    public static function newConnection($data = null)
    {
    }
}
