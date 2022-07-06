<?php
/**
 * forms.php
 *
 * This houses all of the for checking functions for this plugin.
 *
 * @package  Feedback
 * @version  3.0
 * @author   Joey Kimsey <JoeyKimsey@thetempusproject.com>
 * @link     https://TheTempusProject.com
 * @license  https://opensource.org/licenses/MIT [MIT LICENSE]
 */
namespace TheTempusProject\Plugins\Feedback;

use TempusProjectCore\Classes\Input as Input;
use TempusProjectCore\Classes\Check as Check;

class Forms
{
    /**
     * Validates the new comment form.
     *
     * @return boolean
     */
    public static function newComment()
    {
        if (!Input::exists('comment')) {
            Check::addUserError('You cannot post a blank comment.');
            return false;
        }
        if (!Check::token()) {
            return false;
        }
        return true;
    }

    /**
     * Validates the edit comment form.
     *
     * @return boolean
     */
    public static function editComment()
    {
        if (!Input::exists('comment')) {
            Check::addUserError('You cannot post a blank comment.');
            return false;
        }
        if (!Check::token()) {
            return false;
        }
        return true;
    }
}
