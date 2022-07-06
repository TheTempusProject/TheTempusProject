<?php
/**
 * forms.php
 *
 * This houses all of the for checking functions for this plugin.
 *
 * @package  Blog
 * @version  3.0
 * @author   Joey Kimsey <JoeyKimsey@thetempusproject.com>
 * @link     https://TheTempusProject.com
 * @license  https://opensource.org/licenses/MIT [MIT LICENSE]
 */
namespace TheTempusProject\Plugins\Blog;

use TempusProjectCore\Classes\Input;
use TempusProjectCore\Classes\Check;

class Forms
{
    /**
     * Validates the new blog post form.
     *
     * @return boolean
     */
    public static function newBlogPost()
    {
        if (!Input::exists('title')) {
            self::addUserError('You must specify title');
            return false;
        }
        if (!self::dataTitle(Input::post('title'))) {
            self::addUserError('Invalid title');
            return false;
        }
        if (!Input::exists('blogPost')) {
            self::addUserError('You must specify a post');
            return false;
        }
        /** You cannot use the token check due to how tinymce reloads the page
        if (!self::token()) {
            return false;
        }
        */
        return true;
    }

    public static function editBlogPost()
    {
        if (!Input::exists('title')) {
            self::addUserError('You must specify title');
            return false;
        }
        if (!self::dataTitle(Input::post('title'))) {
            self::addUserError('Invalid title');
            return false;
        }
        if (!Input::exists('blogPost')) {
            self::addUserError('You must specify a post');
            return false;
        }
        /** You cannot use the token check due to how tinymce reloads the page
        if (!self::token()) {
            return false;
        }
        */
        return true;
    }
}
