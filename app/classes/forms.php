<?php
/**
 * app/classes/forms.php
 *
 * This class is used in conjunction with TempusProjectCore\Classes\Check
 * to house complete form verification. You can utilize the
 * error reporting to easily define exactly what feedback you
 * would like to give.
 *
 * @version  3.0
 * @author   Joey Kimsey <Joey@thetempusproject.com> 
 * @link     https://TheTempusProject.com
 * @license  https://opensource.org/licenses/MIT [MIT LICENSE]
 */
namespace TheTempusProject\Classes;

use TempusProjectCore\Functions\{
    Input, Check, Debug
};

class Forms extends Check {
    private static $formHandlers = array();

    public static function check($formName) {
        if (empty(self::$formHandlers[$formName])) {
            Debug::error("Form not found: $formName");
            return false;
        }
        $handler = self::$formHandlers[$formName];
        return call_user_func_array([$handler['class'], $handler['method']], $handler['params']);
    }

    public static function addHandler($formName, $class, $method, $params = array()) {
        if (!empty(self::$formHandlers[$formName])) {
            return false;
        }
        self::$formHandlers[$formName] = array(
            'class' => $class,
            'method' => $method,
            'params' => $params,
        );
    }

    /**
     * Checks username formatting.
     *
     * Requirements:
     * - 4 - 16 characters long
     * - must only contain numbers and letters: [A - Z] , [a - z], [0 - 9]
     *
     * @param string $data - The string being tested.
     *
     * @return boolean
     */
    public static function checkUsername($data)
    {
        if (strlen($data) > 16) {
            self::addError("Username must be be 4 to 16 numbers or letters.", $data);

            return false;
        }
        if (strlen($data) < 4) {
            self::addError("Username must be be 4 to 16 numbers or letters.", $data);

            return false;
        }
        if (!ctype_alnum($data)) {
            self::addError("Username must be be 4 to 16 numbers or letters.", $data);

            return false;
        }

        return true;
    }
}