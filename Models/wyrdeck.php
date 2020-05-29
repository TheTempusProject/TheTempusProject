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

class wyrdeck extends Controller
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
        self::$db->newTable('wyr_decks');
        self::$db->addfield('userID', 'int', '11');
        self::$db->addfield('time', 'int', '10');
        self::$db->addfield('title', 'varchar', '80');
        self::$db->addfield('description', 'text', '');
        self::$db->createTable();
        return self::$db->getStatus();
    }

    private static function enabled()
    {
        if (empty(self::$enabled)) {
            self::$enabled = (DB::enabled() && Config::get('wyr/enabled') == true);
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
        $data = self::$db->get('wyr_decks', ['ID', '=', $ID]);
        if ($data->count() == 0) {
            Debug::info('Deck not found.');
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


    public function listCards($deckId = null)
    {
        $data = self::$db->getPaginated('wyr', ["deck", "=" "deckId"]);
        if ($data->count() == 0) {
            Debug::info('No cards found.');
            return false;
        }

        return (object) $this->parse($data->results());
    }
    public function list($filter = null)
    {
        $data = self::$db->getPaginated('wyr_decks', '*');
        if ($data->count() == 0) {
            Debug::info('No cards found.');
            return false;
        }

        return (object) $this->parse($data->results());
    }
    public function listDecksByUser($filter = null)
    {
        $data = self::$db->getPaginated('wyr_decks', ["userID", "=" "deckId"]);
        if ($data->count() == 0) {
            Debug::info('No cards found.');
            return false;
        }

        return (object) $this->parse($data->results());
    }

    public static function create($ID, $title, $description)
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
            'title' => $title,
            'description' => $description,
        ];
        if (!self::$db->insert('wyr_decks', $fields)) {
            new CustomException('wyr_decks');

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
    public function clear($deckId)
    {
        self::$db->delete('wyr_decks', ['ID', '=', $deckId]);
        self::$log->admin("Cleared wyr Deck");
        Debug::info("wyr deck Cleared");
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
            self::$db->delete('wyr_decks', ['ID', '=', $instance]);
            self::$log->admin("Deleted wyr deck: $instance");
            Debug::info("deck deleted: $instance");
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
