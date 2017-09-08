<?php
/**
 * Controllers/Home.php.
 *
 * This is the Home controller.
 *
 * @version 1.0
 *
 * @author  Joey Kimsey <joeyk4816@gmail.com>
 *
 * @link    https://github.com/JoeyK4816/TheTempusProject
 *
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html [GNU GENERAL PUBLIC LICENSE]
 */

namespace TheTempusProject\Controllers;

use TempusProjectCore\Core\Controller as Controller;
use TempusProjectCore\Classes\Debug as Debug;
use TempusProjectCore\Classes\Config as Config;
use TempusProjectCore\Classes\DB as DB;
use TempusProjectCore\Classes\Session as Session;
use TempusProjectCore\Classes\Cookie as Cookie;
use TempusProjectCore\Classes\Log as Log;
use TempusProjectCore\Classes\Input as Input;
use TempusProjectCore\Classes\Email as Email;
use TempusProjectCore\Classes\Check as Check;
use TempusProjectCore\Classes\Issue as Issue;
use TempusProjectCore\Classes\Redirect as Redirect;

class blog extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Debug::group("Controller: " . get_class($this), 1);
        Self::$_template->set_template('blog');
    }
    public function __destruct()
    {
        Debug::log('Controller Destructing: '.get_class($this));
        Self::$_session->update_page(Self::$_title);
        Debug::gend();
        $this->build();
    }
    public function index()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        Self::$_title = '{SITENAME} Blog';
        Self::$_page_description = '{SITENAME} blog';
        $this->view('blog', Self::$_blog->listPosts());
        exit();
    }
    public function rss()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        Self::$_title = '{SITENAME} Feed';
        Self::$_page_description = '{SITENAME} blog RSS feed.';
        Self::$_template->set_template('rss');
        $this->view('blog.rss', Self::$_blog->listPosts());
        exit();
    }
    public function post($data)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        Self::$_title = 'Blog Post';
        $post = Self::$_blog->find($data);
        if (Input::exists('submit')) {
            if (!Self::$_is_logged_in) {
                Issue::notice('You must be logged in to post comments.');
                exit();
            } else {
                if (!Self::$_comment->create('blog', $post->ID)) {
                    Issue::error('There was an error posting you comment, please try again.');
                } else {
                    Issue::success('Comment posted');
                }
            }
        }
        if (Self::$_is_logged_in) {
            Self::$_template->set('NEWCOMMENT', Self::$_template->standard_view('comment.new'));
        } else {
            Self::$_template->set('NEWCOMMENT', '');
        }
        Self::$_template->set('count', Self::$_comment->count('blog', $post->ID));
        Self::$_template->set('COMMENTS', Self::$_template->standard_view('comment.list', Self::$_comment->display(10, 'blog', $post->ID)));
        $this->view('blog.post', $post);
        exit();
    }
    public function author($data)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        Self::$_title = 'Posts by author - {SITENAME}';
        Self::$_page_description = '{SITENAME} blog posts easily and conveniently sorted by author.';
        $this->view('blog', Self::$_blog->byAuthor($data));
        exit();
    }
    public function month($month, $year = 2017)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        Self::$_title = 'Posts By Month - {SITENAME}';
        Self::$_page_description = '{SITENAME} blog posts easily and conveniently sorted by month.';
        $this->view('blog', Self::$_blog->byMonth($month, $year));
        exit();
    }
    public function year($year)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        Self::$_title = 'Posts by Year - {SITENAME}';
        Self::$_page_description = '{SITENAME} blog posts easily and conveniently sorted by years.';
        $this->view('blog', Self::$_blog->byYear($year));
        exit();
    }
}
