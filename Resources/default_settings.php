<?php

namespace TheTempusProject;

use TempusProjectCore\Classes\Input as Input;

$fullArray = explode('/', $_SERVER['PHP_SELF']);
array_pop($fullArray);
$docroot = implode('/', $fullArray) . '/';

$GLOBALS['config'] = array(
    "main" => array(
        "location" => $_SERVER['DOCUMENT_ROOT'].$docroot,
        "base" => "http://".$_SERVER['HTTP_HOST'].$docroot,
        "name" => Input::post_null('site_name'),
        "template" => "Default",
        "loginLimit" => 5,
        "page_limit" => 50,
        "page_default" => 5,
    ),
    "logging" => array(
        "admin" => false,
        "errors" => false,
        "logins" => false,
        "feedback" => false,
        "bug_reports" => false,
    ),
    "database" => array(
        "db_host" => Input::post_null('db_host'),
        "db_username" => Input::post_null('db_username'),
        "db_password" => Input::post_null('db_password'),
        "db_name" => Input::post_null('db_name'),
        "db_type" => "mysql",
        "db_enabled" => false,
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
?>