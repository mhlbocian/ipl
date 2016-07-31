<?php
/*
 * Zamknięcie edycji planu
 */
?>
<h1>Zamknięcie edycji planów zajęć</h1>

<p>Zamknięcie edycji planów spowoduje zapisanie wszystkich planów zajęć
    w bazie IPL oraz XML oraz udostępni możliwość wprowadzania zastępstw oraz
    przęglądu planów zajęć.</p>
<p>
<form action="<?php echo URL::site('admin/doSaveTimetablesPOST'); ?>" method="post">
    <button type="submit" name="btnS">
        Wykonaj operację
    </button>
</form>
</p>