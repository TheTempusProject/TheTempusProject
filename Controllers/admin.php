<?php
/**
 * Controllers/admin.php
 *
 * This is the admin controller.
 *
 * @version 1.0
 *
 * @author  Joey Kimsey <JoeyKimsey@thetempusproject.com>
 *
 * @link    https://TheTempusProject.com
 *
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */

namespace TheTempusProject\Controllers;

use TempusProjectCore\Core\Controller;
use TempusProjectCore\Core\Installer;
use TempusProjectCore\Core\Template;
use TempusProjectCore\Classes\Config;
use TempusProjectCore\Classes\Debug;
use TempusProjectCore\Classes\Input;
use TempusProjectCore\Classes\Email;
use TempusProjectCore\Classes\Issue;
use TempusProjectCore\Classes\Check;
use TempusProjectCore\Classes\Image;
use TempusProjectCore\Classes\Log;

class Admin extends Controller
{
    private static $blog;
    private static $bugreport;
    private static $comment;
    private static $feedback;
    private static $group;
    private static $log;
    private static $message;
    private static $session;
    private static $subscribe;
    private static $tracking;
    private static $user;

    public function __construct()
    {
        self::$template->noFollow();
        self::$template->noIndex();
        self::$session = $this->model('session');
        if (!self::$isLoggedIn) {
            Issue::notice('You must be logged in to view this page.');
            exit();
        }
        if (!self::$isAdmin) {
            Issue::error('You do not have permission to view this page.');
            exit();
        }
        self::$template->setTemplate('admin');
        self::$blog = $this->model('blog');
        self::$bugreport = $this->model('bugreport');
        self::$comment = $this->model('comment');
        self::$feedback = $this->model('feedback');
        self::$group = $this->model('group');
        self::$log = $this->model('log');
        self::$message = $this->model('message');
        self::$subscribe = $this->model('subscribe');
        self::$tracking = $this->model('track');
        self::$user = $this->model('user');
    }

    public function __destruct()
    {
        Debug::log('Controller Destructing: ' . get_class($this));
        self::$session->updatePage(self::$title);
        $this->build();
        Debug::closeAllGroups();
    }

