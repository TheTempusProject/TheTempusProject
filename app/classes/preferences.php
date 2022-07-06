<?php
/**
 * classes/preferences.php
 *
 * This class handles all the hard-coded preferences.
 *
 * @version 3.0
 * @author  Joey Kimsey <JoeyKimsey@thetempusproject.com>
 * @link    https://TheTempusProject.com/Core
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */
namespace TheTempusProject\Classes;

use TempusProjectCore\Functions\{
    Debug, Check
};

class Preferences
{
    private static $preferences = false;
    private $location = false;
    private $initialized = false;

    /**
     * Default constructor which will attempt to load the preferences from the location specified.
     *
     * @param {string} [$location]
     * @return {null|object}
     */ 
    public function __construct($location) {
        $this->initialized = $this->load($location);
        if ($this->initialized !== false) {
            return $this;
        }
    }

    /**
     * Attempts to retrieve then set the preferences from a file.
     * @note This function will reset the preferences every time it is used.
     *
     * @param {string} $location
     * @return {bool}
     */
    public function load($location) {
        self::$preferences = $this->getPrefs($location);
        if (self::$preferences === false) {
            return false;
        }
        $this->location = $location;
        return true;
    }

    /**
     * Opens and decodes the preferences json from the location provided.
     *
     * @param {string} [$location]
     * @return {bool|array}
     */
    public function getPrefs($location) {
        if (file_exists($location)) {
            return json_decode(file_get_contents($location), true);
        } else {
            Debug::warn("Preferences json not found: $location");
            return false;
        }
    }

    /**
     * Retrieves the preference option for $name.
     *
     * @param {string} [$name]
     * @return {WILD}
     */
    public static function get($name) {
        if (self::$preferences === false) {
            Debug::warn("Preferences not loaded.");
            return;
        }
        if (isset(self::$preferences[$name])) {
            return self::$preferences[$name];
        }
        Debug::warn("Preference not found: $name");
        return;
    }

    /**
     * Saves the current preferences.
     *
     * @param {bool} [$default] - Whether or not to save a default copy.
     * @return {bool}
     */
    public function save($default = false) {
        if (self::$preferences === false) {
            Debug::warn("Preferences not loaded.");
            return false;
        }
        if ($this->location === false) {
            Debug::warn("Preferences location not set.");
            return false;
        }
        if ($default) {
            $locationArray = explode('.', $this->location);
            $last = array_pop($locationArray);
            $locationArray[] = 'default';
            $locationArray[] = $last;
            $defaultLocation = implode('.', $locationArray);
            if (file_put_contents($defaultLocation, json_encode(self::$preferences))) {
                return true;
            }
            return false;
        }
        if (file_put_contents($this->location, json_encode(self::$preferences))) {
            return true;
        }
        return false;
    }

    /**
     * Adds a new preference to the $preferences array.
     *
     * @param {string} [$name]
     * @param {string} [$value]
     * @return {bool}
     */
    public function add($name, $value) {
        if (!Check::simpleName($name)) {
            Debug::error("Preference name invalid: $name");
            return false;
        }
        if (isset(self::$preferences[$name])) {
            Debug::warn("Preference already exists: $name");
            return false;
        }
        if (self::$preferences === false) {
            self::$preferences = array();
        }
        self::$preferences[$name] = $value;
        return true;
    }

    /**
     * Adds many new preferences to the $preferences array.
     *
     * @param {array} [$data]
     * @return {bool}
     */
    public function addMany($data) {
        if (!is_array($data)) {
            Debug::error("Preferences must be an array.");
            return false;
        }
        foreach ($data as $name => $value) {
            $this->add($name, $value);
        }
        return true;
    }

    /**
     * Removes an existing preference from the $preferences array.
     *
     * @param {string} [$name]
     * @param {string} [$save]
     * @return {bool}
     */
    public function remove($name, $save = false) {
        if (self::$preferences === false) {
            Debug::warn("Preferences not loaded.");
            return false;
        }
        if (!isset(self::$preferences[$name])) {
            Debug::error("Preference does not exist: $name");
            return false;
        }
        unset(self::$preferences[$name]);
        if ($save === true) {
            return $this->save(true);
        }
        return true;
    }
}
