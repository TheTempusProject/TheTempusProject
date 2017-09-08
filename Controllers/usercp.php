<?php
/**
 * Controllers/userCP.php.
 *
 * This is the userCP controller.
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
use TempusProjectCore\Classes\Debug as Debug;
use TempusProjectCore\Classes\Issue as Issue;
use TempusProjectCore\Classes\Input as Input;
use TempusProjectCore\Classes\Image as Image;
use TempusProjectCore\Classes\Email as Email;
use TempusProjectCore\Classes\Hash as Hash;
use TempusProjectCore\Classes\Code as Code;
use TempusProjectCore\Classes\Check as Check;

class usercp extends Controller
{
    public function __construct()
    {
        parent::__construct(); 
        Debug::group("Controller: " . get_class($this), 1);
        Self::$_template->activePageSelect('nav.usercp');
        Self::$_template->noIndex();
        if (!Self::$_is_logged_in) {
            Issue::notice('You must be logged in to view this page!');
            exit();
        }
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
        Self::$_title = 'User Control Panel';
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        $this->view('user', Self::$_active_user);
        exit();
    }
    public function Settings()
    {
        Self::$_title = 'Preferences.';
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        if (Input::exists('submit')) {
            if (Input::exists('avatar')) {
                if (Image::upload_image('avatar', Self::$_active_user->username)) {
                    $avatar = 'Images/Uploads/' . Self::$_active_user->username . '/' . Image::last();
                } else {
                    $avatar = 'Images/defaultAvatar.png';
                }
            } else {
                $avatar = Self::$_active_prefs->avatar;
            }
            Self::$_user->update_prefs(array(
                'avatar' =>      $avatar,
                "timezone"=>     Input::post('timezone'),
                "date_format"=>  Input::post('dateFormat'),
                "time_format"=>  Input::post('timeFormat'),
                "page_limit"=>  Input::post('page_limit'),
                "email"=>  Input::post('updates'),
                "gender"=>  Input::post('gender'),
                "newsletter"=>   Input::post('newsletter')
            ), Self::$_active_user->ID);
        }
        if ((Input::exists('submit') && (Input::post('updates') === 'true')) || ((Self::$_active_prefs->email === 'true') && (!Input::exists('submit')))) {
            Self::$_template->set('UPDATE_T', 'checked="checked"');
            Self::$_template->set('UPDATE_F', '');
        } else {
            Self::$_template->set('UPDATE_F', 'checked="checked"');
            Self::$_template->set('UPDATE_T', '');
        }
        if ((Input::exists('submit') && (Input::post('newsletter') === 'true')) || ((Self::$_active_prefs->newsletter === 'true') && (!Input::exists('submit')))) {
            Self::$_template->set('NEWSLETTER_T', 'checked="checked"');
            Self::$_template->set('NEWSLETTER_F', '');
        } else {
            Self::$_template->set('NEWSLETTER_F', 'checked="checked"');
            Self::$_template->set('NEWSLETTER_T', '');
        }
        if (Input::exists('submit')) {
            Self::$_template->select_option(Input::post('timezone'));
            Self::$_template->select_option(Input::post('dateFormat'));
            Self::$_template->select_option(Input::post('timeFormat'));
            Self::$_template->select_option(Input::post('page_limit'));
            Self::$_template->select_option(Input::post('gender'));
        } else {
            Self::$_template->select_option((Self::$_active_prefs->timezone));
            Self::$_template->select_option((Self::$_active_prefs->date_format));
            Self::$_template->select_option((Self::$_active_prefs->time_format));
            Self::$_template->select_option((Self::$_active_prefs->page_limit));
            Self::$_template->select_option((Self::$_active_prefs->gender));
        }
        if (empty($avatar)) {
            $avatar = Self::$_active_prefs->avatar;
        }
        Self::$_template->set('AVATAR_SETTINGS', $avatar);
        $this->view('usercp_settings', Self::$_active_user);
        exit();
    }
    public function email()
    {
        Self::$_title = 'Email Settings';
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        if (Self::$_active_user->confirmed != '1') {
            Issue::error('You need to confirm your email address before you can change it! If you would like to resend that confirmation link, please <a href="{BASE}register/resend">click here</a>');
            exit();
        }
        if (!Input::exists()) {    
            $this->view('usercp.email.change');
            exit();
        }
        if (!Check::email(Input::post('email'))) {
            Issue::error('There was an error with your email format.');
            $this->view('usercp.email.change');
            exit();
        }
        if (!Check::no_email_exists(Input::post('email'))) {
            Issue::error('That email is already in use by another member.');
            $this->view('usercp.email.change');
            exit();
        }
        if (Input::post('email') !== Input::post('email2')) {
            Issue::error('Email Addresses do not match.');
            $this->view('usercp.email.change');
            exit();
        }
            $Ccode = Code::new_confirmation();
            Self::$_user->update(array(
                'Confirmed' => 0,
                'email' => Input::post('email2'),
                'Confirmation_code' => $Ccode,
            ), Self::$_active_user->ID);
            Email::send(Self::$_active_user->email, 'email_change_notice', $Ccode, array('template' => true));
            Email::send(Input::post('email2'), 'email_change', $Ccode, array('template' => true));
            Issue::notice('Email has been changed, please check your email to confirm it.');
            exit();
    }
    public function password()
    {   
        Self::$_title = 'Password Settings';
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        if (!Input::exists()) {
            $this->view('password.change');
            exit();
        }
        if ((Check::password(Input::post('password'))) && (Input::post('password') === Input::post('password2')) && (Hash::check(Input::post('curpass'), Self::$_active_user->password))) {
            Self::$_user->update(array(
                'password' => Hash::make(Input::post('password'))
            ), Self::$_active_user->ID);
            Email::send(Self::$_active_user->email, 'password_change', null, array('template' => true));
            Issue::notice('Your Password has been changed!');
            exit();
        }
        Issue::error('Your password doesn\'t meet the requirements.');
        $this->view('password.change');
        exit();
    }
    public function messages($action = null, $data = null)
    {
        Self::$_title = 'Messages';
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        $messages = $this->model('message');
        if (!isset($action)) {
            $messages->inbox();
            $messages->outbox();
            exit();
        }
        switch($action) {
            case 'view_message':
                Self::$_title = $messages->message_title(Input::get('ID'));
                $messages->view_message(Input::get('ID'));
                exit();
                break;
            case 'reply':
                Self::$_title .= ' - Reply to: ' . $messages->message_title(Input::post('message_ID'));
                $messages->reply();
                exit();
                break;
            case 'new_message':
                Self::$_title .= ' - New Message';
                if (!Input::exists()) {
                    if (Input::get('prepopuser')) {
                        Self::$_template->set('prepopuser', Input::get('prepopuser'));
                    } elseif (!empty($data)) {
                        if (Check::username($data)) {
                            Self::$_template->set('prepopuser', $data);
                        } else {
                            Self::$_template->set('prepopuser', '');
                        }
                    } else {
                        Self::$_template->set('prepopuser', '');
                    }
                    $this->view('message.new');
                    exit();
                }
                $messages->new_message();
                $messages->inbox();
                $messages->outbox();
                exit();
                break;
            case 'mark_read':
                $messages->mark_read(Input::get('ID'));
                break;
            case 'delete':
                if (Input::exists('T_')) {
                    $data[] = Input::post('T_');
                    $messages->delete_message($data);
                } 
                if (Input::exists('F_')) {
                    $data[] = Input::post('F_');
                    $messages->delete_message($data);
                }
                if (Input::exists('ID')) {
                    $messages->delete_message(Input::get('ID'));
                }
                break;
        }
        $messages->inbox();
        $messages->outbox();
    }
}
