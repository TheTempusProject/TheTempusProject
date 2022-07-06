<?php
/**
 * app/controllers/admin/deoendencies.php
 *
 * This is the deoendencies controller.
 *
 * @version 3.0
 * @author  Joey Kimsey <JoeyKimsey@thetempusproject.com>
 * @link    https://TheTempusProject.com
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */

namespace TheTempusProject\Controllers;

use TempusProjectCore\Classes\Debug;
use TempusProjectCore\Core\Installer;

class Dependencies extends AdminController
{
    public function index($data = null)
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Admin - Dependencies';
        $installer = new Installer;
        switch ($sub) {
            default:
                $composerJson = $installer->getComposerJson();
                $requiredPackages = $composerJson['require'];
                foreach ($requiredPackages as $name => $version) {
                    $versionsRequired[strtolower($name)] = $version;
                }

                $composerLock = $installer->getComposerLock();
                $installedPackages = $composerLock['packages'];
                foreach ($installedPackages as $package) {
                    $name = strtolower($package['name']);
                    $versionsInstalled[$name] = $package;
                }

                foreach ($versionsInstalled as $package) {
                    $name = strtolower($package['name']);
                    if (!empty($versionsRequired[$name])) {
                        $versionsInstalled[$name]['requiredVersion'] = $versionsRequired[$name];
                    } else {
                        $versionsInstalled[$name]['requiredVersion'] = 'sub-dependency';
                    }
                    $out[] = (object) $versionsInstalled[$name];
                }
                break;
        }
        Views::view('admin.dependencies', $out);
        exit();
    }
}
