<?php
/**
 * controllers/usercp.php
 *
 * This is the userCP controller.
 *
 * @version 3.0
 * @author  Joey Kimsey <JoeyKimsey@thetempusproject.com>
 * @link    https://TheTempusProject.com
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */
namespace TheTempusProject\Controllers;

use TheTempusProject\Classes\Controller;
use TheTempusProject\Models\Message;
use TempusProjectCore\Classes\Email;
use TempusProjectCore\Template\Forms;
use TempusProjectCore\Template\Issues;
use TempusProjectCore\Template\Views;
use TempusProjectCore\Template\Components;
use TempusProjectCore\Functions\{
    Code, Debug, Check, Input, Image, Hash
};
use TheTempusProject\TheTempusProject as App;

class Usercp extends Controller
{
    protected static $message;

    public function index() {
        self::$title = 'User Control Panel';
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        Views::view('profile', App::$activeUser);
    }

    public function settings() {
        self::$title = 'Preferences';
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        Components::set('TIMEZONELIST', Views::standardView('tz_dropdown'));
        $a = Input::exists('submit');
        $value = $a ? Input::post('updates') : App::$activePrefs->email;
        Forms::selectRadio('updates', $value);
        $value = $a ? Input::post('newsletter') : App::$activePrefs->newsletter;
        Forms::selectRadio('newsletter', $value);
        Forms::selectOption(($a ? Input::post('timezone') : App::$activePrefs->timezone));
        Forms::selectOption(($a ? Input::post('dateFormat') : App::$activePrefs->dateFormat));
        Forms::selectOption(($a ? Input::post('timeFormat') : App::$activePrefs->timeFormat));
        Forms::selectOption(($a ? Input::post('pageLimit') : App::$activePrefs->pageLimit));
        Forms::selectOption(($a ? Input::post('gender') : App::$activePrefs->gender));
        Components::set('AVATAR_SETTINGS', App::$activePrefs->avatar);
        if ($a) {
            if (!Forms::check('userPrefs')) {
                Issues::add('error', 'There was an error with your request.', Check::userErrors());
                Views::view('user_cp.settings', App::$activeUser);
                exit();
            }
            if (Input::exists('avatar') && Image::upload('avatar', App::$activeUser->username)) {
                $avatar = 'Uploads/Images/' . App::$activeUser->username . '/' . Image::last();
            }
            $fields = [
                "timezone" =>    Input::post('timezone'),
                "dateFormat" => Input::post('dateFormat'),
                "timeFormat" => Input::post('timeFormat'),
                "pageLimit" => Input::post('pageLimit'),
                "email" => Input::post('updates'),
                "gender" => Input::post('gender'),
                "newsletter" =>  Input::post('newsletter'),
            ];
            if (isset($avatar)) {
                $fields = array_merge($fields, ['avatar' => $avatar]);
            }
            self::$user->updatePrefs($fields, App::$activeUser->ID);
        }
        if (!isset($avatar)) {
            $avatar = App::$activePrefs->avatar;
        }
        Components::set('AVATAR_SETTINGS', $avatar);
        Views::view('user_cp.settings', App::$activeUser);
    }

    public function email() {
        self::$title = 'Email Settings';
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        if (App::$activeUser->confirmed != '1') {
            Issues::add('notice', 'You need to confirm your email address before you can make modifications. If you would like to resend that confirmation link, please <a href="{BASE}register/resend">click here</a>');
            exit();
        }
        if (!Input::exists()) {
            Views::view('user_cp.email_change');
            exit();
        }
        if (!Forms::check('changeEmail')) {
            Issues::add('error', 'There was an error with your request.', Check::userErrors());
            Views::view('user_cp.email_change');
            exit();
        }
        $code = Code::genConfirmation();
        self::$user->update([
            'confirmed' => 0,
            'email' => Input::post('email'),
            'confirmationCode' => $code,
            ], App::$activeUser->ID);
        Email::send(App::$activeUser->email, 'emailChangeNotice', $code, ['template' => true]);
        Email::send(Input::post('email'), 'emailChange', $code, ['template' => true]);
        Issues::add('notice', 'Email has been changed, please check your email to confirm it.');
    }

    public function password() {
        self::$title = 'Password Settings';
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        if (!Input::exists()) {
            Views::view('user_cp.password_change');
            exit();
        }
        if (!Hash::check(Input::post('curpass'), App::$activeUser->password)) {
            Issues::add('error', 'Current password was incorrect.');
            Views::view('user_cp.password_change');
            exit();
        }
        if (!Forms::check('changePassword')) {
            Issues::add('error', 'There was an error with your request.', Check::userErrors());
            Views::view('user_cp.password_change');
            exit();
        }
        self::$user->update(['password' => Hash::make(Input::post('password'))], App::$activeUser->ID);
        Email::send(App::$activeUser->email, 'passwordChange', null, ['template' => true]);
        Issues::add('notice', 'Your Password has been changed!');
    }

    public function messages($action = null, $data = null) {
        self::$title = 'Messages';
        self::$message = new Message;
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        $action = strtolower($action);
        switch ($action) {
            case 'viewmessage':
                self::$title = self::$message->messageTitle($data);
                Views::view('message.message', self::$message->getThread($data, true));
                exit();

            case 'reply':
                if (Input::exists('messageID')) {
                    $data = Input::post('messageID');
                }
                if (!Check::id($data)) {
                    Issues::add('error', 'There was an error with your request.');
                    break;
                }
                self::$title .= ' - Reply to: ' . self::$message->messageTitle($data);
                if (!Input::exists('message')) {
                    Components::set('messageID', $data);
                    Views::view('message.reply');
                    exit();
                }
                if (!Forms::check('replyMessage')) {
                    Issues::add('error', 'There was an problem sending your message.', Check::userErrors());
                    Components::set('messageID', $data);
                    Views::view('message.reply');
                    exit();
                }
                if (!self::$message->newMessageReply($data, Input::post('message'))) {
                    Issues::add('error', 'There was an error with your request.');
                    break;
                }
                Issues::add('success', 'Reply Sent.');
                break;

            case 'newmessage':
                self::$title .= ' - New Message';
                if (Input::get('prepopuser')) {
                    $data = Input::get('prepopuser');
                }
                if (!empty($data) && Check::username($data)) {
                    Components::set('prepopuser', $data);
                } else {
                    Components::set('prepopuser', '');
                }
                if (!Input::exists('submit')) {
                    Views::view('message.new');
                    exit();
                }
                if (!Forms::check('newMessage')) {
                    Issues::add('error', 'There was an problem sending your message.', Check::userErrors());
                    Views::view('message.new');
                    exit();
                }
                if (self::$message->newThread(Input::post('toUser'), Input::post('subject'), Input::post('message'))) {
                    Issues::add('success', 'Message Sent.');
                } else {
                    Issues::add('notice', 'There was an problem sending your message.');
                }
                break;

            case 'markread':
                self::$message->markRead($data);
                break;

            case 'delete':
                if (Input::exists('T_')) {
                    self::$message->deleteMessage(Input::post('T_'));
                }
                if (Input::exists('F_')) {
                    self::$message->deleteMessage(Input::post('F_'));
                }
                if (Input::exists('ID')) {
                    self::$message->deleteMessage([Input::get('ID')]);
                }
                if (!empty($data)) {
                    self::$message->deleteMessage([$data]);
                }
                break;
        }
        Views::view('message.inbox', self::$message->getInbox());
        Views::view('message.outbox', self::$message->getOutbox());
    }
}
