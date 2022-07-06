<?php
/**
 * forms.php
 *
 * This houses all of the for checking functions for this plugin.
 *
 * @package  Subscribe
 * @version  3.0
 * @author   Joey Kimsey <Joey@thetempusproject.com>
 * @link     https://TheTempusProject.com
 * @license  https://opensource.org/licenses/MIT [MIT LICENSE]
 */
namespace TheTempusProject\Plugins\Subscribe;

use TempusProjectCore\Functions\Input;
use TempusProjectCore\Functions\Check;
use TheTempusProject\Classes\Forms;

class SubscribeForms extends Forms {
    /**
     * Adds these functions to the form list.
     */
    public function __construct() {
        self::addHandler('subscribe', __CLASS__, 'subscribe');
        self::addHandler('unsubscribe', __CLASS__, 'unsubscribe');
        self::addHandler('newSubscription', __CLASS__, 'newSubscription');
    }
    /**
     * Validates the subscribe form.
     *
     * @return boolean
     */
    public static function subscribe() {
        if (!self::email(Input::post('email'))) {
            self::addUserError('Invalid email.');
            return false;
        }
        if (!self::token()) {
            return false;
        }
        return true;
    }

    /**
     * Validates the unsubscribe form.
     *
     * @return boolean
     */
    public static function unsubscribe() {
        if (!self::email(Input::post('email'))) {
            self::addUserError('Invalid email.');
            return false;
        }
        if (!self::token()) {
            return false;
        }
        return true;
    }

    /**
     * Validates the new subscription form.
     *
     * @return boolean
     */
    public static function newSubscription() {
        if (!self::token()) {
            return false;
        }
        return true;
    }
}

new SubscribeForms;