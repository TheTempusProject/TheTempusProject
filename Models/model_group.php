<?php
/**
 * Models/model_group.php.
 *
 * This class is used for the manipulation of the groups database table.
 *
 * @version 1.0
 *
 * @author  Joey Kimsey <joeyk4816@gmail.com>
 *
 * @link    https://github.com/JoeyK4816/TheTempusProject
 *
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */

namespace TheTempusProject\Models;

use TempusProjectCore\Classes\Check as Check;
use TempusProjectCore\Classes\Code as Code;
use TempusProjectCore\Core\Controller as Controller;
use TempusProjectCore\Classes\Debug as Debug;
use TempusProjectCore\Classes\Config as Config;
use TempusProjectCore\Classes\DB as DB;
use TempusProjectCore\Classes\Session as Session;
use TempusProjectCore\Classes\Cookie as Cookie;
use TempusProjectCore\Classes\Log as Log;
use TempusProjectCore\Classes\Input as Input;
use TempusProjectCore\Classes\Email as Email;

class model_group extends Controller
{
    public function __construct()
    {
        Debug::log('Model Constructed: '.get_class($this));
        Self::$_db = DB::getInstance();
        Debug::gend();
    }
    public static function sql() 
    {
        $query = "CREATE TABLE `groups` (
  `ID` int(11) NOT NULL,
  `name` varchar(32) NOT NULL COMMENT 'Group Name',
  `permissions` text NOT NULL COMMENT 'Permissions json'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Groups used by the permissions systems';
ALTER TABLE `groups`
  ADD PRIMARY KEY (`ID`);
ALTER TABLE `groups`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'primary index value', AUTO_INCREMENT=0;
INSERT INTO `groups` (`ID`, `name`, `permissions`) VALUES
(1, 'Admin', '{\"upload_images\":true,\"send_messages\":true,\"page_limit\":100,\"admin_cp\":true,\"mod_cp\":true,\"member\":true,\"bug_report\":true,\"feedback\":true}'),
(2, 'Moderator', '{\"upload_images\":true,\"send_messages\":true,\"page_limit\":75,\"admin_cp\":false,\"mod_cp\":true,\"member\":true,\"bug_report\":true,\"feedback\":true}'),
(3, 'Member', '{\"upload_images\":true,\"send_messages\":true,\"page_limit\":50,\"admin_cp\":false,\"mod_cp\":false,\"member\":true,\"bug_report\":true,\"feedback\":true}'),
(4, 'User', '{\"upload_images\":true,\"send_messages\":true,\"page_limit\":25,\"admin_cp\":false,\"mod_cp\":false,\"member\":false,\"bug_report\":true,\"feedback\":true}'),
(5, 'Guest', '{\"upload_images\":false,\"send_messages\":false,\"page_limit\":10,\"admin_cp\":false,\"mod_cp\":false,\"member\":false,\"bug_report\":false,\"feedback\":true}');";
        return $query;
    }
    public static function is_empty($data) 
    {
        if (!Check::ID($data)) {
            return false;
        }
        $db = Self::$_db->get('users', array('user_group', '=', $data));
        if (!$db->count()) {
            return true;
        }
        return false;
    }
    public static function delete_group($ID) 
    {
        if (is_array($ID)) {
            foreach($ID as $instance)
            {
                $x = 0;
                if (is_array($instance)) {
                    foreach ($instance as $id)
                    {
                            if (Self::is_empty($id)) {
                                Self::$_db->delete('groups', array('ID', '=', $id));
                                Debug::log("Group deleted: $id");
                                $x++;
                            }
                    }
                } else {
                        if (Self::is_empty($instance)) {
                            Self::$_db->delete('groups', array('ID', '=', $instance));
                            Debug::log("Group deleted: $instance");
                            $x++;
                        }
                }
                if ($x > 0) {
                    return true;
                }
            }
        } else {
                if (Self::is_empty($ID)) {
                    Self::$_db->delete('groups', array('ID', '=', $ID));
                    Debug::log("Group deleted: $ID");

                    return true;
                }
        }

        return false;
    }
    public static function formToJson() 
    {
        if (Input::exists('upload_images')) {
            $data['upload_images'] = true;
        } else {
            $data['upload_images'] = false;
        }
        if (Input::exists('send_messages')) {
            $data['send_messages'] = true;
        } else {
            $data['send_messages'] = false;
        }
        if (Input::exists('feedback')) {
            $data['feedback'] = true;
        } else {
            $data['feedback'] = false;
        }
        if (Input::exists('bug_report')) {
            $data['bug_report'] = true;
        } else {
            $data['bug_report'] = false;
        }
        if (Input::exists('member')) {
            $data['member'] = true;
        } else {
            $data['member'] = false;
        }
        if (Input::exists('mod_cp')) {
            $data['mod_cp'] = true;
        } else {
            $data['mod_cp'] = false;
        }
        if (Input::exists('admin_cp')) {
            $data['admin_cp'] = true;
        } else {
            $data['admin_cp'] = false;
        }
        if (!is_numeric(Input::post('page_limit'))) {
            Debug::warn('Invalid number supplied for page limit.');
            return false;
        }
        $data['page_limit'] = Input::post('page_limit');
        $out = json_encode($data);
        return $out;
    }
    public static function create() 
    {
        if (!Check::data_title(Input::post('name'))) {
            Debug::info("model_group: illegal group name.");
            
            return false;
        }
        $fields = array(
            'name' => Input::post('name'),
            'permissions' => Self::formToJson(),
            );
        if (Self::$_db->insert('groups', $fields)) {
            return true;
        }
        return false;
    }
    public static function update($data) 
    {
        if (!Check::ID($data)) {
            return false;
        }
        if (!Check::data_title(Input::post('name'))) {
            Debug::info("model_group: illegal group name.");
            
            return false;
        }
        $fields = array(
            'name' => Input::post('name'),
            'permissions' => Self::formToJson(),
            );
        if (Self::$_db->update('groups', $data, $fields)) {
            return true;
        }
        return false;
    }
    public static function getPermissions($data) 
    {
        if (!is_object($data)) {
            $json = (array) json_decode(file_get_contents(Config::get('main/location').'Resources/Permissions/group_permission_default.json'), true);
        } else {
            $json = (array) json_decode($data->permissions, true);
        }
        foreach ($json as $key => $value) {
            $name = $key . '_text';
            $name2 = $key . '_checked';
            if ($value == true && is_bool($value)) {
                $json[$name] = 'yes';
                $json[$name2] = ' checked';
            } else {
                $json[$name] = 'no';
                $json[$name2] = '';
            }
        }
        $json['user_count'] = Self::count_members($data->ID);
        $group_data = (object) array_merge($json, (array) $data);
        return $group_data;
    }

