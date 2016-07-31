<?php
$isf = new Kohana_Isf();
$isf->Connect(APP_DBSYS);
$naucz = $isf->DbSelect('nauczyciele', array('*'), 'order by imie_naz asc');
$c_naucz = count($naucz);
?>
<h1>
    Nowe zastępstwo
</h1>
<?php if ($blad == 'true'): ?>
    <p class="error">Żadne pole z gwiazdką nie może pozostać puste!</p>
<?php endif; ?>
<?php if ($blad == 'day'): ?>
    <p class="error">Nie można wypełnić zastępstwa na sobotę, bądź niedzielę!</p>
<?php endif; ?>
<?php if ($blad == 'data'): ?>
    <p class="error">Nie można wypełnić zastępstwa na miniony dzień!</p>
<?php endif; ?>
<?php if ($blad == 'brak'): ?>
    <p class="error">Wybrany nauczyciel nie prowadzi zajęć w danym dniu!</p>
<?php endif; ?>
<?php if ($c_naucz == 0): ?>
    <p class="notice">
        Brak zdefiniowanych nauczycieli w systemie. System zastępstw nieaktywny
    </p>
<?php else: ?>
    <form action="<?php echo URL::site('zastepstwa/wypeln'); ?>" method="post">
        <p><b>W dniu: </b><input type="text" name="inpDate" id="inpDate"/> *</p>
        <p><b>Za nauczyciela: </b>
            <select name="selNl">
                <?php foreach ($naucz as $rowid => $rowcol): ?>
                    <option><?php echo $rowcol['imie_naz']; ?></option>
                <?php endforeach; ?>
            </select>*
        </p>
        <p>
            <b>Komentarz: </b>
        </p>
        <p><textarea name="inpComment" cols=40 rows=5></textarea></p>
        <button type="submit" name="inpSubmit">Wypełnij zastępstwo</button>
    </form>
<?php endif; ?>