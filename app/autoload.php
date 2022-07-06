<?php
/**
 * autoload.php
 *
 * Uses the TempusProjectCore Autoloader if it has been defined.
 *
 * @version 0.0
 * @author  Joey Kimsey <Joey@thetempusproject.com>
 * @link    https://TheTempusProject.com
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */

namespace TheTempusProject;

use TempusProjectCore\Classes\Autoloader;
use TempusProjectCore\Template;
use TempusProjectCore\Template\Views;

if ( class_exists( 'TempusProjectCore\Classes\Autoloader' ) && TEMPUS_PROJECT_CONSTANTS_LOADED ) {
    $Autoloader = new Autoloader;
    $Autoloader->addNamespace(
        APP_SPACE . '\Controllers',
        CONTROLER_DIRECTORY,
        false,
    );
    $Autoloader->addNamespace(
        APP_SPACE . '\Controllers\Admin',
        ADMIN_CONTROLER_DIRECTORY,
        false,
    );
    $Autoloader->addNamespace(
        APP_SPACE . '\Models',
        MODEL_DIRECTORY,
        false,
    );
    $Autoloader->addNamespace(
        APP_SPACE . '\Classes',
        CLASSES_DIRECTORY,
        false,
    );
    $Autoloader->includeFolder(FUNCTIONS_DIRECTORY);
    $Autoloader->register();

    if (PLUGINS_ENABLED) {
        $errors = [];
        if (!file_exists(PLUGIN_DIRECTORY)) {
            $errors = array_merge($errors, ['errorInfo' => "Models folder is missing: $dir"]);
        }

        // get a list of all plugins in the plugin directory
        $pluginsList = scandir(PLUGIN_DIRECTORY);
        array_shift($pluginsList); // remove the .
        array_shift($pluginsList); // remove the ..

        // loop over each plugin folder
        foreach ($pluginsList as $key => $pluginName) {
            $pluginDir = PLUGIN_DIRECTORY . $pluginName;
            if (is_file($pluginDir)) {
                // skip any files if they exist
                continue;
            }

            // get a list of all directories in this plugin directory
            $pluginDir .= DIRECTORY_SEPARATOR;
            $pluginDirectoryArray = scandir($pluginDir);
            array_shift($pluginDirectoryArray); // remove the .
            array_shift($pluginDirectoryArray); // remove the ..

            // loop over each sub-directory insider plugin directory
            foreach ($pluginDirectoryArray as $key => $file) {
                $currentFolder = $pluginDir . $file . DIRECTORY_SEPARATOR;
                switch ($file) {
                    case 'controllers':
                        $Autoloader->addNamespace(
                            APP_SPACE . '\Controllers',
                            $currentFolder,
                            false,
                        );
                        break;
                    case 'models':
                        $Autoloader->addNamespace(
                            APP_SPACE . '\Models',
                            $currentFolder,
                            false,
                        );
                        break;
                    case 'views':
                        Views::addViewLocation($pluginName, $currentFolder);
                        break;
                    case 'templates':
                        Template::addTemplateLocation($currentFolder);
                        break;
                    case 'forms.php':
                        break;
                    case 'filters.php':
                        break;
                    default:
                        break;
                }
            }
        }
    }
}

define('TEMPUS_PROJECT_AUTOLOADED', true);