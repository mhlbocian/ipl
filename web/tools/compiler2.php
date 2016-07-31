<?php

require_once '../modules/isf/classes/kohana/isf.php';
require_once '../lib/nusoap/nusoap.php';

define('HNAME', 'http://localhost/');

$isf = new Kohana_Isf();
$isf->Connect(APP_DBSYS);

try {
    $wsdl = new nusoap_client(HNAME . 'webapi.php?wsdl');
    $params = array(
        'login' => 'root',
        'password' => 'Plan001',
        'token' => 'd020c9'
    );
    $token = $wsdl->call('doLogin', $params, 'webapi.planlekcji.isf');
} catch (Exception $e) {
    echo $e->getMessage();
    exit;
}

$maxl = 8; //$wsdl->call('doGetRegistryKey', array('token' => $token, 'key' => 'ilosc_godzin_lek'), 'webapi.planlekcji.isf');

function writeln($string) {
    echo $string . PHP_EOL;
}

$parser = simplexml_load_file('config_1a.xml');

$dni = array(
    'Poniedziałek', 'Wtorek', 'Środa', 'Czwartek', 'Piątek',
);

$maxlek = 0;
foreach ($parser->przedmioty->item as $a) {
    $maxlek+=$a->attributes()->godziny;
}

$klasa = $parser->klasa;
$zwykle = array();
$grupowe = array();
$z_ilelek = 0;
$g_ilelek = 0;
foreach ($parser->przedmioty->item as $a) {
    if (isset($a->attributes()->nauczyciel)) {
        $zwykle[] = array(
            'przedmiot' => $a->attributes()->przedmiot,
            'godziny' => $a->attributes()->godziny,
            'nauczyciel' => $a->attributes()->nauczyciel,
        );
        $z_ilelek+=$a->attributes()->godziny;
    }
}
foreach ($parser->przedmioty->grupy->item as $a) {
    $nl = array();
    $aaa = 0;
    foreach ($a->nauczyciel as $b) {
        $aaa++;
        $nl[] = array(
            'nauczyciel' => $b->attributes()->name,
            'grupa' => $b->attributes()->grupa,
        );
    }
    $grupowe[] = array(
        'przedmiot' => $a->attributes()->przedmiot,
        'godziny' => $a->attributes()->godziny,
        'nauczyciele' => $nl,
    );
    $g_ilelek+= ( $a->attributes()->godziny * $aaa);
}

$isf->DbDelete('planlek', 'klasa=\'' . $klasa . '\'');
$isf->DbDelete('plan_grupy', 'klasa=\'' . $klasa . '\'');

/**
 * Rozpoczecie procesu generowania zwyklego planu
 */
reset:
/**
 * Ustawienie licznika lekcji
 */
$plan = array();
$i = 0; // licznik lekcji
$podejscie = 0; // licznik kroku algorytmu
$uzyte = array(); // tablica uzytych przedmiotow
plan: // etykieta plan
/**
 * Wykonuje algo, dopoki nie wszystkie lekcje zostaly wypelnione
 */
while ($i < $z_ilelek) {

    przedmiot: // etykieta wyboru przedmiotu
    if ($podejscie > 10000) {
        goto reset;
    }
    $podejscie++;
    writeln($podejscie . '/10000');
    $a = array_rand($zwykle);
    $p = (string) $zwykle[$a]['przedmiot'];
    $nl = (string) $zwykle[$a]['nauczyciel'];
    if (isset($uzyte[$p])) {
        goto plan;
    }
    $uzyte[$p] = '';
    $ilelek = $zwykle[$a]['godziny'];
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

    $malek = false;

    sala:

    $sale = $isf->DbSelect('przedmiot_sale', array('*'), 'where przedmiot=\'' . $p . '\'');
    if (count($sale) == 0) {
        writeln('Przedmiot nie ma przypisanej sali. Przerwano');
        die();
    }

    $sll = array_rand($sale);
    $sala = $sale[$sll]['sala'];

    $a = $isf->DbSelect('planlek', array('*'), 'where dzien=\'' . $dzien . '\' and lekcja=\'' . $lek . '\' and sala=\'' . $sala . '\'');
    $b = $isf->DbSelect('plan_grupy', array('*'), 'where dzien=\'' . $dzien . '\' and lekcja=\'' . $lek . '\' and sala=\'' . $sala . '\'');

    if (count($a) != 0 || count($b) != 0) {
        goto sala;
    }

    $a_table = 'plan_grupy';
    $a_cols = array('*');
    $a_cond = 'where dzien=\'' . $dzien . '\' and lekcja=\'' . $lek . '\' and ( nauczyciel=\'' . $nl . '\' or sala=\'' . $sala . '\')';

    $b_table = 'planlek';
    $b_cols = array('*');
    $b_cond = 'where dzien=\'' . $dzien . '\' and lekcja=\'' . $lek . '\' and ( nauczyciel=\'' . $nl . '\' or sala=\'' . $sala . '\')';

    $a_r = count($isf->DbSelect($a_table, $a_cols, $a_cond));
    $b_r = count($isf->DbSelect($b_table, $b_cols, $b_cond));

    if (isset($plan[$dzien][$lek]) || $a_r > 0 || $b_r > 0) {
        goto lekcja;
    }
    $la++;
    $plan[$dzien][$lek] = array(
        'przedmiot' => $p,
        'nauczyciel' => $nl,
        'sala' => $sala,
    );
    $i++;
    if ($la < $ilelek) {
        goto lekcja;
    }
}

