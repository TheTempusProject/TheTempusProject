<?php
/**
 * Install.php.
 *
 * This is the installer for the application,
 * after completion YOU SHOULD DELETE THIS FILE.
 * There is a built in safeguard from preventing the
 * rewrite of the config file, but it is still not
 * safe to keep this file on system as it also bypasses
 * the htaccess protections.
 *
 * @version 1.0
 *
 * @author  Joey Kimsey <joeyk4816@gmail.com>
 *
 * @link    https://github.com/JoeyK4816/TheTempusProject
 *
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html [GNU GENERAL PUBLIC LICENSE]
 */

namespace TheTempusProject;

ob_start();
use TempusProjectCore\Classes\Input as Input;
use TempusProjectCore\Classes\Check as Check;
use TempusProjectCore\Classes\DB as DB;
use TempusProjectCore\Classes\Config as Config;
use TempusProjectCore\Classes\Email as Email;
use TempusProjectCore\Classes\Session as Session;
use TempusProjectCore\Classes\Redirect as Redirect;
use TheTempusProject\Models\model_user as model_user;
use TempusProjectCore\Classes\Hash as Hash;

$fullArray = explode('/', $_SERVER['PHP_SELF']);
array_pop($fullArray);
$docroot = implode('/', $fullArray) . '/';

$GLOBALS['config'] = array(
    "main" => array(
        "location" => $_SERVER['DOCUMENT_ROOT'] . $docroot,
        "base" => 'http://'.$_SERVER['HTTP_HOST'] . $docroot,
        "name" => "Powered by: The Tempus Project",
        "template" => "default",
    ),
    "logging" => array(
        "errors" => true,
        "logins" => true,
        "feedback" => true,
        "bug_reports" => true,
    ),
    "database" => array(
        "enabled" => false,
    ),
    "remember" => array(
        "cookie_name" => "hash",
        "cookie_expiry" => 604800,
    ),
    "session" => array(
        "session_name" => "user",
        "token_name" => "token",
    ),
);

//@todo make this use the mvc like everything else noob :/
require_once 'init.php';

