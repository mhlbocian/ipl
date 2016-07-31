<div data-role="content">
    <ul data-role="listview">
	<?php
	$k = $klasa;
	$dni = array('Poniedziałek', 'Wtorek', 'Środa', 'Czwartek', 'Piątek');
	$ct = new Core_Tools();
	?>
	<?php foreach ($dni as $dzien): ?>
    	<li data-role="list-divider"><?php echo $dzien; ?></li>
	    <?php for ($l = 1; $l <= App_Globals::getRegistryKey('ilosc_godzin_lek'); $l++): ?>
		<?php
		$single = $ct->getSingleLesson($klasa, $dzien, $l);
		if ($single == 'fetched:none') {
		    $grupa = $ct->getGroupLesson($klasa, $dzien, $l);
		    if ($grupa == 'fetched:none') {
			$lekcja = '-';
		    } else {
			$lekcja = '';
			foreach ($grupa as $grupa => $zajecia) {
			    $lekcja .= 'gr' . $grupa . ' <b>' . $zajecia['przedmiot'] . '</b>&emsp;';
			}
		    }
		} else {
		    $lekcja = '<b>' . $single['przedmiot'] . '</b>';
		}
		?>
		<li><?php echo $l; ?>.&emsp;<?php echo $lekcja; ?></li>
	    <?php endfor; ?>
	<?php endforeach; ?>
    </ul>
</div>