<?php
/**
 * templates/default/default.inc.php
 *
 * This is the loader for the default template.
 *
 * @version 3.0
 * @author  Joey Kimsey <JoeyKimsey@thetempusproject.com>
 * @link    https://TheTempusProject.com
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */
namespace TheTempusProject\Templates;

use TempusProjectCore\Template\Views;
use TempusProjectCore\Template\Pagination;
use TempusProjectCore\Template\Components;
use TempusProjectCore\Classes\Config;
use TempusProjectCore\Template;
use TheTempusProject\TheTempusProject as App;

class DefaultLoader
{
    const TEMPLATE_NAME = 'Default Tempus Project Template';
    const JQUERY_CDN = 'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/';
    const BOOTSTRAP_CDN = 'https://cdn.jsdelivr.net/npm/bootstrap@3.3.6/dist/';
    const FONT_AWESOME_URL = 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/';

    private $cssIncludes = array();
    private $jsIncludes = array();

    public function __construct()
    {
        Components::set('TEMPLATE_URL', Template::parse('{ROOT_URL}app/templates/default/'));
        Components::set('BOOTSTRAP_CDN', self::BOOTSTRAP_CDN);
        $this->cssIncludes[] = Template::parse('<link rel="stylesheet" href="{BOOTSTRAP_CDN}css/bootstrap-theme.min.css" crossorigin="anonymous">');
        $this->cssIncludes[] = Template::parse('<link rel="stylesheet" href="{TEMPLATE_URL}default.css">');
        $this->jsIncludes[] = Template::parse('<script language="JavaScript" type="text/javascript" src="{TEMPLATE_URL}default.js"></script>');
        // $this->jsIncludes[] = '<script language="JavaScript" type="text/javascript" src="http://www.google.com/recaptcha/api.js"></script>';
        // $this->jsIncludes[] = '<script language="JavaScript" type="text/javascript" src="{ROOT_URL}vendor/tinymce/tinymce/tinymce.min.js"></script>';
        Components::set('LOGO', Config::get('main/logo'));
        Components::set('FOOT', Views::standardView('foot'));
        Components::set('COPY', Views::standardView('copy'));
        // if (App::$isLoggedIn) {
        //     Components::set('STATUS', Views::standardView('nav.statusLoggedIn'));
        //     Components::set('USERNAME', App::$activeUser->username);
        // } else {
            Components::set('STATUS', Views::standardView('nav.statusLoggedOut'));
        // }
        Components::set('MAINNAV', Pagination::activePageSelect('nav.main'));
        Components::set('JQUERY_CDN', self::JQUERY_CDN);
        Components::set('FONT_AWESOME_URL', self::FONT_AWESOME_URL);
        Components::set('TEMPLATE_CSS_INCLUDES', implode("\n", $this->cssIncludes));
        Components::set('TEMPLATE_JS_INCLUDES', implode("\n", $this->jsIncludes));
    }
}
