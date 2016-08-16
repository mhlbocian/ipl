<?php

namespace Ipl\Framework;

/**
 * `Application` class for framework of Ipl
 * 
 * @author Michał Bocian <bocian.michal@outlook.com>
 * @license https://opensource.org/licenses/BSD-3-Clause
 * @version 3.0
 */
class Application {

    private $route;
    private $controller;
    private $action;
    private $parameters;

    public static function Factory(Router $router) {
        return new Application($router);
    }

    public function __construct(Router $router) {
        $this->route = $router->GetRoute();
        return $this;
    }

    public function Load() {
        $this->controller = $this->route["controller"];
        $this->action = $this->route["action"];
        $this->parameters = $this->route["parameters"];
        return $this;
    }

    public function Execute() {
        $function = "\\App\\Controlers\\Controler_" . $this->controller . "::Action_" . $this->action;
        $function();
    }

}
