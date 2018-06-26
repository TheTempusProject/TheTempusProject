<?php
/**
 * Models/track.php
 *
 * This class is used to provide a link and click treacking interface.
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

use TempusProjectCore\Classes\Debug;
use TempusProjectCore\Classes\Check;
use TempusProjectCore\Core\Controller;

class Track extends Controller
{
    protected static $log;

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
    public static function requiredModels()
    {
        $required = [
            'log'
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
            'installPermissions' => false,
            'installConfigs' => false,
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
        self::$db->newTable('tracked');
        self::$db->addfield('referer', 'varchar', '1024');
        self::$db->addfield('trackingHash', 'varchar', '256');
        self::$db->addfield('time', 'int', '10');
        self::$db->addfield('data', 'text', '');
        self::$db->createTable();
        self::$db->newTable('trackingReference');
        self::$db->addfield('createdBy', 'int', '10');
        self::$db->addfield('createdAt', 'int', '10');
        self::$db->addfield('linkType', 'varchar', '32');
        self::$db->addfield('trackingHash', 'varchar', '256');
        self::$db->createTable();
        return self::$db->getStatus();
    }

    /**
     * This method will remove all the installed model components.
     *
     * @return bool - if the uninstall was completed without error
     */
    public static function uninstall()
    {
        self::$db->removeTable('tracked');
        self::$db->removeTable('trackingReference');
        return true;
    }

    /**
     * Retrieves a comment by its ID and parses it.
     *
     * @param  integer $id - The ID of the comment you are
     *                       trying to retrieve.
     *
     * @return object - The parsed comment db entry.
     */
    public function findById($id)
    {
        if (!Check::id($id)) {
            Debug::info("tracking: illegal ID.");
            
            return false;
        }
        $trackingData = self::$db->get('tracked', ['ID', '=', $id]);
        if (!$trackingData->count()) {
            Debug::info("No tracked data found.");

            return false;
        }
        return $trackingData->results();
    }

    /**
     * Function to delete the specified post.
     *
     * @param  int|array $ID the log ID or array of ID's to be deleted
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
            self::$db->delete('tracked', ['ID', '=', $instance]);
            self::$log->admin("Deleted tracked data: $instance");
            Debug::info("tracked data deleted: $instance");
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
