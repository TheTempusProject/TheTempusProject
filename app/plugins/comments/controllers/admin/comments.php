<?php
/**
 * Controllers/Admin/.php
 *
 * This is the xxxxxx controller.
 *
 * @version 3.0
 * @author  Joey Kimsey <JoeyKimsey@thetempusproject.com>
 * @link    https://TheTempusProject.com
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */
namespace TheTempusProject\Controllers;

use TempusProjectCore\Classes\Debug;
use TempusProjectCore\Template\Issues;
use TempusProjectCore\Classes\Input;
use TempusProjectCore\Classes\Check;
use TheTempusProject\Controllers\AdminController;
use TempusProjectCore\Template\Views;

class Comments extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        Debug::log('Controller Constructing: ' . get_class($this));
        self::$title = 'Admin - Comments';
        self::$comment = $this->model('comment');
    }

    public function edit($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        if (!Input::exists('submit')) {
            Views::view('admin.commentEdit', self::$comment->findById($data));
            exit();
        }
        if (!Forms::check('editComment')) {
            Issues::add('error', 'There was an error with your request.', Check::userErrors());
            Views::view('admin.commentEdit', self::$comment->findById($data));
            exit();
        }
        if (self::$comment->update($data, Input::post('comment'))) {
            Issues::add('success', 'Comment updated');
        } else {
            Views::view('admin.commentEdit', self::$comment->findById($data));
            exit();
        }
        $this->index();
    }

    public function viewComment($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        $commentData = self::$comment->findById($data);
        if ($commentData !== false) {
            Views::view('admin.comment', $commentData);
            exit();
        }
        Issues::add('error', 'Comment not found.');
        $this->index();    
    }

    public function delete($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        if ($data == null) {
            if (!Input::exists('C_')) {
            $this->index();
            }
            $data = Input::post('C_');
        }
        if (!self::$comment->delete((array) $data)) {
            Issues::add('error', 'There was an error with your request.');
        } else {
            Issues::add('success', 'Comment has been deleted');
        }
        $this->index();
    }

    public function blog($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        $commentData = self::$comment->display(25, 'blog', $data);
        if ($commentData !== false) {
            Components::set('count', self::$comment->count('blog', $data));
            Views::view('admin.blogComments', $commentData);
            exit();
        }
        Issues::add('notice', 'No comments found.');
        $this->index();
    }

    public function index($data = null)
    {
        Views::view('admin.commentRecent', self::$comment->recent());
        exit();
    }
}
