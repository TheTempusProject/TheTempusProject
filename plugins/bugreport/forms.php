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
        if (!Check::url(Input::post('url'))) {
            Check::addUserError('Invalid url.');
            return false;
        }
        if (!Check::url(Input::post('ourl'))) {
            Check::addUserError('Invalid original url.');
            return false;
        }
        if (!Check::tf(Input::post('repeat'))) {
            Check::addUserError('Invalid repeat value.');
            return false;
        }
        if (!Check::token()) {
            return false;
        }
        return true;
    }
}
