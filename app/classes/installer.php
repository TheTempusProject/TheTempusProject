<?php
/**
 * Core/Installer.php
 *
 * This class is used for the installation, regulation, tracking, and updating of
 * the application. It handles installing the application, installing and updating
 * models as well as the database, and generating and checking the htaccess file.
 *
 * @version 3.0
 * @author  Joey Kimsey <JoeyKimsey@thetempusproject.com>
 * @link    https://TheTempusProject.com/Core
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */
namespace TheTempusProject\Classes;

use TempusProjectCore\Classes\Config;
use TempusProjectCore\Classes\CustomException;
use TempusProjectCore\Classes\Database as Db;
use TempusProjectCore\Classes\Email;

use TempusProjectCore\Functions\Check;
use TempusProjectCore\Functions\Code;
use TempusProjectCore\Functions\Cookie;
use TempusProjectCore\Functions\Debug;
use TempusProjectCore\Functions\Hash;
use TempusProjectCore\Functions\Input;
use TempusProjectCore\Functions\Pagination;
use TempusProjectCore\Functions\Redirect;
use TempusProjectCore\Functions\Routes;
use TempusProjectCore\Functions\Session;
use TempusProjectCore\Functions\Token;

class Installer
{
    private $override = false;
    private $status = null;
    private static $installJson = null;
    private static $errors = [];

// check the server type
//     $_SERVER["SERVER_SOFTWARE"].

//     <?php
//        echo $_SERVER['SERVER_SOFTWARE'];
//    

    /**
     * Install permissions needed for the model.
     *
     * @return bool - If the permissions were added without error
     */
    public function installPermissions()
    {
        return true;
    }
    /**
     * This method will remove all the installed model components.
     *
     * @return bool - If the uninstall was completed without error
     */
    public static function uninstall()
    {
        Preference::removePrefs(self::$preferences, true);
        Permission::removePerms(self::$permissions, true);
        Config::removeConfigCategory(self::$configName);
        self::$db->removeTable(self::$tableName);
        return true;
    }
    
    /**
     * The constructor
     */
    public function __construct()
    {
        Debug::log('Installer initialized.');
        if (self::$installJson === null) {
            self::$installJson = $this->getJson();
        }
    }
    private function getJson() {
        $location = CONFIG_DIRECTORY . 'install.json';
        if (file_exists($location)) {
            $content = file_get_contents($location);
            $json = json_decode($content, true);
        } else {
            touch($location);
            $json = array();
        }
        return $json;
    }
    public function saveJson() {
        $encodedJson = json_encode(self::$installJson);
        // $route = Routes::getLocation('installer')->fullPath;
        $route = CONFIG_DIRECTORY . 'install.json';
        if (!file_exists($route)) {
            $content = file_get_contents($location);
            $json = json_decode($content, true);
            $fh = fopen($route, 'w');
        }
        $writeSuccess = file_put_contents($route, $encodedJson);
        if ($writeSuccess) {
            return true;
        }
        return false;
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
    public function getErrors()
    {
        return self::$errors;
    }
    private function updateInstallStatus($name)
    {
        $node = $this->getNode($name);
        if ($node === false) {
            $modelInfo = [
                'name' => $name,
                'installDate' => time(),
                'lastUpdate' => time(),
                'installStatus' => 'not installed',
                'currentVersion' => $this->getModelVersion($name)
            ];
        } else {
            $modelInfo = $node;
        }

        $installTypes = ['installDB', 'installPermissions', 'installConfigs', 'installResources', 'installPreferences'];
        foreach ($installTypes as $type) {
            if (!in_array($modelInfo[$type], ['success', 'skipped'])) {
                $modelInfo['installStatus'] = 'partially installed';
                Debug::error($type);
                break;
            }
            $modelInfo['installStatus'] = 'installed';
        }
        $this->setNode($name, $modelInfo, true);
    }

    /**
     * Generates the default htaccess file for the application. This will funnel
     * all traffic that comes into the application directory to index.php where we
     * use that data to construct the desired page using the controller.
     *
     * @param  string $docroot - A custom document root to use instead of the default.
     *
     * @return string   - The generated contents of the htaccess file.
     */
    protected function generateHtaccess($docroot = null, $rewrite = true)
    {
        if (empty($docroot)) {
            $docroot = Routes::getRoot();
        }
        $out = "";
        if ($rewrite === true) {
            $out .= "RewriteEngine On";
        }
        $out .= "
RewriteBase $docroot

# Tracking pixel
RewriteRule ^pixel/(.*)$ index.php?tracking=pixel&url=$1 [L,NC,QSA]

# Intercepts for images not found
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^images/(.*)$ index.php?error=image404&url=$1 [L,NC,QSA]

# Intercepts for uploads not found
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^uploads/(.*)$ index.php?error=upload404&url=$1 [L,NC,QSA]

# Intercepts other errors
RewriteRule ^errors/(.*)$ index.php?error=$1 [L,NC,QSA]

# Intercept all traffic not originating locally and not going to images or uploads
RewriteCond %{REMOTE_ADDR} !^127\.0\.0\.1
RewriteCond %{REMOTE_ADDR} !^\:\:1
RewriteCond %{REQUEST_URI} !^images/(.*)$ [NC]
RewriteCond %{REQUEST_URI} !^uploads/(.*)$ [NC]
RewriteRule ^(.+)$ index.php?url=$1 [QSA,L]

# Catchall for any non existent files or folders
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.+)$ index.php?url=$1 [QSA,L]";
        return $out;
    }
    