/**
 * Rozpoczecie procesu generowania
 */
greset:
/**
 * Ustawienie licznika lekcji
 */
$i = 0;
$uzyte = array(); // tablica uzytych przedmiotow
$plangrp = array();
writeln('PLAN DLA GRUP');
gplan: // etykieta plan
/**
 * Wykonuje algo, dopoki nie wszystkie lekcje zostaly wypelnione
 */
while ($i < $g_ilelek) {

    gprzedmiot: // etykieta wyboru przedmiotu
    if ($podejscie > 10000) {
        goto reset;
    }
    $podejscie++;
    writeln($podejscie . '/10000');
    $a = array_rand($grupowe);
    $p = (string) $grupowe[$a]['przedmiot'];
    writeln('WYBRANO ' . $p);
    if (isset($uzyte[$p])) {
        goto gplan;
    }
    $uzyte[$p] = '';
    print_r($uzyte);
    $ilelek = $grupowe[$a]['godziny'];
    writeln('PRZYPADA ' . $ilelek . ' NA TEN PRZEDMIOT DLA KAZDEJ GRUPY');
    foreach ($grupowe[0]['nauczyciele'] as $amm) {
        $la = 0;
        $nl = (string) $amm['nauczyciel'];
        $grupa = (string) $amm['grupa'];

        writeln('GRUPA ' . $grupa . ' NAUCZYCIEL ' . $nl);

        glekcja:
        $podejscie++;
        writeln($podejscie . '/10000');
        if ($podejscie > 10000) {
            goto reset;
        }
        $dzien = array_rand($dni);
        $dzien = $dni[$dzien];
        $lek = rand(1, $maxl);

        $malek = false;

        gsala:

        $sale = $isf->DbSelect('przedmiot_sale', array('*'), 'where przedmiot=\'' . $p . '\'');
        if (count($sale) == 0) {
            writeln('Przedmiot nie ma przypisanej sali. Przerwano');
            die();
        }

        $sll = array_rand($sale);
        $sala = $sale[$sll]['sala'];

        $a = $isf->DbSelect('planlek', array('*'), 'where dzien=\'' . $dzien . '\' and lekcja=\'' . $lek . '\' and sala=\'' . $sala . '\'');
        $b = $isf->DbSelect('plan_grupy', array('*'), 'where dzien=\'' . $dzien . '\' and lekcja=\'' . $lek . '\' and sala=\'' . $sala . '\' and przedmiot != \'' . $p . '\'');

        if (count($a) != 0 || count($b) != 0) {
            goto gsala;
        }

        $a_table = 'plan_grupy';
        $a_cols = array('*');
        $a_cond = 'where dzien=\'' . $dzien . '\' and lekcja=\'' . $lek . '\' and nauczyciel=\'' . $nl . '\' and ( przedmiot == \'' . $p . '\' or sala == \'' . $sala . '\')';

        $b_table = 'planlek';
        $b_cols = array('*');
        $b_cond = 'where dzien=\'' . $dzien . '\' and lekcja=\'' . $lek . '\' and ( nauczyciel=\'' . $nl . '\' or sala=\'' . $sala . '\')';

        $a_r = count($isf->DbSelect($a_table, $a_cols, $a_cond));
        $b_r = count($isf->DbSelect($b_table, $b_cols, $b_cond));

        if (isset($plan[$dzien][$lek]) || $a_r > 0 || $b_r > 0) {
            goto glekcja;
            exit;
        }

        if (isset($plangrp[$dzien][$lek])) {
            $g = $plangrp[$dzien][$lek];
            if (isset($g[$grupa]) || array_key_exists($nl, $g) || array_key_exists($sala, $g)) {
                goto glekcja;
                exit;
            }
        }
        $la++;
        $plangrp[$dzien][$lek] = array(
            $grupa => array(
                'przedmiot' => $p,
                'nauczyciel' => $nl,
                'sala' => $sala,
                'grupa' => $grupa),
        );
        $i++;
        writeln('gr' . $grupa . ' - ' . $la . '/' . $ilelek);
        if ($la < $ilelek) {
            goto glekcja;
        }
    }
    writeln($i . '/' . $g_ilelek);
}

writeln('WPROWADZANIE DANYCH...');
print_r($plan);
print_r($plangrp);

foreach ($plan as $dzien => $dane) {
    foreach ($dane as $lekcja => $dane) {
        $isf->DbInsert('planlek', array(
            'klasa' => $klasa,
            'dzien' => $dzien,
            'przedmiot' => $dane['przedmiot'],
            'nauczyciel' => $dane['nauczyciel'],
            'sala' => $dane['sala'],
            'lekcja' => $lekcja,
        ));
    }
    echo '.';
}

foreach ($plangrp as $dzien => $lekcja) {
    foreach ($lekcja as $grupa => $dane) {
        foreach ($dane as $grupa => $dane) {
            $isf->DbInsert('plan_grupy', array(
                'klasa' => $klasa,
                'dzien' => $dzien,
                'przedmiot' => $dane['przedmiot'],
                'nauczyciel' => $dane['nauczyciel'],
                'sala' => $dane['sala'],
                'grupa' => $grupa,
                'lekcja' => key($lekcja),
            ));
        }
        echo '.';
    }
}

writeln('ZAKONCZONO');