<?php

define('ALLOWED_REFERRER', '');
define('ROOT_DIRECTORY', '.');
define('SEND_BUF_LEN', 1024 * 8);

@set_time_limit(0);
if (function_exists('apache_setenv'))
    @apache_setenv('no-gzip', 1);
@ini_set('zlib.output_compression', 0);
@ini_set('implicit_flush', 1);
while (ob_get_level())
    ob_end_clean();
ob_implicit_flush(1);

$http_ref = strtoupper(@$_SERVER['HTTP_REFERER']);

if (($http_ref !== '') && (ALLOWED_REFERRER !== '')) {
    if (strpos($http_ref, strtoupper(ALLOWED_REFERRER)) === false) {
        header('HTTP/1.1 500 Internal Server Error');
        die('Internal server error.');
    }
}

$file = @$_GET['file'];
if (@get_magic_quotes_gpc())
    $file = stripslashes($file);

$root_dir = realpath(ROOT_DIRECTORY);
$file = realpath($root_dir . '/' . $file);

if ((strpos($file, $root_dir) !== 0) || (!is_file($file))) {
    header('HTTP/1.1 404 File Not Found');
    die('File not found.');
}

$fname = basename($file);
$fsize = filesize($file);
$ftime = filemtime($file);

$fmime = '';
$range = @$_SERVER['HTTP_RANGE'];

$r_start = 0;
$c_length = $fsize;

if (preg_match('/bytes=([0-9]*)-([0-9]*)/', $range, $tmp)) {
    $r_start = (int) $tmp[1];
    $r_stop = (int) $tmp[2];
    if ($r_stop < $r_start)
        $r_stop = $fsize - 1;
    $c_length = $r_stop - $r_start + 1;

    header('HTTP/1.1 206 Partial Content');
    header('Content-Range: bytes ' .
            $r_start . '-' . $r_stop . '/' . $fsize);
}
else {
    header('HTTP/1.1 200 OK');
}

if (function_exists('mime_content_type')) {
    $fmime = mime_content_type($file);
} else if (function_exists('finfo_file')) {
    $finfo = finfo_open(FILEINFO_MIME);
    $fmime = finfo_file($finfo, $file);
    finfo_close($finfo);
}
if ($fmime == '') {
    $fmime = 'application/force-download';
}

header('Accept-Ranges: bytes');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $ftime) . ' GMT');
header('Pragma: public');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Cache-Control: private', false);
header('Content-Description: File Transfer');
header('Content-Disposition: attachment; filename="' . $fname . '"');
header('Content-Type: ' . $fmime);
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . $c_length);

flush();

if ($fp = @fopen($file, 'rb')) {
    @flock($fp, 1);
    @fseek($fp, $r_start);
    while ((!feof($fp)) && ($c_length > SEND_BUF_LEN)) {
        print(fread($fp, SEND_BUF_LEN));
        $c_length = $c_length - SEND_BUF_LEN;
        flush();
        if (connection_status() != 0)
            break;
    }
    if ((!feof($fp)) && (connection_status() == 0)) {
        print(fread($fp, $c_length));
        flush();
    }
    @flock($fp, 3);
    @fclose($fp);
    @unlink($_GET['file']);
    if (isset($_GET['redirect'])) {
        echo '<html><head></head><body onLoad="window.close();"><h1>Zamknij ta strone</h1></body></html>';
    }
}