<?php
/**
 * Models/model_subscribe.php.
 *
 * This class is used for the manipulation of the subscribers database table.
 *
 * @version 0.9
 *
 * @author  Joey Kimsey <joeyk4816@gmail.com>
 *
 * @link    https://github.com/JoeyK4816/TheTempusProject
 *
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 *
 * @todo  make this send a confirmation email
 */

namespace TheTempusProject\Models;

use TempusProjectCore\Classes\Check as Check;
use TempusProjectCore\Classes\Code as Code;
use TempusProjectCore\Core\Controller as Controller;
use TempusProjectCore\Classes\Debug as Debug;

class model_subscribe extends Controller
{
    public function __construct()
    {
        Debug::log('Model Constructed: '.get_class($this));
        Debug::gend();
    }
    public static function sql()
    {
        $query = "CREATE TABLE `subscribers` (
`ID` int(11) NOT NULL,
`confirmed` int(1) NOT NULL,
`confirmation_code` varchar(80) NOT NULL,
`email` varchar(75) NOT NULL COMMENT 'email address being stored'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Email subscriptions list.';
ALTER TABLE `subscribers`
  ADD PRIMARY KEY (`ID`);
ALTER TABLE `subscribers`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'primary index value', AUTO_INCREMENT=0;";

        return $query;
    }

    /**
     * Adds an email to the subscribers database.
     *
     * @param string $data - the email you are trying to add.
     *
     * @return bool
     */
    public static function add($email)
    {
        if (Check::email($email)) {
            $fields = array(
                'email' => $email,
                'confirmation_code' => Code::new_confirmation(),
                'confirmed' => 0,
                );
            $user = Self::$_db->get('subscribers', array('email', '=', $email));
            if (!$user->count()) {
                Self::$_db->insert('subscribers', $fields);

                return true;
            } else {
                Debug::error('email already subscribed.');

                return false;
            }
        }

        return false;
    }
    /**
     * removes an email from the subscribers database.
     *
     * @param string $data - the email you are trying to remove.
     *
     * @return bool
     */
    public static function unsubscribe($email, $code)
    {
        if (!Check::email($email)) {
            return false;
        }
        $user = Self::$_db->get('subscribers', array('email', '=', $email, 'AND', 'confirmation_code', '=', $code));
        if (!$user->count()) {
            return false;
        }
        Self::$_db->delete('subscribers', array('ID', '=', $user->first()->ID));
        return true;
    }
    /**
     * removes an email from the subscribers database.
     *
     * @param string $data - the email you are trying to remove.
     *
     * @return bool
     */
    public static function remove($data)
    {
        if (Check::ID($data)) {
            $subscribed = Self::$_db->get('subscribers', array('ID', '=', $data));
            if (!$subscribed->count()) {
                Debug::error('No one with that email is subscribed.');

                return false;
            }
            Self::$_db->delete('subscribers', array('ID', '=', $data));

            return true;
        }

        return false;
    }

    /**
     * Compiles a list of all users, allowing for filtering the list.
     *
     * @todo
     * 
     * @param  array $filter - A filter to be applied to the users list.
     * 
     * @return bool|object - Depending on success.
     */
    public function subscriber_list($filter = null)
    {
        $data = Self::$_db->get('subscribers', "*");
        if (!$data->count()) {
            return false;
        }
        return (object) $data->results();
    }

    /**
     * Compiles a list of all users, allowing for filtering the list.
     *
     * @todo
     * 
     * @param  array $filter - A filter to be applied to the users list.
     * 
     * @return bool|object - Depending on success.
     */
    public function get($email = null)
    {
        if (!check::email($email)) {
            return false;
        }
        $data = Self::$_db->get('subscribers', array("email", '=', $email));
        if (!$data->count()) {
            return false;
        }
        return (object) $data->first();
    }
}
