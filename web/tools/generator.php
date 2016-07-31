<?php require_once '../config.php'; ?>
<?php require_once '../modules/isf/classes/kohana/isf.php'; ?>
<?php $isf = new Kohana_Isf(); ?>
<?php $isf->Connect(APP_DBSYS); ?>
<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8"/>
        <title>Generator planów zajęć</title>
	<link rel="stylesheet" type="text/css" href="../lib/css/style.css"/>
    </head>
    <body>
	<div id="wrapper">
	    <h1>Generator planów zajęć</h1>
	    <p>
		Witamy w generatorze planów zajęć. Aktualna wersja jest tylko prototypem mającym na
		celu ustalenie końcowej funkcjonalności generatora. W końcowym etapie jest powrót do
		tej strony generatora.
	    </p>
	    <?php
	    $reg = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'edycja_danych\'');
	    $reg = $reg[0]['wartosc'];
	    ?>
	    <?php if ($reg != 0): ?>
		<?php //die('<p>Musisz być w trybie edycji planów zajęć!</p></body></html>'); ?>
	    <?php endif; ?>
	    <?php if (!isset($_POST['step']) || $_POST['step'] == '0'): ?>
		<?php $_POST['step'] = '0'; ?>
    	    <form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
    		<button type="submit" name="step" value="1">Rozpocznij pracę z generatorem</button>
    	    </form>
	    <?php endif; ?>
	    <?php if ($_POST['step'] == '1'): ?>
    	    <form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
    		<button type="submit" name="step" value="0">Wstecz</button>
    	    </form>
    	    <form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
    		<h3>Krok 1 - wybierz klasę
			<?php $klasy = $isf->DbSelect('klasy', array('*'), 'order by klasa asc'); ?>
			<?php if (count($klasy) == '0'): ?>
			    <?php echo '</h3>'; ?>
			    <p class="error">Brak klas w systemie</p>
			<?php else: ?>
			    <select name="klasa">
				<?php foreach ($klasy as $rowid => $rowcol): ?>
	    			<option><?php echo $rowcol['klasa']; ?></option>
				<?php endforeach; ?>
			    </select>
			</h3>
			<button type="submit" name="step" value="2">Dalej</button>
		    </form>
		<?php endif; ?>
	    <?php endif; ?>
	    <?php if ($_POST['step'] == '2'): ?>
    	    <form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
    		<button type="submit" name="step" value="1">Wstecz</button>
    	    </form>
    	    <h3>Krok 2 - wybierz przedmioty dla planu klasy <?php echo $_POST['klasa']; ?></h3>
		<?php
		$przedm = $isf->DbSelect('przedmioty', array('*'), 'order by przedmiot asc');
		$grp = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'ilosc_grup\'');
		$grp = $grp[0]['wartosc'];
		?>
		<?php if (count($przedm) == 0): ?>
		    <p class="error">Brak przedmiotów w systemie</p>
		<?php else: ?>
		    <form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
			<input type="hidden" name="klasa" value="<?php echo $_POST['klasa']; ?>"/>
			<table class="przed">
			    <thead style="background-color: lightsteelblue;">
				<tr>
				    <td>Przedmiot</td>
				    <td>Uwzględnij w generowaniu</td>
				    <?php if ($grp != '0'): ?>
	    			    <td>Zajęcia w grupie</td>
				    <?php endif; ?>
				    <td>Liczba godzin tygodniowo</td>
				</tr>
			    </thead>
			    <?php foreach ($przedm as $rowid => $rowcol): ?>
	    		    <tr>
	    			<td><?php echo $rowcol['przedmiot']; ?></td>
	    			<td>
	    			    <input type="checkbox" name="cb[<?php echo $rowcol['przedmiot']; ?>]"/>
	    			</td>
				    <?php if ($grp != '0'): ?>
					<td>
					    <input type="checkbox" name="grp[<?php echo $rowcol['przedmiot']; ?>]"/>
					</td>
				    <?php endif; ?>
	    			<td>
	    			    <select name="gd[<?php echo $rowcol['przedmiot']; ?>]">
	    				<option>1</option>
	    				<option>2</option>
	    				<option>3</option>
	    				<option>4</option>
	    				<option>5</option>
	    				<option>6</option>
	    				<option>7</option>
	    				<option>8</option>
	    				<option>9</option>
	    				<option>10</option>
	    			    </select>
	    			</td>
	    		    </tr>
			    <?php endforeach; ?>
			</table>
			<p/>
			<button type="submit" name="step" value="3">Dalej</button>
		    </form>
		<?php endif; ?>
	    <?php endif; ?>
	    <?php if ($_POST['step'] == '3'): ?>
    	    <form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
    		<input type="hidden" name="klasa" value="<?php echo $_POST['klasa']; ?>"/>
    		<button type="submit" name="step" value="2">Wstecz</button>
    	    </form>
    	    <h3>Krok 3 - wybierz nauczycieli</h3>
		<?php
		$ilgr = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'ilosc_grup\'');
		$ilgr = $ilgr[0]['wartosc'];
		$igl = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'ilosc_godzin_lek\'');
		$igl = $igl[0]['wartosc'] * 5;
		$gd = 0;
		foreach ($_POST['gd'] as $prz => $ilosc) {
		    if (isset($_POST['cb'][$prz])) {
			$gd += $ilosc;
		    }
		}
		?>
		<?php if (count($_POST['cb']) == 0): ?>
		    <p class="error">Nie zdefiniowałeś przedmiotów!</p>
		<?php else: ?>
		    <p class="info">
			--- oznacza brak przypisanego nauczyciela i powoduje, że
			generator nie przypisuje do danego przedmiotu żadnego nauczyciela
			oraz sali.
		    </p>
		    <form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
			<input type="hidden" name="klasa" value="<?php echo $_POST['klasa']; ?>"/>
			<table class="przed">
			    <thead style="background-color: lightsteelblue;">
				<tr>
				    <td>Przedmiot</td>
				    <td>Nauczyciel</td>
				</tr>
			    </thead>
			    <?php foreach ($_POST['cb'] as $prz => $war): ?>
	    		    <tr>
	    			<td>
	    			    <input type="hidden" name="gd[<?php echo $prz; ?>]" value="<?php echo $_POST['gd'][$prz]; ?>"/>
					<?php echo $prz; ?>
	    			</td>
	    			<td>
					<?php
					$przed = $isf->DbSelect('nl_przedm', array('*'), 'where przedmiot=\'' . $prz . '\' order by nauczyciel asc');
					$przsal = count($isf->DbSelect('przedmiot_sale', array('*'), 'where przedmiot=\'$prz\''));
					?>
					<?php if (!isset($_POST['grp'][$prz])): ?>
					    <select name="nl[<?php echo $prz; ?>]" style="width: 90%;">
						<option>---</option>
						<?php if (count($przed) != 0 || $przsal != 0): ?>
						    <?php foreach ($przed as $rowid => $rowcol): ?>
							<option><?php echo $rowcol['nauczyciel']; ?></option>
						    <?php endforeach; ?>
						<?php endif; ?>
					    </select>
					<?php else: ?>
					    <?php for ($i = 1; $i <= $ilgr; $i++): ?>
		    			    gr <?php echo $i; ?>
		    			    <select name="nl[<?php echo $prz; ?>][<?php echo $i; ?>]" style="width: 90%;">
		    				<option>---</option>
						    <?php if (count($przed) != 0 || $przsal != 0): ?>
							<?php foreach ($przed as $rowid => $rowcol): ?>
			    				<option><?php echo $rowcol['nauczyciel']; ?></option>
							<?php endforeach; ?>
						    <?php endif; ?>
		    			    </select>
					    <?php endfor; ?>
					<?php endif; ?>
	    			</td>
	    		    </tr>
			    <?php endforeach; ?>
			</table>
			<p/>
			<button type="submit" name="step" value="4">Dalej</button>
		    </form>
		<?php endif; ?>
    	    <p><b>Podsumowanie</b></p>
    	    <ul>
    		<li>Maksymalna ilość godzin w planie: <?php echo $igl; ?></li>
    		<li>Ilość godzin zdefiniowanych w planie: <?php echo $gd; ?></li>
		    <?php if ($igl >= $gd): ?>
			<li><p class="notice">Nie przekroczono maksymalnej liczby godzin</p></li>
		    <?php else: ?>
			<?php die('<li><p class="error">Przekroczono liczbę godzin</p></li></ul></html>'); ?>
		    <?php endif; ?>
    	    </ul>
	    <?php endif; ?>
	    <?php if ($_POST['step'] == '4'): ?>
		<?php
		$xml = new XMLWriter();
		$xml->openMemory();
		$xml->startDocument('1.0', 'UTF-8');
		$xml->setIndent(4);
		$xml->startElement('enviroment');
		$xml->writeElement('klasa', $_POST['klasa']);
		$xml->startElement('przedmioty');
		foreach ($_POST['gd'] as $prz => $ilg) {
		    $xml->startElement('item');
		    $xml->startAttribute('przedmiot');
		    $xml->text($prz);
		    $xml->endAttribute();
		    $xml->startAttribute('godziny');
		    $xml->text($ilg);
		    $xml->endAttribute();

		    if ($_POST['nl'][$prz] != '---' && !is_array($_POST['nl'][$prz])) {
			$xml->startAttribute('nauczyciel');
			$xml->text(htmlspecialchars($_POST['nl'][$prz], ENT_QUOTES));
			$xml->endAttribute();
		    }
		    $xml->endElement();
		}
		$group = false;

		foreach ($_POST['nl'] as $prz => $nl) {
		    if (is_array($nl))
			$group = true;
		}
		if ($group == true) {
		    $xml->startElement('grupy');
		    foreach ($_POST['nl'] as $prz => $nl) {
			if (is_array($nl)) {
			    $xml->startElement('item');
			    $xml->startAttribute('przedmiot');
			    $xml->text(htmlspecialchars($prz, ENT_QUOTES));
			    $xml->endAttribute();
			    $xml->startAttribute('godziny');
			    $xml->text(htmlspecialchars($_POST['gd'][$prz], ENT_QUOTES));
			    $xml->endAttribute();
			    foreach ($nl as $id => $nl) {
				$xml->startElement('nauczyciel');
				$xml->startAttribute('grupa');
				$xml->text($id);
				$xml->endAttribute();
				$xml->startAttribute('name');
				$xml->text(htmlspecialchars($nl, ENT_QUOTES));
				$xml->endAttribute();
				$xml->endElement();
			    }

			    $xml->endElement();
			}
		    }
		    $xml->endElement();
		}
		$xml->endElement();
		$xml->endElement();
		$xml->endDocument();
		$fh = fopen('tools/config_' . $_POST['klasa'] . '.xml', 'w');
		fwrite($fh, $xml->outputMemory(true));
		fclose($fh);
		?>
    	    <h3 class="notice">Operacja zakończona powodzeniem!</h3>
    	    Pobierz<b>
    		<a href="tools/downloader.php?file=config_<?php echo $_POST['klasa']; ?>.xml&redirect">tutaj</a></b>
    	    plik konfiguracyjny klasy dla generatora.
	    <?php endif; ?>
	</div>
    </body>
</html>