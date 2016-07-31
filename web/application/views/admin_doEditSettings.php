<?php
/*
 * Zmiana danych systemu Plan Lekcji
 * 
 * 
 */
$isf = new Kohana_Isf();
$isf->Connect(APP_DBSYS);
/** pobiera nazwe szkoly */
$nazwa = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'nazwa_szkoly\'');
/** pobiera tresc strony glownej */
$msg = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'index_text\'');
?>
<table style="width: 100%;">
    <thead class="a_odd">
        <tr>
            <td style="text-align: center;">
                Ustawienia systemowe
            </td>
        </tr>
    </thead>
</table>
<form action="<?php echo URL::site('admin/doEditSettingsPOST'); ?>" method="post" name="formPlan">
    <div class="tableDiv" style="width: 95%;">
        <div class="tableRow">
            <div class="tableCell">
                <label for="inpNazwa">Nazwa szkoły</label>
            </div>
            <div class="tableCell">
                <input
                    type="text"
                    style="width: 100%"
                    name="inpNazwa"
                    value="<?php echo $nazwa[0]['wartosc']; ?>"
                    />
            </div>
        </div>
        <div class="tableRow">
            <div class="tableCell"></div>
            <div class="tableCell">
                zmień zawartość strony głównej
                <a href="javascript:switchDiv('settingsHomePageForm');">
                    pokaż/ukryj
                </a>
            </div>
        </div>
        <div class="tableRow">
            <div class="tableCell">
                <label for="authtype">Autoryzacja użytkowników</label>
            </div>
            <div class="tableCell">
                <?php
                if (!defined('ldap_enable') || ldap_enable != "true") {
                    $auth_value = 'Wbudowany system tokenowy';
                } else {
                    $auth_value = 'LDAP';
                }
                ?>
                <input
                    style="width: 100%;"
                    type="text"
                    value="<?php echo $auth_value; ?>"
                    disabled/>
            </div>
        </div>
        <div class="tableRow">
            <div class="tableCell"></div>
            <div class="tableCell">
                <!--<a href="<?php echo URL::site('admin/authorization'); ?>">-->
                <a href="javascript:alert('not implemented');">
                    zmień ustawienia autoryzacji
                </a>
            </div>
        </div>
        <div class="tableRow">
            <div class="tableCell"></div>
            <div class="tableCell" style="text-align: right;">
                <button type="submit" name="btnSubmit">Zapisz ustawienia</button>
            </div>
        </div>
    </div>
    <p id="settingsHomePageForm" style="display: none;">
        <textarea name="txtMsg" style="width: 95%; height: 300px;">
            <?php echo $msg[0]['wartosc']; ?>
        </textarea>
    </p>
</form>