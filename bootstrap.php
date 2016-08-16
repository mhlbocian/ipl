<?php

/**
 * Bootstrap of application
 * 
 * @author Michał Bocian <bocian.michal@outlook.com>
 * @license https://opensource.org/licenses/BSD-3-Clause
 * @version 3.0
 */
define("DS", DIRECTORY_SEPARATOR);
define("ROOTDIR", dirname(__FILE__));
/* application constants */
define("APPDIR", ROOTDIR . DS . "application");
define("CONFIGDIR", ROOTDIR . DS . "configuration");
define("MODDIR", ROOTDIR . DS . "modules");
define("PUBDIR", ROOTDIR . DS . "public");
define("RESDIR", ROOTDIR . DS . "system");
define("SYSDIR", ROOTDIR . DS . "system");
define("SSTDIR", SYSDIR . DS . "static");

set_error_handler(function($errNo, $errStr) {
    if (!(error_reporting() & $errNo)) {
        return;
    }

    switch ($errNo) {
        case E_ALL:
        case E_ERROR:
        case E_CORE_ERROR:
        case E_USER_ERROR:
            $result = file_get_contents(SSTDIR . DS . "errorPage.html");
            $result = str_replace("{{errcode}}", $errNo, $result);
            $result = str_replace("{{errmsg}}", $errStr, $result);
            echo $result;
            exit(1);
            break;

        default:
            $result = file_get_contents(SSTDIR . DS . "errorDiv.html");
            $result = str_replace("{{errcode}}", $errNo, $result);
            $result = str_replace("{{errmsg}}", $errStr, $result);
            echo $result;
            break;
    }

    return true;
});

spl_autoload_register(function($className) {
    $nameParts = explode("\\", $className);
    $file = str_replace("\\", ".", $className);
    switch ($nameParts[0]) {
        case "Ipl":
            $path = SYSDIR;
            break;
        case "App":
            if ($nameParts[1] == "Controlers") {
                $path = APPDIR . DS . "controller";
                $file = str_replace("Controler_", "", $file);
            }
            break;
        default:
            $path = MODDIR;
    }
    require_once $path . DS . $file . ".php";
});
