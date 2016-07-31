<p/>
<table border="0" width="100%">
    <tbody>
        <tr>
            <td>
		<a href="<?php echo URL::site('sale/index'); ?>">Sale</a>
            </td>
        </tr>
        <tr>
            <td>
		<a href="<?php echo URL::site('przedmioty/index'); ?>">Przedmioty</a>
            </td>
        </tr>
        <tr>
            <td>
		<a href="<?php echo URL::site('nauczyciele/index'); ?>">Nauczyciele</a>
            </td>
        </tr>
        <tr>
            <td>
		<a href="<?php echo URL::site('klasy/index'); ?>">Klasy</a>
            </td>
        </tr>
        <tr>
            <td>
		<?php if (defined('ldap_enable') && ldap_enable == "true"): ?>
    		Moduł użytkowników w trybie LDAP jest nieaktywny
		<?php else: ?>
    		<a href="<?php echo URL::site('admin/users'); ?>">Użytkownicy</a>
		<?php endif; ?>
            </td>
        </tr>
        <tr>
            <td>
		<a href="<?php echo URL::site('godziny/index'); ?>">Godziny lekcyjne i przerwy</a>
            </td>
        </tr>
        <tr>
            <td>
		<a href="<?php echo URL::site('regedit'); ?>">Podgląd rejestru</a>
            </td>
        </tr>
	<?php
	/*
	  <tr>
	  <td>
	  <a href="<?php echo URL::site('admin/logs'); ?>">Podgląd dzienników</a>
	  </td>
	  </tr>
	 */
	?>
        <tr>
            <td>
		<b><a href="<?php echo URL::site('admin/doEditTimetables'); ?>">Plan Zajęć</a></b>
            </td>
        </tr>
    </tbody>
</table>
<p class="grptxt">System aktualnie umożliwia edycję takich danych, jak sale, przedmioty,
    nauczyciele i inne. Dopóki nie zamkniesz trybu edycji systemu, edycja planów zajęć nie będzie dostępna.
    Późniejszy powrót do tej strony jest możliwy poprzez opcję <b>Wyczyść system</b>.</p>