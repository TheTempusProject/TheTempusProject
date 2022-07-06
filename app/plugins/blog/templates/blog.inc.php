<?php
/**
 * plugins/blog/templates/blog.inc.php
 *
 * This is the loader for the blog template.
 *
 * The template engine will automatically require this file and
 * initiate its constructor. Next it will call the values function
 * to load the components array into the Template Engine $values
 * array for later use.
 *
 * @version 1.0
 * @author  Joey Kimsey <JoeyKimsey@thetempusproject.com>
 * @link    https://TheTempusProject.com
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */

namespace TheTempusProject\Templates;

use TheTempusProject\Models\Blog;
use TempusProjectCore\Template\Components;
use TempusProjectCore\Template\Views;

class BlogLoader extends DefaultLoader
{
    /**
     * The array that will be loaded into Template::$values.
     *
     * @var array
     */
    private $components = [];

    /**
     * This is the function used to generate any components that may be
     * needed by this template.
     */
    public function __construct()
    {
        $blog = new Blog;
        Components::set('SIDEBAR', Views::standardView('blog.sidebar', $blog->recent(5)));
        Components::set('SIDEBAR2', Views::standardView('blog.sidebar2', $blog->archive()));
    }
}
