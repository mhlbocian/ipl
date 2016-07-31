<?php
$isf = new Kohana_Isf();
$isf->Connect(APP_DBSYS);
$zast = $isf->DbSelect('zast_id', array('*'), 'order by dzien desc');
$ile = count($zast);
$enpl_days = array(
    'Monday' => 'Poniedziałek',
    'Tuesday' => 'Wtorek',
    'Wednesday' => 'Środa',
    'Thursday' => 'Czwartek',
    'Friday' => 'Piątek',
    'Saturday' => 'Sobota',
    'Sunday' => 'Niedziela',
);
?>
<form name="print" action="<?php echo URL::site('zastepstwa/drukuj'); ?>" method="post">
    <table class="przed" width="100%">
        <thead style="text-align: center;">
            <tr class="a_odd">
                <td colspan="
		<?php if (isset($_SESSION['token'])): ?>
    		    5
		    <?php else: ?>
    		    4
		    <?php endif; ?>">
                    Zarządzanie zastępstwami
                </td>
            <tr class="a_even">
                <td>Data</td>
                <td>Za nauczyciela</td>
                <td>Dodatkowe informacje</td>
                <td></td>
		<?php if (isset($_SESSION['token'])): ?>
    		<td><img src="<?php echo URL::base(); ?>lib/images/printer.png" width="16" height="16"/></td>
		<?php endif; ?>
            </tr>
            <tr>
                <td colspan="
		<?php if (isset($_SESSION['token'])): ?>
    		    5
		    <?php else: ?>
    		    4
		    <?php endif; ?>">
                    <p>
                        <font class="notice">█ nadchodzące</font>&emsp;
                        <font class="hlt">█ dzisiejsze</font>&emsp;
                        <font class="error">█ minione</font>&emsp;
			<?php if (isset($_SESSION['token'])): ?>
    			<a href="#" onClick="document.forms['print'].submit();">
    			    <img src="<?php echo URL::base(); ?>lib/images/printer.png" width="16" height="16"/>
    			    Wydrukuj zaznaczone zastępstwa</a>
			<?php endif; ?>
                    </p>
                </td>
            </tr>
        </thead>
	<?php if ($ile == 0): ?>
    	<tr>
    	    <td colspan="
		<?php if (isset($_SESSION['token'])): ?>
			5
		    <?php else: ?>
			4
		    <?php endif; ?>">
    		<p class="info" style="text-align: center">
    		    Brak zastępstw
    		</p>
    	    </td>
    	</tr>
	<?php else: ?>
	    <?php foreach ($zast as $rowid => $rowcol): ?>
		<tr>
		    <td>
			<?php
			$today = date('Y-m-d');
			if ($rowcol['dzien'] > $today) {
			    echo '<font class="notice">█</font>';
			} else {
			    if ($rowcol['dzien'] == $today) {
				echo '<font class="hlt">█</font>';
			    } else {
				echo '<font class="error">█</font>';
			    }
			}
			echo '<a href="' . URL::site('zastepstwa/przeglad/' . $rowcol['zast_id']) . '">';
			if ($rowcol['dzien'] == $today) {
			    echo '<b> ' . $rowcol['dzien'];
			    $day = date('l', strtotime($rowcol['dzien']));
			    echo ' (' . $enpl_days[$day] . ')</b>';
			} else {
			    echo ' ' . $rowcol['dzien'];
			    $day = date('l', strtotime($rowcol['dzien']));
			    echo ' (' . $enpl_days[$day] . ')';
			}
			echo '</a>';
			?>

		    </td>
		    <td><?php echo $rowcol['za_nl']; ?></td>
		    <td><?php echo $rowcol['info']; ?></td>
		    <td>
			<a href="<?php echo URL::site('zastepstwa/przeglad/' . $rowcol['zast_id']); ?>">podgląd</a>&emsp;
			<?php if (isset($_SESSION['token'])): ?>
	    		<a href="#" onClick="confirmation(<?php echo $rowcol['zast_id']; ?>)">usuń</a>
			<?php endif; ?>
		    </td>
		    <?php if (isset($_SESSION['token'])): ?>
	    	    <td>
			    <?php if ($rowcol['dzien'] >= $today): ?>
				<input type="checkbox" name="print[<?php echo $rowcol['zast_id']; ?>]" value="on" />
			    <?php endif; ?>
	    	    </td>
		    <?php endif; ?>
		</tr>
	    <?php endforeach; ?>
	<?php endif; ?>
    </table>
</form>
<script type="text/javascript">
    function confirmation(n){
        var answer = confirm("Czy chcesz usunąć zastępstwo nr "+n);
        if(answer){
            window.location = "<?php echo URL::site('zastepstwa/usun'); ?>/"+n;
        }else{
        }
    }
</script>