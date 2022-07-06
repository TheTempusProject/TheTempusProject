<?php
/**
 * Controllers/admin.php
 *
 * This is the admin controller.
 *
 * @version 1.0
 * @author  Joey Kimsey <JoeyKimsey@thetempusproject.com>
 * @link    https://TheTempusProject.com
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
use TempusProjectCore\Template\Issues;
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
        $users = Views::standardView('admin.dashUsers', self::$user->recent(5));
        $comments = Views::standardView('admin.dashComments', self::$comment->recent('all', 5));
        $posts = Views::standardView('admin.dashPosts', self::$blog->recent(5));
        Components::set('userDash', $users);
        Components::set('blogDash', $posts);
        Components::set('commentDash', $comments);
        Views::view('admin.dash');
    }
    public function tickets($sub = null, $data = null, $data2 = null)
    {
        Debug::log('Controller initiated: '.__METHOD__.'.');
        self::$title = 'Admin - Tickets';
        Views::view('nav.admin.ticket');
        switch ($sub) {
            case 'status':
                Views::view('nav.admin.ticket.status');
                if (!empty($data)) {
                    Views::view('admin.ticket.list', self::$ticket->listTickets('status', $data));
                } else {
                    Views::view('admin.ticket.list', self::$ticket->listTickets('newest'));
                }
                exit();
            case 'category':
                Views::view('nav.admin.ticket.category');
                if (!empty($data)) {
                    Views::view('admin.ticket.list', self::$ticket->listTickets('category', $data));
                } else {
                    Views::view('admin.ticket.list', self::$ticket->listTickets('newest'));
                }
                exit();
            case 'project':
                Views::view('nav.admin.ticket.project');
                if (!empty($data)) {
                    Views::view('admin.ticket.list', self::$ticket->listTickets('project', $data));
                } else {
                    Views::view('admin.ticket.list', self::$ticket->listTickets('newest'));
                }
                exit();
            case 'newest':
                Views::view('admin.ticket.list', self::$ticket->listTickets('newest'));
                exit();
            case 'oldest':
                Views::view('admin.ticket.list', self::$ticket->listTickets('oldest'));
                exit();
            case 'list':
                Views::view('admin.ticket.list', self::$ticket->listTickets($data, $data2));
                exit();
            case 'view':
                $ticketData = self::$ticket->get($data);
                if ($ticketData !== false) {
                    if (Input::exists('submit')) {
                        if (!Forms::check('newComment')) {
                            Issue::error('There was a problem posting your comment.', Check::userErrors());
                        } elseif (self::$comment->create('ticket', $ticketData->ID, Input::post('comment'))) {
                            Issue::success('Comment posted');
                        } else {
                            Issue::error('There was an error posting you comment, please try again.');
                        }
                    }
                    Components::set('NEWCOMMENT', Views::standardView('comment.new'));
                    Components::set('count', self::$comment->count('ticket', $ticketData->ID));
                    Components::set('COMMENTS', Views::standardView('comment.list', self::$comment->display(25, 'ticket', $ticketData->ID)));
                    Views::view('admin.ticket.view', $ticketData);
                    exit();
                }
                Issue::error('Ticket not found.');
                break;
                // self::$comment->create('ticket', $post->ID, Input::post('comment'))) {
            case 'new':
                if (Input::exists('submit')) {
                    if (self::$ticket->create()) {
                        Issue::success('Ticket created');
                    }
                } else {
                    Components::set('categorySelect', Views::standardView('admin.ticket.select.category'));
                    Components::set('projectSelect', Views::standardView('admin.ticket.select.project'));
                    Views::view('admin.ticket.new');
                    exit();
                }
                break;
            case 'edit':
                if (Input::exists('submit')) {
                    if (self::$ticket->update($data)) {
                        Issue::success('Ticket updated');
                    }
                } else {
                    $ticket = self::$ticket->get($data);
                    Forms::selectOption($ticket->project);
                    Forms::selectOption($ticket->category);
                    Forms::selectOption($ticket->status);
                    Components::set('categorySelect', Views::standardView('admin.ticket.select.category'));
                    Components::set('projectSelect', Views::standardView('admin.ticket.select.project'));
                    Components::set('statusSelect', Views::standardView('admin.ticket.select.status'));
                    Views::view('admin.ticket.edit', $ticket);
                    exit();
                }
                break;
            default:
                Views::view('admin.ticket.list', self::$ticket->listTickets());
                exit();
        }
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
                    $out[] = (object) [
                        'name' => $model->name,
                        'installDate' => 'null',
                        'lastUpdate' => 'null',
                        'currentVersion' => 'not installed',
                        'installedVersion' => $installer->getModelVersion('models', $model->name),
                        'installDB' => 'not Installed',
                        'installPermissions' => 'not Installed',
                        'installConfigs' => 'not Installed',
                        'installResources' => 'not Installed',
                        'installPreferences' => 'not Installed'
                    ];
                } else {
                    $out[] = (object) [
                        'name' => $node['name'],
                        'installDate' => $node['installDate'],
                        'lastUpdate' => $node['lastUpdate'],
                        'currentVersion' => $node['currentVersion'],
                        'installedVersion' => $installer->getModelVersion('models', $node['name']),
                        'installDB' => $node['installDB'],
                        'installPermissions' => $node['installPermissions'],
                        'installConfigs' => $node['installConfigs'],
                        'installResources' => $node['installResources'],
                        'installPreferences' => $node['installPreferences']
                    ];
                }
                Views::view('admin.installedView', $out);
                exit();
            case 'install':
                Components::set('MODEL', $name);
                if (!Input::exists('installHash')) {
                    Views::view('admin.install');
                    exit();
                }
                if (!$installer->installModel('Models', $name)) {
                    Issue::error('There was an error with the Installation.', $installer->getErrors());
                }
                exit();
            case 'uninstall':
                Components::set('MODEL', $name);
                if (!Input::exists('uninstallHash')) {
                    Views::view('admin.uninstall');
                    exit();
                }
                if (!$installer->uninstallModel('Models', $name)) {
                    Issue::error('There was an error with the Installation.', $installer->getErrors());
                }
                exit();
        }
        $models = $installer->getModelVersionList('Models');
        foreach ($models as $model) {
            $node = $installer->getNode($model->name);
            if ($node === false) {
                $out[] = (object) [
                    'name' => $model->name,
                    'installDate' => 'null',
                    'lastUpdate' => 'null',
                    'currentVersion' => 'not installed',
                    'installedVersion' => $installer->getModelVersion('models', $model->name)
                ];
            } else {
                $out[] = (object) [
                    'name' => $node['name'],
                    'installDate' => $node['installDate'],
                    'lastUpdate' => $node['lastUpdate'],
                    'currentVersion' => $node['currentVersion'],
                    'installedVersion' => $installer->getModelVersion('models', $node['name'])
                ];
            }
        }
        Views::view('admin.installed', $out);
        exit();
    }
    public function redirects()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Admin - Redirects';
        Views::view('admin.dash');
    }
    public function tracking()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Admin - Tracking';
        Views::view('admin.dash');
    }
    public function api()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Admin - API';
        Views::view('admin.dash');
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
        Views::view('admin.dependencies', $out);
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
                        'currentVersion' => '',
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
                
                Views::view('admin.installedView', $out);
                exit();
            case 'install':
                Components::set('MODEL', $name);
                if (!Input::exists('installHash')) {
                    Views::view('admin.install');
                    exit();
                }
                if (!$installer->installModel('Models', $name)) {
                    Issue::error('There was an error with the Installation.', $installer->getErrors());
                }
                break;
            case 'uninstall':
                Components::set('MODEL', $name);
                if (!Input::exists('uninstallHash')) {
                    Views::view('admin.uninstall');
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
                $node = [
                    'name' => $model->name,
                    'installDate' => '',
                    'lastUpdate' => '',
                    'installStatus' => 'not installed',
                    'installedVersion' => '',
                    'installDB' => '',
                    'installPermissions' => '',
                    'installConfigs' => '',
                    'installResources' => '',
                    'installPreferences' => '',
                    'currentVersion' => '',
                    'version' => $installer->getModelVersion('Models', $model->name)
                ];
            }
            $out[] = (object) array_merge($modelArray, $node);
        }
        
        Views::view('admin.installed', $out);
        exit();
    }
    public function groups($sub = null, $data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        self::$title = 'Admin - Groups';
        switch ($sub) {
            case 'view':
                $groupData = self::$group->findById($data);
                if ($groupData !== false) {
                    Views::view('admin.groupView', $groupData);
                    exit();
                }
                Issue::error('Group not found');
                break;

            case 'listmembers':
                $groupData = self::$group->findById($data);
                if ($groupData !== false) {
                    Components::set('groupName', $groupData->name);
                    Views::view('admin.groupListMembers', self::$group->listMembers($groupData->ID));
                    exit();
                }
                Issue::error('Group not found');
                break;

            case 'new':
                if (!Input::exists('submit')) {
                    Views::view('admin.groupNew');
                    exit();
                }
                if (!Forms::check('newGroup')) {
                    Issue::error('There was an error with your request.', Check::userErrors());
                    Views::view('admin.groupNew');
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
                    Forms::selectOption($groupData->pageLimit);
                    Forms::selectRadio('uploadImages', $groupData->uploadImages_string);
                    Forms::selectRadio('sendMessages', $groupData->sendMessages_string);
                    Forms::selectRadio('feedback', $groupData->feedback_string);
                    Forms::selectRadio('bugreport', $groupData->bugReport_string);
                    Forms::selectRadio('member', $groupData->memberAccess_string);
                    Forms::selectRadio('modCP', $groupData->modAccess_string);
                    Forms::selectRadio('adminCP', $groupData->adminAccess_string);
                    Views::view('admin.groupEdit', $groupData);
                    exit();
                }
                if (!Forms::check('newGroup')) {
                    Issue::error('There was an error with your request.', Check::userErrors());
                    Views::view('admin.groupNew');
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
        Views::view('admin.groupList', self::$group->listGroups());
        exit();
    }
    public function blog($sub = null, $data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        self::$title = 'Admin - Blog';
        switch ($sub) {
            case 'new':
                if (!Input::exists('submit')) {
                    Views::view('admin.blogNew');
                    exit();
                }
                if (!Forms::check('newBlogPost')) {
                    Issue::error('There was an error with your request.', Check::userErrors());
                    break;
                }
                self::$blog->newPost(Input::post('title'), Input::post('blogPost'), Input::post('submit'));
                break;
            case 'edit':
                if (!Input::exists('submit')) {
                    Views::view('admin.blogEdit', self::$blog->find($data));
                    exit();
                }
                if (Input::post('submit') == 'preview') {
                    Views::view('admin.blogPreview', self::$blog->preview(Input::post('title'), Input::post('blogPost')));
                    exit();
                }
                if (!Forms::check('editBlogPost')) {
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
                    Views::view('admin.blogView', $blogData);
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
                Views::view('admin.blogPreview', self::$blog->preview(Input::post('title'), Input::post('blogPost')));
                exit();
            default:
                break;
        }
        Views::view('admin.blogList', self::$blog->listPosts(['includeDrafts' => true]));
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
        Views::view('admin.contact');
    }
    public function comments($sub = null, $data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        self::$title = 'Admin - Comments';
        switch ($sub) {
            case 'edit':
                if (!Input::exists('submit')) {
                    Views::view('admin.commentEdit', self::$comment->findById($data));
                    exit();
                }
                if (!Forms::check('editComment')) {
                    Issue::error('There was an error with your request.', Check::userErrors());
                    Views::view('admin.commentEdit', self::$comment->findById($data));
                    exit();
                }
                if (self::$comment->update($data, Input::post('comment'))) {
                    Issue::success('Comment updated');
                } else {
                    Views::view('admin.commentEdit', self::$comment->findById($data));
                    exit();
                }
                break;
            case 'view':
                $commentData = self::$comment->findById($data);
                if ($commentData !== false) {
                    Views::view('admin.comment', $commentData);
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
                    Components::set('count', self::$comment->count('blog', $data));
                    Views::view('admin.blogComments', $commentData);
                    exit();
                }
                Issue::notice('No comments found.');
                break;
        }
        Views::view('admin.commentRecent', self::$comment->recent());
        exit();
    }
    public function settings()
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        self::$title = 'Admin - Settings';
        $installer = new Installer;
        $a = Input::exists('submit');
        Components::set('TIMEZONELIST', Views::standardView('timezoneDropdown'));
        if (Input::exists('logo') && Image::upload('logo', 'System')) {
            $logo = 'Uploads/Images/System/' . Image::last();
        } else {
            $logo = Config::get('main/logo');
        }
        if ($a) {
            Config::updateConfig('main', 'name', Input::post('name'));
            Config::updateConfig('main', 'template', Input::post('template'));
            Config::updateConfig('main', 'loginLimit', (int) Input::post('loginLimit'));
            Config::updateConfig('main', 'logo', $logo);
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
        $select = Views::standardView('admin.groupSelect', self::$group->listGroups());
        Components::set('groupSelect', $select);
        Components::set('LOGO', $logo);
        Components::set('NAME', $a ? Input::post('name') : Config::get('main/name'));
        Components::set('TEMPLATE', $a ? Input::post('template') : Config::get('main/template'));
        Components::set('maxFileSize', $a ? Input::post('fileSize') : Config::get('uploads/maxFileSize'));
        Components::set('maxImageSize', $a ? Input::post('imageSize') : Config::get('uploads/maxImageSize'));
        Components::set('cookieExpiry', $a ? Input::post('cookieExpiry') : Config::get('cookie/cookieExpiry'));
        Components::set('LIMIT', $a ? Input::post('loginLimit') : Config::get('main/loginLimit'));
        Forms::selectOption($a ? Input::post('groupSelect') : Config::get('group/defaultGroup'));
        Forms::selectOption($a ? Input::post('timezone') : Config::get('main/timezone'));
        Forms::selectOption($a ? Input::post('pageLimit') : Config::get('main/pageLimit'));
        Forms::selectRadio('feedback', $a ? Input::post('logF') : Config::getString('feedback/enabled'));
        Forms::selectRadio('errors', $a ? Input::post('logE') : Config::getString('logging/errors'));
        Forms::selectRadio('logins', $a ? Input::post('logL') : Config::getString('logging/logins'));
        Forms::selectRadio('bugReports', $a ? Input::post('logBR') : Config::getString('bugreports/enabled'));
        Forms::selectRadio('uploads', $a ? Input::post('uploads') : Config::getString('uploads/enabled'));
        Components::set('securityHash', $installer->getNode('installHash'));
        Views::view('admin.settings');
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
                        Views::view('admin.userView', $userData);
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
                        if (Image::upload('avatar', App::$activeUser->username)) {
                            $avatar = 'Uploads/Images/' . App::$activeUser->username . '/' . Image::last();
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
                    Forms::selectOption(Input::post('groupSelect'));
                    Forms::selectOption(Input::post('timezone'));
                    Forms::selectOption(Input::post('dateFormat'));
                    Forms::selectOption(Input::post('timeFormat'));
                    Forms::selectOption(Input::post('pageLimit'));
                    Forms::selectOption(Input::post('gender'));
                } else {
                    Forms::selectOption(($currentUser->userGroup));
                    Forms::selectOption(($currentUser->timezone));
                    Forms::selectOption(($currentUser->dateFormat));
                    Forms::selectOption(($currentUser->timeFormat));
                    Forms::selectOption(($currentUser->pageLimit));
                    Forms::selectOption(($currentUser->gender));
                }
                if (empty($avatar)) {
                    $avatar = $currentUser->avatar;
                }
                Components::set('AVATAR_SETTINGS', $avatar);
                Components::set('TIMEZONELIST', Views::standardView('timezoneDropdown'));
                $select = Views::standardView('admin.groupSelect', self::$group->listGroups());
                Components::set('groupSelect', $select);
                Views::view('admin.userEdit', $currentUser);
                exit();
        }
        Views::view('admin.userList', self::$user->userList());
        exit();
    }
    public function logs($sub = null, $data = null)
    {
        $regex = "#\<ul(.*)id=\"log-menu\" class=\"collapse\"\>#i";
        $replace = "<ul$1id=\"$yyyyyy\"$2class=\"\">";
        Filters::add('logui', $regTop, $repTop, true);
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        self::$title = 'Admin - Logs';
        Filters::add('logMenu', "#<ul id=\"log-menu\" class=\"collapse\">#is", "<ul id=\"log-menu\" class=\"\">", true);
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
                    Views::view('admin.log', self::$log->getLog($data));
                    exit();
                }
                Issue::error('Log not found.');
                break;
        }
        Views::view('admin.logAdminList', self::$log->adminList());
        Views::view('admin.logErrorList', self::$log->errorList());
        Views::view('admin.logLoginList', self::$log->loginList());
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
                if (!Forms::check('subscribe')) {
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
        Views::view('admin.subscribersList', self::$subscribe->listSubscribers());
        exit();
    }
    public function admin($sub = null, $data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        self::$title = 'Admin - Admin Logs';
        Filters::add('logMenu', "#<ul id=\"log-menu\" class=\"collapse\">#is", "<ul id=\"log-menu\" class=\"\">", true);
        switch ($sub) {
            case 'view':
                Views::view('admin.logAdmin', self::$log->get($data));
                exit();
        }
        Views::view('admin.logAdminList', self::$log->adminList());
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
                    Views::view('admin.bugreport', $reportData);
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
        Views::view('admin.bugreportList', self::$bugreport->listReports());
        exit();
    }
    public function feedback($sub = null, $data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        self::$title = 'Admin - Feedback';
        switch ($sub) {
            case 'view':
                Views::view('admin.feedback', self::$feedback->get($data));
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
        Views::view('admin.feedbackList', self::$feedback->getList());
        exit();
    }
    public function errors($sub = null, $data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        self::$title = 'Admin - Errors';
        Filters::add('logMenu', "#<ul id=\"log-menu\" class=\"collapse\">#is", "<ul id=\"log-menu\" class=\"\">", true);
        switch ($sub) {
            case 'view':
                Views::view('admin.logError', self::$log->getError($data));
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
        Views::view('admin.logErrorList', self::$log->errorList());
        exit();
    }
    public function logins($sub = null, $data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        self::$title = 'Admin - Login Logs';
        Filters::add('logMenu', "#<ul id=\"log-menu\" class=\"collapse\">#is", "<ul id=\"log-menu\" class=\"\">", true);
        switch ($sub) {
            case 'view':
                Views::view('admin.logLogin', self::$log->get($data));
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

        Views::view('admin.logLoginList', self::$log->loginList());
        exit();
    }
    public function redirects()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Admin - Redirects';
        Views::view('admin.dash');
    }
    public function tracking()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Admin - Tracking';
        Views::view('admin.dash');
    }
    public function api()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Admin - API';
        Views::view('admin.dash');
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
        Views::view('admin.dependencies', $out);
        exit();
    }
}