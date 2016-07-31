<?php

if (!isset($_POST['doBackup'])) {
    $img1 = URL::base() . 'lib/images/gplv3.png';
    $csspath = URL::base() . 'lib/css/style.css';
    $phpself = $_SERVER['PHP_SELF'];
    echo <<< START
<h1>Kopia zapasowa systemu</h1>
<p>System wykona cały zrzut danych systemowych do pliku XML.</p>
<form action="$phpself" method="post">
    <button type="submit" name="doBackup">Wykonaj kopię zapasową</button>
</form>
START;
} else {
    Kohana_Request::factory()->redirect('admin/doBackup');
}