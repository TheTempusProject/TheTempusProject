<?php
/**
 * app/ontrollers/admin/users.php
 *
 * This is the users admin controller.
 *
 * @version 3.0
 * @author  Joey Kimsey <JoeyKimsey@thetempusproject.com>
 * @link    https://TheTempusProject.com
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */

namespace TheTempusProject\Controllers;

use TempusProjectCore\Classes\Debug;
use TempusProjectCore\Classes\Input;
use TempusProjectCore\Template\Issues;
use TempusProjectCore\Classes\Check;
use TempusProjectCore\Classes\Image;

class Users extends AdminController
{
    public function index($data = null)
    {
        Views::view('admin.userList', self::$user->userList());
        exit();
    }
    public function delete($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        if (Input::exists('submit')) {
            $data = Input::post('U_');
        }
        if (self::$user->delete((array) $data)) {
            Issues::add('success', 'User Deleted');
        } else {
            Issues::add('error', 'There was an error deleting that user');
        }
        $this->index();
    }
    public function viewUser($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        if (!empty($data)) {
            $userData = self::$user->get($data);
            if ($userData !== false) {
                Views::view('admin.userView', $userData);
                exit();
            }
            Issues::add('error', 'User not found.');
        }
        $this->index();
    }
    public function edit($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        if (empty($data) && Input::exists('U_')) {
            $data = Input::post('U_');
        }
        if (!Check::id($data)) {
            Issues::add('error', 'invalid user.');
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
                Issues::add('success', 'User Updated.');
            } else {
                Issues::add('warning', 'There was an error with your request, please try again.');
            }
            Forms::selectOption(Input::post('groupSelect'));
            Forms::selectOption(Input::post('timezone'));
            Forms::selectOption(Input::post('dateFormat'));
            Forms::selectOption(Input::post('timeFormat'));
            Forms::selectOption(Input::post('pageLimit'));
            Forms::selectOption(Input::post('gender'));
        } else {
            Forms::selectOption($currentUser->userGroup);
            Forms::selectOption($currentUser->timezone);
            Forms::selectOption($currentUser->dateFormat);
            Forms::selectOption($currentUser->timeFormat);
            Forms::selectOption($currentUser->pageLimit);
            Forms::selectOption($currentUser->gender);
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
}
