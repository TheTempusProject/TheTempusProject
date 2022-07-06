<?php
/**
 * app/classes/forms.php
 *
 * This class is used in conjunction with TempusProjectCore\Classes\Check
 * to house complete form verification. You can utilize the
 * error reporting to easily define exactly what feedback you
 * would like to give.
 *
 * @version  3.0
 * @author   Joey Kimsey <Joey@thetempusproject.com>
 * @link     https://TheTempusProject.com
 * @license  https://opensource.org/licenses/MIT [MIT LICENSE]
 */
namespace TheTempusProject;

use TempusProjectCore\Functions\Input;
use TempusProjectCore\Functions\Check;
use TheTempusProject\Classes\Forms;
use TempusProjectCore\Classes\Database;

class TTPForms extends Forms {
    /**
     * Adds these functions to the form list.
     */
    public function __construct() {
        self::addHandler('install', __CLASS__, 'install');
        self::addHandler('passwordResetCode', __CLASS__, 'passwordResetCode');
        self::addHandler('register', __CLASS__, 'register');
        self::addHandler('login', __CLASS__, 'login');
        self::addHandler('changeEmail', __CLASS__, 'changeEmail');
        self::addHandler('changePassword', __CLASS__, 'changePassword');
        self::addHandler('passwordReset', __CLASS__, 'passwordReset');
        self::addHandler('emailConfirmation', __CLASS__, 'emailConfirmation');
        self::addHandler('confirmationResend', __CLASS__, 'confirmationResend');
        self::addHandler('replyMessage', __CLASS__, 'replyMessage');
        self::addHandler('newMessage', __CLASS__, 'newMessage');
        self::addHandler('userPrefs', __CLASS__, 'userPrefs');
        self::addHandler('newGroup', __CLASS__, 'newGroup');
        self::addHandler('editGroup', __CLASS__, 'editGroup');
        self::addHandler('installStart', __CLASS__, 'install', ['start']);
        self::addHandler('installAgreement', __CLASS__, 'install', ['agreement']);
        self::addHandler('installCheck', __CLASS__, 'install', ['check']);
        self::addHandler('installConfigure', __CLASS__, 'install', ['configure']);
        self::addHandler('installhtaccess', __CLASS__, 'install', ['htaccess']);
        self::addHandler('installModels', __CLASS__, 'install', ['models']);
        self::addHandler('installResources', __CLASS__, 'install', ['resources']);
        self::addHandler('installAdminUser', __CLASS__, 'install', ['adminUser']);
    }

    /**
     * Validates the installer forms.
     */
    public static function install( $page = '' ) {
        if (!self::token()) {
            return false;
        }
        switch ( $page ) {
            case 'configure':
                if (!Database::check(Input::post('dbHost'), Input::post('dbName'), Input::post('dbUsername'), Input::post('dbPassword'))) {
                    self::addUserError('DB connection error.');
                    return false;
                }
                break;
            case 'adminUser':
                if (!self::checkUsername(Input::post('newUsername'))) {
                    self::addUserError('Invalid username.');
                    return false;
                }
                if (!self::password(Input::post('userPassword'))) {
                    self::addUserError('Invalid password.');
                    return false;
                }
                if (Input::post('userPassword') !== Input::post('userPassword2')) {
                    self::addUserError('Passwords do not match.');
                    return false;
                }
                if (Input::post('userEmail') !== Input::post('userEmail2')) {
                    self::addUserError('Emails do not match.');
                    return false;
                }
                if (!self::token()) {
                    return false;
                }
                break;
            case 'check':
                if (!self::uploads()) {
                    self::addUserError('Uploads are disabled.');
                    return false;
                }
                if (!self::php()) {
                    self::addUserError('PHP version is too old.');
                    return false;
                }
                if (!self::phpExtensions()) {
                    self::addUserError('PHP extensions are missing.');
                    return false;
                }
                if (!self::sessions()) {
                    self::addUserError('There is an error with Sessions.');
                    return false;
                }
                if (!self::mail()) {
                    self::addUserError('PHP mail is not enabled.');
                    return false;
                }
                if (!self::safe()) {
                    self::addUserError('Safe mode is enabled.');
                    return false;
                }
                break;
            case 'start':
            case 'agreement':
            case 'htaccess':
            case 'models':
            case 'resources':
            default:
                return true;
        }
        return true;
    }

    /**
     * Validates the password re-send form.
     */
    public static function passwordResetCode() {
        if (!self::token()) {
            return false;
        }
        return true;
    }

    /**
     * Validates the registration form.
     */
    public static function register() {
        if (!self::checkUsername(Input::post('username'))) {
            self::addUserError('Invalid username.');
            return false;
        }
        if (!self::password(Input::post('password'))) {
            self::addUserError('Invalid password.');
            return false;
        }
        if (!self::email(Input::post('email'))) {
            self::addUserError('Invalid Email.');
            return false;
        }
        if (!self::noEmailExists(Input::post('email'))) {
            self::addUserError('A user with that email is already registered.');
            return false;
        }
        if (Input::post('password') !== Input::post('password2')) {
            self::addUserError("Passwords do not match.");
            return false;
        }
        if (Input::post('email') !== Input::post('email2')) {
            self::addUserError("Emails do not match.");
            return false;
        }
        if (Input::post('terms') != '1') {
            self::addUserError("You must agree to the terms of service.");
            return false;
        }
        if (!self::token()) {
            return false;
        }
        return true;
    }

