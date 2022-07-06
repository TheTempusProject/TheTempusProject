<?php
/**
 * controllers/error.php
 *
 * This is the error controller.
 *
 * @version 2.0
 * @author  Joey Kimsey <JoeyKimsey@thetempusproject.com>
 * @link    https://TheTempusProject.com
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */
namespace TheTempusProject\Controllers;

use TheTempusProject\Classes\Controller;
use TempusProjectCore\Functions\Debug;

class Error extends Controller
{
    public function index()
    {
        self::$title = 'Error';
    }
}
