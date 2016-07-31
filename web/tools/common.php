<?php

require_once '../lib/nusoap/nusoap.php';
require_once '../config.php';

define('HTTP_ADDR', 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . APP_PATH);

function redirect($url=null) {
    if($url==null){
        $url = HTTP_ADDR.'index.php';
    }
    header('Location: ' . $url);
    exit;
}

session_start();
try {
    $wsdl = new nusoap_client(HTTP_ADDR . 'webapi.php?wsdl');
} catch (Exception $e) {
    echo $e->getMessage();
    exit;
}

if (!isset($_SESSION['token'])) {
    redirect();
    exit;
} else {
    $auth = $wsdl->call('doShowAuthTime', array('token' => $_SESSION['token']), 'webapi.planlekcji.isf');
    if (strtotime($_SESSION['token_time']) < time()) {
        $wsdl->call('doLogout', array('token' => $_SESSION['token']), 'webapi.planlekcji.isf');
        session_destroy();
        redirect();
        exit;
    }
    if ($auth == 'auth:failed') {
        redirect();
        exit;
    }
    if ($_SESSION['user'] != 'root') {
        echo '<h1>Dostep dla innych niz root zabroniony</h1>';
        exit;
    }
}

