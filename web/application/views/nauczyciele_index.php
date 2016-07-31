<?php
/*
 * Zarządzanie nauczycielami
 * 
 * 
 */
$isf = Isf2::Connect();

$res = $isf->Select('nauczyciele')->OrderBy(array('skrot' => 'asc'))
		->Execute()->fetchAll();
?>
<?php if (count($res) == 0): ?>
    <table style="width: 100%;">
        <thead>
    	<tr>
    	    <td class="a_odd" style="text-align: center;">
    		Brak nauczycieli w systemie.
    	    </td>
    	</tr>
    	<tr>
    	    <td class="a_even" style="text-align: center;">
    		<form action="<?php echo URL::site('nauczyciele/commit'); ?>" method="post" name="form1">
    		    Imię i nazwisko: <input type="text" name="inpTeacherName"/>&nbsp;
    		    <button type="submit" name="btnAddTeacher">Dodaj nauczyciela</button>
    		</form>
    	    </td>
    	</tr>

        </thead>
    </table>
<?php else: ?>
    <form action="<?php echo URL::site('nauczyciele/commit'); ?>" method="post" name="formTeacher" id="formTeacher">
        <table style="width: 100%;">
    	<thead>
    	    <tr>
    		<td colspan="4" class="a_odd" style="text-align: center;">
    		    Imię i nazwisko: <input type="text" name="inpTeacherName"/>&nbsp;
    		    <button type="submit" name="btnAddTeacher">
    			Dodaj nauczyciela
    		    </button>
    		</td>
    	    </tr>
    	    <tr class="a_even">
    		<td style="width: 200px;">Imię i nazwisko</td>
    		<td>Przedmioty</td>
    		<td>Klasy</td>
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
		    <tr <?php echo ($i % 2 == 0) ? '' : 'class="a_even"'; ?>>
			<td>
			    <input type="radio"
				   name="rdTeacher"
				   value="<?php echo $rowcol['skrot']; ?>"
				   class="inpNoneBorder"/>
			    <?php echo $rowcol['skrot']; ?> - 
			    <?php echo $rowcol['imie_naz']; ?>
			</td>
			<td>
			    <?php
			    $nl_przedm = $isf->Select('nl_przedm')
					    ->Where(array('nauczyciel' => $rowcol['imie_naz']))
					    ->Execute()->fetchAll();
			    ?>
			    <?php foreach ($nl_przedm as $rid => $rcl): ?>
				<?php echo $rcl['przedmiot']; ?>, 
			    <?php endforeach; ?>
			</td>
			<td style="max-width: 250px; width: 100px;">
			    <?php
			    $nl_klasy = $isf->Select('nl_klasy')
					    ->Where(array('nauczyciel' => $rowcol['imie_naz']))
					    ->OrderBy(array('klasa' => 'asc'))
					    ->Execute()->fetchAll();
			    ?>
			    <?php foreach ($nl_klasy as $rid => $rcl): ?>
				<?php echo $rcl['klasa']; ?>, 
			    <?php endforeach; ?>
			</td>
			<td>
			    <input type="checkbox" name="tDel[<?php echo $i; ?>]" value="<?php echo $rowcol['skrot']; ?>"
				   class="inpNoneBorder"/>
			</td>
		    </tr>
		    <?php $i++; ?>
		<?php endforeach; ?>
    	    <tr>
    		<td colspan="2">
    		    <button type="submit" name="btnEdit">Edytuj</button>
    		</td>
    		<td colspan="2" style="text-align: right;">
    		    <button type="submit" name="btnDel">Usuń zaznaczone</button>
    		</td>
    	    </tr>
    	</tbody>
        </table>
    </form>
<?php endif; ?>
<?php switch ($_err): ?>
<?php case 'e1': ?>
<p class="error">Nauczyciel już istnieje</p>
<?php break; ?>
<?php case 'e2': ?>
<p class="error">Ciąg zawiera niedozwolone znaki</p>
<?php break; ?>
<?php case 'nonesel': ?>
<p class="error">Nie zaznaczono żadnego nauczyciela</p>
<?php break; ?>
<?php case 'e3': ?>
<p class="error">Ciąg nie może być pusty</p>
<?php break; ?>
<?php case 'pass': ?>
<p class="notice">Nauczyciel został wpisany</p>
<?php break; ?>
<?php case 'completed': ?>
<p class="notice">Operacja została wykonana poprawnie</p>
<?php break; ?>
<?php endswitch; ?>
<script type="text/javascript">
    function checkAll(n){
	for(var i=0; i<=n;i++){
	    document.formTeacher.elements['tDel['+i+']'].checked = document.formTeacher.chAll.checked.valueOf();
	}
    }
</script>