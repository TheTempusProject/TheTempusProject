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

class template_rss extends Controller
{
    private $test = array();
    public function __construct()
    {
        $this->test["COPY"] = Template::standard_view('copy');
    }
    public function values()
    {
        return serialize($this->test);
    }
}