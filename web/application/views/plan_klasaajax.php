<?php
/*
 * Edycja planu lekcji
 * 
 * 
 */
/**
 * Nowy obiekt frameworka
 */
$isf = new Kohana_Isf();
$isf->Connect(APP_DBSYS);
/**
 * Pobiera z rejestru ilość godzin lekcyjnych
 */
$ilosc_lek = $isf->DbSelect('rejestr', array('wartosc'), 'where opcja=\'ilosc_godzin_lek\'');
$ilosc_lek = $ilosc_lek[0]['wartosc'];
/**
 * Pobiera godziny lekcyjne
 */
$lek_godziny = $isf->DbSelect('lek_godziny', array('*'));
$k = $klasa; // argument funkcji w klasie kontrolera, klasa
$GLOBALS['k'] = $klasa; // ustawienie zmiennej globalnej
/**
 * Pobiera i zwraca pojedyncza komorke planu lekcji
 *
 * @global string $k lekcje klasy do pobrania
 * @param string $dzien dzien do pobrania
 * @param int $lekcja lekcja do pobrania
 * @return string Zwraca pojedyncza komorke planu
 */

function pobierzdzien($dzien, $lekcja) {
    /**
     * Ustawienie globalnej
     */
    global $k;
    /**
     * Nowy obiekt frameworka
     */
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
    /**
     * Pobiera lekcję dla danej klasy, w danym dniu o danej godzinie
     * z normalnego planu lekcji
     */
    $lek = $isf->DbSelect('planlek', array('*'), 'where lekcja=\'' . $lekcja . '\' and dzien=\'' . $dzien . '\' and klasa=\'' . $k . '\'');
    $ret = ''; // zmienna zawierajaca dane zwracane przez funkcję
    $vl = '';
    if (count($lek) == 0) {
        $lekx = $isf->DbSelect('plan_grupy', array('*'), 'where lekcja=\'' . $lekcja . '\' and dzien=\'' . $dzien . '\' and klasa=\'' . $k . '\'');
        if (count($lekx) >= 1) {
            $ret .= '<b>[ zajęcia w grupach ]</b><br/>';
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
    $ret .= '<select style=\'width:200px;\' name=\'' . $dzien . '[' . $lekcja . ']\'>';
    if ($vl != '') {
        $ret .= '<option selected>' . $vl . '</option>';
    }
    $ret .= '<option>---</option><optgroup label=\'Przedmiot - Sala - Nauczyciel\'>';
    foreach ($a as $rowid => $rowcol) {
        $b_table = 'planlek';
        $b_cols = array('*');
        $b_cond = 'where dzien=\'' . $dzien . '\' and lekcja=\'' . $lekcja . '\' and ( nauczyciel=\'' . $rowcol['nauczyciel'] . '\' or sala=\'' . $rowcol['sala'] . '\')';
        if (count($isf->DbSelect($b_table, $b_cols, $b_cond)) == 0) {
            $b_table = 'plan_grupy';
            $b_cols = array('*');
            $b_cond = 'where dzien=\'' . $dzien . '\' and lekcja=\'' . $lekcja . '\' and nauczyciel=\'' . $rowcol['nauczyciel'] . '\'';
            if (count($isf->DbSelect($b_table, $b_cols, $b_cond)) == 0) {
                $v = $rowcol['przedmiot'] . ':' . $rowcol['sala'] . ':' . $rowcol['nauczyciel'];
                $ret.='<option>' . $v . '</option>';
            }
        } else {
            
        }
    }
    $ret.='</optgroup><optgroup label=\'Zwykły przedmiot\'>';
    foreach ($isf->DbSelect('przedmioty', array('*'), 'order by przedmiot asc') as $rc => $ri) {
        $ret.='<option>' . $ri['przedmiot'] . '</option>';
    }
    $ret.='</optgroup></select>';
    return $ret;
}
?>
<form action="<?php echo URL::site('plan/zatwierdz'); ?>" method="post" name="formPlan"
<?php if (!isset($alternative)): ?>
          style="margin-top: 100px;">
          <?php else: ?>
        >
    <?php endif; ?>
    <?php if ($alternative != false): ?>
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base() ?>lib/css/style.css"/>
        <h1>Edycja planu dla klasy <?php echo $klasa; ?>
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
<script type="text/javascript">
    function confirmation(){
        var answer = confirm("Czy chcesz zapisać, usuwając jednocześnie plan dla grup?");
        if(answer){
            document.forms['formPlan'].submit();
        }else{
        }
    }
</script>