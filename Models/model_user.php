<?php
/**
 * Models/model_user.php.
 *
 * This class is used for the manipulation of the user database table.
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
use TempusProjectCore\Core\Controller as Controller;
use TempusProjectCore\Classes\Debug as Debug;
use TempusProjectCore\Classes\Config as Config;
use TempusProjectCore\Classes\DB as DB;
use TempusProjectCore\Classes\Session as Session;
use TempusProjectCore\Classes\Cookie as Cookie;
use TempusProjectCore\Classes\Log as Log;
use TempusProjectCore\Classes\Input as Input;
use TempusProjectCore\Classes\Email as Email;
use TempusProjectCore\Classes\CustomException as CustomException;
use TempusProjectCore\Classes\Issue as Issue;
use TempusProjectCore\Classes\Hash as Hash;

class model_user extends Controller
{
    private $_usernames;
    private $_avatars;
    private $_data = array();
    private static $_default_prefs = null;
    public function __construct()
    {
        Debug::log('Model Constructed: '.get_class($this));
        if (!isset(Self::$_db)) {
            Self::$_db = DB::getInstance();
        }
        Self::$_default_prefs = json_decode(file_get_contents(Config::get('main/location').'Resources/Permissions/user_preference_default.json') ,true);
        Debug::gend();
    }
    public static function sql() 
    {
        $query = "CREATE TABLE `users` (
  `ID` int(11) NOT NULL,
  `username` varchar(16) NOT NULL,
  `password` varchar(80) NOT NULL,
  `email` varchar(75) NOT NULL,
  `name` varchar(20) NOT NULL,
  `registered` int(10) NOT NULL,
  `terms` int(1) NOT NULL,
  `confirmed` int(1) NOT NULL,
  `confirmation_code` varchar(80) NOT NULL,
  `user_group` int(11) NOT NULL DEFAULT '5',
  `last_login` int(10) NOT NULL DEFAULT '0',
  `prefs` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`);
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'primary index value', AUTO_INCREMENT=0;";
        return $query;
    }

    /**
     * Since we need a cache of the usernames, we use this function 
     * to find/return all usernames based on ID.
     * 
     * @param  $ID - The ID of the user you are looking for.
     * 
     * @return string - Either the username or unknown will be returned.
     */
    public function get_username($ID)
    {
        if (!Check::ID($ID)) {
            return false;
        }
        if (!isset($this->_usernames[$ID])) {
            $user = Self::get($ID);
            if ($user !== false) {
                $this->_usernames[$ID] = $user->username;
            } else {
                $this->_usernames[$ID] = 'Unknown';
            }
        }
        return $this->_usernames[$ID];
    }

    /**
     * Since we need a cache of the usernames, we use this function 
     * to find/return all usernames based on ID.
     * 
     * @param  $ID - The ID of the user you are looking for.
     * 
     * @return string - Either the username or unknown will be returned.
     *
     * @todo  add this to users model
     */
    public function get_avatar($ID)
    {
        if (!Check::ID($ID)) {
            return false;
        }
        if (!isset($this->_avatars[$ID])) {
            if (Self::$_user->get($ID)) {
                $this->_avatars[$ID] = Self::$_user->data()->avatar;
            } else {
                $this->_avatars[$ID] = '{BASE}Images/defaultAvatar.png';
            }
        }
        return $this->_avatars[$ID];
    }

    public function prefs()
    {
        return Self::$_default_prefs;
    }

    public function delete_user($ID)
    {
        if (!Check::ID($ID)) {
            Debug::info('Invalid ID');
            return false;
        }
        if (Self::$_active_user->ID == $ID) {
            Debug::info('Attempt to delete own account.');
            Issue::error('You cannot delete your own user account.');
            return false;
        }
        Self::$_db->delete('users', array('ID', '=', $ID));
        Debug::log("User deleted: $ID");
        return true;
    }

    /**
     * Function to delete the specified user.
     * 
     * @param  int|array $ID the user ID or array of ID's to be deleted
     * 
     * @return bool
     */
    public function delete($ID)
    {
        if (is_array($ID)) {
            foreach($ID as $instance)
            {
                if (is_array($instance)) {
                    foreach ($instance as $id)
                    {
                        if (!Check::ID($id)) {
                            Debug::info('Invalid ID');
                            return false;
                        }
                        Self::delete_user($id);
                    }
                }
                if (!Check::ID($instance)) {
                    Debug::info('Invalid ID');
                    return false;
                }
                Self::delete_user($instance);
            }
        }
        if (!Check::ID($ID)) {
            Debug::info('Invalid ID');
            return false;
        }
        Self::delete_user($ID);
        return true;
    }

    /**
     * This function is responsible for all the business logic of logging in.
     * 
     * @param  string  $username - The username being used to login.
     * @param  string  $password - The un-hashed password.
     * @param  boolean $remember - Whether the user wishes to be remembered or not.
     * 
     * @return bool            Returns true or false depending on success.
     */
    public function log_in($username, $password, $remember = false)
    {
        Debug::group('login', 1);
        if (!Check::username($username)) {
            Issue::notice('Username or Password incorrect.');
            return false;
        }
        if (!Self::$_user->get($username)) {
            Self::$_log->login(0, "User not found: $username");
            Issue::notice('Username or Password incorrect.');
            return false;
        }
        $timeLimit = (time() - 3600);
        $limit = Config::get('main/loginLimit');
        $user = Self::$_user->data();
        if ($limit > 0) {
            $limitCheck = Self::$_db->get('logs', array('user_ID', '=', $user->ID, 'AND', 'time', '>=', $timeLimit, 'AND', 'action', '!=', 'pass'));
            if ($limitCheck->count() >= $limit) {
                Debug::info('login: Limit reached.', 1);
                Self::$_log->login($user->ID, 'Too many failed attempts.');
                Issue::notice('Too many failed attempts, please try again later.');
                return false;
            }
        }
        if (!Check::password($password)) {
            Issue::notice('Username or Password incorrect.');
            Self::$_log->login($user->ID, 'Invalid Password.');
            return false;
        }
        if (!Hash::check($password, $user->password)) {
            Issue::notice('Username or Password incorrect.');
            Self::$_log->login($user->ID, 'Wrong Password.');
            return false;
        }
        if (!$remember) {
            $expire = (time() + (60 * 60 * 24));
        } else {
            $expire = (time() + (60 * 60 * 24 * 30));
        }


        Self::$_session->new_session(Self::$_user->data()->ID, $expire);
        $data = Self::$_session->session_data();
        if ($remember) {
            Cookie::put(Self::$_cookie_name, $data->token, $expire);
        }
        Session::put(Self::$_session_name, $data->ID);
        Self::$_log->login(Self::$_user->data()->ID, 'pass');

        $set = array('last_login' => time());
        Self::$_user->update($set, Self::$_user->data()->ID);

        Self::$_is_logged_in = true;
        Self::$_active_user = Self::$_session->user_data();
        Self::$_active_group = Self::$_session->group_data();
        Self::$_active_prefs = json_decode(Self::$_active_user->prefs);
        Self::$_is_member = Self::$_active_group->member;
        Self::$_is_mod = Self::$_active_group->mod_cp;
        Self::$_is_admin = Self::$_active_group->admin_cp;
    
        Debug::gend();
        return true;
    }

    /**
     * Function for logging out a user.
     * 
     * @return null
     */
    public function log_out()
    {
        Debug::group("Logout", 1);
        $session = $this->model('session');
        $session->destroy(Session::get(Self::$_session_name));
        Self::$_is_logged_in = false;
        Self::$_is_member = false;
        Self::$_is_admin = false;
        Self::$_active_user = null;
        Cookie::delete(Self::$_cookie_name);
        Session::delete(Self::$_session_name);
        Debug::info("User has been logged out.");
        Debug::gend();
        return null;
    }

    /**
     * Function to change a user's password.
     * 
     * @param  string $code     - The confirmation code required from the password email.
     * @param  string $password - The new password for the user's account.
     * 
     * @return bool       
     */
    public function change_password($code, $password)
    {
        if (!Check::password($password)) {
            return false;
        }
        $data = Self::$_db->get('users', array('confirmation_code', '=', $code));
        if ($data->count()) {
            $this->_data = $data->first();
            $this->update(array(
                    'password' => Hash::make($password),
                    'confirmation_code' => '',
                ), $this->_data->ID);
            return true;
        }
        return false;
    }

    /**
     * Compiles a list of all users, allowing for filtering the list.
     *
     * @todo
     * 
     * @param  array $filter - A filter to be applied to the users list.
     * 
     * @return bool|object - Depending on success.
     */
    public function user_list($filter = null)
    {
        if (!empty($filter)) {
            switch ($filter) {
                case 'newsletter':
                    $data = Self::$_db->search('users', 'prefs', 'newsletter":"true');
                    break;
                default:
                    $data = Self::$_db->get('users', "*");
                    break;
            }
        } else {
            $data = Self::$_db->get('users', "*");
        }
        if (!$data->count()) {
            return false;
        }
        return (object) $data->results();
    }

    /**
     * Compiles a list of recently registered users, allowing for filtering the list.
     * 
     * @param  array $filter - A filter to be applied to the users list.
     * 
     * @return bool|object - Depending on success.
     */
    public function recent($limit = null)
    {
        if (empty($limit)) {
            $data = Self::$_db->get_paginated('users', '*');
        } else {
            $data = Self::$_db->get('users', array('ID', '>', '0'), 'ID', 'DESC', array(0,$limit));
        }
        if (!$data->count()) {
            return false;
        }
        return (object) $data->results();
    }

    /**
     * This function is used to check a confirmation code for a user.
     * 
     * @param  string $data - The confirmation code being checked.
     * 
     * @return bool
     */
    public function check_code($data)
    {
        $data = Self::$_db->get('users', array('confirmation_code', '=', $data));
        if ($data->count() > 0) {
            return true;
        }
        return false;
    }
    
    /**
     * Generates a new confirmation code for the user
     * specified and updates the user's DB entry with 
     * the new code.
     * 
     * @param  int $data - The user ID to update the confirmation code for.
     * 
     * @return bool
     */
    public function new_code($data)
    {
        $data = Self::$_db->get('users', array('ID', '=', $data));
        if ($data->count() == 0) {
            return false;
        }
        $this->_data = $data->first();
        $Ccode = md5(uniqid());
        $this->update(array(
                'confirmation_code' => $Ccode,
            ), $this->_data->ID);

        return true;
    }

    /**
     * This function is used for confirming a user's registration on the site.
     * 
     * @param  string $data - The confirmation code sent to the user.
     * 
     * @return bool
     */
    public function confirm($data)
    {
        $data = Self::$_db->get('users', array('confirmation_code', '=', $data));
        if ($data->count()) {
            $this->_data = $data->first();
            $this->update(array(
                    'confirmed' => 1,
                    'confirmation_code' => '',
                ), $this->_data->ID);

            return true;
        }
        return false;
    }

    /**
     * Check if the specified user exists or not.
     *
     * @return bool - returns true or false depending on if the user was found or not.
     */
    public function exists()
    {
        return (!empty($this->_data)) ? true : false;
    }

    /**
     * Function to get a user's info from an ID or username.
     * 
     * @param  int|string $user - Either the username or user ID being searched for.
     * 
     * @return bool|array
     */
    public function get($user)
    {
        $field = (ctype_digit($user)) ? 'ID' : 'username';
        if ($field == 'username') {
            if (!Check::username($user)) {
                Debug::info("User_model: Username improperly formatted.");

                return false;
            }
        } else {
            if (!Check::ID($user)) {
                Debug::info("User_model: Invalid ID.");

                return false;
            }
        }
        $data = Self::$_db->get('users', array($field, '=', $user));
        if (!$data->count()) {
            Debug::info("USER_MODEL: User not found.");

            return false;
        }
        $this->_data = $data->first();
        $json = (array) json_decode($this->_data->prefs, true);
        if ($this->_data->confirmed == 1) {
            $this->_data->Confirmed_text = 'Yes';
        } else {
            $this->_data->Confirmed_text = 'No';
        }
        if (($json['avatar'] == 'defaultAvatar.png') || (empty($json['avatar']))) {
            $json['avatar'] = 'Images/defaultAvatar.png';
        }
        $group = Self::$_group->find_by_ID($this->_data->user_group);
        $json['group_name'] = $group->name;
        $this->_data = (object) array_merge($json, (array) $this->_data);
        return $this->_data;
    }

    /**
     * Function for finding a User by email address.
     *
     * @param string $email - The email being searched for.
     *
     * @return bool
     */
    public function find_by_email($email)
    {
        if (Check::email($email)) {
            $data = Self::$_db->get('users', array('email', '=', $email));
            if ($data->count()) {
                $this->_data = $data->first();

                return true;
            }
        }
        Debug::error("User not found by email: $email");

        return false;
    }

    /**
     * Create a User with the specified information.
     *
     * @param array $fields - The New User's data.
     * 
     * @return  bool
     */
    public function create($fields = array())
    {
        if (empty($fields)) {
            return false;
        }
        $json = json_encode(Self::$_default_prefs);
        $add = array('prefs' => $json);
        $out = array_merge($add, $fields);
        if (!Self::$_db->insert('users', $out)) {
             new CustomException('exception_register');
             Debug::error("User not created: $fields");

             return false;
        }
        return true;
    }

    /**
     * Update a user's DB entry.
     *
     * @param array  $fields - The fields to be updated.
     * @param int $id     - The user ID being updated.
     *
     * @return  bool
     */
    public function update($fields = array(), $ID = null)
    {
        if (!Check::ID($ID)) {
            return false;
        }
        if (!Self::$_db->update('users', $ID, $fields)) {
             new Custom_Exception('User_update');
             Debug::error("User: $ID not updated: $fields");

             return false;
        }
        return true;
    }

    /**
     * Update a user's preferences.
     *
     * @param array  $fields - The fields to be updated.
     * @param int $id     - The user ID being updated.
     *
     * @return  bool
     */
    public function update_prefs($fields = array(), $ID = null)
    {
        if (!Check::ID($ID)) {
            return false;
        }
        $data = Self::get($ID);
        $json = (array) json_decode($data->prefs, true);
        $prefs_input = $json;
        /**
         * NOTE: This is case sensitive and will break when provided incorrect values.
         */
        foreach ($fields as $name => $value) {
            $prefs_input[$name] = $value;
        }
        $json = json_encode($prefs_input);
        $db_fields = array('prefs' => $json);
        if (!Self::$_db->update('users', $ID, $db_fields)) {
             new Custom_Exception('User_update');
             Debug::error("User: $ID not updated: $db_fields");

             return false;
        }
        return true;
    }

    /**
     * Fetches an array for the currently selected user.
     *
     * @return array - returns an array of the user data
     */
    public function data()
    {
        return $this->_data;
    }
}
