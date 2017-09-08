<?php
/**
 * Controllers/Admin.php.
 *
 * This is the Admin controller.
 *
 * @version 1.0
 *
 * @author  Joey Kimsey <joeyk4816@gmail.com>
 *
 * @link    https://github.com/JoeyK4816/TheTempusProject
 *
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */

namespace TheTempusProject\Controllers;

use TempusProjectCore\Core\Controller as Controller;
use TempusProjectCore\Core\Template as Template;
use TempusProjectCore\Classes\Config as Config;
use TempusProjectCore\Classes\Debug as Debug;
use TempusProjectCore\Classes\Input as Input;
use TempusProjectCore\Classes\Email as Email;
use TempusProjectCore\Classes\Issue as Issue;
use TempusProjectCore\Classes\Check as Check;
use TempusProjectCore\Classes\Image as Image;
use TempusProjectCore\Classes\Log as Log;

class admin extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Debug::group('Controller: '.get_class($this), 1);
        Self::$_template->noFollow();
        Self::$_template->noIndex();
        if (!Self::$_is_logged_in) {
            Issue::notice('You must be logged in to view this page.');
            exit();
        }
        if (!Self::$_is_admin) {
            Issue::error('You do not have permission to view this page.');
            exit();
        }
        Self::$_template->set_template('admin');
    }

    public function __destruct()
    {
        Debug::log('Controller Destructing: '.get_class($this));
        Self::$_session->update_page(Self::$_title);
        Debug::gend();
        $this->build();
    }

    public function Index()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        Self::$_title = 'Admin - Home';
        $users = Template::standard_view('admin.dash.users', Self::$_user->recent(5));
        $comments = Template::standard_view('admin.dash.comments', Self::$_comment->recent('all',5));
        $posts = Template::standard_view('admin.dash.posts', Self::$_blog->recent(5));
        Self::$_template->set('user_dash', $users);
        Self::$_template->set('blog_dash', $posts);
        Self::$_template->set('comment_dash', $comments);
        $this->view('admin.dash');
    }

    public function groups($sub = null, $data = null)
    {
        Debug::log('Controller initiated: '.__METHOD__.'.');
        Self::$_title = 'Admin - Groups';
        switch ($sub) {
            case 'view':
                $this->view('admin.group.view', Self::$_group->find_by_ID($data));
                exit();
                break;
            case 'listmembers':
                $name = Self::$_group->find_by_ID($data);
                Self::$_template->set('group_name', $name->name);
                $this->view('admin.group.list.members', Self::$_group->list_members($data));
                exit();
                break;
            case 'new':
                if (Input::exists('submit')) {
                    if (Self::$_group->create()) {
                        Issue::success('Group created');
                    }
                } else {
                    $this->view('admin.group.new');
                    exit();
                }
                break;
            case 'edit':
                if (Input::exists('submit')) {
                    if (Self::$_group->update($data)) {
                        Issue::success('Group updated');
                        Self::$_template->select_option(Input::post('page_limit'));
                    }
                } else {
                    $group = Self::$_group->find_by_ID($data);
                    Self::$_template->select_option($group->page_limit);
                    $this->view('admin.group.edit', $group);
                    exit();
                }
                break;
            case 'delete':
                if ($data == null) {
                    if (Input::exists('G_')) {
                        $data = Input::post('G_');
                    }
                }
                if (!Self::$_group->delete_group($data)) {
                    Issue::error('There was an error with your request.');
                } else {
                    Issue::success('Group has been deleted');
                }
                break;
            default:
                break;
        }
        $this->view('admin.group.list', Self::$_group->list_groups());
        exit();
    }

    public function blog($sub = null, $data = null)
    {
        Debug::log('Controller initiated: '.__METHOD__.'.');
        Self::$_title = 'Admin - Blog';
        switch ($sub) {
            case 'new':
                if (!Input::exists('submit')) {
                    $this->view('admin.blog.new');
                    exit();
                }
                Self::$_blog->newPost(Input::post('submit'));
                break;
            case 'edit':
                if (!Input::exists('submit')) {
                    $this->view('admin.blog.edit', Self::$_blog->find($data));
                    exit();
                }
                if (Input::post('submit') == 'preview') {
                    $this->view('admin.blog.preview', Self::$_blog->preview());
                    exit();
                }
                Self::$_blog->update($data);
                break;
            case 'view':
                $this->view('admin.blog.view', Self::$_blog->find($data));
                exit();
                break;
            case 'delete':
                if ($data == null) {
                    if (Input::exists('B_')) {
                        $data = Input::post('B_');
                    }
                }
                if (!Self::$_blog->delete($data)) {
                    Issue::error('There was an error with your request.');
                } else {
                    Issue::success('Post has been deleted');
                }
                break;
            case 'preview':
                $this->view('admin.blog.preview', Self::$_blog->preview());
                exit();
                break;
            default:
                break;
        }
        $this->view('admin.blog.list', Self::$_blog->listPosts());
        exit();
    }

    public function contact($sub = null, $data = null)
    {
        Debug::log('Controller initiated: '.__METHOD__.'.');
        Self::$_title = 'Admin - Contact';
        if (Input::exists('mail_type')) {
            $params = array(
                'subject' => Input::post('mail_subject'),
                'title' => Input::post('mail_title'),
                'message' => Input::post('mail_message')
                );
            switch (Input::post('mail_type')) {
                case 'registered':
                    $list = Self::$_user->user_list();
                    foreach ($list as $recipient) {
                        Email::send($recipient->email, 'contact', $params, array('template' => true));
                    }
                    Issue::success('Email(s) Sent');
                break;
                case 'newsletter':
                    $list = Self::$_user->user_list('newsletter');
                    foreach ($list as $recipient) {
                        //make unsub
                        Email::send($recipient->email, 'contact', $params, array('template' => true));
                    }
                    Issue::success('Email(s) Sent');
                break;
                case 'all':
                    $list = Self::$_user->user_list();
                    foreach ($list as $recipient) {
                        //make unsub
                        Email::send($recipient->email, 'contact', $params, array('template' => true));
                    }
                    $list = Self::$_subscribe->subscriber_list();
                    foreach ($list as $recipient) {
                        $params['confirmation_code'] = $recipient->confirmation_code;
                        Email::send($recipient->email, 'contact', $params, array('template' => true, 'unsubscribe' => true));
                    }
                    Issue::success('Email(s) Sent');
                break;
                case 'opt':
                    $list = Self::$_user->user_list('newsletter');
                    foreach ($list as $recipient) {
                        //make unsub
                        Email::send($recipient->email, 'contact', $params, array('template' => true));
                    }
                    $list = Self::$_subscribe->subscriber_list();
                    foreach ($list as $recipient) {
                        $params['confirmation_code'] = $recipient->confirmation_code;
                        Email::send($recipient->email, 'contact', $params, array('template' => true, 'unsubscribe' => true));
                    }
                    Issue::success('Email(s) Sent');
                break;
                case 'subscribers':
                    $list = Self::$_subscribe->subscriber_list();
                    foreach ($list as $recipient) {
                        $params['confirmation_code'] = $recipient->confirmation_code;
                        Email::send($recipient->email, 'contact', $params, array('template' => true, 'unsubscribe' => true));
                    }
                    Issue::success('Email(s) Sent');
                break;
                default:
                    return false;
                break;
            }
        }
        $this->view('admin.contact');
    }

    public function comments($sub = null, $data = null)
    {
        Debug::log('Controller initiated: '.__METHOD__.'.');
        Self::$_title = 'Admin - Comments';
        switch ($sub) {
            case 'edit':
                if (!Input::exists('submit')) {
                    $this->view('admin.comment.edit', Self::$_comment->find_by_ID($data));
                    exit();
                }
                if (Self::$_comment->update($data)) {
                    Issue::success('Comment updated');
                } else {
                    $this->view('admin.comment.edit', Self::$_comment->find_by_ID($data));
                    exit();
                }
                break;
            case 'view':
                $this->view('admin.comment', Self::$_comment->find_by_ID($data));
                exit();
                break;
            case 'delete':
                if ($data == null) {
                    if (Input::exists('U_')) {
                        $data = Input::post('U_');
                    }
                }
                if (!Self::$_comment->delete($data)) {
                    Issue::error('There was an error with your request.');
                } else {
                    Issue::success('Comment has been deleted');
                }
                break;
            case 'blog':
                $this->view('admin.comment.recent', Self::$_comment->recent('blog'));
                exit();
                break;
            default:
                break;
        }
        $this->view('admin.comment.recent', Self::$_comment->recent());
        exit();
    }

    public function settings()
    {
        Debug::log('Controller initiated: '.__METHOD__.'.');
        Self::$_title = 'Admin - Settings';
        if (Input::exists()) {
            Config::update();
        }
        Self::$_template->set('NAME', Config::get('main/name'));
        Self::$_template->set('TEMPLATE', Config::get('main/template'));
        Self::$_template->set('LIMIT', Config::get('main/loginLimit'));
        if ((Input::exists('submit') && (Input::post('log_F') === 'true')) || ((Config::get('logging/feedback') === true) && (!Input::exists('submit')))) {
            Self::$_template->set('FEEDBACK_T', 'checked="checked"');
            Self::$_template->set('FEEDBACK_F', '');
        } else {
            Self::$_template->set('FEEDBACK_F', 'checked="checked"');
            Self::$_template->set('FEEDBACK_T', '');
        }
        if ((Input::exists('submit') && (Input::post('log_E') === 'true')) || ((Config::get('logging/errors') === true) && (!Input::exists('submit')))) {
            Self::$_template->set('ERRORS_T', 'checked="checked"');
            Self::$_template->set('ERRORS_F', '');
        } else {
            Self::$_template->set('ERRORS_F', 'checked="checked"');
            Self::$_template->set('ERRORS_T', '');
        }
        if ((Input::exists('submit') && (Input::post('log_L') === 'true')) || ((Config::get('logging/logins') === true) && (!Input::exists('submit')))) {
            Self::$_template->set('LOGINS_T', 'checked="checked"');
            Self::$_template->set('LOGINS_F', '');
        } else {
            Self::$_template->set('LOGINS_F', 'checked="checked"');
            Self::$_template->set('LOGINS_T', '');
        }
        if ((Input::exists('submit') && (Input::post('log_BR') === 'true')) || ((Config::get('logging/bug_reports') === true) && (!Input::exists('submit')))) {
            Self::$_template->set('BUG_REPORTS_T', 'checked="checked"');
            Self::$_template->set('BUG_REPORTS_F', '');
        } else {
            Self::$_template->set('BUG_REPORTS_F', 'checked="checked"');
            Self::$_template->set('BUG_REPORTS_T', '');
        }
        $this->view('admin.settings');
        exit();
    }

    public function users($sub = null, $data = null)
    {
        Debug::log('Controller initiated: '.__METHOD__.'.');
        Self::$_title = 'Admin - Users';
        $user = $this->model('user');
        if ($sub == 'delete' || Input::post('submit') == 'delete') {
            if (Input::exists('submit')) {
                $data[] = Input::post('U_');
            }
            $user->delete($data);
        }
        if ($sub == 'view') {
            $current_user = $user->get($data);
            $this->view('admin.user.view', $current_user);
            exit();
        }
        if ($sub == 'edit' || Input::post('submit') == 'edit') {
            if (Input::exists('U_')) {
                $data = Input::post('U_');
            }
            if (!Check::ID($data)) {
                Issue::error('invalid user.');
                exit();
            }
            $current_user = $user->get($data);
            if (Input::exists('submit') && Input::post('submit') != 'edit') {
                if (Input::exists('avatar')) {
                    if (Image::upload_image('avatar', Self::$_active_user->username)) {
                        $avatar = 'Images/Uploads/'.Self::$_active_user->username.'/'.Image::last();
                    } else {
                        $avatar = $current_user->avatar;
                    }
                } else {
                    $avatar = $current_user->avatar;
                }
                $passed = Self::$_user->update_prefs(array(
                    'avatar' => $avatar,
                    'timezone' => Input::post('timezone'),
                    'date_format' => Input::post('dateFormat'),
                    'time_format' => Input::post('timeFormat'),
                    'page_limit' => Input::post('page_limit'),
                    ), $current_user->ID);
                Self::$_user->update(array('username' => Input::post('username'), 'user_group' => Input::post('groupSelect')), $current_user->ID);
                if ($passed) {
                    Issue::success('Preferences Updated.');
                } else {
                    Issue::warning('There was an error with your request, please try again.');
                }
                Self::$_template->select_option(Input::post('groupSelect'));
                Self::$_template->select_option(Input::post('timezone'));
                Self::$_template->select_option(Input::post('dateFormat'));
                Self::$_template->select_option(Input::post('timeFormat'));
                Self::$_template->select_option(Input::post('page_limit'));
                Self::$_template->select_option(Input::post('gender'));
            } else {
                Self::$_template->select_option(($current_user->user_group));
                Self::$_template->select_option(($current_user->timezone));
                Self::$_template->select_option(($current_user->date_format));
                Self::$_template->select_option(($current_user->time_format));
                Self::$_template->select_option(($current_user->page_limit));
                Self::$_template->select_option(($current_user->gender));
            }
            if (empty($avatar)) {
                $avatar = $current_user->avatar;
            }
            Self::$_template->set('AVATAR_SETTINGS', $avatar);
            $select = Self::$_template->standard_view('admin.group.select', Self::$_group->list_groups());
            Self::$_template->set('groupSelect', $select);
            $this->view('admin.user.edit', $current_user);
            exit();
        }
        $this->view('admin.user.list', $user->user_list());
        exit();
    }

    public function logs($sub = null, $data = null)
    {
        Debug::log('Controller initiated: '.__METHOD__.'.');
        Self::$_title = 'Admin - Logs';
        /*
         * @todo since i didn't want to tackled adding support 
         * for dynamically deleting different logs in one spot.
         */
        if ($sub == 'delete') {
            if (Check::id($data)) {
                Self::$_log->delete($data);
            }
        }
        if ($sub == 'view') {
            $current_log = Self::$_log->get_log($data);
            $this->view('admin.log', $current_log);
            exit();
        }
        $this->view('admin.log.bug.report.list', Self::$_log->bug_report_list());
        $this->view('admin.log.feedback.list', Self::$_log->feedback_list());
        $this->view('admin.log.error.list', Self::$_log->error_list());
        $this->view('admin.log.login.list', Self::$_log->login_list());
        exit();
    }

    public function subscriptions($sub = null, $data = null)
    {
        Debug::log('Controller initiated: '.__METHOD__.'.');
        Self::$_title = 'Admin - Email Subscribers';
        if ($sub == 'delete') {
            if (Input::exists('submit')) {
                $data = Input::post('S_');
            }
            foreach ($data as $key) {
                if (Self::$_subscribe->remove($key)) {
                    Issue::success('Subscriber removed.');
                } else {
                    Issue::error('There was an error with your request, please try again.');
                }
            }
            Self::$_log->delete($data);
        }
        if ($sub == 'add') {
            if (Input::exists('email')) {
                if (Self::$_subscribe->add(Input::get('email'))) {
                    Issue::success('Subscriber added!');
                } else {
                    Issue::error('There was an error with your request, please try again.');
                }
            }
        }
        $this->view('admin.subscribers.list', Self::$_log->subscriber_list());
        exit();
    }

    public function bug_reports($sub = null, $data = null)
    {
        Debug::log('Controller initiated: '.__METHOD__.'.');
        Self::$_title = 'Admin - Bug Reports';
        switch ($sub) {
            case 'view':
                $current_log = Self::$_log->get_bug_report($data);
                $this->view('admin.log.bug.report', $current_log);
                exit();
                break;
            case 'delete':
                if (Input::exists('submit')) {
                    $data[] = Input::post('BR_');
                }
                Self::$_log->delete($data);
                break;
            case 'clear':
                Self::$_log->clear('bug_report');
                break;
            default:
                break;
        }
        $this->view('admin.log.bug.report.list', Self::$_log->bug_report_list());
        exit();
    }

    public function feedback($sub = null, $data = null)
    {
        Debug::log('Controller initiated: '.__METHOD__.'.');
        Self::$_title = 'Admin - Feedback';
        switch ($sub) {
            case 'view':
                $current_log = Self::$_log->get_feedback($data);
                $this->view('admin.log.feedback', $current_log);
                exit();
                break;
            case 'delete':
                if (Input::exists('submit')) {
                    $data[] = Input::post('F_');
                }
                Self::$_log->delete($data);
                break;
            case 'clear':
                Self::$_log->clear('feedback');
                break;
            default:
                break;
        }
        $this->view('admin.log.feedback.list', Self::$_log->feedback_list());
        exit();
    }

    public function errors($sub = null, $data = null)
    {
        Debug::log('Controller initiated: '.__METHOD__.'.');
        Self::$_title = 'Admin - Errors';
        switch ($sub) {
            case 'view':
                $current_log = Self::$_log->get_error($data);
                $this->view('admin.log.error', $current_log);
                exit();
                break;
            case 'delete':
                if (Input::exists('submit')) {
                    $data[] = Input::post('E_');
                }
                Self::$_log->delete($data);
                break;
            case 'clear':
                Self::$_log->clear('error');
                break;
            default:
                break;
        }
        $this->view('admin.log.error.list', Self::$_log->error_list());
        exit();
    }

    public function logins($sub = null, $data = null)
    {
        Debug::log('Controller initiated: '.__METHOD__.'.');
        Self::$_title = 'Admin - Login Logs';
        switch ($sub) {
            case 'view':
                $this->view('admin.log.login', Self::$_log->get_log($data));
                exit();
                break;
            case 'delete':
                if (Input::exists('submit')) {
                    $data[] = Input::post('L_');
                }
                Self::$_log->delete($data);
                break;
            case 'clear':
                Self::$_log->clear('login');
                break;
            default:
                break;
        }
        $this->view('admin.log.login.list', Self::$_log->login_list());
        exit();
    }
}
