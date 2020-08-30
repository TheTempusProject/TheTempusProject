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
     * Validates the feedback form.
     *
     * @return boolean
     */
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

    /**
     * Validates the new blog post form.
     *
     * @return boolean
     */
    public static function newBlogPost()
    {
        if (!Input::exists('title')) {
            Check::addUserError('You must specify title');
            return false;
        }
        if (!Check::dataTitle(Input::post('title'))) {
            Check::addUserError('Invalid title');
            return false;
        }
        if (!Input::exists('blogPost')) {
            Check::addUserError('You must specify a post');
            return false;
        }
        /** You cannot use the token check due to how tinymce reloads the page
        if (!Check::token()) {
            return false;
        }
        */
        return true;
    }

    public static function editBlogPost()
    {
        if (!Input::exists('title')) {
            Check::addUserError('You must specify title');
            return false;
        }
        if (!Check::dataTitle(Input::post('title'))) {
            Check::addUserError('Invalid title');
            return false;
        }
        if (!Input::exists('blogPost')) {
            Check::addUserError('You must specify a post');
            return false;
        }
        /** You cannot use the token check due to how tinymce reloads the page
        if (!Check::token()) {
            return false;
        }
        */
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
