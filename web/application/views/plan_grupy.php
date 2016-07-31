<?php
/**
 * Strona ze skryptem AJAX, ktory pobiera wlasciwa
 * strone edycji planu (klasaajax)
 */
$k = $klasa;
$isf = new Kohana_Isf();
$isf->JQUi_AjaxdivDoAjax('progress', URL::site('plan/grupaajax/' . $klasa), true);
?>
<h1>
    <a href="<?php echo URL::site('default/index'); ?>">
        <img src="<?php echo URL::base() ?>lib/icons/back.png" alt="powrót"/></a>&emsp;
    Edycja planu dla <?php echo $klasa; ?> (grupowy)&emsp;
    <a href="#" onClick="document.forms['formPlan'].submit();">
        <img src="<?php echo URL::base() ?>lib/icons/save.png" alt="zapisz"/></a>
</h1>
<?php
$alternative = '<b>Przeglądarka nie obsługuje JavaScript?
                Spróbuj <a href="' . URL::site('plan/grupaajax/' . $klasa . '/true') . '">metodę alternatywną</a></b>';
$customload = 'Trwa przypisywanie sal, przedmiotów i nauczycieli...';
echo $isf->JQUi_AjaxdivCreate('progress', true, false, $alternative, $customload);
?>