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

$sym = App_Globals::getTeacherSym($k);
?>
<table class="przed" align="center" style="font-size: 9pt; width: auto;">
    <thead style="background: #ccccff;">
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
		    $z_cond = 'where skrot=\'' . $sym . '\' and dzien=\'' . $dzien . '\' and lekcja=\'' . $values['lekcja'] . '\'';
		    $zwykla = $isf->DbSelect('planlek', array('*'), $z_cond);
		    ?>
		    <?php if (count($zwykla) != 0): ?>
			<?php echo $zwykla[0]['przedmiot']; ?>
	    	    <span class="grptxt">
	    		<a href="<?php echo URL::site('podglad/klasa/' . $zwykla[0]['klasa']); ?>">
				<?php echo $zwykla[0]['klasa']; ?></a>&nbsp;
	    		<a href="<?php echo URL::site('podglad/sala/' . $zwykla[0]['sala']); ?>">
				<?php echo $zwykla[0]['sala']; ?></a>
	    	    </span>
		    <?php else: ?>
			<?php
			$g_cond = 'where skrot=\'' . $sym . '\' and dzien=\'' . $dzien . '\' and lekcja=\'' . $values['lekcja'] . '\'';
			$grupa = $isf->DbSelect('plan_grupy', array('*'), $g_cond);
			?>
			<?php if (count($grupa) != 0): ?>
			    <?php foreach ($grupa as $rowid => $values): ?>
		    	    <p class="grplek">
				    <?php echo $values['przedmiot']; ?>
		    		<span class="grptxt">
		    		    <span class="grptxt">
					<a href="<?php echo URL::site('podglad/klasa/' . $values['klasa']); ?>">
						<?php echo $values['klasa']; ?></a>-<?php echo $values['grupa'].'/'.$ilosc_grup; ?>
		    			<a href="<?php echo URL::site('podglad/sala/' . $values['sala']); ?>">
						<?php echo $values['sala']; ?></a>
		    		    </span>
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