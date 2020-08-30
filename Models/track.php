<?php
/**
 * Models/tracking.php
 *
 * This class is used to provide a link and click treacking interface.
 *
 * @version 2.1
 *
 * @author  Joey Kimsey <JoeyKimsey@thetempusproject.com>
 *
 * @link    https://TheTempusProject.com
 *
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */

namespace TheTempusProject\Models;

use TempusProjectCore\Classes\Check;
use TempusProjectCore\Classes\Code;
use TempusProjectCore\Core\Controller;
use TempusProjectCore\Classes\Debug;
use TempusProjectCore\Classes\Config;
use TempusProjectCore\Classes\DB;
use TempusProjectCore\Classes\Session;
use TempusProjectCore\Classes\Cookie;
use TempusProjectCore\Classes\Log;
use TempusProjectCore\Classes\Input;
use TempusProjectCore\Classes\Email;
use TempusProjectCore\Core\Installer;

class Track extends Controller
{
    private static $log;

    public function __construct()
    {
        Debug::log('Model Constructed: '.get_class($this));
    }

    /**
     * This function is used to install database structures and configuration
     * options needed for this model.
     *
     * @return boolean - The status of the completed install.
     */
    public static function installDB()
    {
        self::$db->newTable('tracked');
        self::$db->addfield('referer', 'varchar', '1024');
        self::$db->addfield('hash', 'varchar', '256');
        self::$db->addfield('time', 'int', '10');
        self::$db->addfield('data', 'text', '');
        self::$db->createTable();
        self::$db->newTable('trackingReference');
        self::$db->addfield('createdBy', 'varchar', '32');
        self::$db->addfield('created', 'int', '10');
        self::$db->addfield('linkType', 'varchar', '32');
        self::$db->addfield('hash', 'varchar', '256');
        self::$db->createTable();
        return self::$db->getStatus();
    }

    public static function requiredModels()
    {
        $required = [
            'log'
        ];
        return $required;
    }

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

    public static function modelVersion()
    {
        return '2.0.0';
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

    public function count($contentType, $hash)
    {
        if (!Check::id($contentID)) {
            Debug::info("tracked: illegal ID.");
            
            return false;
        }
        if (!Check::dataTitle($contentType)) {
            Debug::info("tracked: illegal Type.");
            
            return false;
        }
        $where = ['contentType', '=', $contentType, 'AND', 'contentID', '=', $contentID];
        $data = self::$db->get('tracked', $where);
        if (!$data->count()) {
            Debug::info("No tracked data found.");

            return 0;
        }
        return $data->count();
    }

    public function display($displayCount, $contentType, $contentID)
    {
        if (!Check::id($contentID)) {
            Debug::info("Comments: illegal ID.");
            
            return false;
        }
        if (!Check::dataTitle($contentType)) {
            Debug::info("Comments: illegal Type.");
            
            return false;
        }
        $where = ['contentType', '=', $contentType, 'AND', 'contentID', '=', $contentID];
        $commentData = self::$db->get('comments', $where, 'created', 'DESC', [0, $displayCount]);
        if (!$commentData->count()) {
            Debug::info("No comments found.");

            return false;
        }
        return self::filterComments($commentData->results());
    }

    public function track($contentType, $contentID, $comment)
    {
        if (!Check::id($contentID)) {
            Debug::info("Comments: illegal ID.");
            
            return false;
        }
        if (!Check::dataTitle($contentType)) {
            Debug::info("Comments: illegal Type.");
            
            return false;
        }
        $fields = [
            'author' => self::$activeUser->ID,
            'edited' => time(),
            'created' => time(),
            'content' => $comment,
            'contentType' => $contentType,
            'contentID' => $contentID,
            'approved' => 0,
            ];
        if (!self::$db->insert('comments', $fields)) {
            new CustomException('newComment');
            Debug::error("Comments: $data not created: $fields");

            return false;
        }
        return true;
    }
    
    public function create($contentType, $contentID, $comment)
    {
        if (!Check::id($contentID)) {
            Debug::info("Comments: illegal ID.");
            
            return false;
        }
        if (!Check::dataTitle($contentType)) {
            Debug::info("Comments: illegal Type.");
            
            return false;
        }
        $fields = [
            'author' => self::$activeUser->ID,
            'edited' => time(),
            'created' => time(),
            'content' => $comment,
            'contentType' => $contentType,
            'contentID' => $contentID,
            'approved' => 0,
            ];
        if (!self::$db->insert('comments', $fields)) {
            new CustomException('newComment');
            Debug::error("Comments: $data not created: $fields");

            return false;
        }
        return true;
    }
}
