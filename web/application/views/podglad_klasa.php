<?php
/**
 * Nowy podglad Planow Lekcji
 */
$isf = Kohana_Isf::factory();
$isf->Connect(APP_DBSYS);

$appglobals = new App_Globals();

$ilosc_grup = $appglobals->getRegistryKey('ilosc_grup');
$k = $klasa;
$dni = array('Poniedziałek', 'Wtorek', 'Środa', 'Czwartek', 'Piątek');

$godziny = $isf->DbSelect('lek_godziny', array('*'));
?>
<table class="przed" align="center" style="font-size: 9pt; width: auto;">
    <thead>
        <tr class="a_odd">
            <td colspan="7" style="text-align: center">
                <p>
                    <span class="pltxt"><?php echo $klasa; ?></span>
                </p>
            </td>
        </tr>
	<tr class="a_even" style="text-align: center;">
            <td></td>
            <td>Godziny</td>
	    <?php foreach ($dni as $dzien): ?>
    	    <td style="width: 130px;"><?php echo $dzien; ?></td>
	    <?php endforeach; ?>
        </tr>
    </thead>
    <?php $i = 0; ?>
    <?php foreach ($godziny as $rowid => $values): ?>
        <tr <?php echo ($i % 2 != 0) ? ('class="a_even"') : ""; ?>>
    	<td><?php echo $values['lekcja']; ?></td>
    	<td><?php echo $values['godzina']; ?></td>
	    <?php foreach ($dni as $dzien): ?>
		<td>
		    <?php
		    $z_cond = 'where klasa=\'' . $k . '\' and dzien=\'' . $dzien . '\' and lekcja=\'' . $values['lekcja'] . '\'';
		    $zwykla = $isf->DbSelect('planlek', array('*'), $z_cond);
		    ?>
		    <?php if (count($zwykla) != 0): ?>
			<?php echo $zwykla[0]['przedmiot']; ?>
			<?php if (isset($zwykla[0]['sala']) && isset($zwykla[0]['skrot'])): ?>
			    <span class="grptxt">
				<a href="<?php echo URL::site('podglad/nauczyciel/' . $zwykla[0]['skrot']); ?>">
				    <?php echo $zwykla[0]['skrot']; ?></a>&nbsp;
				<a href="<?php echo URL::site('podglad/sala/' . $zwykla[0]['sala']); ?>">
				    <?php echo $zwykla[0]['sala']; ?></a>
			    </span>
			<?php endif; ?>
		    <?php else: ?>
			<?php
			$g_cond = 'where klasa=\'' . $k . '\' and dzien=\'' . $dzien . '\' and lekcja=\'' . $values['lekcja'] . '\'';
			$grupa = $isf->DbSelect('plan_grupy', array('*'), $g_cond);
			?>
			<?php if (count($grupa) != 0): ?>
			    <?php foreach ($grupa as $rowid => $values): ?>
		    	    <p class="grplek">
				    <?php echo $values['przedmiot']; ?>
		    		<span class="grptxt">
					<?php echo $values['grupa']; ?>/<?php echo $ilosc_grup; ?>
					<?php if (isset($values['sala']) && isset($values['skrot'])): ?>
					    <span class="grptxt">
						<a href="<?php echo URL::site('podglad/nauczyciel/' . $values['skrot']); ?>">
						    <?php echo $values['skrot']; ?></a>&nbsp;
						<a href="<?php echo URL::site('podglad/sala/' . $values['sala']); ?>">
						    <?php echo $values['sala']; ?></a>
					    </span>
					<?php endif; ?>
		    		</span>
		    	    </p>
			    <?php endforeach; ?>
			<?php endif; ?>
		    <?php endif; ?>
		</td>
	    <?php endforeach; ?>
        </tr>
	<?php $i++; ?>
    <?php endforeach; ?>
</table>