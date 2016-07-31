<?php
$isf = new Kohana_Isf();
$isf->Connect(APP_DBSYS);
?>
<table style="width:100%;">
    <thead style="text-align: center;">
        <tr class="a_odd">
            <td colspan="2">
                Zarządzanie rejestrem systemowym
            </td>
        </tr>
        <tr class="a_even">
            <td>Klucz</td>
            <td>Obecne ustawienie</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($isf->DbSelect('rejestr', array('*')) as $rowid => $rowcol): ?>
            <tr>
                <td>
                    <b><?php echo $rowcol['opcja']; ?></b>
                </td>
                <td>
                    <?php echo htmlspecialchars($rowcol['wartosc']); ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<br/>