<?php
$isf = new Kohana_Isf();

function pobierznl($dzien, $lekcja) {
    $isf = new Kohana_Isf();
    $isf->Connect(APP_DBSYS);

    $r = 1;
    $res = array();

    $nl_przedm = $isf->DbSelect('nl_przedm', array('*'), 'order by przedmiot asc');
    foreach ($nl_przedm as $rowid => $rowcol) {

	$przedm_sale = $isf->DbSelect('przedmiot_sale', array('*'), 'where przedmiot=\'' . $rowcol['przedmiot'] . '\' order by sala asc');

	foreach ($przedm_sale as $rid => $rcl) {

	    $res[$r]['przedmiot'] = $rowcol['przedmiot'];
	    $res[$r]['sala'] = $rcl['sala'];
	    $res[$r]['nauczyciel'] = $rowcol['nauczyciel'];
	    $r++;
	}
    }

    echo '<select name="zast[' . $lekcja . ']"><option selected>---</option>';
    echo '<optgroup label="Rodzaj zastępstwa"><option>lekcja wolna</option>';
    echo '<option>odwołane</option></optgroup><optgroup label="Zajęcia z nauczycielem">';
    foreach ($res as $rowid => $rowcol) {
	$res2 = $isf->DbSelect('planlek', array('*'), 'where dzien=\'' . $dzien . '\' and lekcja=\'' . $lekcja . '\'
            and (nauczyciel=\'' . $rowcol['nauczyciel'] . '\' or sala=\'' . $rowcol['sala'] . '\')');
	if (count($res2) == 0) {
	    echo '<option>' . $rowcol['przedmiot'] . ':' . $rowcol['sala'] . ':' . $rowcol['nauczyciel'] . '</option>';
	}
    }
    echo '</optgroup><optgroup label="Przedmioty">';
    $res = $isf->DbSelect('przedmioty', array('*'), 'order by przedmiot asc');
    foreach ($res as $a => $b) {
	echo '<option>' . $b['przedmiot'] . '</option>';
    }
    echo '</optgroup></select>';
}

function pobierzdzien($dzien, $nauczyciel) {
    $isf = new Kohana_Isf();
    $isf->Connect(APP_DBSYS);
    echo '<table class="przed"><thead class="a_odd">
        <tr><td></td><td>Godzina</td><td colspan=2>Lekcja</td></tr></thead>';
    $i_l = 0;
    foreach ($isf->DbSelect('lek_godziny', array('*')) as $rowid => $rowcol) {
	$rowid++;
	$lek_nr = $rowid;
	if ($lek_nr % 2 == 0) {
	    echo '<tr class="a_even"><td>';
	} else {
	    echo '<tr><td>';
	}
	echo $rowid . '</td><td>' . $rowcol['godzina'] . '</td>';
	$res = $isf->DbSelect('planlek', array('*'), 'where nauczyciel=\'' . $nauczyciel . '\'
            and dzien=\'' . $dzien . '\' and lekcja=\'' . $lek_nr . '\'');
	if (count($res) == 1) {
	    $i_l++;
	    echo '<td><p class="grplek">
                <b>' . $res[0]['klasa'] . '</b> - ' . $res[0]['przedmiot'] . ' (' . $res[0]['sala'] . ')
                    </p>';
	    echo '</td><td>';
	    pobierznl($dzien, $rowid);
	    echo '</td></tr>';
	} else {
	    $res = $isf->DbSelect('plan_grupy', array('*'), 'where nauczyciel=\'' . $nauczyciel . '\'
            and dzien=\'' . $dzien . '\' and lekcja=\'' . $lek_nr . '\'');
	    if (count($res) > 0) {
		$i_l++;
		echo '<td>';
		foreach ($res as $rowid => $rowcol) {
		    echo '<p class="grplek">
                <b>' . $rowcol['klasa'] . ' gr ' . $rowcol['grupa'] . '</b> - ' . $rowcol['przedmiot'] . ' (' . $rowcol['sala'] . ')
                    </p>';
		}
		echo '</td><td>';
		pobierznl($dzien, $lek_nr);
		echo '</td></tr>';
	    } else {
		echo '<td colspan=2>---</td></tr>';
	    }
	}
    }
    echo '</table>';
    if ($i_l == 0) {
	Kohana_Request::factory()->redirect('zastepstwa/edycja/brak');
    }
}
?>
<h1>
    <a href="#" onClick="document.forms['formPlan'].submit();">
        <img src="<?php echo URL::base() ?>lib/images/save.png" alt="zapisz"/></a>
    Zastępstwo za <?php echo $nauczyciel; ?>
</h1>
<h3><a href="<?php echo URL::site('zastepstwa/edycja'); ?>">Powrót</a>&emsp;W dniu <?php echo $data; ?> (<?php echo $dzien; ?>)</h3>
<p><b>Komentarz: </b><i><?php echo $komentarz; ?></i></p>
<form name="formPlan" action="<?php echo URL::site('zastepstwa/zatwierdz'); ?>" method="post">
    <?php pobierzdzien($dzien, $nauczyciel); ?>
    <input type="hidden" name="dzien" value="<?php echo $data; ?>"/>
    <input type="hidden" name="za_nl" value="<?php echo $nauczyciel; ?>"/>
    <input type="hidden" name="info" value="<?php echo $komentarz; ?>"/>
</form>