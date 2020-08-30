<?php
/**
 * forms.php
 *
 * This houses all of the for checking functions for this plugin.
 *
 * @package  Subscribe
 * @version  3.0
 * @author   Joey Kimsey <JoeyKimsey@thetempusproject.com>
 * @link     https://TheTempusProject.com
 * @license  https://opensource.org/licenses/MIT [MIT LICENSE]
 */
namespace TheTempusProject\Plugins\Subscribe;

use TempusProjectCore\Classes\Input as Input;
use TempusProjectCore\Classes\Check as Check;

class Forms
{
    /**
     * Validates the subscribe form.
     *
     * @return boolean
     */
    public static function subscribe()
    {
        if (!Check::email(Input::post('email'))) {
            Check::addUserError('Invalid email.');
            return false;
        }
        if (!Check::token()) {
            return false;
        }
        return true;
    }

    /**
     * Validates the unsubscribe form.
     *
     * @return boolean
     */
    public static function unsubscribe()
    {
        if (!Check::email(Input::post('email'))) {
            Check::addUserError('Invalid email.');
            return false;
        }
        if (!Check::token()) {
            return false;
        }
        return true;
    }
    
    /**
     * Validates the new subscription form.
     *
     * @return boolean
     */
    public static function newSubscription()
    {
        if (!Check::token()) {
            return false;
        }
        return true;
    }
}
