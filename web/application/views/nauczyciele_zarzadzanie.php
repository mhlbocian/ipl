<?php
/*
 * Zarządzanie nauczycielami
 * 
 * 
 */
$isf = new Kohana_Isf();
$isf->Connect(APP_DBSYS);
$db = Isf2::Connect();
?>
<h1><?php echo $nauczyciel; ?></h1>
<form action="<?php echo URL::site('nauczyciele/commit') ?>" method="post">
    <fieldset>
	<legend>Nauczane klasy</legend>

	<input type="hidden" name="skrot" value="<?php echo $skrot; ?>"/>

	<?php
	$assigned_classes = $db->Select('nl_klasy')
			->Where(array('nauczyciel' => $nauczyciel))
			->OrderBy(array('klasa' => 'asc'))
			->Execute()->fetchAll();
	?>
	<?php if (count($assigned_classes) == 0): ?>
    	<p class="error">Brak nauczanych klas</p>
	<?php else: ?>
    	<ul style="list-style: none;">
		<?php foreach ($assigned_classes as $id => $colval): ?>
		    <li>
			<input type="radio"
			       name="rdClass"
			       value="<?php echo $colval['klasa']; ?>"
			       class="inpNoneBorder"/>
			       <?php echo $colval['klasa']; ?>
		    </li>
		<?php endforeach; ?>
    	</ul>
    	<p style="text-align: right;">
    	    <button type="submit" name="btnDelClass">Usuń zaznaczone</button>
    	</p>
	<?php endif; ?>

	<?php
	$klasy = $db->Select('klasy', array('klasa'))->Except($db->Query()
				->Select('nl_klasy', array('klasa'))
				->Where(array('nauczyciel' => $nauczyciel))
				->OrderBy(array('klasa' => 'asc'))
		)->Execute()->fetchAll();
	?>
	<?php if (count($klasy) == 0): ?>
    	<p class="error">Brak dostępnych klas</p>
	<?php else: ?>
    	<p>
    	    <label for="selKlasy">Klasa</label>
    	    <select name="selKlasy">
		    <?php foreach ($klasy as $sid => $scol): ?>
			<option><?php echo $scol['klasa']; ?></option>
		    <?php endforeach; ?>
    	    </select>
    	</p>
    	<p style="text-align: right;">
    	    <button type="submit" name="btnAddClass">Dodaj klasę</button>
    	</p>
	<?php endif; ?>
    </fieldset>

    <fieldset>
	<legend>Nauczane przedmioty</legend>
	<?php
	$nlp = $db->Select('nl_przedm', array('przedmiot'))
			->Where(array('nauczyciel' => $nauczyciel))
			->Execute()->fetchAll();
	?>
	<?php if (count($nlp) == 0): ?>
    	<p class="error">Brak nauczanych przedmiotów</p>
	<?php else: ?>
    	<ul style="list-style: none;">
		<?php foreach ($nlp as $r => $c): ?>
		    <li>
			<input type="radio"
			       name="rdSubject"
			       value="<?php echo $c['przedmiot']; ?>"
			       class="inpNoneBorder"/>
			       <?php echo $c['przedmiot']; ?>
		    </li>
		<?php endforeach; ?>
    	</ul>
    	<p style="text-align: right;">
    	    <button type="submit" name="btnDelSubject">Usuń zaznaczone</button>
    	</p>
	<?php endif; ?>
	<?php
	$przedm = $db->Select('przedmioty', array('przedmiot'))->Except($db->Query()
				->Select('nl_przedm', array('przedmiot'))
				->Where(array('nauczyciel' => $nauczyciel))
				->OrderBy(array('przedmiot' => 'asc'))
		)->Execute()->fetchAll();
	?>
	<?php if (count($przedm) == 0): ?>
    	<p class="error">Brak dostępnych przedmiotów</p>
	<?php else: ?>
    	<p>
    	    <label for="selPrzedm">Przedmiot</label>
    	    <select name="selPrzedm">
		    <?php foreach ($przedm as $sid => $scol): ?>
			<option><?php echo $scol['przedmiot']; ?></option>
		    <?php endforeach; ?>
    	    </select>
    	</p>
    	<p style="text-align: right;">
    	    <button type="submit" name="btnAddSubject">Dodaj przedmiot</button>
    	</p>
	<?php endif; ?>
    </fieldset>
</form>
<a href="<?php echo URL::site('nauczyciele/index'); ?>">Powrót</a>