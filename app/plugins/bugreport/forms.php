<?php
/**
 * forms.php
 *
 * This houses all of the for checking functions for this plugin.
 *
 * @package  Bugreport
 * @version  3.0
 * @author   Joey Kimsey <JoeyKimsey@thetempusproject.com>
 * @link     https://TheTempusProject.com
 * @license  https://opensource.org/licenses/MIT [MIT LICENSE]
 */
namespace TheTempusProject\Plugins\Bugreport;

use TempusProjectCore\Classes\Input as Input;
use TempusProjectCore\Classes\Check as Check;

class Forms
{
    /**
     * Validates the bug report form.
     *
     * @return boolean
     */
    public static function bugreport()
    {
        if (!self::url(Input::post('url'))) {
            self::addUserError('Invalid url.');
            return false;
        }
        if (!self::url(Input::post('ourl'))) {
            self::addUserError('Invalid original url.');
            return false;
        }
        if (!self::tf(Input::post('repeat'))) {
            self::addUserError('Invalid repeat value.');
            return false;
        }
        if (!self::token()) {
            return false;
        }
        return true;
    }
}
