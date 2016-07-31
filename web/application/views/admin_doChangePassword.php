<?php
/*
 * Zmiana hasła administratora
 */
?>
<h1>Zmiana hasła administratora</h1>
<form action="<?php echo URL::site('admin/doChangePasswordPOST'); ?>" method="post" name="form">
    <p class="info">
        Wszystkie pola muszą być wypełnione oraz hasło musi mieć min. 6 znaków
    </p>
    <?php
    switch ($_tplerr) {
        case 'false':
            ?>
            <p class="error">
                Podane hasła się nie zgadzają, bądź nie mają odpowiedniej długości
                6 znaków.
            </p>
            <?php
            break;
        case 'pass':
            ?>
            <p class="notice">
                Hasło użytkownika zostało zmienione pomyślnie
            </p>
            <?php
            break;
        default:
            break;
    }
    ?>
    <table border="0">
        <tr>
            <td>Stare hasło</td>
            <td><input type="password" name="inpSH"/></td>
        </tr>
        <tr>
            <td>Nowe hasło</td>
            <td><input type="password" name="inpNH"/></td>
        </tr>
        <tr>
            <td>Powtórz hasło</td>
            <td><input type="password" name="inpPH"/></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: right">
                <button type="submit" name="btnSubmit">Zmień hasło</button>
            </td>
        </tr>
    </table>
</form>