    protected function buildHtaccess()
    {
        $write = '';
        // find another way
        // if (file_exists(Routes::getLocation('htaccess')->fullPath)) {
            // $currentHtaccess = file_get_contents(Routes::getLocation('htaccess')->fullPath);
            if ($currentHtaccess !== $this->generateHtaccess()) {
                $findRewrite1 = "RewriteEngine On";
                $findRewrite2 = "\nRewriteBase " . Routes::getRoot() . "\nRewriteCond %{REQUEST_FILENAME} !-f\nRewriteCond %{REQUEST_FILENAME} !-d\nRewriteRule ^(.+)$ index.php?url=$1 [QSA,L]";
                if (stripos($currentHtaccess, $findRewrite1) === false) {
                    $write .= $this->generateHtaccess();
                } elseif (stripos($currentHtaccess, $findRewrite2) === false) {
                    $write .= $this->generateHtaccess(null, false);
                }
            } else {
                $write = $currentHtaccess;
            }
        // } else {
        //     $write = $this->generateHtaccess();
        // }

        // file_put_contents(Routes::getLocation('htaccess')->fullPath, $write);
        return true;
    }

    /**
     * Checks the root directory for a .htaccess file and compares it with
     * the .htaccess file the application generates by default.
     *
     * NOTE: The $override flag will cause this function to automatically generate a
     * new htaccess file if the .htaccess found in the root directory does not match
     * the default generated version.
     *
     * @param  boolean $create - Optional flag to generate and save a new htaccess
     *                           if none is found.
     *
     * @return boolean - Returns true if the htaccess file was found or
     *                   created, false otherwise.
     */
    public function checkHtaccess($create = false)
    {
        // find another way
        // if (file_exists(Routes::getLocation('htaccess')->fullPath)) {
        //     $htaccess = file_get_contents(Routes::getLocation('htaccess')->fullPath);
            if ($htaccess === $this->generateHtaccess()) {
                return true;
            }
            $check = 0;
            $findRewrite1 = "RewriteEngine On\n";
            $findRewrite2 = "RewriteBase " . Routes::getRoot() . "\nRewriteCond %{REQUEST_FILENAME} !-d\nRewriteCond %{REQUEST_FILENAME} !-f\nRewriteCond %{REQUEST_FILENAME} !-l\nRewriteRule ^(.+)$ index.php?url=$1 [QSA,L]";
            if (stripos($htaccess, $findRewrite1)) {
                $check++;
            }
            if (stripos($htaccess, $findRewrite2)) {
                $check++;
            }
            if ($check === 2) {
                if ($create) {
                    $errors[] = ['errorInfo' => "Previous htaccess file did not need to be edited."];
                }
                return true;
            }
        // }
        if (!$create) {
            return false;
        }
        return $this->buildHtaccess();
    }

    public function checkSession()
    {
        if (!isset(self::$installJson['installHash'])) {
            Debug::error("install hash not found on file.");

            return false;
        }
        if (!Session::exists('installHash') && !Cookie::exists('installHash')) {
            Debug::error("install hash not found in session or cookie.");

            return false;
        }
        if (Cookie::exists('installHash') && !Session::exists('installHash')) {
            if (Cookie::get('installHash') !== self::$installJson['installHash']) {
                Cookie::delete('installHash');
                return false;
            }
            Session::put('installHash', Cookie::get('installHash'));
        }
        if (Session::get('installHash') !== self::$installJson['installHash']) {
            Session::delete('installHash');
            return false;
        }
        return true;
    }

    public function nextStep($page, $redirect = false)
    {
        $newHash = Code::genInstall();
        $this->setNode('installHash', $newHash, true);
        $this->setNode('installStatus', $page, true);
        Session::put('installHash', $newHash);
        Cookie::put('installHash', $newHash);
        if ($redirect === true) {
            Redirect::reload();
        }
        return true;
    }

    public function getStatus()
    {
        if (isset(self::$installJson['installStatus'])) {
            return self::$installJson['installStatus'];
        }
        Debug::error("install status not found.");

        return false;
    }

    public function getNode($name)
    {
        if (isset(self::$installJson[$name])) {
            return self::$installJson[$name];
        }
        Debug::error("install node not found: $name");
        
        return false;
    }

    public function setNode($name, $value, $save = false)
    {
        self::$installJson[$name] = $value;
        if ($save !== false) {
            return $this->saveJson();
        }
        return true;
    }
}
