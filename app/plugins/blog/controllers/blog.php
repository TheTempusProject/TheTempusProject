<?php
/**
 * Controllers/blog.php
 *
 * This is the blog controller.
 *
 * @todo  This needs a refactor along with/following
 *        refactoring of the blog and comments models
 *
 * @version 1.0
 * @author  Joey Kimsey <JoeyKimsey@thetempusproject.com>
 * @link    https://TheTempusProject.com
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */
namespace TheTempusProject\Controllers;

use TheTempusProject\Classes\Controller;
use TempusProjectCore\Functions\Debug;
use TempusProjectCore\Classes\Config;
use TempusProjectCore\Classes\DB;
use TempusProjectCore\Classes\Session;
use TempusProjectCore\Classes\Cookie;
use TempusProjectCore\Classes\Log;
use TempusProjectCore\Classes\Input;
use TempusProjectCore\Classes\Email;
use TempusProjectCore\Classes\Check;
use TempusProjectCore\Template\Issues;
use TempusProjectCore\Template\Views;
use TempusProjectCore\Template;
use TempusProjectCore\Classes\Redirect;
use TheTempusProject\Models\Comment;
use TheTempusProject\Models\Sessions;
use TheTempusProject\Models\Blog as BlogModel;
use TheTempusProject\TheTempusProject as App;

class Blog extends Controller
{
    protected static $blog;
    protected static $comment;

    public function __construct() {
        parent::__construct();
        Template::setTemplate('blog');
        self::$blog = new BlogModel;
        self::$comment = new Comment;
    }

    public function index() {
        self::$title = '{SITENAME} Blog';
        self::$pageDescription = '{SITENAME} blog';
        Views::view('blog.list', self::$blog->listPosts());
    }

    public function rss() {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = '{SITENAME} Feed';
        self::$pageDescription = '{SITENAME} blog RSS feed.';
        Template::setTemplate('rss');
        header('Content-Type: text/xml');
        Views::view('blog.rss', self::$blog->listPosts(['stripHtml' => true]));
        exit();
    }

    public function comments($sub, $data) {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        if (!App::$isLoggedIn) {
            Issues::add('notice', 'You must be logged in to do that.');
            exit();
        }
        switch ($sub) {
            case 'post':
                $post = self::$blog->find($data);
                if (!Forms::check('newComment')) {
                    Issues::add('error', 'There was a problem posting your comment.', Check::userErrors());
                    break;
                }
                self::$comment->create('blog', $post->ID, Input::post('comment'));
                Issues::add('success', 'Comment posted');
                break;
             
            case 'edit':
                $comment = self::$comment->findById($data);
                if (!App::$isLoggedIn || (!self::$isAdmin && $comment->author != App::$activeUser->ID)) {
                    Issues::add('error', 'You do not have permission to edit this comment');
                    $data = $comment->contentID;
                    break;
                }
                if (!Input::exists('submit')) {
                    Views::view('admin.commentEdit', self::$comment->findById($data));
                    exit();
                }
                if (!Forms::check('editComment')) {
                    Issues::add('error', 'There was a problem editing your comment.', Check::userErrors());
                    Views::view('admin.commentEdit', self::$comment->findById($data));
                    exit();
                }
                if (!self::$comment->update($data, Input::post('comment'))) {
                    Issues::add('error', 'There was a problem editing your comment.', Check::userErrors());
                    $data = $comment->contentID;
                    break;
                }
                Issues::add('success', 'Comment updated');
                $data = self::$comment->findById($data)->author;
                break;
            
            case 'delete':
                $comment = self::$comment->findById($data);
                if (!App::$isLoggedIn || (!self::$isAdmin && $comment->author != App::$activeUser->ID)) {
                    Issues::add('error', 'You do not have permission to edit this comment');
                    break;
                }
                if (!self::$comment->delete((array) $data)) {
                    Issues::add('error', 'There was an error with your request.');
                } else {
                    Issues::add('success', 'Comment has been deleted');
                }
                break;
        }
        $this->post($data);
    }

    public function post($data) {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Blog Post';
        Components::set('POST_ID', $data);
        if (App::$isLoggedIn) {
            Components::set('NEWCOMMENT', Views::standardView('comments.create'));
        } else {
            Components::set('NEWCOMMENT', '');
        }
        $post = self::$blog->find($data);
        Components::set('count', self::$comment->count('blog', $post->ID));
        Components::set('COMMENTS', Views::standardView('comments.list', self::$comment->display(10, 'blog', $post->ID)));
        Views::view('blog.post', $post);
        exit();
    }

    public function author($data) {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Posts by author - {SITENAME}';
        self::$pageDescription = '{SITENAME} blog posts easily and conveniently sorted by author.';
        Views::view('blog.list', self::$blog->byAuthor($data));
        exit();
    }

    public function month($month, $year = 2017) {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Posts By Month - {SITENAME}';
        self::$pageDescription = '{SITENAME} blog posts easily and conveniently sorted by month.';
        Views::view('blog.list', self::$blog->byMonth($month, $year));
        exit();
    }

    public function year($year) {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Posts by Year - {SITENAME}';
        self::$pageDescription = '{SITENAME} blog posts easily and conveniently sorted by years.';
        Views::view('blog.list', self::$blog->byYear($year));
        exit();
    }
}
