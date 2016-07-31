<?php
$res = App_Globals::getRegistryKey('ilosc_godzin_lek');
$dlugosc = App_Globals::getRegistryKey('dlugosc_lekcji');
$grz = App_Globals::getRegistryKey('godz_rozp_zaj');
?>
<table border="0" style="width: 100%;">
    <thead style="text-align: center;">
        <tr class="a_odd" colspan="2">
            <td colspan="2">Godziny systemowe</td>
        </tr>
    </thead>
    <tr valign="top">
        <td style="width: 50%;">
	    <fieldset>
		<legend>Ilość oraz długość godzin lekcyjnych</legend>
		<form action="<?php echo URL::site('godziny/ustaw'); ?>" method="post">
		    <p>
			Ilość godzin: <select name="iloscgodzin">
			    <?php for ($i = 1; $i <= 15; $i++): ?>
				<?php if ($res == $i): ?>
				    <option selected><?php echo $i; ?></option>
				<?php else: ?>
				    <option><?php echo $i; ?></option>
				<?php endif; ?>
			    <?php endfor; ?>
			</select><br/>
			Długość godziny lekcyjnej:
			<select name="dlugosclekcji">
			    <?php for ($i = 5; $i <= 60; $i = $i + 5): ?>
    			    <option value="<?php echo $i; ?>"
				<?php echo ($dlugosc == $i) ? 'selected' : ''; ?>
    				    >
				    <?php echo $i; ?> min
    			    </option>
			    <?php endfor; ?>
			</select>
		    </p>
		    <p style="text-align: right;">
			<button type="submit" name="btnSubmit">
			    Zastosuj
			</button>
		    </p>
		</form>
	    </fieldset>
        </td>
	<td style="width: 50%;">
	    <fieldset>
		<legend>Czas godzin lekcyjnych</legend>
		<form action="<?php echo URL::site('godziny/lekcje'); ?>" method="post">
		    <p>
			Rozpoczęcie zajęć
			<?php
			$t_time = explode(':', $grz);
			?>
			<select name="czasRZH">
			    <?php for ($h = 0; $h < 24; $h++): ?>
				<?php
				$value = ((strlen($h) == 1) ? '0' : '') . $h;
				?>
    			    <option <?php echo ($t_time[0] == $value) ? 'selected' : ''; ?>>
				    <?php echo $value; ?>
    			    </option>
			    <?php endfor; ?>
			</select>
			<select name="czasRZM">
			    <?php for ($h = 0; $h < 60; $h+=5): ?>
				<?php
				$value = ((strlen($h) == 1) ? '0' : '') . $h;
				?>
    			    <option <?php echo ($t_time[1] == $value) ? 'selected' : ''; ?>>
				    <?php echo $value; ?>
    			    </option>
			    <?php endfor; ?>
			</select>
		    </p>
		    <table style="width:100%;">
			<thead>
			    <tr class="a_even">
				<td>

				</td>
				<td>
				    Przerwa
				</td>
				<td>
				    Ustawiony czas lekcji
				</td>
			    </tr>
			</thead>
			<tbody>
			    <?php for ($i = 1; $i <= $res; $i++): ?>
    			    <tr <?php echo ($i % 2 == 0) ? 'class="a_even"' : ''; ?>>
    				<td>
					<?php echo $i; ?>
    				</td>
    				<td>
					<?php
					$godz = Isf2::Connect()->Select('lek_godziny')
							->Where(array('lekcja' => $i))
							->Execute()->fetchAll();

					if (count($godz) == 0) {
					    $godz[0]['dl_prz'] = '00:00';
					}
					?>
    				    <select name="lekcja[<?php echo $i; ?>]">
					    <?php for ($p = 0; $p <= 35; $p+=5): ?>
						<?php
						$value = '00:' . ((strlen($p) == 1) ? '0' : '') . $p;
						?>
						<option
						    value="<?php echo $value; ?>"
						    <?php echo (($godz[0]['dl_prz'] == $value) ? 'selected' : '');
						    ?>
						    >
						    <?php echo $p; ?> min
						</option>
					    <?php endfor; ?>
    				    </select>
    				</td>
    				<td>
					<?php
					if (!isset($godz[0]['godzina'])):
					    echo '';
					else:
					    echo $godz[0]['godzina'];
					endif;
					?>
    				</td>
    			    </tr>
			    <?php endfor; ?>
			</tbody>
			<tfoot>
			    <tr>
				<td colspan="3" style="text-align: center">
				    <p style="text-align: right">
					<button type="submit" name="btnSubmitLek">
					    Zastosuj
					</button>
				    </p>
				</td>
			    </tr>
			</tfoot>
		    </table>
		</form>
	    </fieldset>
        </td>
    </tr>
</table>