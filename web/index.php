<?php

require_once "bootstrap.php";

if (!defined('KOHANA_START_TIME')) {
    define('KOHANA_START_TIME', microtime(TRUE));
}

if (!defined('KOHANA_START_MEMORY')) {
    define('KOHANA_START_MEMORY', memory_get_usage());
}



try {
    Core_Tools::CheckIsInstalled();
} catch (Exception $e) {
    if ($e->getCode() == 501) {
        include "install.php";
        exit;
    }
}

define('APP_PATH', global_app_path);
define('APP_DBSYS', global_app_dbsys);

require APLDIR . DS . "bootstrap.php";
echo Request::factory()
        ->execute()
        ->send_headers()
        ->body();
