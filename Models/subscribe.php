<?php
/**
 * Models/subscribe.php
 *
 * This class is used for the manipulation of the subscribers database table.
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
use TempusProjectCore\Classes\Code;
use TempusProjectCore\Core\Controller;
use TempusProjectCore\Classes\Debug;

class Subscribe extends Controller
{
    private static $log;

    /**
     * The model constructor.
     */
    public function __construct()
    {
        Debug::log('Model Constructed: ' . get_class($this));
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
        self::$db->newTable('subscribers');
        self::$db->addfield('confirmed', 'int', '1');
        self::$db->addfield('subscribed', 'int', '10');
        self::$db->addfield('confirmationCode', 'varchar', '80');
        self::$db->addfield('email', 'varchar', '75');
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
        self::$db->removeTable('subscribers');
        return true;
    }

    /**
     * Adds an email to the subscribers database.
     *
     * @param string $email - the email you are trying to add.
     *
     * @return bool
     */
    public function add($email)
    {
        if (!Check::email($email)) {
            return false;
        }
        $alreadyExists = self::$db->get('subscribers', ['email', '=', $email]);
        if ($alreadyExists->count()) {
            Debug::info('email already subscribed.');

            return false;
        }
        $fields = [
            'email'             => $email,
            'confirmationCode' => Code::genConfirmation(),
            'confirmed'         => 0,
            'subscribed'         => time(),
        ];
        self::$db->insert('subscribers', $fields);
        return true;
    }

    /**
     * Removes an email from the subscribers database.
     *
     * @param string $data - The email you are trying to remove.
     * @param string $code - The confirmation code to unsubscribe.
     *
     * @return boolean
     */
    public function unsubscribe($email, $code)
    {
        if (!Check::email($email)) {
            return false;
        }
        $user = self::$db->get('subscribers', ['email', '=', $email, 'AND', 'confirmationCode', '=', $code]);
        if (!$user->count()) {
            Debug::info('subscribe::unsubscribe - Cannot find subscriber with that email and code');
            return false;
        }
        self::$db->delete('subscribers', ['ID', '=', $user->first()->ID]);
        return true;
    }

    /**
     * Removes an email from the subscribers table.
     *
     * @param string $id - The email you are trying to remove.
     *
     * @return bool
     */
    public function remove($data)
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
            self::$db->delete('subscribers', ['ID', '=', $instance]);
            self::$log->admin("Deleted subscriber: $instance");
            Debug::info("subscriber Deleted: $instance");
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

    /**
     * Compiles a list of all subscribers, allowing for filtering the list.
     *
     * @param  array $filter - A filter to be applied to the subscriber list.
     *
     * @return bool|object - Depending on success.
     */
    public function listSubscribers($filter = null)
    {
        $data = self::$db->getPaginated('subscribers', "*");
        if (!$data->count()) {
            Debug::info('subscribe::listSubscribers - No subscribers found');
            return false;
        }
        return (object) $data->results();
    }

    /**
     * Returns a subscriber object for the provided email address.
     *
     * @param  string $email - An email address to look for.
     *
     * @return bool|object - Depending on success.
     */
    public function get($email)
    {
        if (!Check::email($email)) {
            return false;
        }

        $data = self::$db->get('subscribers', ["email", '=', $email]);
        if (!$data->count()) {
            Debug::info('subscribe::listSubscribers - Email not found');
            return false;
        }

        return (object) $data->first();
    }
}
