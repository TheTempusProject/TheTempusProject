<?php
// defined in bin/autoload.php
// - APP_SPACE
// - APP_ROOT_DIRECTORY
// - CONFIG_DIRECTORY

# Directories
	// Main Directories
		define('APP_DIRECTORY', APP_ROOT_DIRECTORY . 'app' . DIRECTORY_SEPARATOR);
		define('BIN_DIRECTORY', APP_ROOT_DIRECTORY . 'bin' . DIRECTORY_SEPARATOR);
		define('CSS_DIRECTORY', APP_ROOT_DIRECTORY . 'css' . DIRECTORY_SEPARATOR);
		define('IMAGE_DIRECTORY', APP_ROOT_DIRECTORY . 'images' . DIRECTORY_SEPARATOR);
		define('JAVASCRIPT_DIRECTORY', APP_ROOT_DIRECTORY . 'js' . DIRECTORY_SEPARATOR);
		define('UPLOAD_DIRECTORY', APP_ROOT_DIRECTORY . 'uploads' . DIRECTORY_SEPARATOR);
			define('IMAGE_UPLOAD_DIRECTORY', UPLOAD_DIRECTORY . 'images' . DIRECTORY_SEPARATOR);
		define('VENDOR_DIRECTORY', APP_ROOT_DIRECTORY . 'vendor' . DIRECTORY_SEPARATOR);
	// App Directories
		if (!defined('CONFIG_DIRECTORY')) {
			define('CONFIG_DIRECTORY', APP_DIRECTORY . 'config' . DIRECTORY_SEPARATOR);
		}
		define('PLUGIN_DIRECTORY', APP_DIRECTORY . 'plugins' . DIRECTORY_SEPARATOR);
		define('RESOURCE_DIRECTORY', APP_DIRECTORY . 'resources' . DIRECTORY_SEPARATOR);
		define('TEMPLATE_DIRECTORY', APP_DIRECTORY . 'templates' . DIRECTORY_SEPARATOR);
		define('CLASSES_DIRECTORY', APP_DIRECTORY . 'classes' . DIRECTORY_SEPARATOR);
		define('FUNCTIONS_DIRECTORY', APP_DIRECTORY . 'functions' . DIRECTORY_SEPARATOR);
		define('MODEL_DIRECTORY', APP_DIRECTORY . 'models' . DIRECTORY_SEPARATOR);
		define('VIEW_DIRECTORY', APP_DIRECTORY . 'views' . DIRECTORY_SEPARATOR);
			define('ERRORS_DIRECTORY', VIEW_DIRECTORY . 'errors' . DIRECTORY_SEPARATOR);
		define('CONTROLER_DIRECTORY', APP_DIRECTORY . 'controllers' . DIRECTORY_SEPARATOR);
			define('ADMIN_CONTROLER_DIRECTORY', CONTROLER_DIRECTORY . 'admin' . DIRECTORY_SEPARATOR);
	// Other Locations
		define('COMMON_FUNCTIONS_LOCATION', BIN_DIRECTORY . 'common.php');
	    if ( is_dir( VENDOR_DIRECTORY . 'thetempusproject' )) {
	        define('TP_VENDOR_DIRECTORY', VENDOR_DIRECTORY . 'thetempusproject' . DIRECTORY_SEPARATOR);
	    } elseif ( is_dir( VENDOR_DIRECTORY . 'TheTempusProject' )) {
	        define('TP_VENDOR_DIRECTORY', VENDOR_DIRECTORY . 'TheTempusProject' . DIRECTORY_SEPARATOR);
	    } else {
			define('TP_VENDOR_DIRECTORY', VENDOR_DIRECTORY);
	    }
	    if (defined('TP_VENDOR_DIRECTORY')) {
		    if ( is_dir( TP_VENDOR_DIRECTORY . 'tempusprojectcore' )) {
		        define('TPC_ROOT_DIRECTORY', TP_VENDOR_DIRECTORY . 'tempusprojectcore' . DIRECTORY_SEPARATOR);
		    } elseif ( is_dir( TP_VENDOR_DIRECTORY . 'TempusProjectCore' )) {
		        define('TPC_ROOT_DIRECTORY', TP_VENDOR_DIRECTORY . 'TempusProjectCore' . DIRECTORY_SEPARATOR);
		    }
		    if ( is_dir( TP_VENDOR_DIRECTORY . 'TempusDebugger' )) {
		        define('TD_ROOT_DIRECTORY', TP_VENDOR_DIRECTORY . 'TempusDebugger' . DIRECTORY_SEPARATOR);
		    } elseif ( is_dir( TP_VENDOR_DIRECTORY . 'tempusdebugger' )) {
		        define('TD_ROOT_DIRECTORY', TP_VENDOR_DIRECTORY . 'tempusdebugger' . DIRECTORY_SEPARATOR);
		    }
		}

# Tempus Project Core
	// Tempus Debugger
		define('TEMPUS_DEBUGGER_SHOW_LINES', false);
		define('TEMPUS_DEBUGGER_SECURE_HASH', 'd73ed7591a30f0ca7d686a0e780f0d05');
	// Debug
		define('DEBUG_ENABLED', true);
		define('REDIRECTS_ENABLED', true);
		define('RENDERING_ENABLED', true);
		define('DEBUG_TRACE_ENABLED', false);
		define('DEBUG_TO_CONSOLE', false);
	// Check
		define('MINIMUM_PHP_VERSION', 5.6);
		define('DATA_TITLE_PREG', '#^[a-z 0-9\-\_ ]+$#mi');
		define('PATH_PREG_REQS', '#^[^/?*:;\\{}]+$#mi');
		define('SIMPLE_NAME_PREG', '#^[a-zA-Z0-9\-\_]+$#mi');
		define('ALLOWED_IMAGE_UPLOAD_EXTENTIONS', [".jpg",".jpeg",".gif",".png"]);
	// Token
		define('DEFAULT_TOKEN_NAME', 'TP_SESSION_TOKEN');
		define('TOKEN_ENABLED', true);
	// Database
		define('MAX_RESULTS_PER_PAGE', 50);
		define('DEFAULT_RESULTS_PER_PAGE', 5);
		// define('MINIMUM_MYSQL_VERSION', 5.6);
		// define('MINIMUM_SQLITE_VERSION', 5.6);
		// define('MINIMUM_PGSQL_VERSION', 5.6);
		// define('MINIMUM_SQLSRV_VERSION', 5.6);
	// Cookies
		define('DEFAULT_COOKIE_EXPIRATION', 604800);
		define('DEFAULT_COOKIE_PREFIX', 'TP_');
	// Sessions
		define('DEFAULT_SESSION_PREFIX', 'TP_');
	// Other
		define('DEFAULT_CONTROLER_CLASS', 'Home');
		define('DEFAULT_CONTROLER_METHOD', 'index');

# Tempus Project Specific
	define('PLUGINS_ENABLED', true);

# Prod Only
	define('DOWNLOADS_DIRECTORY', APP_ROOT_DIRECTORY . 'downloads' . DIRECTORY_SEPARATOR);

# Tell the app all; constants have been loaded.
	define('TEMPUS_PROJECT_CONSTANTS_LOADED', true);