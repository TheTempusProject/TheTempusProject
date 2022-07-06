<?php
/**
 * app/controllers/admin/installed.php
 *
 * This is the installed controller.
 *
 * @version 3.0
 * @author  Joey Kimsey <JoeyKimsey@thetempusproject.com>
 * @link    https://TheTempusProject.com
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */
namespace TheTempusProject\Controllers;

use TempusProjectCore\Classes\Debug;
use TempusProjectCore\Template\Issues;
use TempusProjectCore\Classes\Input;
use TempusProjectCore\Core\Installer;

require_once 'AdminController.php';

class Installed extends AdminController
{
    public $installer = null;

    public function index($data = null)
    {
        $models = $this->installer->getModelVersionList();
        foreach ($models as $model) {
            $modelArray = (array) $model;
            $node = $this->installer->getNode($model->name);
            if ($node === false) {
                $node = [
                    'name' => $model->name,
                    'installDate' => '',
                    'lastUpdate' => '',
                    'installStatus' => 'not installed',
                    'installedVersion' => '',
                    'installDB' => '',
                    'installPermissions' => '',
                    'installConfigs' => '',
                    'installResources' => '',
                    'installPreferences' => '',
                    'currentVersion' => '',
                    'version' => $this->installer->getModelVersion($model->name)
                ];
            }
            $out[] = (object) array_merge($modelArray, $node);
        }
        Views::view('admin.installed', $out);
        exit();
    }
    public function viewModel($data = null)
    {
        $node = $this->installer->getNode($data);
        if ($node === false) {
            $out = [
                'name' => $data,
                'installDate' => '',
                'lastUpdate' => '',
                'installStatus' => 'not installed',
                'currentVersion' => '',
                'installedVersion' => '',
                'installDB' => '',
                'installPermissions' => '',
                'installConfigs' => '',
                'installResources' => '',
                'installPreferences' => '',
                'version' => ''
            ];
        } else {
            $out = array_merge(['version' => $this->installer->getModelVersion($data)], (array) $node);
        }
        Views::view('admin.installedView', $out);
        exit();
    }
    public function install($data = null)
    {
        Components::set('MODEL', $data);
        if (!Input::exists('installHash')) {
            Views::view('admin.install');
            exit();
        }
        if (!$this->installer->installModel($data)) {
            Issues::add('error', 'There was an error with the Installation.', $this->installer->getErrors());
        }
        $this->index();
    }
    public function uninstall($data = null)
    {
        Components::set('MODEL', $data);
        if (!Input::exists('uninstallHash')) {
            Views::view('admin.uninstall');
            exit();
        }
        if (!$this->installer->uninstallModel($data)) {
            Issues::add('error', 'There was an error with the uninstall.', $this->installer->getErrors());
        }
        $this->index();
    }
}
