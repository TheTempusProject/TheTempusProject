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
    public static function feedback()
    {
        if (!Input::exists('name')) {
            Check::addUserError('You must provide a name.');
            return false;
        }
        if (!Check::name(Input::post('name'))) {
            Check::addUserError('Invalid name.');
            return false;
        }
        if (Input::exists('feedbackEmail') && !Check::email(Input::post('feedbackEmail'))) {
            Check::addUserError('Invalid Email.');
            return false;
        }
        if (Input::post('entry') == '') {
            Check::addUserError('Feedback cannot be empty.');
            return false;
        }
        if (!Check::token()) {
            return false;
        }
        return true;
    }
}