    public function index()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Admin - Home';
        $users = Template::standardView('admin.dashUsers', self::$user->recent(5));
        $comments = Template::standardView('admin.dashComments', self::$comment->recent('all', 5));
        $posts = Template::standardView('admin.dashPosts', self::$blog->recent(5));
        self::$template->set('userDash', $users);
        self::$template->set('blogDash', $posts);
        self::$template->set('commentDash', $comments);
        $this->view('admin.dash');
    }

    public function dependencies($sub = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Admin - Dependencies';
        $installer = new Installer;
        switch ($sub) {
            default:
                $composerJson = $installer->getComposerJson();
                $requiredPackages = $composerJson['require'];
                foreach ($requiredPackages as $name => $version) {
                    $versionsRequired[strtolower($name)] = $version;
                }

                $composerLock = $installer->getComposerLock();
                $installedPackages = $composerLock['packages'];
                foreach ($installedPackages as $package) {
                    $name = strtolower($package['name']);
                    $versionsInstalled[$name] = $package;
                }

                foreach ($versionsInstalled as $package) {
                    $name = strtolower($package['name']);
                    if (!empty($versionsRequired[$name])) {
                        $versionsInstalled[$name]['requiredVersion'] = $versionsRequired[$name];
                    } else {
                        $versionsInstalled[$name]['requiredVersion'] = 'sub-dependency';
                    }
                    $out[] = (object) $versionsInstalled[$name];
                }
                break;
        }
        $this->view('admin.dependencies', $out);
        exit();
    }

    public function installed($sub = null, $name = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Admin - Installed';
        $installer = new Installer;
        switch ($sub) {
            case 'view':
                $node = $installer->getNode($name);
                if ($node === false) {
                    $out = [
                        'name' => $name,
                        'installDate' => '',
                        'lastUpdate' => '',
                        'installStatus' => 'not installed',
                        'installedVersion' => '',
                        'installDB' => '',
                        'installPermissions' => '',
                        'installConfigs' => '',
                        'installResources' => '',
                        'installPreferences' => '',
                        'version' => ''
                    ];
                } else {
                    $out = array_merge(['version' => $installer->getModelVersion('Models', $name)], (array) $node);
                }
                
                $this->view('admin.installedView', $out);
                exit();
            case 'install':
                self::$template->set('MODEL', $name);
                if (!Input::exists('installHash')) {
                    $this->view('admin.install');
                    exit();
                }
                if (!$installer->installModel('Models', $name)) {
                    Issue::error('There was an error with the Installation.', $installer->getErrors());
                }
                break;
            case 'uninstall':
                self::$template->set('MODEL', $name);
                if (!Input::exists('uninstallHash')) {
                    $this->view('admin.uninstall');
                    exit();
                }
                if (!$installer->uninstallModel('Models', $name)) {
                    Issue::error('There was an error with the Installation.', $installer->getErrors());
                }
                break;
        }
        $models = $installer->getModelVersionList('Models');
        foreach ($models as $model) {
            $modelArray = (array) $model;
            $node = $installer->getNode($model->name);
            if ($node === false) {
                $out = [
                    'name' => $name,
                    'installDate' => '',
                    'lastUpdate' => '',
                    'installStatus' => 'not installed',
                    'installedVersion' => '',
                    'installDB' => '',
                    'installPermissions' => '',
                    'installConfigs' => '',
                    'installResources' => '',
                    'installPreferences' => '',
                    'version' => ''
                ];
            }
            $out[] = (object) array_merge($modelArray, $node);
        }
        
        $this->view('admin.installed', $out);
        exit();
    }

    /**
     * @todo add redirects on success.
     */
    public function groups($sub = null, $data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        self::$title = 'Admin - Groups';
        switch ($sub) {
            case 'view':
                $groupData = self::$group->findById($data);
                if ($groupData !== false) {
                    $this->view('admin.groupView', $groupData);
                    exit();
                }
                Issue::error('Group not found');
                break;

            case 'listmembers':
                $groupData = self::$group->findById($data);
                if ($groupData !== false) {
                    self::$template->set('groupName', $groupData->name);
                    $this->view('admin.groupListMembers', self::$group->listMembers($groupData->ID));
                    exit();
                }
                Issue::error('Group not found');
                break;

            case 'new':
                if (!Input::exists('submit')) {
                    $this->view('admin.groupNew');
                    exit();
                }
                if (!Check::form('newGroup')) {
                    Issue::error('There was an error with your request.', Check::userErrors());
                    $this->view('admin.groupNew');
                    exit();
                }
                if (self::$group->create(Input::post('name'), self::$group->formToJson(Input::post('pageLimit')))) {
                    Issue::success('Group created');
                } else {
                    Issue::error('There was an error creating your group.');
                }
                break;

            case 'edit':
                if (!Input::exists('submit')) {
                    $groupData = self::$group->findById($data);
                    self::$template->selectOption($groupData->pageLimit);
                    self::$template->selectRadio('uploadImages', $groupData->uploadImages_string);
                    self::$template->selectRadio('sendMessages', $groupData->sendMessages_string);
                    self::$template->selectRadio('feedback', $groupData->feedback_string);
                    self::$template->selectRadio('bugreport', $groupData->bugReport_string);
                    self::$template->selectRadio('member', $groupData->memberAccess_string);
                    self::$template->selectRadio('modCP', $groupData->modAccess_string);
                    self::$template->selectRadio('adminCP', $groupData->adminAccess_string);
                    $this->view('admin.groupEdit', $groupData);
                    exit();
                }
                if (!Check::form('newGroup')) {
                    Issue::error('There was an error with your request.', Check::userErrors());
                    $this->view('admin.groupNew');
                    exit();
                }
                if (self::$group->update($data, Input::post('name'), self::$group->formToJson(Input::post('pageLimit')))) {
                    Issue::success('Group updated');
                } else {
                    Issue::error('There was an error with your request.');
                }
                break;
            case 'delete':
                if (Input::exists('G_')) {
                    $data = Input::post('G_');
                }
                if (!self::$group->deleteGroup((array) $data)) {
                    Issue::error('There was an error with your request.');
                } else {
                    Issue::success('Group has been deleted');
                }
                break;
        }
        $this->view('admin.groupList', self::$group->listGroups());
        exit();
    }

    public function blog($sub = null, $data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        self::$title = 'Admin - Blog';
        switch ($sub) {
            case 'new':
                if (!Input::exists('submit')) {
                    $this->view('admin.blogNew');
                    exit();
                }
                if (!Check::form('newBlogPost')) {
                    Issue::error('There was an error with your request.', Check::userErrors());
                    break;
                }
                self::$blog->newPost(Input::post('title'), Input::post('blogPost'), Input::post('submit'));
                break;
            case 'edit':
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
                break;
            case 'view':
                $blogData = self::$blog->find($data);
                if ($blogData !== false) {
                    $this->view('admin.blogView', $blogData);
                    exit();
                }
                Issue::error('Post not found.');
                break;
            case 'delete':
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
                break;
            case 'preview':
                $this->view('admin.blogPreview', self::$blog->preview(Input::post('title'), Input::post('blogPost')));
                exit();
            default:
                break;
        }
        $this->view('admin.blogList', self::$blog->listPosts(['includeDrafts' => true]));
        exit();
    }

    public function contact($sub = null, $data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        self::$title = 'Admin - Contact';
        if (Input::exists('mailType')) {
            $params = [
                'subject' => Input::post('mailSubject'),
                'title'   => Input::post('mailTitle'),
                'message' => Input::post('mailMessage'),
            ];
            switch (Input::post('mailType')) {
                case 'registered':
                    $list = self::$user->userList();
                    foreach ($list as $recipient) {
                        Email::send($recipient->email, 'contact', $params, ['template' => true]);
                    }
                    Issue::success('Email(s) Sent');
                    break;
                case 'newsletter':
                    $list = self::$user->userList('newsletter');
                    foreach ($list as $recipient) {
                        //make unsub
                        Email::send($recipient->email, 'contact', $params, ['template' => true]);
                    }
                    Issue::success('Email(s) Sent');
                    break;
                case 'all':
                    $list = self::$user->userList();
                    foreach ($list as $recipient) {
                        //make unsub
                        Email::send($recipient->email, 'contact', $params, ['template' => true]);
                    }
                    $list = self::$subscribe->listSubscribers();
                    foreach ($list as $recipient) {
                        $params['confirmationCode'] = $recipient->confirmationCode;
                        Email::send($recipient->email, 'contact', $params, ['template' => true, 'unsubscribe' => true]);
                    }
                    Issue::success('Email(s) Sent');
                    break;
                case 'opt':
                    $list = self::$user->userList('newsletter');
                    foreach ($list as $recipient) {
                        //make unsub
                        Email::send($recipient->email, 'contact', $params, ['template' => true]);
                    }
                    $list = self::$subscribe->listSubscribers();
                    foreach ($list as $recipient) {
                        $params['confirmationCode'] = $recipient->confirmationCode;
                        Email::send($recipient->email, 'contact', $params, ['template' => true, 'unsubscribe' => true]);
                    }
                    Issue::success('Email(s) Sent');
                    break;
                case 'subscribers':
                    $list = self::$subscribe->listSubscribers();
                    foreach ($list as $recipient) {
                        $params['confirmationCode'] = $recipient->confirmationCode;
                        Email::send($recipient->email, 'contact', $params, ['template' => true, 'unsubscribe' => true]);
                    }
                    Issue::success('Email(s) Sent');
                    break;
                default:
                    Issue::error('Invalid Request');
                    break;
            }
        }
        $this->view('admin.contact');
    }

    public function comments($sub = null, $data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        self::$title = 'Admin - Comments';
        switch ($sub) {
            case 'edit':
                if (!Input::exists('submit')) {
                    $this->view('admin.commentEdit', self::$comment->findById($data));
                    exit();
                }
                if (!Check::form('editComment')) {
                    Issue::error('There was an error with your request.', Check::userErrors());
                    $this->view('admin.commentEdit', self::$comment->findById($data));
                    exit();
                }
                if (self::$comment->update($data, Input::post('comment'))) {
                    Issue::success('Comment updated');
                } else {
                    $this->view('admin.commentEdit', self::$comment->findById($data));
                    exit();
                }
                break;
            case 'view':
                $commentData = self::$comment->findById($data);
                if ($commentData !== false) {
                    $this->view('admin.comment', $commentData);
                    exit();
                }
                Issue::error('Comment not found.');
                break;
            case 'delete':
                if ($data == null) {
                    if (!Input::exists('C_')) {
                        break;
                    }
                    $data = Input::post('C_');
                }
                if (!self::$comment->delete((array) $data)) {
                    Issue::error('There was an error with your request.');
                } else {
                    Issue::success('Comment has been deleted');
                }
                break;
            case 'blog':
                $commentData = self::$comment->display(25, 'blog', $data);
                if ($commentData !== false) {
                    self::$template->set('count', self::$comment->count('blog', $data));
                    $this->view('admin.blogComments', $commentData);
                    exit();
                }
                Issue::notice('No comments found.');
                break;
        }
        $this->view('admin.commentRecent', self::$comment->recent());
        exit();
    }

    public function settings()
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        self::$title = 'Admin - Settings';
        $installer = new Installer;
        $a = Input::exists('submit');
        self::$template->set('TIMEZONELIST', self::$template->standardView('timezoneDropdown'));
        if ($a) {
            Config::updateConfig('main', 'name', Input::post('name'));
            Config::updateConfig('main', 'template', Input::post('template'));
            Config::updateConfig('main', 'loginLimit', (int) Input::post('loginLimit'));
            Config::updateConfig('main', 'logo', Input::post('logo'));
            Config::updateConfig('main', 'timezone', Input::post('timezone'));
            Config::updateConfig('main', 'pageLimit', (int) Input::post('pageLimit'));
            Config::updateConfig('uploads', 'enabled', Input::post('uploads'));
            Config::updateConfig('uploads', 'maxFileSize', (int) Input::post('fileSize'));
            Config::updateConfig('uploads', 'maxImageSize', (int) Input::post('imageSize'));
            Config::updateConfig('cookie', 'cookieExpiry', (int) Input::post('cookieExpiry'));
            Config::updateConfig('feedback', 'enabled', Input::post('logF'));
            Config::updateConfig('logging', 'errors', Input::post('logE'));
            Config::updateConfig('logging', 'logins', Input::post('logL'));
            Config::updateConfig('bugreports', 'enabled', Input::post('logBR'));
            Config::updateConfig('group', 'defaultGroup', Input::post('groupSelect'));
            Config::saveConfig();
        }
        $select = self::$template->standardView('admin.groupSelect', self::$group->listGroups());
        self::$template->set('groupSelect', $select);
        self::$template->set('NAME', $a ? Input::post('name') : Config::get('main/name'));
        self::$template->set('TEMPLATE', $a ? Input::post('template') : Config::get('main/template'));
        self::$template->set('maxFileSize', $a ? Input::post('fileSize') : Config::get('uploads/maxFileSize'));
        self::$template->set('maxImageSize', $a ? Input::post('imageSize') : Config::get('uploads/maxImageSize'));
        self::$template->set('cookieExpiry', $a ? Input::post('cookieExpiry') : Config::get('cookie/cookieExpiry'));
        self::$template->set('LIMIT', $a ? Input::post('loginLimit') : Config::get('main/loginLimit'));
        self::$template->selectOption($a ? Input::post('groupSelect') : Config::get('group/defaultGroup'));
        self::$template->selectOption($a ? Input::post('timezone') : Config::get('main/timezone'));
        self::$template->selectOption($a ? Input::post('pageLimit') : Config::get('main/pageLimit'));
        self::$template->selectRadio('feedback', $a ? Input::post('logF') : Config::getString('feedback/enabled'));
        self::$template->selectRadio('errors', $a ? Input::post('logE') : Config::getString('logging/errors'));
        self::$template->selectRadio('logins', $a ? Input::post('logL') : Config::getString('logging/logins'));
        self::$template->selectRadio('bugReports', $a ? Input::post('logBR') : Config::getString('bugreports/enabled'));
        self::$template->selectRadio('uploads', $a ? Input::post('uploads') : Config::getString('uploads/enabled'));
        self::$template->set('securityHash', $installer->getNode('installHash'));
        $this->view('admin.settings');
        exit();
    }

    public function users($sub = null, $data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        self::$title = 'Admin - Users';
        if (Input::post('submit') == 'delete') {
            $sub = 'delete';
        }
        if (Input::post('submit') == 'edit') {
            $sub = 'edit';
        }
        switch ($sub) {
            case 'delete':
                if (Input::exists('submit')) {
                    $data = Input::post('U_');
                }
                if (self::$user->delete((array) $data)) {
                    Issue::success('User Deleted');
                } else {
                    Issue::error('There was an error deleting that user');
                }
                break;
            case 'view':
                if (!empty($data)) {
                    $userData = self::$user->get($data);
                    if ($userData !== false) {
                        $this->view('admin.userView', $userData);
                        exit();
                    }
                    Issue::error('User not found.');
                }
                break;
            case 'edit':
                if (empty($data) && Input::exists('U_')) {
                    $data = Input::post('U_');
                }
                if (!Check::id($data)) {
                    Issue::error('invalid user.');
                    exit();
                }
                $currentUser = self::$user->get($data);
                if (Input::exists('submit') && Input::post('submit') != 'edit') {
                    if (Input::exists('avatar')) {
                        if (Image::upload('avatar', self::$activeUser->username)) {
                            $avatar = 'Uploads/Images/' . self::$activeUser->username . '/' . Image::last();
                        } else {
                            $avatar = $currentUser->avatar;
                        }
                    } else {
                        $avatar = $currentUser->avatar;
                    }

                    $passed = self::$user->updatePrefs([
                        'avatar'      => $avatar,
                        'timezone'    => Input::post('timezone'),
                        'gender'    => Input::post('gender'),
                        'dateFormat' => Input::post('dateFormat'),
                        'timeFormat' => Input::post('timeFormat'),
                        'pageLimit'  => Input::post('pageLimit'),
                    ], $currentUser->ID);
                    self::$user->update(['username' => Input::post('username'), 'userGroup' => Input::post('groupSelect')], $currentUser->ID);
                    if ($passed) {
                        Issue::success('User Updated.');
                    } else {
                        Issue::warning('There was an error with your request, please try again.');
                    }
                    self::$template->selectOption(Input::post('groupSelect'));
                    self::$template->selectOption(Input::post('timezone'));
                    self::$template->selectOption(Input::post('dateFormat'));
                    self::$template->selectOption(Input::post('timeFormat'));
                    self::$template->selectOption(Input::post('pageLimit'));
                    self::$template->selectOption(Input::post('gender'));
                } else {
                    self::$template->selectOption(($currentUser->userGroup));
                    self::$template->selectOption(($currentUser->timezone));
                    self::$template->selectOption(($currentUser->dateFormat));
                    self::$template->selectOption(($currentUser->timeFormat));
                    self::$template->selectOption(($currentUser->pageLimit));
                    self::$template->selectOption(($currentUser->gender));
                }
                if (empty($avatar)) {
                    $avatar = $currentUser->avatar;
                }
                self::$template->set('AVATAR_SETTINGS', $avatar);
                self::$template->set('TIMEZONELIST', self::$template->standardView('timezoneDropdown'));
                $select = self::$template->standardView('admin.groupSelect', self::$group->listGroups());
                self::$template->set('groupSelect', $select);
                $this->view('admin.userEdit', $currentUser);
                exit();
        }
        $this->view('admin.userList', self::$user->userList());
        exit();
    }

    public function logs($sub = null, $data = null)
    {
        $regex = "#\<ul(.*)id=\"log-menu\" class=\"collapse\"\>#i";
        $replace = "<ul$1id=\"$yyyyyy\"$2class=\"\">";
        self::$template->addFilter('logui', $regTop, $repTop, true);
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        self::$title = 'Admin - Logs';
        /*
         * @todo since i didn't want to tackled adding support
         * for dynamically deleting different logs in one spot.
         */
        switch ($sub) {
            case 'delete':
                if (self::$log->delete($data) === true) {
                    Issue::success('Log Deleted');
                } else {
                    Issue::error('There was an error with your request.');
                }
                break;
            
            case 'view':
                $logData = self::$log->getLog($data);
                if ($logData !== false) {
                    $this->view('admin.log', self::$log->getLog($data));
                    exit();
                }
                Issue::error('Log not found.');
                break;
        }
        $this->view('admin.logAdminList', self::$log->adminList());
        $this->view('admin.logErrorList', self::$log->errorList());
        $this->view('admin.logLoginList', self::$log->loginList());
        exit();
    }

    public function subscriptions($sub = null, $data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        self::$title = 'Admin - Email Subscribers';
        switch ($sub) {
            case 'delete':
                if (Input::exists('submit')) {
                    $data = Input::post('S_');
                }
                if (self::$subscribe->remove((array) $data)) {
                    Issue::success('Subscriber removed.');
                    break;
                }
                Issue::error('There was an error with your request, please try again.');
                break;
            case 'add':
                if (!Check::form('subscribe')) {
                    Issue::error('There was an error with your request.', Check::userErrors());
                    break;
                }
                if (!self::$subscribe->add(Input::post('email'))) {
                    Issue::error('There was an error with your request, please try again.');
                    break;
                }
                Issue::success('Subscriber added.');
                break;
        }
        $this->view('admin.subscribersList', self::$subscribe->listSubscribers());
        exit();
    }

    public function admin($sub = null, $data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        self::$title = 'Admin - Admin Logs';
        switch ($sub) {
            case 'view':
                $this->view('admin.logAdmin', self::$log->get($data));
                exit();
        }
        $this->view('admin.logAdminList', self::$log->adminList());
        exit();
    }

    public function bugReports($sub = null, $data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        self::$title = 'Admin - Bug Reports';
        switch ($sub) {
            case 'view':
                $reportData = self::$bugreport->get($data);
                if ($reportData !== false) {
                    $this->view('admin.bugreport', $reportData);
                    exit();
                }
                Issue::error('Report not found.');
                break;
                
            case 'delete':
                if (Input::exists('submit')) {
                    $data = Input::post('BR_');
                }
                if (self::$bugreport->delete((array) $data)) {
                    Issue::success('Bug Report Deleted');
                } else {
                    Issue::error('There was an error with your request.');
                }
                break;
            case 'clear':
                self::$bugreport->clear();
                break;
        }
        $this->view('admin.bugreportList', self::$bugreport->listReports());
        exit();
    }

    public function feedback($sub = null, $data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        self::$title = 'Admin - Feedback';
        switch ($sub) {
            case 'view':
                $this->view('admin.feedback', self::$feedback->get($data));
                exit();
            case 'delete':
                if (Input::exists('submit')) {
                    $data = Input::post('F_');
                }
                if (self::$feedback->delete((array) $data)) {
                    Issue::success('feedback deleted');
                } else {
                    Issue::error('There was an error with your request.');
                }
                break;
            case 'clear':
                self::$feedback->clear();
                break;
            default:
                break;
        }
        $this->view('admin.feedbackList', self::$feedback->getList());
        exit();
    }

    public function errors($sub = null, $data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        self::$title = 'Admin - Errors';
        switch ($sub) {
            case 'view':
                $this->view('admin.logError', self::$log->getError($data));
                exit();
            case 'delete':
                if (Input::exists('submit')) {
                    $data[] = Input::post('E_');
                }
                if (self::$log->delete((array) $data)) {
                    Issue::success('error log deleted');
                } else {
                    Issue::error('There was an error with your request.');
                }
                break;
            case 'clear':
                self::$log->clear('error');
                break;
            default:
                break;
        }
        $this->view('admin.logErrorList', self::$log->errorList());
        exit();
    }

    public function logins($sub = null, $data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        self::$title = 'Admin - Login Logs';
        switch ($sub) {
            case 'view':
                $this->view('admin.logLogin', self::$log->get($data));
                exit();

            case 'delete':
                if (Input::exists('L_')) {
                    $data = Input::post('L_');
                }
                if (self::$log->delete((array) $data)) {
                    Issue::success('login log deleted');
                } else {
                    Issue::error('There was an error with your request.');
                }
                break;

            case 'clear':
                self::$log->clear('login');
                Issue::success('Login Logs Cleared');
                break;
        }

        $this->view('admin.logLoginList', self::$log->loginList());
        exit();
    }
}
