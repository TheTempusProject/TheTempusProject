<?php
/**
 * app/templates/admin/admin.inc.php
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

class AdminLoader extends DefaultLoader
{
    public function __construct()
    {
        parent::__construct();
        Components::set('ADMINNAV', Pagination::activePageSelect('nav.admin', 'admin/' . App::$controllerName);
    }
}
