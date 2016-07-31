<?php
function insert_log($modul, $wiadomosc) {
    $fileName = "ipl-" . date("Ymd") . ".log";
    $fileHandler = fopen(RESDIR . DS . $fileName, "a");
    $timestamp = date('H:i:s');
    $message = "[$timestamp] $modul: $wiadomosc" . PHP_EOL;

    fwrite($fileHandler, $message);
    fclose($fileHandler);
}