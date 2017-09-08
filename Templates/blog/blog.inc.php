<?php
/**
 * Templates/Default.inc.php
 *
 * This is the loader for the Default template.
 *
 * @version 1.0
 *
 * @author  Joey Kimsey <joeyk4816@gmail.com>
 *
 * @link    https://github.com/JoeyK4816/TheTempusProject
 *
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html [GNU GENERAL PUBLIC LICENSE]
 */

namespace TheTempusProject\Templates;

use TempusProjectCore\Core\Controller as Controller;
use TempusProjectCore\Core\Template as Template;
use TempusProjectCore\Classes\Config as Config;

class template_blog extends Controller
{
    private $test = array();
    public function __construct()
    {
        $this->test['LOGO'] = Config::get('main/logo');
        $this->test['FOOT'] = Template::standard_view('foot');
        $this->test["COPY"] = Template::standard_view('copy');
        $this->test["SIDEBAR"] = Template::standard_view('blog.sidebar', Self::$_blog->recent(5));
        $this->test["SIDEBAR2"] = Template::standard_view('blog.sidebar2', Self::$_blog->archive());
        if (Self::$_is_logged_in) {
            $this->test['STATUS'] = Template::standard_view('status_logged_in');
            $this->test['USERNAME'] = Self::$_active_user->username;
        } else {
            $this->test['STATUS'] = Template::standard_view('status_logged_out');
        }
        $this->test['MAINNAV'] = Template::standard_view('nav_main');
        $this->test['MAINNAV'] = Template::activePageSelect(null, null, $this->test['MAINNAV']);
    }
    public function values()
    {
        return serialize($this->test);
    }
}