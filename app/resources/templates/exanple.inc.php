<?php
/**
 * Templates/admin/admin.inc.php
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
use TheTempusProject\TheTempusProject as App;

class AdminLoader extends Controller
{
    private $components = [];

    public function __construct()
    {
        $this->components['LOGO'] = Config::get('main/logo');
        $this->components['FOOT'] = Views::standardView('foot');
        $this->components["COPY"] = Views::standardView('copy');
        if (App::$isLoggedIn) {
            $this->components['STATUS'] = Views::standardView('nav.statusLoggedIn');
            $this->components['USERNAME'] = App::$activeUser->username;
        } else {
            $this->components['STATUS'] = Views::standardView('nav.statusLoggedOut');
        }
        $this->components['ADMINNAV'] = Pagination::activePageSelect('nav.admin', 'admin/' . App::$controllerName);
        $this->components['MAINNAV'] = Pagination::activePageSelect('nav.main');
    }

    public function values()
    {
        return serialize($this->components);
    }
}
