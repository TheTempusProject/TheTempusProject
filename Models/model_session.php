<?php
/**
 * Models/model_session.php.
 *
 * This model is used for the modification and management of the session data.
 * It also acts as an interpreter for the DB.
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

class model_session extends Controller
{

    private $_session_data = null;
    private $_user_data = null;
    private $_group_data = null;
    private $_exists = false;
    public function __construct()
    {
        Debug::log('Model Constructed: '.get_class($this));
        Debug::gend();
        return $this;
    }
    public static function sql() 
    {
        $query = "CREATE TABLE `sessions` (
  `ID` int(11) NOT NULL,
  `user_ID` int(5) NOT NULL,
  `IP` varchar(45) NOT NULL,
  `hash` varchar(80) NOT NULL,
  `last_page` varchar(35) NOT NULL COMMENT 'Last page the user was active on.',
  `username` varchar(20) NOT NULL COMMENT 'Username',
  `user_group` int(5) NOT NULL COMMENT 'users group',
  `expire` int(10) NOT NULL,
  `token` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`ID`);
ALTER TABLE `sessions`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'primary index value', AUTO_INCREMENT=0;";
        return $query;
    }

    public function authenticate() 
    {
        $this->check(Session::get(Config::get('session/session_name')));
        if (!$this->exists()) {
            $this->check_cookie(Cookie::get(Config::get('remember/cookie_name')));
        }
        if (!$this->exists()) {
            return false;
        }
        return true;
    }
    /**
     * Check if a session exists, verifies the username,
     * password, and IP address to prevent forgeries.
     * 
     * @param  int $ID - The ID of the session being checked.
     * 
     * @return bool
     */
    public function check($ID)
    {
        if ($ID == false) {
            return false;
        }
        if (!Check::ID($ID)) {
            return false;
        }
        $data = Self::$_db->get('sessions', array('ID', '=', $ID));
        if ($data->count() == 0) {
            Debug::info('Session_check: Session not found in DB '. $ID);
            return false;
        }
        $session = $data->first();
        if(Self::$_user->get($session->user_ID) === false) {
            Debug::info('User not found in DB.');
            $this->destroy($ID);
            return false;
        }
        $user = Self::$_user->data();
        if ($user->username != $session->username)
        {
            Debug::info("Usernames do not match.");
            $this->destroy($ID);
            return false;
        }
        if ($user->password != $session->hash)
        {
            Debug::info("Session Password does not match.");
            $this->destroy($ID);
            return false;
        }
        if (time() > $session->expire)
        {
            Debug::info("Session Expired.");
            $this->destroy($ID);
            return false;
        }
        if ($user->user_group !== $session->user_group)
        {
            Debug::info("Groups do not match.");
            $this->destroy($ID);
            return false;
        }
        if ($_SERVER['REMOTE_ADDR'] != $session->IP)
        {
            Debug::info("IP addresses do not match.");
            $this->destroy($ID);
            return false;
        }
        $this->_session_data = $session;
        $this->_user_data = $user;
        $this->_group_data = Self::$_group->find_by_ID($user->user_group);
        $this->_exists = true;
        return true;
    }

    /**
     * Checks the "remember me" cookie we use to identify 
     * unique sessions across multiple visits. Checks that
     * the tokens match, checks the username as well as the 
     * password from the database to ensure it hasn't been 
     * modified elsewhere between visits.
     * 
     * @param  string $token - The unique token saved as a cookie that is being checked.
     * 
     * @return bool
     */
    public function check_cookie($token)
    {
        if ($token == false) {
            return false;
        }
        $data = Self::$_db->get('sessions', array('token', '=', $token));
        if (!$data->count()) {
            Cookie::delete(Config::get('remember/cookie_name'));
            return false;
        }
        $session = $data->first();
        if(Self::$_user->get($session->user_ID) === false) {
            Cookie::delete(Config::get('remember/cookie_name'));
            return false;
        }
        $user = Self::$_user->data();
        if ($user->username != $session->username)
        {
            Debug::info("Usernames do not match.");
            Cookie::delete(Config::get('remember/cookie_name'));
            $this->destroy($ID);
            return false;
        }
        if ($user->password != $session->hash)
        {
            Debug::info("Session Password does not match.");
            Cookie::delete(Config::get('remember/cookie_name'));
            $this->destroy($ID);
            return false;
        }
        if (time() > $session->expire)
        {
            Debug::info("Session Expired.");
            Cookie::delete(Config::get('remember/cookie_name'));
            $this->destroy($ID);
            return false;
        }
        //2 weeks
        $expire = (time() + (60 * 60 * 24 * 14));
        return $this->new_session($user->ID, $expire);
    }
    /**
     * Creates a new session from the data provided. The 
     * expiration time is optional and will be set to the 
     * system default if not provided.
     * 
     * @param  int $ID     The User ID of the new session holder.
     * @param  int $expire The expiration time (in seconds).
     * 
     * @return bool
     */
    public function new_session($ID, $expire = null)
    {
        $IP = $_SERVER['REMOTE_ADDR'];
        $last_page = "Index";
        if (!isset($expire))
        {
            //1 day
            $expire = (time() + (3600 * 24));
            Debug::log('Using default expiration time');
        }
        if(Self::$_user->get($ID) === false)
        {
            Debug::info("User not found.");
            return false;
        }
        $user = Self::$_user->data();
        $username = $user->username;
        $hash = $user->password;
        $data = Self::$_db->get('sessions', array('user_ID', '=', $ID));
        if ($data->count()) {
            Debug::log('Deleting old session from db');
            $session = Self::$_db->first();
            $this->destroy($session->ID);
        }
        Self::$_db->insert('sessions', array(
                            'username' => $username,
                            'hash' => $hash,
                            'user_group' => $user->user_group,
                            'user_ID' => $ID,
                            'last_page' => $last_page,
                            'expire' => $expire,
                            'IP' => $IP,
                            'token' => Code::new_token(),
                            ));
        $data = Self::$_db->get('sessions', array('user_ID', '=', $ID));
        $session = Self::$_db->first();
        $this->_group_data = Self::$_group->find_by_ID($user->user_group);
        $this->_session_data = $session;
        $this->_user_data = $user;
        $this->_exists = true;
        return true;
    }

    /**
     * Function to update the users' current active page.
     *     
     * @param  string $page The name of the page you are updating to.
     * @param  int|null $id  The ID of the session you are updating. 
     * 
     * NOTE: Current session assumed if no $id is provided.
     * 
     * @return bool      true or false depending on success.
     */
    public function update_page($page, $ID = null)
    {
        if (!isset($ID)) {
            if (isset($this->_session_data)) {
                $ID = $this->_session_data->ID;
            }
        }
        if (empty($ID)) {
            Debug::info("Session empty.");

            return false;
        }
        if (!Check::ID($ID)) {
            Debug::info('Invalid session ID.');

            return false;
        }
        $set = array('last_page' => $page);
        if (!Self::$_db->update('sessions', $ID, $set)) {
            Debug::info("Failed to update Session.");

            return false;
        }
        return true;
    }

    /**
     * Destroy a session.
     * 
     * @param  int $ID - The ID of the session you wish to destroy.
     * 
     * @return bool     true if destroyed, false if it failed.
     */
    public function destroy($ID)
    {
        if (!Check::ID($ID)) {
            Debug::info("Invalid Session ID.");

            return false;
        }
        $data = Self::$_db->get('sessions', array('ID', '=', $ID));
        if (!$data->count()) {
            Debug::info('Session_Destroy: Session not found in DB.');

            return false;
        }
        $where = array('ID', '=', $ID);
        Self::$_db->delete('sessions', $where);
        $this->_session_data = null;
        $this->_user_data = null;
        $this->_group_data = Self::$_group->find_by_ID(5);
        $this->_exists = false;
        return true;
    }

    /**
     * Function for returning the current session's data.
     *     
     * @return array|null - an array with the current session stored in it.
     */
    public function session_data()
    {
        return $this->_session_data;
    }

    /**
     * Function for returning the current session's data.
     *     
     * @return array|null - an array with the current session stored in it.
     */
    public function group_data()
    {
        return $this->_group_data;
    }

    /**
     * Function to return the current users information.
     *     
     * @return array - The active user's data.
     */
    public function user_data()
    {
        return $this->_user_data;
    }

    /**
     * Function for checking if a session exists.
     *    
     * @return bool - Whether the session exists or not.
     */
    public function exists()
    {
        return $this->_exists;
    }
}