    public static function find_by_name($data) 
    {
        if (!Check::data_string($data)) {
            return false;
        }
        $db = Self::$_db->get('groups', array('name', '=', $data));
        if (!$db->count()) {
            Debug::warn('Could not find a group named: ' . $data);
            return false;
        }
        $out = Self::getPermissions($db->first());
        return $out;
    }

    public static function find_by_ID($data) 
    {
    	if (!Check::ID($data)) {
            return false;
        }
        $db = Self::$_db->get('groups', array('ID', '=', $data));
        if (!$db->count()) {
            Debug::warn('Could not find a group with ID: ' . $data);
            return false;
        }
        $out = Self::getPermissions($db->first());
        return $out;
    }
    public static function list_groups() 
    {
        $db = Self::$_db->get_paginated('groups', array('ID', '>=', '0'));
        if (!$db->count()) {
            Debug::warn('Could not find any groups');
            return false;
        }
        $groups = $db->results();
        foreach ($groups as $group) {
            $group->user_count = Self::count_members($group->ID);
        }
        return $groups;
    }
    public static function list_members($data) 
    {
        if (!Check::ID($data)) {
            return false;
        }
        $group = Self::find_by_ID($data);
        if ($group === false) {
            return false;
        }
    	$members = Self::$_db->get_paginated('users', array('user_group', '=', $data));
        if (!$members->count()) {
            Debug::warn('list members: Could not find anyone in group: ' . $data);
            return false;
        }
        $out = $members->results();
        return $out;
    }
    public static function count_members($data) 
    {
        if (!Check::ID($data)) {
            return false;
        }
        $db = Self::$_db->get('users', array('user_group', '=', $data));
        if (!$db->count()) {
            Debug::warn('count members: Could not find anyone in group: ' . $data);
            return 0;
        }
        return $db->count();
    }
}
