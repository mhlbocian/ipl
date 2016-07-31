<?php
/*
 * Zarządzanie przedmiotami
 * 
 * 
 */
$isf = Isf2::Connect();
?>
<?php if (count($res) == 0): ?>
    <table style="width: 100%;">
        <thead>
    	<tr>
    	    <td class="a_odd" style="text-align: center;">
    		Brak zdefiniowanych przedmiotów
    	    </td>
    	</tr>
    	<tr>
    	    <td class="a_even" style="text-align: center;">
    		<form action="<?php echo URL::site('przedmioty/commit'); ?>" method="post" name="form1">
    		    Przedmiot: <input type="text" name="inpPrzedmiot" autofocus/>&nbsp;
    		    <button type="submit" name="btnAddSubject">Dodaj przedmiot</button>
    		</form>
    	    </td>
    	</tr>
        </thead>
    </table>
<?php else: ?>
    <form action="<?php echo URL::site('przedmioty/commit'); ?>" method="post" name="formPrzedm" id="formPrzedm">
        <table style="width: 100%;">
    	<thead>
    	    <tr>
    		<td colspan="4" class="a_odd" style="text-align: center;">
    		    Przedmiot: <input type="text" name="inpPrzedmiot"/>&nbsp;
    		    <button type="submit" name="btnAddSubject">Dodaj przedmiot</button>
    		</td>
    	    </tr>
    	    <tr class="a_even">
    		<td style="width: 100px;">Przedmiot</td>
    		<td style="width: 150px; max-width: 200px;">Przypisane sale</td>
    		<td>Nauczyciele uczący</td>
    		<td style="width: 20px;">
    		    <input type="checkbox" name="chAll" id="chAll"
    			   onclick="checkAll(<?php echo count($res) - 1; ?>);"
    			   class="inpNoneBorder"/>
    		</td>
    	    </tr>
    	</thead>
    	<tbody>
		<?php $i = 0; ?>
		<?php foreach ($res as $rowid => $rowcol): ?>
		    <tr valign="top" <?php echo ($i % 2 == 1) ? 'class="a_even"' : ''; ?>>
			<td>
			    <input type="radio"
				   name="rdSubject"
				   value="<?php echo $rowcol['przedmiot']; ?>"
				   class="inpNoneBorder"/>
				   <?php echo $rowcol['przedmiot']; ?>
			</td>
			<td> 
			    <?php
			    $przedmiot_sale = $isf->Select('przedmiot_sale', array('sala'))
				    ->Where(array('przedmiot'=>$rowcol['przedmiot']))
				    ->OrderBy(array('sala'=>'asc'))
				    ->Execute()->fetchAll();
			    ?>
			    <?php foreach ($przedmiot_sale as $rid => $rcl): ?>
				<?php echo $rcl['sala']; ?>&nbsp;
			    <?php endforeach; ?>
			</td>
			<td>
			    <?php
			    $nl_przedm = $isf->Select('nl_przedm', array('nauczyciel'))
				    ->Where(array('przedmiot'=>$rowcol['przedmiot']))
				    ->OrderBy(array('nauczyciel'=>'asc'))
				    ->Execute()->fetchAll();
			    ?>
			    <?php foreach ($nl_przedm as $rid => $rcl): ?>
	    		    &bull; <?php echo $rcl['nauczyciel']; ?><br/>
			    <?php endforeach; ?>
			</td>
			<td>
			    <input type="checkbox" name="sDel[<?php echo $i; ?>]" value="<?php echo $rowcol['przedmiot']; ?>"
				   class="inpNoneBorder"/>
			</td>
		    </tr>
		    <?php $i++; ?>
		<?php endforeach; ?>
    	    <tr>
    		<td colspan="2">
		    <button type="submit" name="btnEditSubject">Edytuj przedmiot</button>
		</td>
    		<td colspan="2" style="text-align: right;">
    		    <button type="submit" name="btnDelSubjects">Usuń zaznaczone przedmioty</button>
    		</td>
    	    </tr>
    	</tbody>
        </table>
    </form>
<?php endif; ?>
<?php switch ($_err): ?>
<?php case 'e1': ?>
<p class="error">Wybrany przedmiot już istnieje</p>
<?php break; ?>
<?php case 'e2': ?>
<p class="error">Ciąg zawiera niedozwolone znaki</p>
<?php break; ?>
<?php case 'e3': ?>
<p class="error">Ciąg nie może być pusty</p>
<?php break; ?>
<?php case 'pass': ?>
<p class="notice">Przedmiot został utworzony</p>
<?php break; ?>
<?php case 'usun': ?>
<p class="notice">Przedmiot został usunięty</p>
<?php break; ?>
<?php case 'nse': ?>
<p class="error">Nie zaznaczono przedmiotu do edycji</p>
<?php break; ?>
<?php case 'nsd': ?>
<p class="error">Nie zaznaczono przedmiotów do usunięcia</p>
<?php break; ?>
<?php endswitch; ?>
<script type="text/javascript">
    function checkAll(n){
	for(var i=0; i<=n;i++){
	    document.formPrzedm.elements['sDel['+i+']'].checked = document.formPrzedm.chAll.checked.valueOf();
	}
    }
</script>
