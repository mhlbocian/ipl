<?php
/*
 * Edycja planu lekcji dla grupy
 * 
 * 
 */
$isf = new Kohana_Isf();
$isf->Connect(APP_DBSYS);
/**
 * Pobiera ilość godzin lekcyjnych
 */
$ilosc_lek = $isf->DbSelect('rejestr', array('wartosc'), 'where opcja=\'ilosc_godzin_lek\'');
$ilosc_lek = $ilosc_lek[0]['wartosc'];
/**
 *  pobiera czas godzin lekcyjnych
 */
$lek_godziny = $isf->DbSelect('lek_godziny', array('*'));
/**
 * zmienna w URL dla jakiej klasy jest edycja planu
 */
$k = $klasa;
/**
 * ustawienie zmiennej globalnej k
 */
$GLOBALS['k'] = $klasa;
/**
 *  pobranie ilosci grup
 */
$ilosc_grp = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'ilosc_grup\'');

/**
 * Pobiera pojedyncza komorke z tabeli edycji planu
 * W zaleznosci od sytuacji dobiera wlasciwe dane do elementow formularza
 *
 * @global string $k klasa
 * @param string $dzien dzien
 * @param int $lekcja ktora lekcja
 * @return string Pole formularza select-option z przedmiotami
 */
function pobierzdzien($dzien, $lekcja) {
    global $k; // odwolanie do globalnej k
    $isf = new Kohana_Isf();
    $isf->Connect(APP_DBSYS);
    /**
     * Pobiera wszystkich nauczycieli uczących klasę
     */
    $nk = $isf->DbSelect('nl_klasy', array('*'), 'where klasa=\'' . $k . '\' order by nauczyciel asc');
    $r = 1; // wskaźnik tablicy
    $a = array(); // zwracana tablica
    foreach ($nk as $rowid => $rowcol) {
        /**
         * Pobiera przedmioty nauczane przez nauczyciela
         */
        $p = $isf->DbSelect('nl_przedm', array('*'), 'where nauczyciel=\'' . $rowcol['nauczyciel'] . '\'
        order by przedmiot asc');

        foreach ($p as $rid => $rcl) {
            /**
             * Pobiera sale dla przedmiotu
             */
            $sl = $isf->DbSelect('przedmiot_sale', array('*'), 'where przedmiot=\'' . $rcl['przedmiot'] . '\'
            order by sala asc');

            /**
             * Pętla zwraca tablicę wynikową $a
             */
            foreach ($sl as $ri => $rc) {

                $a[$r]['nauczyciel'] = $rowcol['nauczyciel'];
                $a[$r]['klasa'] = $rowcol['klasa'];
                $a[$r]['przedmiot'] = $rcl['przedmiot'];
                $a[$r]['sala'] = $rc['sala'];

                $r++;
            }
        }
    }

    /** pobiera dotychczasowa przypisana lekcje dla klasy w danym dniu i danej godzinie */
    $lek = $isf->DbSelect('planlek', array('*'), 'where lekcja=\'' . $lekcja . '\' and dzien=\'' . $dzien . '\' and klasa=\'' . $k . '\'');
    $ret = ''; // zmienna do ktorej bedzie pozniej przypisana wartosc do zwrocenia przez funkcje
    $vl = '';
    // $options - elementy pola z przedmiotami i salami
    $options = '<optgroup label=\'Przedmiot - Sala - Nauczyciel\'>';
    /**
     * Dla kazdego wiersza nl-przedm-sala
     */
    foreach ($a as $rowid => $rowcol) {
        /**
         * Sprawdza czy istnieje juz lekcja z danym nauczycielem lub w danej sali
         * W zaleznosci od sytuacji dopisuje do zmiennej $options, odpowiednie
         * dane
         */
        $b_table = 'planlek';
        $b_cols = array('*');
        $b_cond = 'where dzien=\'' . $dzien . '\' and lekcja=\'' . $lekcja . '\' and ( nauczyciel=\'' . $rowcol['nauczyciel'] . '\' or sala=\'' . $rowcol['sala'] . '\')';
        if (count($isf->DbSelect($b_table, $b_cols, $b_cond)) == 0) { //gdy nie
            $b_table = 'plan_grupy';
            $b_cols = array('*');
            $b_cond = 'where dzien=\'' . $dzien . '\' and lekcja=\'' . $lekcja . '\' and nauczyciel=\'' . $rowcol['nauczyciel'] . '\' and sala!=\'' . $rowcol['sala'] . '\'';
            if (count($isf->DbSelect($b_table, $b_cols, $b_cond)) == 0) {
                $v = $rowcol['przedmiot'] . ':' . $rowcol['sala'] . ':' . $rowcol['nauczyciel'];
                $options.='<option>' . $v . '</option>';
            }
        } else { //gdy tak
        }
    }
    $options .= '</optgroup>';
    if (count($lek) == 0) { // gdy brak przypisanej lekcji dla klasy
        // pobiera ilosc grup
        $ilosc_grp = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'ilosc_grup\'');
        // przypisuje do zmiennej g, ilosc grup
        $g = $ilosc_grp[0]['wartosc'];
        $i = 0;
        while ($i < $g) {
            $i++;
            $ret .= '<p class=\'grplek\'>gr' . $i;
            $ret .= '<select style=\'width:200px;\' name=\'' . $dzien . '[' . $lekcja . '][' . $i . ']\'>';
            $lg = $isf->DbSelect('plan_grupy', array('*'), 'where dzien=\'' . $dzien . '\' and lekcja=\'' . $lekcja . '\' and grupa=\'' . $i . '\' and klasa=\'' . $k . '\'');
            if (count($lg) != 0) {
                if (isset($lg[0]['sala']) && isset($lg[0]['nauczyciel'])) {
                    $vg = $lg[0]['przedmiot'] . ':' . $lg[0]['sala'] . ':' . $lg[0]['nauczyciel'];
                } else {
                    $vg = $lg[0]['przedmiot'];
                }
                $ret .= '<option selected>' . $vg . '</option>';
            }
            $ret .= '<option>---</option>';
            $ret .= $options;
            $ret.='<optgroup label=\'Zwykłe przedmioty\'>';
            foreach ($isf->DbSelect('przedmioty', array('*'), 'order by przedmiot asc') as $rc => $ri) {
                $ret.='<option>' . $ri['przedmiot'] . '</option>';
            }
            $ret.='</optgroup></select></p>';
        }
    } else {
        if ($vl != '---') {
            if ($lek[0]['sala'] == '' && $lek[0]['nauczyciel'] == '') {
                $ret .= '<b>' . $lek[0]['przedmiot'] . '</b><br/>';
                $vl = $lek[0]['przedmiot'];
            } else {
                $ret .= '<b>' . $lek[0]['przedmiot'] . '</b>(' . $lek[0]['sala'] . ')(' . $lek[0]['nauczyciel'] . ')<br/>';
                $vl = $lek[0]['przedmiot'] . ':' . $lek[0]['sala'] . ':' . $lek[0]['nauczyciel'];
            }
        }
    }

    return $ret;
}
?>
<?php if ($ilosc_grp[0]['wartosc'] == 0): ?>
    <h3>Nie można dokonać edycji z powodu braku ustawionych grup</h3>
<?php else: ?>
    <form action="<?php echo URL::site('plan/grupazatw'); ?>" method="post" name="formPlan" name="formPlan"
    <?php if (!isset($alternative)): ?>
              style="margin-top: 100px;">
              <?php else: ?>
            >
        <?php endif; ?>
        <?php if ($alternative != false): ?>
            <link rel="stylesheet" type="text/css" href="<?php echo URL::base() ?>lib/css/style.css"/>
            <h1>Edycja planu grupowego dla klasy <?php echo $klasa; ?>
                &emsp;<button type="submit" name="btnSubmit">Zapisz zmiany</button></h1>
        <?php endif; ?>
        <input type="hidden" name="klasa" value="<?php echo $klasa; ?>"/>
        <table class="przed">
            <thead class="a_odd">
                <tr>
                    <td></td>
                    <td>Godziny</td>
                    <td>Poniedziałek</td>
                    <td>Wtorek</td>
                    <td>Środa</td>
                    <td>Czwartek</td>
                    <td>Piątek</td>
                </tr>
            </thead>
            <tbody>
                <?php for ($i = 1; $i <= $ilosc_lek; $i++): ?>
                    <?php if ($i % 2 == 0): ?>
                        <?php $str = "class=\"a_even\""; ?>
                    <?php else: ?>
                        <?php $str = ""; ?>
                    <?php endif; ?>
                    <tr <?php echo $str; ?>>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $lek_godziny[$i-1]['godzina']; ?></td>
                        <td>
                            <?php echo pobierzdzien('Poniedziałek', $i); ?>
                        </td>
                        <td>
                            <?php echo pobierzdzien('Wtorek', $i); ?>
                        </td>
                        <td>
                            <?php echo pobierzdzien('Środa', $i); ?>
                        </td>
                        <td>
                            <?php echo pobierzdzien('Czwartek', $i); ?>
                        </td>
                        <td>
                            <?php echo pobierzdzien('Piątek', $i); ?>
                        </td>
                    </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </form>
<?php endif; ?>