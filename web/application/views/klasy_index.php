<?php
/*
 * Główna strona klas
 * 
 * 
 */
?>
<?php
$isf = Isf2::Connect();
$res = $isf->Select('klasy', array('klasa'))
                ->OrderBy(array('klasa' => 'asc'))
                ->Execute()->fetchAll();
$grp = App_Globals::getRegistryKey('ilosc_grup');
?>
<table width="100%">
    <thead style="text-align: center;">
        <tr class="a_odd">
            <td colspan="2">Zarządzanie klasami</td>
        </tr>
    </thead>
    <tr>
        <td style="width: 50%; text-align: center; font-weight: bold;">
            <?php if (count($res) != 0): ?>
                Klasy
            <?php else: ?>
                Brak klas w systemie!
            <?php endif; ?>
        </td>
        <td rowspan="<?php echo count($res) + 1; ?>" style="width: 50%; vertical-align: top;">
            <h3>Dodaj klasę</h3>
            <form action="<?php echo URL::site('klasy/dodaj'); ?>" method="post" name="form1">
                <input type="text" name="inpKlasa"/>&nbsp;
                <button type="submit" name="btnSubmit">Dodaj klasę</button>
            </form>
            <hr/>
            <h3>Grupy klasowe (<?php echo $grp; ?>)</h3>
            <form action="<?php echo url::site('klasy/grupyklasowe'); ?>" method="post" name="form">
                <select name="grp">
                    <?php for ($i = 0; $i <= 10; $i++): ?>
                        <?php if ($i == 1): ?>
                        <?php else: ?>
                            <?php if ($i == $grp): ?>
                                <option selected><?php echo $i; ?></option>
                            <?php else: ?>
                                <option><?php echo $i; ?></option>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endfor; ?>
                </select>
                <button type="submit" name="btnSubmit">Ustaw ilość grup</button>
            </form>
        </td>
    </tr>
    <?php if (count($res) != 0): ?>
        <form action="<?php echo URL::site('klasy/usun'); ?>" method="post">
            <?php $i = 0; ?>
            <?php foreach ($res as $rowid => $rowcol): ?>
                <tr <?php echo ($i % 2 == 0) ? 'class="a_even"' : ''; ?>>
                    <td style="text-align: center;">
                        <?php echo $rowcol['klasa']; ?>&emsp;
                        <button type="submit" name="btnClass"
                                value="<?php echo $rowcol['klasa']; ?>"
                                class="btnDelClassImg">
                            Usuń
                        </button>
                    </td>
                </tr>
                <?php $i++; ?>
            <?php endforeach; ?>
        </form>
    <?php endif; ?>
</table>
<?php /** kody bledow */ ?>
<?php if ($_err == 'e1'): ?>
    <p class="error">Klasa już istnieje</p>
<?php endif; ?>
<?php if ($_err == 'e2'): ?>
    <p class="error">Ciąg zawiera niedozwolone znaki</p>
<?php endif; ?>
<?php if ($_err == 'e3'): ?>
    <p class="error">Ciąg nie może być pusty</p>
<?php endif; ?>
<?php if ($_err == 'pass'): ?>
    <p class="notice">Klasa została wpisana</p>
<?php endif; ?>
<?php if ($_err == 'usun'): ?>
    <p class="notice">Klasa została usunięta</p>
<?php endif; ?>