    /**
     * Validates the user login form.
     */
    public static function login() {
        if (!self::checkUsername(Input::post('username'))) {
            self::addUserError('Invalid username.');
            return false;
        }
        if (!self::password(Input::post('password'))) {
            self::addUserError('Invalid password.');
            return false;
        }
        if (!self::token()) {
            return false;
        }
        return true;
    }

    /**
     * Validates the email change form.
     *
     * @return boolean
     */
    public static function changeEmail() {
        if (!self::email(Input::post('email'))) {
            self::addUserError('Invalid Email.');
            return false;
        }
        if (Input::post('email') !== Input::post('email2')) {
            self::addUserError("Emails do not match.");
            return false;
        }
        if (!self::token()) {
            return false;
        }
        
        return true;
    }

    /**
     * Validates the password change form.
     *
     * @return boolean
     */
    public static function changePassword() {
        if (!self::password(Input::post('password'))) {
            self::addUserError('Invalid password.');
            return false;
        }
        if (Input::post('password') !== Input::post('password2')) {
            self::addUserError('Passwords do not match.');
            return false;
        }
        if (!self::token()) {
            return false;
        }
        
        return true;
    }

    /**
     * Validates the password reset form.
     */
    public static function passwordReset() {
        if (!self::password(Input::post('password'))) {
            self::addUserError('Invalid password.');
            return false;
        }
        if (Input::post('password') !== Input::post('password2')) {
            self::addUserError('Passwords do not match.');
            return false;
        }
        if (!self::token()) {
            return false;
        }
        return true;
    }

    /**
     * Validates the email confirmation re-send form.
     */
    public static function emailConfirmation() {
        if (!Input::exists('confirmationCode')) {
            self::addUserError('No confirmation code provided.');
            return false;
        }
        if (!self::token()) {
            return false;
        }
        return true;
    }

    /**
     * Validates the email confirmation re-send form.
     */
    public static function confirmationResend() {
        if (!Input::exists('resendConfirmation')) {
            self::addUserError('Confirmation not provided.');
            return false;
        }
        if (!self::token()) {
            return false;
        }
        return true;
    }

    /**
     * Validates the reply message form.
     */
    public static function replyMessage() {
        if (!Input::exists('message')) {
            self::addUserError('Reply cannot be empty.');
            return false;
        }
        if (!Input::exists('messageID')) {
            return false;
        }
        if (!self::token()) {
            return false;
        }
        return true;
    }

    /**
     * Validates the new message form.
     */
    public static function newMessage() {
        if (!Input::exists('toUser')) {
            self::addUserError('You must specify a user to send the message to.');
            return false;
        }
        if (!Input::exists('subject')) {
            self::addUserError('You must have a subject for your message.');
            return false;
        }
        if (!Input::exists('message')) {
            self::addUserError('No message entered.');
            return false;
        }
        if (!self::token()) {
            return false;
        }
        return true;
    }
    
    /**
     * Validates the user preferences form.
     */
    public static function userPrefs() {
        // @todo make this a real check
        if (!Input::exists('timeFormat')) {
            self::addUserError('You must specify timeFormat');
            return false;
        }
        if (!Input::exists('pageLimit')) {
            self::addUserError('You must specify pageLimit');
            return false;
        }
        if (!Input::exists('gender')) {
            self::addUserError('You must specify gender');
            return false;
        }
        if (!Input::exists('dateFormat')) {
            self::addUserError('You must specify dateFormat');
            return false;
        }
        if (!Input::exists('timezone')) {
            self::addUserError('You must specify timezone');
            return false;
        }
        if (!Input::exists('updates')) {
            self::addUserError('You must specify updates');
            return false;
        }
        if (!Input::exists('newsletter')) {
            self::addUserError('You must specify newsletter');
            return false;
        }
        if (!self::token()) {
            return false;
        }
        return true;
    }

    /**
     * Validates the new group form.
     */
    public static function newGroup() {
        if (!Input::exists('name')) {
            self::addUserError('You must specify a name');
            return false;
        }
        if (!self::dataTitle(Input::exists('name'))) {
            self::addUserError('invalid group name');
            return false;
        }
        if (!Input::exists('pageLimit')) {
            self::addUserError('You must specify a pageLimit');
            return false;
        }
        if (!self::token()) {
            return false;
        }
        return true;
    }

    /**
     * Validates the edit group form.
     */
    public static function editGroup() {
        if (!Input::exists('name')) {
            self::addUserError('You must specify a name');
            return false;
        }
        if (!self::dataTitle(Input::exists('name'))) {
            self::addUserError('invalid group name');
            return false;
        }
        if (!Input::exists('pageLimit')) {
            self::addUserError('You must specify a pageLimit');
            return false;
        }
        if (!self::token()) {
            return false;
        }
        return true;
    }
}

new TTPForms;