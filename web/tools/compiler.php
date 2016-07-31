<?php

$pathx = realpath(__DIR__ . '/../');
define('DS', DIRECTORY_SEPARATOR);
define('APP_ROOT', realpath('../..'));

require_once $pathx . DS . 'config.php';
echo APP_ROOT;
require_once $pathx . DS . 'modules' . DS . 'isf' . DS . 'classes' . DS . 'kohana' . DS . 'isf.php';

$start_time = microtime();

function writeln($string) {
    echo $string . PHP_EOL;
}

function ustalplan($klasa) {
    echo 'KLASA: ' . $klasa . PHP_EOL;
    $maxl = 8;
    $podejscie = 1;
    $ilosc_godz_tyg = $maxl * 8;

    $isf = new Kohana_Isf();
    $isf->Connect(APP_DBSYS);

    $isf->DbDelete('planlek', 'where klasa like \'%\'');

    $prz = array();
    foreach ($isf->DbSelect('przedmioty', array('*')) as $rowid => $rowcol) {
	$prz[] = $rowcol['przedmiot'];
    }

    // ustalenie losowej liczby max lekcji danego przedmiotu tyg
    $przlek = array();
    foreach ($prz as $rowid => $rowcol) {
	if ($rowcol == 'matematyka' || $rowcol == 'język polski' || $rowcol == 'wf') {
	    $przlek[$rowcol] = rand(4, 5);
	} else {
	    if ($rowcol == 'lw') {
		$przlek[$rowcol] = 1;
	    } else {
		$przlek[$rowcol] = rand(1, 2);
	    }
	}
    }

// sprawdzenie czy lekcji jest > niz godzin
    $wynik = 0;
    foreach ($przlek as $przed => $godz) {
	$wynik+=$godz;
    }
    if ($ilosc_godz_tyg < $wynik) {
	echo 'Laczna liczba godzin lekcyjnych jest za duza. [' . $wynik . ']' . PHP_EOL;
	exit;
    } else {
	echo 'GODZIN LEKCYJNYCH: ' . $wynik . PHP_EOL;
    }

//tablica dni
    $dni = array(
	'Poniedziałek', 'Wtorek', 'Środa', 'Czwartek', 'Piątek'
    );
    reset:
    $plan = array();
    $uzyte = array();
    $i = 0;
    $podejscie = 0;
    plan:
    if (count($uzyte) > count($prz))
	goto reset;
    while ($i < $wynik) {
	przedmiot:
	if ($podejscie > 10000) {
	    goto reset;
	}
	$podejscie++;
	writeln($podejscie . '/10000');
	$p = array_rand($prz);
	$p = $prz[$p];
	if (isset($uzyte[$p])) {
	    goto plan;
	}
	$uzyte[$p] = '';
	$ilelek = $przlek[$p];
	$la = 0;
	lekcja:
	$podejscie++;
	writeln($podejscie . '/10000');
	if ($podejscie > 10000) {
	    goto reset;
	}
	$dzien = array_rand($dni);
	$dzien = $dni[$dzien];
	$lek = rand(1, $maxl);
	if (isset($plan[$dzien][$lek])) {
	    goto lekcja;
	}
	$la++;
	$plan[$dzien][$lek] = $p;
	$i++;
	if ($la < $ilelek) {
	    goto lekcja;
	}
    }
    echo 'WPROWADZANIE DANYCH';
    foreach ($plan as $dzien => $rowcol) {
	foreach ($rowcol as $lek => $prz) {
	    $in['dzien'] = $dzien;
	    $in['klasa'] = $klasa;
	    $in['lekcja'] = $lek;
	    $in['przedmiot'] = $prz;
	    $isf->DbInsert('planlek', $in);
	}
	echo '.';
    }
    echo PHP_EOL;
    echo '---KONIEC---' . PHP_EOL . PHP_EOL;
}

$isf = new Kohana_Isf();
$isf->Connect(APP_DBSYS);
foreach ($isf->DbSelect('klasy', array('*')) as $rowid => $rowcol) {
    ustalplan($rowcol['klasa']);
}
echo 'DZIEKUJE ZA SKORZYSTANIE Z GENERATORA PLANOW';