<?php
/**
 * app/classes/plugins.php
 *
 * This class is used in conjunction with TempusProjectCore\Classes\Check
 * to house complete form verification. You can utilize the
 * error reporting to easily define exactly what feedback you
 * would like to give.
 *
 * @version  3.0
 * @author   Joey Kimsey <Joey@thetempusproject.com>
 * @link     https://TheTempusProject.com
 * @license  https://opensource.org/licenses/MIT [MIT LICENSE]
 */
namespace TheTempusProject\Classes;

class Plugins extends FormThingy
{
    

    public function getModelVersion($name, $folder = null)
    {
        // FIND ANOTHER WAY
        // $docroot = Routes::getLocation('models', $name, $folder);
        if ($docroot->error) {
            self::$errors = array_merge(self::$errors, ['errorInfo' => "$name was not installed: $docroot->errorString"]);
            return false;
        }
        // require_once $docroot->fullPath;
        if (method_exists($docroot->className, 'modelVersion')) {
            $version = call_user_func_array([$docroot->className, 'modelVersion'], []);
        } else {
            $version = 'unknown';
        }
        return $version;
    }

    public function getModelList($folder = null)
    {
        // FIND ANOTHER WAY
        // $dir = Routes::getLocation('models', '', $folder)->folder;
        if (!file_exists($dir)) {
            self::$errors = array_merge(self::$errors, ['errorInfo' => "Models folder is missing: $dir"]);
            return [];
        }
        $files = scandir($dir);
        array_shift($files);
        array_shift($files);
        foreach ($files as $key => $value) {
            $modelList[] = str_replace('.php', '', $value);
        }
        return $modelList;
    }

    public function getModelVersionList($folder = null)
    {
        $modelsList = $this->getModelList($folder);
        foreach ($modelsList as $model) {
            $modelList[] = (object) [
                'name' => $model,
                'version' => $this->getModelVersion($model),
            ];
        }
        return $modelList;
    }

    /**
     * This function automatically attempts to install all models in the
     * specified directory.
     *
     * NOTE: The 'Models/ folder is used by default.
     *
     * @param  string $directory - The directory you wish to install all
     *                             models from.
     *
     * @return boolean
     */
    public function installModels($directory = null, $modelList = [], $flags = null)
    {
        self::$db = DB::getInstance('', '', '', '', true);
        $query = 'SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
                  SET time_zone = "+05:00"';
        self::$db->raw($query);
        if (empty($modelList)) {
            $list = $this->getModelList($directory);
            foreach ($list as $model) {
                $modelList[] = [$model => true];
            }
        }
        Debug::log('Installing selected models in folder: ' . $directory);
        foreach ($modelList as $key => $value) {
            if ($value === true) {
                if (!$this->installModel($key, $directory, $flags)) {
                    $fail = true;
                }
            }
        }

        if (!isset($fail)) {
            return true;
        }

        return false;
    }

    public function getModelList($folder = null)
    {
        // FIND ANOTHER WAY
        // $dir = Routes::getLocation('models', '', $folder)->folder;
        if (!file_exists($dir)) {
            self::$errors = array_merge(self::$errors, ['errorInfo' => "Models folder is missing: $dir"]);
            return [];
        }
        $files = scandir($dir);
        array_shift($files);
        array_shift($files);
        foreach ($files as $key => $value) {
            $modelList[] = str_replace('.php', '', $value);
        }
        return $modelList;
    }
    public function uninstallModel($name, $folder = null, $flags = null)
    {
        Debug::log('Uninstalling Model: ' . $name);
        // FIND ANOTHER WAY
        // $docroot = Routes::getLocation('models', $name, $folder);
        if ($docroot->error) {
            self::$errors = array_merge(self::$errors, ['errorInfo' => "$name was not installed: $docroot->errorString"]);
            return false;
        }
        $errors = null;
        // require_once $docroot->fullPath;
        $node = $this->getNode($name);
        if ($node === false) {
            Debug::error('Cannot uninstall model that has not been installed.');
            return false;
        }
        if ($node['installStatus'] === 'not installed') {
            Debug::error('Cannot uninstall model that has not been installed.');
            return false;
        }
        if (!method_exists($docroot->className, 'uninstall')) {
            Debug::error('Model has no uninstall method.');
            return false;
        }
        if (!call_user_func_array([$docroot->className, 'uninstall'], [])) {
            $errors[] = ['errorInfo' => "$name failed to execute uninstall properly."];
        } else {
            $node['currentVersion'] = '';
            $node['installStatus'] = 'uninstalled';
            $node['lastUpdate'] = time();
        }
        $installTypes = ['installDB', 'installPermissions', 'installConfigs', 'installResources', 'installPreferences'];
        foreach ($installTypes as $type) {
            if ($node[$type] !== 'skipped') {
                $node[$type] = 'uninstalled';
            }
        }
        $this->setNode($name, $node, true);
        if ($errors !== null) {
            $errors[] = ['errorInfo' => "$name did not uninstall properly."];
            self::$errors = array_merge(self::$errors, $errors);
            return false;
        }
        self::$errors = array_merge(self::$errors, ['errorInfo' => "$name has been uninstalled."]);

        return true;
    }
    public $installFlags = [
        'installDB' => true
    ];
    /**
     * This function is used to install database structures needed for this model.
     *
     * @return boolean - The status of the completed install
     */
    public static function installDB()
    {
        return true;
    }
    public static function install( $plugin ) {
        self::installTables();
        self::installConfigs();
        self::installPermissions();
        self::installPreferences();
    }

    /**
     * Install preferences needed for the model.
     *
     * @return bool - If the preferences were added without error
     */
    public function installPreferences()
    {
        return true;
    }
    public static function installTables( $plugin ) {
        
    }


    /**
     * Install configuration options needed for the model.
     *
     * @return bool - If the configurations were added without error
     */
    public function installConfigs() {

        return true;
    }
    public static function installConfigs( $plugin ) {
        
    }

    public static function installPermissions( $plugin ) {
        
    }

    public static function installPreferences( $plugin ) {
        
    }

}
