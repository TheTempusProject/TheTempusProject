<?php
/**
 * Models/bugreport.php
 *
 * This class is used for the manipulation of the bugreports database table.
 *
 * @version 3.0
 *
 * @author  Joey Kimsey <JoeyKimsey@thetempusproject.com>
 *
 * @link    https://TheTempusProject.com
 *
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */

namespace TheTempusProject\Models;

use TempusProjectCore\Classes\Check;
use TempusProjectCore\Classes\Permission;
use TempusProjectCore\Classes\Config;
use TempusProjectCore\Core\Controller;
use TempusProjectCore\Classes\Debug;
use TempusProjectCore\Classes\CustomException;
use TempusProjectCore\Classes\DB;

class Bugreport extends Controller
{
    private static $log;
    private static $user;
    private static $enabled = null;
    
    /**
     * The model constructor.
     */
    public function __construct()
    {
        Debug::log('Model Constructed: '.get_class($this));
    }

    /**
     * Returns the current model version.
     *
     * @return string - the correct model version
     */
    public static function modelVersion()
    {
        return '3.0.0';
    }

    /**
     * Returns an array of models required to run this model without error.
     *
     * @return array - An array of models
     */
    public function requiredModels()
    {
        $required = [
            'log',
            'user'
        ];
        return $required;
    }
    
    /**
     * Tells the installer which types of integrations your model needs to install.
     *
     * @return array - Install flags
     */
    public static function installFlags()
    {
        $flags = [
            'installDB' => true,
            'installPermissions' => true,
            'installConfigs' => true,
            'installResources' => false,
            'installPreferences' => false
        ];
        return $flags;
    }

    /**
     * This function is used to install database structures needed for this model.
     *
     * @return boolean - The status of the completed install
     */
    public static function installDB()
    {
        self::$db->newTable('bugreports');
        self::$db->addfield('userID', 'int', '11');
        self::$db->addfield('time', 'int', '10');
        self::$db->addfield('repeat', 'varchar', '5');
        self::$db->addfield('ourl', 'varchar', '256');
        self::$db->addfield('url', 'varchar', '256');
        self::$db->addfield('ip', 'varchar', '15');
        self::$db->addfield('description', 'text', '');
        self::$db->createTable();
        return self::$db->getStatus();
    }
    
    /**
     * Install permissions needed for the model.
     *
     * @return bool - If the permissions were added without error
     */
    public static function installPermissions()
    {
        Permission::addPerm('bugreport', false);
        return Permission::savePerms(true);
    }

    /**
     * Install configuration options needed for the model.
     *
     * @return bool - If the configurations were added without error
     */
    public static function installConfigs()
    {
        Config::addConfigCategory('bugreports');
        Config::addConfig('bugreports', 'enabled', true);
        Config::addConfig('bugreports', 'email', true);
        Config::addConfig('bugreports', 'emailCopy', true);
        Config::addConfig('bugreports', 'emailTemplate', true);
        return Config::saveConfig();
    }

    /**
     * This method will remove all the installed model components.
     *
     * @return bool - If the uninstall was completed without error
     */
    public static function uninstall()
    {
        Config::removeConfigCategory('bugreports', true);
        Permission::removePerm('bugreport', true);
        self::$db->removeTable('bugreports');
        return true;
    }

    /**
     * Checks if the model and database are both enabled.
     *
     * @return bool - if the model is enabled or not
     */
    private static function enabled()
    {
        if (empty(self::$enabled)) {
            self::$enabled = (DB::enabled() && Config::get('bugreports/enabled') == true);
        }
        return self::$enabled;
    }

    /**
     * Select a bug report from the logs table.
     *
     * @param  int $ID - The bug report ID.
     *
     * @return array
     */
    public function get($ID)
    {
        if (!Check::id($ID)) {
            return false;
        }
        $data = self::$db->get('bugreports', ['ID', '=', $ID]);
        if ($data->count() == 0) {
            Debug::info('Bug report not found.');
            return false;
        }
        return $this->parse($data->first());
    }

    /**
     * This function parses the bug reports description and
     * separates it into separate keys in the array.
     *
     * @param  array $data - The data being parsed.
     *
     * @return array
     */
    private function parse($data)
    {
        if (!isset(self::$user)) {
            self::$user = $this->model('user');
        }
        foreach ($data as $instance) {
            if (!is_object($instance)) {
                $instance = $data;
                $end = true;
            }
            $instance->submittedBy = self::$user->getUsername($instance->userID);
            $out[] = $instance;
            if (!empty($end)) {
                break;
            }
        }
        return $out;
    }

    /**
     * Retrieves a list of all bug reports
     *
     * @param  string $filter WIP
     *
     * @return bool|array
     */
    public function listReports($filter = null)
    {
        $data = self::$db->getPaginated('bugreports', '*');
        if ($data->count() == 0) {
            Debug::info('No bug reports found.');
            return false;
        }

        return (object) $this->parse($data->results());
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
    public static function create($ID, $url, $oUrl, $repeat, $description)
    {
        if (!Check::id($ID)) {
            return false;
        }
        if (!self::enabled()) {
            Debug::info('Bug Report Logging is disabled in the config.');

            return false;
        }
        $fields = [
            'userID' => $ID,
            'time' => time(),
            'repeat' => $repeat,
            'ourl' => $oUrl,
            'url' => $url,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'description' => $description,
        ];
        if (!self::$db->insert('bugreports', $fields)) {
            new CustomException('bugreports');

            return false;
        }
        return true;
    }

    /**
     * Function to clear logs of a defined type.
     *
     * @param  string $data - The log type to be cleared
     *
     * @return bool
     *
     * @todo  this is probably dumb
     */
    public function clear()
    {
        if (!isset(self::$log)) {
            self::$log = $this->model('log');
        }
        self::$db->delete('bugreports', ['ID', '>=', '0']);
        self::$log->admin("Cleared Bug Reports");
        Debug::info("Bug Reports Cleared");
        return true;
    }
    
    /**
     * Function to delete the specified log.
     *
     * @param  int|array $data the log ID or array of ID's to be deleted
     *
     * @return bool
     */
    public function delete($data)
    {
        if (!isset(self::$log)) {
            self::$log = $this->model('log');
        }
        foreach ($data as $instance) {
            if (!is_array($data)) {
                $instance = $data;
                $end = true;
            }
            if (!Check::id($instance)) {
                $error = true;
            }
            self::$db->delete('bugreports', ['ID', '=', $instance]);
            self::$log->admin("Deleted Bug Report: $instance");
            Debug::info("Report deleted: $instance");
            if (!empty($end)) {
                break;
            }
        }
        if (!empty($error)) {
            Debug::info('One or more invalid ID\'s.');
            return false;
        }
        return true;
    }
}
