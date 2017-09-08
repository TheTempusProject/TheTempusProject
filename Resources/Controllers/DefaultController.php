<?php
/**
 * Controllers/DefaultController.php.
 *
 * This is the Default controller design.
 *
 * @version 1.0
 *
 * @author  Joey Kimsey <joeyk4816@gmail.com>
 *
 * @link    https://github.com/JoeyK4816/Pyromania
 *
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html [GNU GENERAL PUBLIC LICENSE]
 */

namespace Pyromania;

class DefaultController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Debug::group('Controller: '.get_class($this));
    }
    public function __destruct()
    {
        Debug::log('Controller Destructing: '.get_class($this));
        Debug::gend();
        $this->build();
    }
    public function index()
    {
        Debug::log('Controller initiated: '.__METHOD__.'.');
        Self::$_template->set('TITLE', 'Default - Index');
    }
}
