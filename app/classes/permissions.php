<?php
/**
 * classes/permissions.php
 *
 * This class handles all the hard-coded permissions.
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

class Permissions
{
    private static $permissions = false;
    private $location = false;
    private $initialized = false;

    /**
     * Default constructor which will attempt to load the permissions from the location specified.
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
     * Attempts to retrieve then set the configuration from a file.
     * @note This function will reset the permissions every time it is used.
     *
     * @param {string} $location
     * @return {bool}
     */
    public function load($location) {
        self::$permissions = $this->getPerms($location);
        if (self::$permissions === false) {
            return false;
        }
        $this->location = $location;
        return true;
    }

    /**
     * Opens and decodes the permissions json from the location provided.
     *
     * @param {string} [$location]
     * @return {bool|array}
     */
    public function getPerms($location) {
        if (file_exists($location)) {
            return json_decode(file_get_contents($location), true);
        } else {
            Debug::warn("Permissions json not found: $location");
            return false;
        }
    }

    /**
     * Retrieves the permissions option for $name.
     *
     * @param {string} [$name]
     * @return {WILD}
     */
    public static function get($name) {
        if (self::$permissions === false) {
            Debug::warn("Permissions not loaded.");
            return;
        }
        if (isset(self::$permissions[$name])) {
            return self::$permissions[$name];
        }
        Debug::warn("Permission not found: $name");
        return;
    }

    /**
     * Saves the current permissions.
     *
     * @param {bool} [$default] - Whether or not to save a default copy.
     * @return {bool}
     */
    public function save($default = false) {
        if (self::$permissions === false) {
            Debug::warn("Permissions not loaded.");
            return false;
        }
        if ($this->location === false) {
            Debug::warn("Permissions location not set.");
            return false;
        }
        if ($default) {
            $locationArray = explode('.', $this->location);
            $last = array_pop($locationArray);
            $locationArray[] = 'default';
            $locationArray[] = $last;
            $defaultLocation = implode('.', $locationArray);
            if (file_put_contents($defaultLocation, json_encode(self::$permissions))) {
                return true;
            }
            return false;
        }
        if (file_put_contents($this->location, json_encode(self::$permissions))) {
            return true;
        }
        return false;
    }

    /**
     * Adds a new permission to the $permissions array.
     *
     * @param {string} [$name]
     * @param {string} [$value]
     * @return {bool}
     */
    public function add($name, $value) {
        if (!Check::simpleName($name)) {
            Debug::error("Permission name invalid: $name");
            return false;
        }
        if (isset(self::$permissions[$name])) {
            Debug::warn("Permission already exists: $name");
            return false;
        }
        if (self::$permissions === false) {
            self::$permissions = array();
        }
        self::$permissions[$name] = $value;
        return true;
    }

    /**
     * Adds many new permissions to the $permissions array.
     *
     * @param {array} [$data]
     * @return {bool}
     */
    public function addMany($data) {
        if (!is_array($data)) {
            Debug::error("Permissions must be an array.");
            return false;
        }
        foreach ($data as $name => $value) {
            $this->add($name, $value);
        }
        return true;
    }

    /**
     * Removes an existing permission from the $permissions array.
     *
     * @param {string} [$name]
     * @param {string} [$save]
     * @return {bool}
     */
    public function remove($name, $save = false) {
        if (self::$permissions === false) {
            Debug::warn("Permissions not loaded.");
            return false;
        }
        if (!isset(self::$permissions[$name])) {
            Debug::error("Permission does not exist: $name");
            return false;
        }
        unset(self::$permissions[$name]);
        if ($save === true) {
            return $this->save(true);
        }
        return true;
    }
}
