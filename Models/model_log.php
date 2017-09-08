<?php
/**
 * Models/model_log.php.
 *
 * Model for handling our logging.
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
use TempusProjectCore\Classes\Input as Input;
use TempusProjectCore\Classes\Email as Email;

class model_log extends Controller
{
    private static $_enabled;
    private $_usernames;

    public function __construct()
    {
        Debug::log('Model Constructed: '.get_class($this));
        Debug::gend();
    }
    public static function sql() 
    {
        $query = "CREATE TABLE `logs` (
  `ID` int(11) NOT NULL,
  `user_ID` int(5) NOT NULL DEFAULT '0' COMMENT 'User ID being logged.',
  `time` int(10) NOT NULL COMMENT 'Time the event was logged',
  `action` text NOT NULL COMMENT 'Action being logged',
  `IP` varchar(14) NOT NULL COMMENT 'IP address of the user',
  `source` text NOT NULL COMMENT 'Source of the log.'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Logs for all major actions from the framework';
ALTER TABLE `logs`
  ADD PRIMARY KEY (`ID`);
ALTER TABLE `logs`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'primary index value', AUTO_INCREMENT=0;";
        return $query;
    }

    /**
     * Creates a DB connection.
     */
    private static function enabled($type)
    {
        if (empty(Self::$_db)) {
            Self::$_db = DB::getInstance();
        }
        if (empty(Self::$_enabled)) {
            Self::$_enabled = DB::enabled();
        }
        if (Self::$_enabled == false) {
            return false;
        }
        return Config::get('logging/' . $type);
    }

    /**
     * Retrieves a log from the database.
     * 
     * @param  int $ID - The Log ID we are searching for
     * 
     * @return bool|object
     */
    public function get_log($ID)
    {
        if (!Check::ID($ID)) {
            return false;
        }
        $data = Self::$_db->get('logs', array('ID', '=', $ID));
        if (!$data->count()) {
            return false;
        }
        return $data->first();
    }

    /**
     * Select feedback from the logs table.
     *     
     * @param  int $ID - The feedback ID.
     * 
     * @return array
     */
    public function get_error($ID)
    {
        if (!Check::ID($ID)) {
            return false;
        }
        $data = Self::$_db->get('logs', array('ID', '=', $ID));
        if (!$data->count()) {
            return false;
        }
        return $this->parse_error($data->first());
    }

    /**
     * Select feedback from the logs table.
     *     
     * @param  int $ID - The feedback ID.
     * 
     * @return array
     */
    public function get_feedback($ID)
    {
        if (!Check::ID($ID)) {
            return false;
        }
        $data = Self::$_db->get('logs', array('ID', '=', $ID));
        if (!$data->count()) {
            return false;
        }
        return $this->parse_feedback($data->first());
    }

    /**
     * Select a bug report from the logs table.
     *     
     * @param  int $ID - The bug report ID.
     * 
     * @return array
     */
    public function get_bug_report($ID)
    {
        if (!Check::ID($ID)) {
            return false;
        }
        $data = Self::$_db->get('logs', array('ID', '=', $ID));
        if (!$data->count()) {
            return false;
        }
        return $this->parse_bug_report($data->first());
    }

    /**
     * This function parses the error description and 
     * separates it into separate keys in the array.
     * 
     * @param  array $data - An array of feedback we need to convert.
     * 
     * @return array
     */
    private function parse_error($data)
    {
        $x = 0;
        foreach ($data as $instance) 
        {
            if (!is_object($instance)) {
                $instance = $data;
                $end = 1;
            }
            $to_array = (array) $instance;
            if (isset($end)) {
                unset($end);
                $data = (object) array_merge(json_decode($instance->action, true), $to_array);
                break;
            }
            $data[$x] = (object) array_merge(json_decode($instance->action, true), $to_array);
            $x++;
        }
        return $data;
    }

    /**
     * This function parses the feedbacks description and 
     * separates it into separate keys in the array.
     * 
     * @param  array $data - An array of feedback we need to convert.
     * 
     * @return array
     */
    private function parse_feedback($data)
    {
        $x = 0;
        foreach ($data as $instance) 
        {
            if (!is_object($instance)) {
                $instance = $data;
                $end = 1;
            }
            $to_array = (array) $instance;
            if (isset($end)) {
                unset($end);
                $data = (object) array_merge(json_decode($instance->action, true), $to_array);
                break;
            }
            $data[$x] = (object) array_merge(json_decode($instance->action, true), $to_array);
            $x++;
        }
        return $data;
    }

    /**
     * This function parses the bug reports description and 
     * separates it into separate keys in the array.
     *    
     * @param  array $data - The data being parsed.
     * 
     * @return array
     */
    private function parse_bug_report($data)
    {
        $x = 0;
        foreach ($data as $instance) 
        {
            if (!is_object($instance)) {
                $instance = $data;
                $end = 1;
            }
            $to_array = (array) $instance;
            $to_array['submitted_by'] = Self::$_user->get_username($instance->user_ID);
            if (isset($end)) {
                unset($end);
                $data = (object) array_merge(json_decode($instance->action, true), $to_array);
                break;
            }
            $data[$x] = (object) array_merge(json_decode($instance->action, true), $to_array);
            $x++;
        }
        return $data;
    }

    /**
     * Retrieves a list of all errors.
     * 
     * @param  string $filter WIP
     * 
     * @return bool|array         
     */
    public function subscriber_list($filter = null)
    {
        $data = Self::$_db->get_paginated('subscribers', array('ID', '>=', '0'));
        if (!$data->count()) {
            return false;
        }
        return $data->results();
    }

    /**
     * Retrieves a list of all errors.
     * 
     * @param  string $filter WIP
     * 
     * @return bool|array         
     */
    public function error_list($filter = null)
    {
        $data = Self::$_db->get_paginated('logs', array('source', '=', 'Error'));
        if (!$data->count()) {
            return false;
        }
        return $this->parse_error($data->results());
    }

    /**
     * Retrieves a list of all feedback.
     * 
     * @param  string $filter WIP
     * 
     * @return bool|array
     */
    public function feedback_list($filter = null)
    {
        $data = Self::$_db->get_paginated('logs', array('source', '=', 'Feedback'));
        if (!$data->count()) {

            return false;
        }
        return (object) $this->parse_feedback($data->results());
    }

    /**
     * Retrieves a list of all logins
     * 
     * @param  string $filter WIP
     * 
     * @return bool|obj
     */
    public function login_list($filter = null)
    {
        $data = Self::$_db->get_paginated('logs', array('source', '=', 'login'));
        if (!$data->count()) {

            return false;
        }

        return (object) $data->results();
    }

    /**
     * Retrieves a list of all bug reports
     * 
     * @param  string $filter WIP
     * 
     * @return bool|array         
     */
    public function bug_report_list($filter = null)
    {
        $data = Self::$_db->get_paginated('logs', array('source', '=', 'Bug Report'));
        if (!$data->count()) {

            return false;
        }

        return (object) $this->parse_bug_report($data->results());
    }
    /**
     * Function to delete the specified log.
     * 
     * @param  int|array $ID the log ID or array of ID's to be deleted
     * 
     * @return bool
     */
    public function delete($ID)
    {
        if (is_array($ID)) {
            foreach($ID as $instance)
            {
                $x = 0;
                if (is_array($instance)) {
                    foreach ($instance as $id)
                    {
                        if (Check::ID($id)) {
                            Self::$_db->delete('logs', array('ID', '=', $id));
                            Debug::log("Log deleted: $id");
                            $x++;
                        }
                    }
                } else {
                    if (Check::ID($instance)) {
                        Self::$_db->delete('logs', array('ID', '=', $instance));
                        Debug::log("Log deleted: $instance");
                        $x++;
                    }
                }
                if ($x > 0) {
                    return true;
                }
            }
        } else {
            if (Check::ID($ID)) {
                Self::$_db->delete('logs', array('ID', '=', $ID));
                Debug::log("Log deleted: $ID");

                return true;
            }
        }

        return false;
    }

    /**
     * Function to clear logs of a defined type.
     * 
     * @param  string $data - The log type to be cleared
     * 
     * @return bool
     */
    public function clear($data)
    {
        if ($data == "login") {
            Self::$_db->delete('logs', array('source', '=', $data));
            return true;
        }
        if ($data == "error") {
            Self::$_db->delete('logs', array('source', '=', $data));
            return true;
        }
        if ($data == "feedback") {
            Self::$_db->delete('logs', array('source', '=', $data));
            return true;
        }
        if ($data == "bug_report") {
            Self::$_db->delete('logs', array('source', '=', $data));
            return true;
        }
        return false;
    }

    /**
     * Function for updating a log.
     * 
     * @param  int $ID ID of the log you are modifying
     * 
     * @return null
     * 
     * @todo
     */
    public function update($ID)
    {
        return;
    }

    /**
     * logs an error to the DB.
     *
     * @param int    $errorID - Any error code associated with the error, default is 500
     * @param string $data    - The action to be logged.
     *
     * @return null
     */
    public static function error($errorID = 500, $class = null, $function = null, $error = null, $data = null)
    {
        if (Self::enabled('errors')) {
            $data = array('class' => $class, 'function' => $function, 'error' => $error, 'description' => $data);
            $output = json_encode($data);
            $fields = array(
                'user_ID' => $errorID,
                'action' => $output,
                'time' => time(),
                'source' => 'Error',
            );
            if (!Self::$_db->insert('logs', $fields)) {
                new CustomException('LogError', $data);
            }
        } else {
            Debug::info('Error Logging is disabled in the config.');
        }
    }

    /**
     * Logs a login to the DB.
     *
     * @param int    $userID - The User ID being logged in
     * @param string $action - Must be 'pass' or 'fail'
     *
     * @return null
     */
    public static function login($userID, $action = 'fail')
    {
        if (Self::enabled('logins')) {
            $fields = array(
                'user_ID' => $userID,
                'action' => $action,
                'time' => time(),
                'source' => 'login',
                'IP' => $_SERVER['REMOTE_ADDR'],
            );
            if (!Self::$_db->insert('logs', $fields)) {
                new CustomException('LogLogin');
            }
        } else {
            Debug::info('Login Logging is disabled in the config.');
        }
    }

    /**
     * Logs a feedback form.
     *
     * @param  string $name     the name on the form
     * @param  string $email    the email provided
     * @param  string $feedback contents of the feedback form.
     *
     * @return null
     */
    public static function feedback($name, $email, $feedback)
    {
        if (Self::enabled('feedback')) {
            $data = array('name' => $name, 'email' => $email, 'feedback' => $feedback);
            $output = json_encode($data);
            $fields = array(
                'user_ID' => 0,
                'action' => $output,
                'time' => time(),
                'source' => 'Feedback',
                'IP' => $_SERVER['REMOTE_ADDR'],
            );
            if (!Self::$_db->insert('logs', $fields)) {
                new CustomException('LogFeedback');
            }
        } else {
            Debug::info('Feedback Logging is disabled in the config.');
        }
    }

    /**
     * Logs a Bug Report form.
     *
     * @param  int $ID           the user ID submitting the form
     * @param  string $url          the url
     * @param  string $o_url        the original url
     * @param  int $repeat       is repeatable?
     * @param  string $description_ description of the event.
     *
     * @return null
     */
    public static function bug_report($ID, $url, $o_url, $repeat, $description_)
    {
        if (!Check::ID($ID)) {
            return false;
        }
        if (Self::enabled('bug_reports')) {
            $data = array('url' => $url, 'ourl' => $o_url, 'repaeat' => $repeat, 'description' => $description_);
            $output = json_encode($data);
            $fields = array(
                'user_ID' => $ID,
                'action' => $output,
                'time' => time(),
                'source' => 'Bug Report',
                'IP' => $_SERVER['REMOTE_ADDR'],
            );
            if (!Self::$_db->insert('logs', $fields)) {
                new CustomException('LogBugReport');
            }
        } else {
            Debug::info('Bug Report Logging is disabled in the config.');
        }
    }
}
