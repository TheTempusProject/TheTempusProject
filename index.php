<?php
/**
 * index.php.
 *
 * All traffic should be run through this page via get variables.
 *
 * @version 1.0
 *
 * @author  Joey Kimsey <joeyk4816@gmail.com>
 *
 * @link    https://github.com/JoeyK4816/TheTempusProject
 *
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html [GNU GENERAL PUBLIC LICENSE]
 */

namespace TheTempusProject;

use TempusProjectCore\Classes\Debug as Debug;
use TempusProjectCore\Core\Controller as Controller;
use TempusProjectCore\Core\Template as Template;
use TempusProjectCore\Classes\Session as Session;
use TempusProjectCore\Classes\Config as Config;
use TempusProjectCore\Classes\Cookie as Cookie;
use TempusProjectCore\Classes\Pagination as Pagination;
use TempusProjectCore\Classes\Issue as Issue;

require_once "init.php";

Debug::log('Initiating New Application');

class appload extends Controller
{
    public function __construct() 
    {
        parent::__construct();
        Self::$_log = $this->model('log');
        Self::$_user = $this->model('user');
        Self::$_blog = $this->model('blog');
        Self::$_group = $this->model('group');
        Self::$_message = $this->model('message');
        Self::$_session = $this->model('session');
        Self::$_subscribe = $this->model('subscribe');
        Self::$_comment = $this->model('comment');
        if (Self::$_session->authenticate() !== false) {
            Self::$_is_logged_in = true;
            Self::$_active_user = Self::$_session->user_data();
            Self::$_active_group = Self::$_session->group_data();
            Self::$_active_prefs = json_decode(Self::$_active_user->prefs);
            Self::$_is_member = Self::$_active_group->member;
            Self::$_is_mod = Self::$_active_group->mod_cp;
            Self::$_is_admin = Self::$_active_group->admin_cp;
        } else {
            Self::$_active_prefs = (object) Self::$_user->prefs();
        }
        Pagination::update_prefs(Self::$_active_prefs->page_limit);
        Pagination::generate();
        Self::$_template->add_filter('member', '#{MEMBER}(.*?){/MEMBER}#is', Self::$_is_member);
        Self::$_template->add_filter('mod', '#{MOD}(.*?){/MOD}#is', Self::$_is_mod);
        Self::$_template->add_filter('admin', '#{ADMIN}(.*?){/ADMIN}#is', Self::$_is_admin);
        if (Self::$_is_admin) {
            if (file_exists(Self::$_location . "install.php")) {
                if (!Debug::status()) {
                    Issue::error("You have not removed the installer. This is a security risk that should be corrected immediately.");
                } else {
                    Debug::warn("You have not removed the installer yet.");
                }
            }
        }
        if (Self::$_message->unread_count() > 0) {
            $data = array('MESSAGE_COUNT' => Self::$_message->unread_count());
            Self::$_unread = Template::standard_view('message_badge', $data);
        } else {
            Self::$_unread = '';
        }
        if (Self::$_is_logged_in) {
            $data = Self::$_message->recent();
            $stuff = Template::standard_view('message.recent', $data);
            Self::$_template->set('RECENT_MESSAGES', $stuff);
        } else {
            Self::$_template->set('RECENT_MESSAGES', '');
        }
        Self::$_template->set('SITENAME', Config::get('main/name'));
    }
}

//Instantiate a new instance of our application.
$appload = new appload();
$app = new \TempusProjectCore\App();