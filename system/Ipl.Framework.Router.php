<?php

namespace Ipl\Framework;

/**
 * `Router` class
 * 
 * @author Michał Bocian <bocian.michal@outlook.com>
 * @license https://opensource.org/licenses/BSD-3-Clause
 * @version 3.0
 */
class Router {

    private static $defaultController = "Main";
    private static $defaultAction = "Index";
    private static $defaultErrorControler = "Error";
    private static $defaultErrorAction = "Index";
    private static $routingTable = [];
    private $route;

    public static function SetDefaultRoute(string $controller, string $action) {
        self::$defaultController = $controller;
        self::$defaultAction = $action;
    }

    public static function SetDeafultErrorRoute(string $controller, string $action) {
        self::$defaultErrorControler = $controller;
        self::$defaultErrorAction = $action;
    }

    public static function AddRoute(string $path, array $parameters) {
        $parameters["controller"] = $parameters["controller"] ?? self::$defaultController;
        $parameters["action"] = $parameters["action"] ?? self::$defaultAction;
        $parameters["parameters"] = $parameters["parameters"] ?? [];
        self::$routingTable[self::FormatPath($path)] = $parameters;
    }

    public static function GetStaticRoute(string $path) {
        return self::$routingTable[self::FormatPath($path)] ?? NULL;
    }

    private static function FormatPath($path) {
        if (isset($path[0]) && $path[0] == "/") {
            $path = substr($path, 1);
        }
        if (isset($path[strlen($path) - 1]) && $path[strlen($path) - 1] == "/") {
            $path = substr($path, 0, -1);
        }
        return $path;
    }

    public static function Factory(string $path) {
        return new Router($path);
    }

    public function __construct(string $path) {
        if (($route = self::GetStaticRoute($path)) !== NULL) {
            $this->route = $route;
        } else {
            $this->route = $this->ConvertPathToRoute($path);
        }
        return $this;
    }

    public function GetRoute() {
        return $this->route;
    }

    private function ConvertPathToRoute(string $path) {
        $pathArray = array_filter(explode("/", $path), function($val) {
            return $val !== "";
        });
        $pathArray = array_values($pathArray);
        $return = [
            "controller" => $pathArray[0] ?? self::$defaultController,
            "action" => $pathArray[1] ?? self::$defaultAction,
        ];
        unset($pathArray[0], $pathArray[1]);
        $return["parameters"] = array_values($pathArray);
        return $return;
    }

}
