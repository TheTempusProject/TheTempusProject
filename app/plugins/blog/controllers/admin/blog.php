<?php
/**
 * Controllers/Admin/Blog.php
 *
 * This is the Blog admin controller.
 *
 * @version 3.0
 * @author  Joey Kimsey <JoeyKimsey@thetempusproject.com>
 * @link    https://TheTempusProject.com
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */

namespace TheTempusProject\Controllers;

use TempusProjectCore\Core\Controller;
use TempusProjectCore\Classes\Debug;
use TempusProjectCore\Template\Issues;
use TempusProjectCore\Classes\Input;
use TempusProjectCore\Classes\Check;
use TempusProjectCore\Template\Views;
use TheTempusProject\Controllers\AdminController;

require_once 'AdminController.php';

class Blog extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        Debug::log('Controller Constructing: ' . get_class($this));
        self::$blog = $this->model('blog');
        self::$title = 'Admin - Blog';
    }
    public function index($data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        Views::view('admin.blogList', self::$blog->listPosts(['includeDrafts' => true]));
        exit();
    }

    public function newPost($data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        if (!Input::exists('submit')) {
            Views::view('admin.blogNew');
            exit();
        }
        if (!Forms::check('newBlogPost')) {
            Issues::add('error', 'There was an error with your request.', Check::userErrors());
            $this->index();
        }
        self::$blog->newPost(Input::post('title'), Input::post('blogPost'), Input::post('submit'));
        $this->index();
    }
    public function edit($data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        if (!Input::exists('submit')) {
            Views::view('admin.blogEdit', self::$blog->find($data));
            exit();
        }
        if (Input::post('submit') == 'preview') {
            Views::view('admin.blogPreview', self::$blog->preview(Input::post('title'), Input::post('blogPost')));
            exit();
        }
        if (!Forms::check('editBlogPost')) {
            Issues::add('error', 'There was an error with your form.', Check::userErrors());
            $this->index();
        }
        if (self::$blog->updatePost($data, Input::post('title'), Input::post('blogPost'), Input::post('submit')) === true) {
            Issues::add('success', 'Post Updated.');
            $this->index();
        }
        Issues::add('error', 'There was an error with your request.');
        $this->index();
    }
    public function viewPost($data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        $blogData = self::$blog->find($data);
        if ($blogData !== false) {
            Views::view('admin.blogView', $blogData);
            exit();
        }
        Issues::add('error', 'Post not found.');
        $this->index();
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
            Issues::add('error', 'There was an error with your request.');
        } else {
            Issues::add('success', 'Post has been deleted');
        }
        $this->index();
    }
    public function preview($data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        Views::view('admin.blogPreview', self::$blog->preview(Input::post('title'), Input::post('blogPost')));
        exit();
    }
}
