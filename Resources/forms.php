<?php
/**
 * Classes/Check.php
 *
 * This class is used to test various inputs for a variety of purposes.
 * In this class we verify emails, inputs, passwords, and even entire forms.
 *
 * @version 1.0
 *
 * @author  Joey Kimsey <joeyk4816@gmail.com>
 *
 * @link    https://github.com/JoeyK4816/tempus-project-core
 *
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */

namespace TempusProjectCore\Classes;

class Forms
{
    /**
     * Checks the main components for the installer.
     *
     * @return bool
     */
    public static function install()
    {
        if (!Check::php()) {
            return false;
        }
        if (!Check::mail()) {
            return false;
        }
        if (!Check::safe()) {
            return false;
        }
        if (!Check::sessions()) {
            return false;
        }
        return true;
    }

    /**
     * Check the bug registration form.
     *
     * @return bool
     */
    public static function register()
    {
        if (!Check::username(Input::post('username'))) {
            return false;
        }
        if (!Check::password(Input::post('password'))) {
            return false;
        }
        if (!Check::no_email_exists(Input::post('email'))) {
            return false;
        }
        if (Input::post('password') !== Input::post('password2')) {
            Check::add_error("Passwords do not match.\n");
            return false;
        }
        if (Input::post('email') !== Input::post('email2')) {
            Check::add_error("Emails do not match.\n");
            return false;
        }
        if (Input::post('terms') != '1') {
            Check::add_error("You must agree to the terms of service.\n");
            return false;
        }
        return true;
    }

    /**
     * Check the feedback form.
     *
     * @return bool
     */
    public static function feedback()
    {
        if (!Check::token()) {
            return false;
        }
        if (!Check::name(Input::post('name'))) {
            return false;
        }
        if (Input::exists('email') && !Check::email(Input::post('email'))) {
            return false;
        }
        if (Input::post('entry') == '') {
            return false;
        }
        /**
        if (Check::(Input::post('entry'))) {
            return false;
        }*/
        return true;
    }

    /**
     * Check the bug report form.
     *
     * @return bool
     */
    public static function bug_report()
    {
        if (!Check::url(Input::post('url'))) {
            return false;
        }
        if (!Check::url(Input::post('ourl'))) {
            return false;
        }
        if (!Check::tf(Input::post('repeat'))) {
            return false;
        }
        if (!Check::token()) {
            return false;
        }
        return true;
    }

    /**
     * Checks the main components for the installer.
     *
     * @return bool
     */
    public static function subscribe()
    {
        if (!Check::email(Input::post('email'))) {
            return false;
        }
        if (!Check::token()) {
            return false;
        }
        return true;
    }

    /**
     * Checks the main components for the installer.
     *
     * @return bool
     */
    public static function unsubscribe()
    {
        if (!Check::email(Input::post('email'))) {
            return false;
        }
        if (!Check::token()) {
            return false;
        }
        return true;
    }

    /**
     * Check the group form.
     *
     * @return bool
     *
     * @todo  finish/start
     */
    public static function group_form()
    {
        return true;
    }
}