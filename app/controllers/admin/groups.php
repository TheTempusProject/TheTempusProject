<?php
/**
 * app/controllers/admin/group.php
 *
 * This is the group controller.
 *
 * @version 3.0
 * @author  Joey Kimsey <JoeyKimsey@thetempusproject.com>
 * @link    https://TheTempusProject.com
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */
namespace TheTempusProject\Controllers;

use TempusProjectCore\Template\Issues;
use TempusProjectCore\Classes\Input;
use TempusProjectCore\Classes\Debug;
use TempusProjectCore\Classes\Check;

class Groups extends AdminController
{
    public function index($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        Views::view('admin.groupList', self::$group->listGroups());
        exit();
    }
    public function viewGroup($data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        $groupData = self::$group->findById($data);
        if ($groupData !== false) {
            Views::view('admin.groupView', $groupData);
            exit();
        }
        Issues::add('error', 'Group not found');
        $this->index();
    }
    public function listmembers($data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        $groupData = self::$group->findById($data);
        if ($groupData !== false) {
            Components::set('groupName', $groupData->name);
            Views::view('admin.groupListMembers', self::$group->listMembers($groupData->ID));
            exit();
        }
        Issues::add('error', 'Group not found');
        $this->index();
    }
    public function newGroup($data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        if (!Input::exists('submit')) {
            Views::view('admin.groupNew');
            exit();
        }
        if (!Forms::check('newGroup')) {
            Issues::add('error', 'There was an error with your request.', Check::userErrors());
            Views::view('admin.groupNew');
            exit();
        }
        if (self::$group->create(Input::post('name'), self::$group->formToJson(Input::post('pageLimit')))) {
            Issues::add('success', 'Group created');
        } else {
            Issues::add('error', 'There was an error creating your group.');
        }
        $this->index();
    }
    public function edit($data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
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
            Issues::add('error', 'There was an error with your request.', Check::userErrors());
            Views::view('admin.groupNew');
            exit();
        }
        if (self::$group->update($data, Input::post('name'), self::$group->formToJson(Input::post('pageLimit')))) {
            Issues::add('success', 'Group updated');
        } else {
            Issues::add('error', 'There was an error with your request.');
        }
        $this->index();
    }
    public function delete($data = null)
    {
        Debug::log('Controller initiated: ' . __METHOD__ . '.');
        if (Input::exists('G_')) {
            $data = Input::post('G_');
        }
        if (!self::$group->deleteGroup((array) $data)) {
            Issues::add('error', 'There was an error with your request.');
        } else {
            Issues::add('success', 'Group has been deleted');
        }
        $this->index();
    }
}
