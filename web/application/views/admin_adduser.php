<?php
$isf = Isf2::Connect();
$uid = $isf->Select('uzytkownicy', array('*'))
	->OrderBy(array('uid' => 'desc'));
$uid = $uid[0]['uid'] + 1;
?>
<form action="<?php echo URL::site('admin/douseradd'); ?>" method="post">
    <input type="hidden" name="inpUid" value="<?php echo $uid; ?>"/>
    <table style="width: 100%;">
        <thead>
            <tr>
                <td colspan="4" class="a_odd" style="text-align: center;">
                    Dodawanie użytkownika
                </td>
            </tr>
            <tr class="a_even">
                <td style="width: 50%;">Login</td>
                <td style="width: 50%;">Hasło</td>
            </tr>
        </thead>
        <tr>
            <td>
                <input type="text" name="inpLogin" style="width: 100%; font-size: 14pt;"/>
            </td>
            <td>
                <input type="text" name="inpHaslo" style="width: 100%; font-size: 14pt;"/>
            </td>
        </tr>
        <tr>
            <td colspan="4" class="a_even" style="text-align: center;">
                <button type="submit" name="btnSubmit">Dodaj użytkownika</button>
            </td>
        </tr>
    </table>
</form>
<?php if ($err == 'data'): ?>
    <p class="error">Login zawiera niedozwolone znaki</p>
<?php endif; ?>
<?php if ($err == 'leng'): ?>
    <p class="error">Login musi zawierać min. 5 znaków, a hasło min. 6</p>
<?php endif; ?>