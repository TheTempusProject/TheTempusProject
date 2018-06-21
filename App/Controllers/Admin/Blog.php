<?php
/**
 * Controllers/Admin/Blog.php
 *
 * This is the Blog admin controller.
 *
 * @version 3.0
 *
 * @author  Joey Kimsey <JoeyKimsey@thetempusproject.com>
 *
 * @link    https://TheTempusProject.com
 *
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */

namespace TheTempusProject\Controllers;

use TempusProjectCore\Core\Controller;
use TempusProjectCore\Classes\Debug;
use TempusProjectCore\Classes\Issue;
use TempusProjectCore\Classes\Input;
use TempusProjectCore\Classes\Hash;
use TempusProjectCore\Classes\Code;
use TheTempusProject\Controllers\AdminController;

require_once 'AdminController.php';

class Blog extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        Debug::log('Controller Constructing: ' . get_class($this));
        self::$blog = $this->model('blog');
    }
    public function index($data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        self::$title = 'Admin - Blog';
        $this->view('admin.blogList', self::$blog->listPosts(['includeDrafts' => true]));
        exit();
    }

    public function newPost($data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        if (!Input::exists('submit')) {
            $this->view('admin.blogNew');
            exit();
        }
        if (!Check::form('newBlogPost')) {
            Issue::error('There was an error with your request.', Check::userErrors());
            break;
        }
        self::$blog->newPost(Input::post('title'), Input::post('blogPost'), Input::post('submit'));
        $this->view('admin.blogList', self::$blog->listPosts(['includeDrafts' => true]));
        exit();
    }
    public function edit($data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        if (!Input::exists('submit')) {
            $this->view('admin.blogEdit', self::$blog->find($data));
            exit();
        }
        if (Input::post('submit') == 'preview') {
            $this->view('admin.blogPreview', self::$blog->preview(Input::post('title'), Input::post('blogPost')));
            exit();
        }
        if (!Check::form('editBlogPost')) {
            Issue::error('There was an error with your form.', Check::userErrors());
            break;
        }
        if (self::$blog->updatePost($data, Input::post('title'), Input::post('blogPost'), Input::post('submit')) === true) {
            Issue::success('Post Updated.');
            break;
        }
        Issue::error('There was an error with your request.');
        $this->view('admin.blogList', self::$blog->listPosts(['includeDrafts' => true]));
        exit();
    }
    public function viewPost($data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        $blogData = self::$blog->find($data);
        if ($blogData !== false) {
            $this->view('admin.blogView', $blogData);
            exit();
        }
        Issue::error('Post not found.');
        $this->view('admin.blogList', self::$blog->listPosts(['includeDrafts' => true]));
        exit();
    }
    public function delete($data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        if ($data == null) {
            if (Input::exists('B_')) {
                $data = Input::post('B_');
            }
        }
        if (!self::$blog->delete((array) $data)) {
            Issue::error('There was an error with your request.');
        } else {
            Issue::success('Post has been deleted');
        }
        $this->view('admin.blogList', self::$blog->listPosts(['includeDrafts' => true]));
        exit();
    }
    public function preview($data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        $this->view('admin.blogPreview', self::$blog->preview(Input::post('title'), Input::post('blogPost')));
        exit();
    }
}
