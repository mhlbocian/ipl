<?php
header('Content-Type: text/html; charset=UTF-8');
if(file_exists($_GET['file'])){
    highlight_file($_GET['file']);
}else{
    die('Plik nie istnieje');
}