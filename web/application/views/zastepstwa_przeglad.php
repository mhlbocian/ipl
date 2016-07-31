<?php

function pobierznl($lekcja, $id) {
    $isf = new Kohana_Isf();
    $isf->Connect(APP_DBSYS);


    $res = $isf->DbSelect('zastepstwa', array('*'), 'where zast_id=\'' . $id . '\' and lekcja=\'' . $lekcja . '\'');

    if (count($res) != 0) {

	if (empty($res[0]['sala']) || empty($res[0]['nauczyciel'])) {
	    echo $res[0]['przedmiot'];
	} else {
	    echo $res[0]['przedmiot'] . ' (' . $res[0]['sala'] . ') - ' . $res[0]['nauczyciel'];
	}
    }
}

function pobierzdzien($id) {
    $isf = new Kohana_Isf();
    $isf->Connect(APP_DBSYS);
    $nl = $isf->DbSelect('zast_id', array('*'), 'where zast_id=\'' . $id . '\'');
    $nauczyciel = $nl[0]['za_nl'];

    $enpl_days = array(
	'Monday' => 'Poniedziałek',
	'Tuesday' => 'Wtorek',
	'Wednesday' => 'Środa',
	'Thursday' => 'Czwartek',
	'Friday' => 'Piątek',
	'Saturday' => 'Sobota',
	'Sunday' => 'Niedziela',
    );
    $day = date('l', strtotime($nl[0]['dzien']));
    $dzien = $enpl_days[$day];

    echo '<table class="przed" style="width: 400px"><thead class="a_odd">
            <tr><td colspan=3><h1>' . $nauczyciel . '</h1>
                <h3>' . $dzien . ' - ' . $nl[0]['dzien'] . '</h3></td></tr>
        <tr><td width="20"></td><td>Lekcja</td></tr></thead>';
    foreach ($isf->DbSelect('lek_godziny', array('*')) as $rowid => $rowcol) {
	$rowid++;
	$lek_nr = $rowid;
	if ($lek_nr % 2 == 0) {
	    echo '<tr class="a_even"><td>';
	} else {
	    echo '<tr><td>';
	}
	echo $rowid . '</td><td>';
	$res = $isf->DbSelect('planlek', array('*'), 'where nauczyciel=\'' . $nauczyciel . '\'
            and dzien=\'' . $dzien . '\' and lekcja=\'' . $rowid . '\'');
	if (count($res) == 1) {
	    echo '<span class="grptxt">
                <b>' . $res[0]['klasa'] . '</b> - ';
	    pobierznl($lek_nr, $id);
	    echo '</span></td></tr>';
	}
	if (count($res) == 0) {
	    $res = $isf->DbSelect('plan_grupy', array('*'), 'where nauczyciel=\'' . $nauczyciel . '\'
            and dzien=\'' . $dzien . '\' and lekcja=\'' . $lek_nr . '\'');
	    if (count($res) > 0) {
		foreach ($res as $rowid => $rowcol) {
		    echo '<p class="grplek">
                        <span class="grptxt">
                <b>' . $rowcol['klasa'] . ' gr ' . $rowcol['grupa'] . '</b> - ';
		    pobierznl($lek_nr, $id);
		}
		echo '</span></p></td></tr>';
	    } else {
		echo '---</td></tr>';
	    }
	}
    }
    echo '</table>';
}

function pobierzzast($id) {
    pobierzdzien($id);
}
?>
<?php pobierzzast($zast_id); ?>
<p>
    <span style="font-size: 16pt;">
        ◂
    </span>
    <a href="<?php echo URL::site('zastepstwa/index'); ?>">Powrót do zastępstw</a>
</p>