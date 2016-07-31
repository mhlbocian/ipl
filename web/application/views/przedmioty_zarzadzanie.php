<?php
/*
 * Zarządzanie przedmiotami
 * 
 */
$isf = Isf2::Connect();
$prs = $isf->Select('nl_przedm')
		->Where(array('przedmiot' => $przedmiot))
		->OrderBy(array('przedmiot' => 'asc'))
		->Execute()->fetchAll();
$prz = $isf->Select('nauczyciele', array('imie_naz'))
		->Except($isf->Query()->Select('nl_przedm', array('nauczyciel'))->
			Where(array('przedmiot' => $przedmiot)))
		->Execute()->fetchAll();

$sale = $isf->Select('przedmiot_sale', array('sala'))
		->Where(array('przedmiot' => $przedmiot))
		->OrderBy(array('cast(sala as numeric)' => 'asc'))
		->Execute()->fetchAll();
$sale_dod = $isf->Select('sale', array('sala'))
		->Except($isf->Query()->Select('przedmiot_sale', array('sala'))
			->Where(array('przedmiot' => $przedmiot)))
		->OrderBy(array('sala' => 'asc'))
		->Execute()->fetchAll();
?>
<h1><?php echo $przedmiot; ?></h1>

<fieldset>
    <legend>Nauczyciele uczący</legend>
    <?php if (count($prs) == 0): ?>
        <p class="error">Brak nauczycieli uczących tego przedmiotu</p>
    <?php else: ?>
        <form action="<?php echo url::site('przedmioty/commit'); ?>" method="post">
    	<input type="hidden" name="przedmiot" value="<?php echo $przedmiot; ?>"/>
    	<ul style="list-style: none;">
		<?php foreach ($prs as $r => $c): ?>
		    <li>
			<input type="radio"
			       name="rdNauczyciel"
			       value="<?php echo $c['nauczyciel']; ?>"
			       class="inpNoneBorder"/>
			       <?php echo $c['nauczyciel']; ?>
		    </li>
		<?php endforeach; ?>
    	</ul>
    	<p style="text-align: right;">
    	    <button type="submit" name="btnWypiszNl">Wypisz wybranego nauczyciela</button>
    	</p>
        </form>
    <?php endif; ?>
</fieldset>
<fieldset>
    <legend>Przypisanie nauczyciela</legend>
    <form action="<?php echo url::site('przedmioty/commit'); ?>" method="post">
	<input type="hidden" name="przedmiot" value="<?php echo $przedmiot; ?>"/>
	<?php if (count($prz) > 0): ?>
    	<select name="selNaucz">
		<?php foreach ($prz as $rid => $rcl): ?>
		    <option><?php echo $rcl['imie_naz']; ?></option>
		<?php endforeach; ?>
    	</select>
    	<p style="text-align: right;">
    	    <button type="submit" name="btnPrzypiszNl">Przypisz nauczyciela</button>
    	</p>
	<?php else: ?>
    	<p class="error">
    	    Brak dostępnych nauczycieli
    	</p>
	<?php endif; ?>
    </form>
</fieldset>
<fieldset>
    <legend>Przypisane sale</legend>
    <?php if (count($sale) == 0): ?>
        <p><b>Brak sal skojarzonych z tym przedmiotem</b></p>
    <?php else: ?>
        <p><b>Sale skojarzone z tym przedmiotem:</b></p>
        <ul>
	    <?php foreach ($res as $rowid => $rowcol): ?>
		<li><b><?php echo $rowcol['sala']; ?></b>
		    <a href="<?php echo URL::site('przedmioty/przypisusun/' . $przedmiot . '/' . $rowcol['sala']) ?>">[ wypisz ]</a></li>
	    <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <?php if (count($sale_dod) == 0): ?>
        <p><b>Brak do przypisania.</b></p>
    <?php else: ?>
        <form action="<?php echo URL::site('przedmioty/commit') ?>" method="post">
    	<input type="hidden" name="formPrzedmiot" value="<?php echo $przedmiot; ?>"/>
    	<select name="selSale">
		<?php foreach ($sale_dod as $sid => $scol): ?>
		    <option><?php echo $scol['sala']; ?></option>
		<?php endforeach; ?>
    	</select>
    	<p style="text-align: right;">
    	    <button type="submit" name="btnAddClassroom">Przypisz salę</button>
    	</p>
        </form>
    </fieldset>
<?php endif; ?>
<p>
    <a href="<?php echo URL::site('przedmioty/index'); ?>">powrót</a>
</p>