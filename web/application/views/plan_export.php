<h1>Eksporter planów zajęć</h1>
<p>Dzięki tej stronie, można wyeksportować plany zajęć (oddziały, sale, nauczyciele) do
postaci plików HTML spakowanych w archiwum ZIP.</p>
<form action="<?php echo URL::site('podglad/export'); ?>" method="post">
    <p>Proszę wybrać motyw: 
	<select name="motyw">
	    <?php
	    foreach (App_Globals::getThemes() as $theme) {
		echo '<option>' . $theme . '</option>';
	    }
	    ?>
	</select>
	<button type="submit" name="btnSubmit">Skompiluj plany zajęć</button>
    </p>
</form>