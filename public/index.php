<?php

require_once '../bootstrap.php';

use Ipl\Framework\Router;
use Ipl\Framework\Application;

Router::AddRoute("aad", [
    "controller" => "doPerformAction",
    "action" => "action1",
]);

Application::Factory(Router::Factory(filter_input(INPUT_SERVER, "PATH_INFO") ?? ""))
        ->Load()
        ->Execute();
