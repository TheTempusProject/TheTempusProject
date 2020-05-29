<?php
/**
 * Models/wyr.php
 *
 * This class is used for the manipulation of the wouldYouRather database table.
 *
 * @version 1.0
 *
 * @author  Joey Kimsey <JoeyKimsey@thetempusproject.com>
 *
 * @link    https://TheTempusProject.com
 *
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */

namespace TheTempusProject\Models;

use TempusProjectCore\Classes\Check as Check;
use TempusProjectCore\Classes\Permission as Permission;
use TempusProjectCore\Classes\Config as Config;
use TempusProjectCore\Core\Controller as Controller;
use TempusProjectCore\Classes\Debug as Debug;
use TempusProjectCore\Classes\CustomException as CustomException;
use TempusProjectCore\Classes\DB as DB;
use TempusProjectCore\Core\Updater as Updater;

class Wyr extends Controller
{
    private static $enabled = null;
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
    public static function install()
    {
        self::$db->newTable('wyr');
        self::$db->addfield('userID', 'int', '11');
        self::$db->addfield('time', 'int', '10');
        self::$db->addfield('deck', 'int', '5');
        self::$db->addfield('cardText', 'text', '');
        self::$db->createTable();
        Permission::addPerm('wyr', false);
        Permission::savePerms(true);
        Config::addConfigCategory('wyr');
        Config::addConfig('wyr', 'enabled', true);
        Config::saveConfig();
        return self::$db->getStatus();
    }

    private static function enabled()
    {
        return true;
        if (empty(self::$enabled)) {
            self::$enabled = (DB::enabled() && Config::get('wyr/enabled') == true);
        }
        return self::$enabled;
    }

    public static function count($deckID)
    {
        $where = ['deck', '=', $deckID];
        $data = self::$db->get('wyr', $where);
        if (!$data->count()) {
            Debug::info("No comments found.");

            return 0;
        }
        return $data->count();
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
        $data = self::$db->get('wyr', ['ID', '=', $ID]);
        if ($data->count() == 0) {
            Debug::info('Card not found.');
            return false;
        }
        return $this->parse($data->first());
    }
    public function getRandFromDeck($deckID, $count)
    {
        if (!Check::id($deckID)) {
            return false;
        }
        $data = self::$db->get('wyr', ['deck', '=', $deckID]);
        if ($data->count() == 0) {
            Debug::info('Card not found.');
            return false;
        }
        return $this->parse($data->results()[($count - 1)]);
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


    public function list($filter = null)
    {
        $data = self::$db->getPaginated('wyr', '*');
        if ($data->count() == 0) {
            Debug::info('No cards found.');
            return false;
        }

        return (object) $this->parse($data->results());
    }
    
    public function listByDeck($deckID)
    {
        $data = self::$db->getPaginated('wyr', ['deck', '=', $deckID]);
        if ($data->count() == 0) {
            Debug::info('No cards found.');
            return false;
        }

        return (object) $this->parse($data->results());
    }
    public function listByUser($filter = null)
    {
        $data = self::$db->getPaginated('wyr', '*');
        if ($data->count() == 0) {
            Debug::info('No cards found.');
            return false;
        }

        return (object) $this->parse($data->results());
    }

    public static function create($ID, $deck, $cardText)
    {
        if (!Check::id($ID)) {
            return false;
        }
        if (!self::enabled()) {
            Debug::info('Would You Rather is disabled in the config.');

            return false;
        }
        $fields = [
            'userID' => $ID,
            'time' => time(),
            'deck' => $deck,
            'cardText' => $cardText,
        ];
        if (!self::$db->insert('wyr', $fields)) {
            new CustomException('wyr');

            return false;
        }
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
        foreach ($data as $instance) {
            if (!is_array($data)) {
                $instance = $data;
                $end = true;
            }
            if (!Check::id($instance)) {
                $error = true;
            }
            self::$db->delete('wyr', ['ID', '=', $instance]);
            self::$log->admin("Deleted wyr card: $instance");
            Debug::info("card deleted: $instance");
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
