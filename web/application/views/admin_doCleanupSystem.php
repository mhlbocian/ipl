<?php
/*
 * Resetowanie systemu Plan Lekcji
 */
$isf = new Kohana_Isf();
$isf->Connect(APP_DBSYS);
$res = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'edycja_danych\'');
$res = $res[0]['wartosc'];
?>
<table border="0">
    <thead>
        <tr>
            <?php if ($res != 1): ?>
                <td style="min-width: 50%; width: 50%; max-width: 50%">
                    <h3>Usunięcie planów zajęć</h3>
                </td>
            <?php endif; ?>
            <td style="min-width: 50%; width: 50%; max-width: 50%">
                <h3>Czyszczenie danych systemu</h3>
            </td>
        </tr>
    </thead>
    <tr>
        <?php if ($res != 1): ?>
            <td valign="top">
                <div class="ui-state-highlight ui-corner-all" style="margin: 5px; padding: 0pt 0.7em;">
                    <p>
                        <span class="ui-icon ui-icon-info" style="float: left; margin-right: 0.3em;"></span>
                        Ta opcja powoduje usunięcie wszystkich planów zajęć oraz zastępstw. W przypadku systemu
                        otwartego dla innych, następuje również ponownie powrót do edycji planów zajęć. Sale,
                        przedmioty, klasy i inne ustawienia pozostają nienaruszone.
                    </p>
                </div>
                <form action="<?php echo url::site('admin/doTimetablesCleanup'); ?>" method="post">
                    <button type="submit" name="btnSubmit" class="button-jq ui-state-default ui-button" style="margin: 5px;">
                        Usuń tylko dane planów zajęć i zastępstw
                    </button>
                </form>
            </td>
        <?php endif; ?>
        <td valign="top">
            <div class="ui-state-highlight ui-corner-all" style="margin: 5px; padding: 0pt 0.7em;">
                <p>
                    <span class="ui-icon ui-icon-info" style="float: left; margin-right: 0.3em;"></span>
                    Ta opcja powoduje czyszczenie planów zajęć i zastępstw oraz powrót do możlwiości edycji
                    takich danych jak sale, przedmioty, nauczyciele itp.
                    Gdy opcja <b>wyczyść cały system</b>, nie jest zaznaczona, wówczas usunięciu ulegną tylko
                    plany zajęć i zastępstwa i nastąpi powrót do trybu edycji danych systemu, osiągalny tylko z menu
                    administratora systemu.
                </p>
            </div>
            <form action="<?php echo url::site('admin/doCleanupSystemPOST'); ?>" method="post">
                <div class="ui-state-error ui-corner-all" style="margin: 5px; padding: 0pt 0.7em;">
                    <p>
                    <p><b>Wyczyść cały system </b><input type="checkbox" name="cl"/></p>
                    <span class="ui-icon ui-icon-alert" style="float: left; margin-right: 0.3em;"></span>
                    Operacja jest nieodwracalna
                    </p>
                </div>
                <button type="submit" name="btnSubmit" class="button-jq ui-state-default ui-button" style="margin: 5px;">
                    Wyczyść dane systemu
                </button>
            </form>
        </td>
    </tr>
</table>