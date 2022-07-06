<?php
/**
 * app/controllers/admin/settings.php
 *
 * This is the xxxxxx controller.
 *
 * @version 3.0
 * @author  Joey Kimsey <JoeyKimsey@thetempusproject.com>
 * @link    https://TheTempusProject.com
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */

namespace TheTempusProject\Controllers;

use TempusProjectCore\Classes\Debug;
use TempusProjectCore\Classes\Input;
use TempusProjectCore\Classes\Image;
use TempusProjectCore\Classes\Config;
use TempusProjectCore\Core\Installer;

class Settings extends AdminController
{
    public function index()
    {
        $installer = new Installer;
        $a = Input::exists('submit');
        Components::set('TIMEZONELIST', Views::standardView('timezoneDropdown'));
        if (Input::exists('logo') && Image::upload('logo', 'System')) {
            $logo = 'Uploads/Images/System/' . Image::last();
        } else {
            $logo = Config::get('main/logo');
        }
        if ($a) {
            Config::updateConfig('main', 'name', Input::post('name'));
            Config::updateConfig('main', 'template', Input::post('template'));
            Config::updateConfig('main', 'loginLimit', (int) Input::post('loginLimit'));
            Config::updateConfig('main', 'logo', $logo);
            Config::updateConfig('main', 'timezone', Input::post('timezone'));
            Config::updateConfig('main', 'pageLimit', (int) Input::post('pageLimit'));
            Config::updateConfig('uploads', 'enabled', Input::post('uploads'));
            Config::updateConfig('uploads', 'maxFileSize', (int) Input::post('fileSize'));
            Config::updateConfig('uploads', 'maxImageSize', (int) Input::post('imageSize'));
            Config::updateConfig('cookie', 'cookieExpiry', (int) Input::post('cookieExpiry'));
            Config::updateConfig('feedback', 'enabled', Input::post('logF'));
            Config::updateConfig('logging', 'errors', Input::post('logE'));
            Config::updateConfig('logging', 'logins', Input::post('logL'));
            Config::updateConfig('bugreports', 'enabled', Input::post('logBR'));
            Config::updateConfig('group', 'defaultGroup', Input::post('groupSelect'));
            Config::updateConfig('recaptcha', 'siteKey', Input::post('siteHash'));
            Config::updateConfig('recaptcha', 'privateKey', Input::post('privateHash'));
            Config::updateConfig('recaptcha', 'sendIP', Input::post('sendIP'));
            Config::updateConfig('recaptcha', 'enabled', Input::post('recaptcha'));
            Config::saveConfig();
        }
        $select = Views::standardView('admin.groupSelect', self::$group->listGroups());
        Components::set('groupSelect', $select);
        Components::set('LOGO', $logo);
        Components::set('NAME', $a ? Input::post('name') : Config::get('main/name'));
        Components::set('TEMPLATE', $a ? Input::post('template') : Config::get('main/template'));
        Components::set('maxFileSize', $a ? Input::post('fileSize') : Config::get('uploads/maxFileSize'));
        Components::set('maxImageSize', $a ? Input::post('imageSize') : Config::get('uploads/maxImageSize'));
        Components::set('cookieExpiry', $a ? Input::post('cookieExpiry') : Config::get('cookie/cookieExpiry'));
        Components::set('siteHash', $a ? Input::post('siteHash') : Config::get('recaptcha/siteKey'));
        Components::set('privateHash', $a ? Input::post('privateHash') : Config::get('recaptcha/privateKey'));
        Components::set('LIMIT', $a ? Input::post('loginLimit') : Config::get('main/loginLimit'));
        Components::set('securityHash', $installer->getNode('installHash'));
        Forms::selectOption($a ? Input::post('groupSelect') : Config::get('group/defaultGroup'));
        Forms::selectOption($a ? Input::post('timezone') : Config::get('main/timezone'));
        Forms::selectOption($a ? Input::post('pageLimit') : Config::get('main/pageLimit'));
        Forms::selectRadio('feedback', $a ? Input::post('logF') : Config::getString('feedback/enabled'));
        Forms::selectRadio('errors', $a ? Input::post('logE') : Config::getString('logging/errors'));
        Forms::selectRadio('logins', $a ? Input::post('logL') : Config::getString('logging/logins'));
        Forms::selectRadio('bugReports', $a ? Input::post('logBR') : Config::getString('bugreports/enabled'));
        Forms::selectRadio('uploads', $a ? Input::post('uploads') : Config::getString('uploads/enabled'));
        Forms::selectRadio('sendIP', $a ? Input::post('sendIP') : Config::getString('recaptcha/sendIP'));
        Forms::selectRadio('recaptcha', $a ? Input::post('recaptcha') : Config::getString('recaptcha/enabled'));
        Views::view('admin.settings');
        exit();
    }
}