function install() {
    $fullArray = explode('/', $_SERVER['PHP_SELF']);
    array_pop($fullArray);
    $docroot = implode('/', $fullArray) . '/';
    $form = '<form action="" method="post" class="form-horizontal">
    <legend>Install</legend>
    <fieldset>
    <div class="form-group">
        <label for="db_host" class="col-lg-6 control-label">Database Host:</label>
        <div class="col-lg-2">
            <input class="form-control" type="text" name="db_host" id="db_host" value="' . Input::post_null('db_host') . '">
        </div>
    </div>
    <div class="form-group">
        <label for="db_name" class="col-lg-6 control-label">Database Name:</label>
        <div class="col-lg-2">
            <input class="form-control" type="text" name="db_name" id="db_name" value="' . Input::post_null('db_name') . '">
        </div>
    </div>
    <div class="form-group">
        <label for="db_username" class="col-lg-6 control-label">Database Username:</label>
        <div class="col-lg-2">
            <input class="form-control" type="text" name="db_username" id="db_username" value="' . Input::post_null('db_username') . '">
        </div>
    </div>
    <div class="form-group">
        <label for="db_password" class="col-lg-6 control-label">Database Password:</label>
        <div class="col-lg-2">
            <input class="form-control" type="password" name="db_password" id="db_password">
        </div>
    </div>
    <div class="form-group">
        <label for="site_name" class="col-lg-6 control-label">Site Name:</label>
        <div class="col-lg-2">
            <input class="form-control" type="text" name="site_name" id="site_name" value="' . Input::post_null('site_name') . '">
        </div>
    </div>
    <div class="form-group">
        <label for="username" class="col-lg-6 control-label">User Username:</label>
        <div class="col-lg-2">
            <input class="form-control" type="text" name="username" id="username" value="' . Input::post_null('username') . '">
        </div>
    </div>
    <div class="form-group">
        <label for="email" class="col-lg-6 control-label">User Email:</label>
        <div class="col-lg-2">
            <input class="form-control" type="email" name="email" id="email" value="' . Input::post_null('email') . '">
        </div>
    </div>
    <div class="form-group">
        <label for="email2" class="col-lg-6 control-label">Re-enter Email:</label>
        <div class="col-lg-2">
            <input class="form-control" type="email" name="email2" id="email2" value="' . Input::post_null('email2') . '">
        </div>
    </div>
    <div class="form-group">
        <label for="password" class="col-lg-6 control-label">User Password:</label>
        <div class="col-lg-2">
            <input class="form-control" type="password" name="password" id="password">
        </div>
    </div>
    <div class="form-group">
        <label for="password2" class="col-lg-6 control-label">re-enter Site Password:</label>
        <div class="col-lg-2">
            <input class="form-control" type="password" name="password2" id="password2">
        </div>
    </div>
    </fieldset>
    <button class="btn btn-lg btn-success center-block" type="submit" name="submit" value="submit">Install</button><br>
</form>';
    $htaccess = "RewriteEngine On
RewriteBase $docroot
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-l
RewriteRule ^(.+)$ index.php?url=$1 [QSA,L]";
    $globals_config = '<?php

namespace TheTempusProject;

$GLOBALS[\'config\'] = array(
    "main" => array(
        "location" => "'.$_SERVER['DOCUMENT_ROOT'].$docroot.'",
        "base" => "http://'.$_SERVER['HTTP_HOST'].$docroot.'",
        "name" => "'.Input::post_null('site_name').'",
        "template" => "default",
        "loginLimit" => 5,
        "page_limit" => 50,
        "page_default" => 5,
    ),
    "logging" => array(
        "admin" => true,
        "errors" => true,
        "logins" => true,
        "feedback" => true,
        "bug_reports" => true,
    ),
    "database" => array(
        "db_host" => "'.Input::post_null('db_host').'",
        "db_username" => "'.Input::post_null('db_username').'",
        "db_password" => "'.Input::post_null('db_password').'",
        "db_name" => "'.Input::post_null('db_name').'",
        "db_type" => "mysql",
        "db_enabled" => true,
        "db_max_query" => 100,
    ),
    "remember" => array(
        "cookie_name" => "hash",
        "cookie_expiry" => 604800,
    ),
    "session" => array(
        "session_name" => "user",
        "token_name" => "token",
    ),
);
?>';
    $htaccess_path = $_SERVER['DOCUMENT_ROOT'].$docroot.'.htaccess';
    $globals_config_path = $_SERVER['DOCUMENT_ROOT'].$docroot.'App/settings.php';

    if (file_exists($globals_config_path)) {
        echo "Config file already exists so the installer has been halted. If there was an error with installation, please delete App/settings.php manually, empty the DB, and try Again.";
        return;
    }

    if (file_exists($htaccess_path)) {
        if (file_get_contents($htaccess_path) === $htaccess) {
            echo 'Previous htaccess file did not need to be overridden.';
        } else {
            echo '<div class="alert alert-warning" role="alert">htaccess file already exists in the root directory. You will need to manually modify it or remove it completely before continuing.</div> <div class="alert alert-danger" role="alert">WARNING:The htaccess file could easily be created/used by other applications. Please double check before removing or modifying it.</div>';
            return;
        }
    } else {
        file_put_contents($htaccess_path, $htaccess);
    }

    if (!Input::exists()) {
        echo $form;
        return;
    }

    if (!Check::form('install')) {
        echo Check::errors();
        echo $form;
        return;
    }

    if (!Check::db(Input::post('db_host'), Input::post('db_name'), Input::post('db_username'), Input::post('db_password'))) {
        echo Check::errors();
        echo $form;
        return;
    }

    if (!Check::username(Input::post('username')) || !Check::password(Input::post('password')) || Input::post('password') !== Input::post('password2') || Input::post('email') !== Input::post('email2')) {
        echo 'There was an error with your USER username/email/password, please try again.';
        echo Check::errors();
        return;
    }
    file_put_contents($globals_config_path, $globals_config);
    $install = DB::getInstance(Input::post('db_host'), Input::post('db_name'), Input::post('db_username'), Input::post('db_password'));
    $query = 'SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
              SET time_zone = "+05:00"';
    $install->raw($query);
    $uploaddir = Config::get('main/location') . 'Models/';
    $files = scandir($uploaddir);
    foreach ($files as $key => $value) {
        if (strpos($value, 'model_') === false) {continue;}
        require_once $uploaddir . $value;
        $modelname = str_replace('.php', '', $value);
        $full_name = APP_SPACE . "\\Models\\" . $modelname;
        $query = call_user_func_array(array($full_name, 'sql'), array());
        $install->raw($query);
    }
    $user = new model_user();
    $user->create(array(
        'username' => Input::post('username'),
        'password' => Hash::make(Input::post('password')),
        'email' => Input::post('email'),
        'registered' => time(),
        'confirmed' => 1,
        'terms' => 1,
        'user_group' => 1,
    ));
    Email::send(Input::post('email'), 'install', null, array('template' => true));
    Session::flash('success', 'Install has successfully completed!');
    Redirect::to('home/index');
    return;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="Images/favicon.ico">
        <title>The Tempus Project Installer</title>
        <!-- Bootstrap core CSS -->
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <!-- Custom styles for this template -->
        <link href="Templates/default/default.css" rel="stylesheet" type="text/css">
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    </head>
    <body>
        <!-- Fixed navbar -->
        <nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="https://www.TheTempusProject.com">The Tempus Project</a>
                </div>
                <div id="navbar" class="navbar-collapse collapse">
                    <!-- Main_Nav -->
                    <ul class="nav navbar-nav">
                        <li class="active"><a href="install.php">Installer</a></li>
                    </ul>
                    <!-- /Main_Nav -->
                </div>
                <!--/.nav-collapse -->
            </div>
        </nav>
        <div class="container">
            <div class="UI-page-buffer">
                <!-- Content -->
                <?php
                    install();
                ?>
                <!-- /Content -->
            </div>
        </div>
        <!-- Copy -->
        <footer>
            <div class="footer">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
                            <p class="text-muted">Powered by <a href="https://www.thetempusproject.com">The Tempus Project</a>.</p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- /Copy -->
        <!-- Bootstrap core JavaScript
        ================================================== -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>