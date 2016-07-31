<?php
/**
 * Strona ze skryptem AJAX, ktory pobiera wlasciwa
 * strone edycji planu (klasaajax)
 */
?>
<h1>
    <a href="<?php echo URL::site('default/index'); ?>">
	<img src="<?php echo URL::base() ?>lib/icons/back.png" alt="powrót"/></a>&emsp;
    Edycja planu dla <?php echo $klasa; ?>&emsp;
    <a href="#" onClick="confirmation();">
	<img src="<?php echo URL::base() ?>lib/icons/save.png" alt="zapisz"/></a>
</h1>
<?php
$isf = new Kohana_Isf();
$alternative = '<b>Przeglądarka nie obsługuje JavaScript?
                Spróbuj <a href="' . URL::site('plan/klasaajax/' . $klasa . '/true') . '">metodę alternatywną</a></b>';
$customload = ' Trwa przypisywanie sal, przedmiotów i nauczycieli...';
echo $isf->JQUi_AjaxdivCreate('progress', true, false, $alternative, $customload);
?>