<?php

define("DS", DIRECTORY_SEPARATOR);
define("EXT", ".php");
define("APPDIR", realpath(".."));
define("WEBDIR", APPDIR . DS . "web");
// Kohana's constants (only for compability)
define("DOCROOT", APPDIR . DS . "web" . DS);
define("APPPATH", DOCROOT . DS . "application" . DS);
define("MODPATH", DOCROOT . DS . "modules" . DS);

$appconstants = [
    "RESDIR" => APPDIR . DS . "resources",
    "LIBDIR" => WEBDIR . DS . "lib",
    "APLDIR" => WEBDIR . DS . "application",
    "MODDIR" => WEBDIR . DS . "modules",
    "SYSPATH" => WEBDIR . DS . "system" . DS,
    "TLSDIR" => WEBDIR . DS . "tools",
    "TIMEZN" => "Europe/Warsaw",
];

foreach ($appconstants as $name => $value) {
    define($name, $value);
}

function loadIplLibraries(array $libraries) {
    $libDir = APLDIR . DS . "lib";
    foreach ($libraries as $library) {
        require_once $libDir . DS . $library . ".php";
    }
}
