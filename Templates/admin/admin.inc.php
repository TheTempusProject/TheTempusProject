<?php
/**
 * templates/admin/admin.inc.php
 *
 * This is the loader for the admin template.
 *
 * @version 3.0
 * @author  Joey Kimsey <JoeyKimsey@thetempusproject.com>
 * @link    https://TheTempusProject.com
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */
namespace TheTempusProject\Templates;

use TempusProjectCore\Core\Controller;
use TempusProjectCore\Core\Template;
use TempusProjectCore\Classes\Config;

class AdminLoader extends Controller
{
    private $components = [];

    public function __construct()
    {
        $this->components['LOGO'] = Config::get('main/logo');
        $this->components['FOOT'] = Template::standardView('foot');
        $this->components["COPY"] = Template::standardView('copy');
        if (self::$isLoggedIn) {
            $this->components['STATUS'] = Template::standardView('navigation.statusLoggedIn');
            $this->components['USERNAME'] = self::$activeUser->username;
        } else {
            $this->components['STATUS'] = Template::standardView('navigation.statusLoggedOut');
        }
        $this->components['ADMINNAV'] = Template::standardView('navigation.admin');
        $this->components['ADMINNAV'] = Template::activePageSelect(null, 'admin/' . CORE_CONTROLLER, $this->components['ADMINNAV']);
        $this->components['MAINNAV'] = Template::standardView('navigation.main');
        $this->components['MAINNAV'] = Template::activePageSelect(null, null, $this->components['MAINNAV']);
    }

    public function values()
    {
        return serialize($this->components);
    }
}
