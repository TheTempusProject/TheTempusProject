<?php
/**
 * Models/recaptcha.php
 *
 * This class is for the use and management of google's recapcha2.
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
use ReCaptcha\ReCaptcha as GoogleReCaptcha;
use TempusProjectCore\Classes\Check;
use TempusProjectCore\Classes\Config;
use TempusProjectCore\Core\Controller;

class ReCaptcha extends Controller
{
    public static $errors = null;
    private $recaptcha;
    private static $log;
    private static $enabled = null;
    private static $privateKey = null;
    private static $siteKey = null;
    private static $sendIP = null;

    /**
     * The model constructor.
     */
    public function __construct()
    {
        Debug::log('Model Constructed: '.get_class($this));
        $this->load();
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
            'installDB' => false,
            'installPermissions' => false,
            'installConfigs' => true,
            'installResources' => false,
            'installPreferences' => false
        ];
        return $flags;
    }

    /**
     * Install configuration options needed for the model.
     *
     * @return bool - If the configurations were added without error
     */
    public static function installConfigs()
    {
        Config::addConfigCategory('recaptcha');
        Config::addConfig('recaptcha', 'siteKey', '');
        Config::addConfig('recaptcha', 'privateKey', '');
        Config::addConfig('recaptcha', 'sendIP', false);
        Config::addConfig('recaptcha', 'enabled', false);
        return Config::saveConfig();
    }

    /**
     * This method will remove all the installed model components.
     *
     * @return bool - if the uninstall was completed without error
     */
    public static function uninstall()
    {
        Config::removeConfigCategory('recaptcha', true);
        return true;
    }

    public function verify($hash)
    {
        self::$errors = null;
        $this->recaptcha = new GoogleReCaptcha(self::$privateKey);
        if (self::$sendIP) {
            $response = $this->recaptcha->verify($hash, $_SERVER['REMOTE_ADDR']);
        } else {
            $response = $this->recaptcha->verify($hash);
        }
        if (!$response->isSuccess()) {
            self::$errors = $response->getErrorCodes();
            return false;
        }
        return true;
    }

    public function load()
    {
        if (self::$enabled == null) {
            $mods = apache_get_modules();
            if (!in_array('mod_rewrite', $mods)) {
                self::$enabled = false;
            }
            self::$enabled = Config::get('recaptcha/enabled');
            self::$privateKey = Config::get('recaptcha/privateKey');
            self::$siteKey = Config::get('recaptcha/siteKey');
            self::$sendIP = Config::get('recaptcha/sendIP');
        }
        return self::$enabled;
    }

    public function enabled()
    {
        if (self::$enabled == null) {
            $this->load();
        }
        return self::$enabled;
    }
    public function getErrors()
    {
        return self::$errors;
    }
}
