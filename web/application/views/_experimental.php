<?php
$MPZ = new MPZ();
$t_dni = array(
    'Poniedziałek',
    'Wtorek',
    'Środa',
    'Czwartek',
    'Piątek',
);
$t_lekcje = App_Globals::getRegistryKey('ilosc_godzin_lek');
$klasa = '2D';
?>
<!DOCTYPE html>
<html lang="pl">
    <head>
	<style>
	    @import url('<?php echo URL::base(); ?>lib/css/style.css');
	    @import url('<?php echo URL::base(); ?>lib/css/themes/domyslny.css');
	</style>
	<meta charset="UTF-8"/>
	<title>Moduł Planu Zajęć - klasa <?php echo $klasa; ?></title>
    </head>
    <body class="t_page">
	<?php
	try {
	    Isf2::Connect()->CreateTable('aaa', array('test'=>'text'))->Execute();
	    echo 'gut';
	} catch (Exception $e) {
	    echo Core_Tools::ShowError($e->getMessage(), $e->getCode());
	}
	?>
	<?php
	/*
	  <div class="tableDiv" style="display: none;">
	  <div class="tableRow a_odd">
	  <div class="tableCell"></div>
	  <div class="tableCell">Godzina</div>
	  <?php foreach ($t_dni as $dzien): ?>
	  <div class="tableCell t_day"><?php echo $dzien; ?></div>
	  <?php endforeach; ?>
	  </div>
	  <?php for ($l = 1; $l <= $t_lekcje; $l++): ?>
	  <div class="tableRow <?php echo ($l % 2 == 0) ? 'a_even' : ''; ?>">
	  <div class="tableCell"><?php echo $l; ?></div>
	  <div class="tableCell"><?php echo $MPZ->getLessonHour($l); ?></div>
	  <?php foreach ($t_dni as $dzien): ?>
	  <div class="tableCell t_day">
	  <?php $lekcja = $MPZ->getLesson($klasa, $dzien, $l); ?>
	  <?php if (!isset($lekcja['t_single']) && count($lekcja > 0)): ?>
	  <span class="grptxt">[zajęcia w grupach]</span>
	  <?php endif; ?>
	  </div>
	  <?php endforeach; ?>
	  </div>
	  <?php endfor; ?>
	  </div> */
	?>
    </body>
</html>