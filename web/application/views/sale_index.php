<?php if (count($res) == 0): ?>
    <table style="width: 100%;">
        <thead>
    	<tr class="a_odd">
    	    <td colspan="3" style="text-align: center;">
    		Brak zdefiniowanych sal lekcyjnych
    	    </td>
    	</tr>
    	<tr class="a_even">
    	    <td colspan="3" style="text-align: center;">
    		<form action="<?php echo URL::site('sale/commit'); ?>" method="post" name="form1">
    		    Sala: <input type="text" name="inpSala"/>&nbsp;
    		    <button type="submit" name="btnAdd">Dodaj salę</button>
    		</form>
    	    </td>
    	</tr>
        </thead>
    </table>
<?php else: ?>
    <form action="<?php echo URL::site('sale/commit'); ?>" method="post" name="formSala" id="formSala">
        <table style="width: 100%;">
    	<thead>
    	    <tr class="a_odd">
    		<td colspan="3" style="text-align: center;">
    		    Sala: <input type="text" name="inpSala"/>&nbsp;
    		    <button type="submit" name="btnAdd">Dodaj salę</button>
    		</td>
    	    </tr>
    	    <tr class="a_even">
    		<td style="width: 100px;">Sala</td>
    		<td>Przedmioty</td>
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
		    <tr <?php echo ($i % 2 == 1) ? 'class="a_even"' : ''; ?>>
			<td>
			    <input type="radio"
				   name="rdClassroom"
				   value="<?php echo $rowcol['sala']; ?>"
				   class="inpNoneBorder"/>
				   <?php echo $rowcol['sala']; ?>
			</td>
			<td>
			    <?php
			    $rs = Isf2::Connect()->Select('przedmiot_sale', array('przedmiot'))
					    ->Where(array('sala' => $rowcol['sala']))
					    ->Execute()->fetchAll();
			    ?>
			    <?php foreach ($rs as $rid => $rcl): ?>
				<?php echo $rcl['przedmiot']; ?>,&nbsp;
			    <?php endforeach; ?>
			</td>
			<td>
			    <input type="checkbox" name="sDel[<?php echo $i; ?>]" value="<?php echo $rowcol['sala']; ?>"
				   class="inpNoneBorder"/>
			</td>
		    </tr>
		    <?php $i++; ?>
		<?php endforeach; ?>
    	    <tr>
    		<td><button type="submit" name="btnEditClasses">Edytuj salę</button></td>
    		<td colspan="2" style="text-align: right;">
    		    <button type="submit" name="btnDelClasses">Usuń zaznaczone sale</button>
    		</td>
    	    </tr>
    	</tbody>
        </table>
    </form>
<?php endif; ?>
<?php switch ($_err): ?>
<?php case 'e1': ?>
<p class="error">Wybrana sala już istnieje</p>
<?php break; ?>
<?php case 'e2': ?>
<p class="error">Ciąg zawiera niedozwolone znaki</p>
<?php break; ?>
<?php case 'e3': ?>
<p class="error">Ciąg nie może być pusty</p>
<?php break; ?>
<?php case 'nchk': ?>
<p class="error">Nie wybrano żadnej sali do usunięcia</p>
<?php break; ?>
<?php case 'nchkc': ?>
<p class="error">Nie wybrano żadnej sali</p>
<?php break; ?>
<?php case 'pass': ?>
<p class="notice">Operacja wykonana poprawnie</p>
<?php break; ?>
<?php case 'usun': ?>
<p class="notice">Operacja wykonana poprawnie</p>
<?php break; ?>
<?php endswitch; ?>
<script type="text/javascript">
    function checkAll(n){
	for(var i=0; i<=n;i++){
	    document.formSala.elements['sDel['+i+']'].checked = document.formSala.chAll.checked.valueOf();
	}
    }
</script>