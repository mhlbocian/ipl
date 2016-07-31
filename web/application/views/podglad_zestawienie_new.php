<?php
$Teachers = new Core_Teacher_Managment();
$Classes = new Core_Classes_Managment();
?>
<!DOCTYPE html>
<html lang="pl" dir="ltr">
    <head>
        <meta charset="UTF-8"/>
        <title>Zestawienie planów - <?php echo App_Globals::getRegistryKey('nazwa_szkoly'); ?></title>
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base() ?>lib/css/style.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base() ?>lib/css/themes/{{theme}}.css"/>
    </head>
    <body style="background-color: #FFFFFF;">
        <table class="przed">
            <!-- tu sa naglowki -->
            <thead class="a_odd centerize" style="text-align: center;">
                <tr>
                    <td colspan=2 rowspan=2>Godziny</td>
                    <td colspan=<?php echo count($Classes->getClasses()) * 3; ?>>Klasy</td>
                    <?php if (count($Teachers->getTeachers()) > 0) : ?>
                        <td colspan=<?php echo count($Teachers->getTeachers()) * 2; ?>>Nauczyciele</td>
                    <?php endif; ?>
                </tr>
                <tr>
                    <!-- pobiera klasy -->
                    <?php foreach ($Classes->getClasses() as $id => $col): ?>
                        <td colspan="3"><?php echo "{$col["klasa"]}"; ?></td>
                    <?php endforeach; ?>
                    <!-- pobiera nauczycieli -->
                    <?php foreach ($Teachers->getTeachers() as $id => $col): ?>
                        <td colspan="2"><?php echo "{$col["skrot"]}"; ?></td>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tr class="centerize">
                <td></td>
                <td>Godzina</td>
                <!-- pobiera klasy -->
                <?php foreach ($Classes->getClasses() as $id => $col): ?>
                    <td>P</td>
                    <td>S</td>
                    <td>N</td>
                <?php endforeach; ?>
                <!-- pobiera nauczycieli -->
                <?php foreach ($Teachers->getTeachers() as $id => $col): ?>
                    <td>K</td>
                    <td>S</td>
                <?php endforeach; ?>
            </tr>
            <!-- konkret -->
            <?php
            $dni = array('Poniedziałek', 'Wtorek', 'Środa', 'Czwartek', 'Piątek');
            $colspan = 2 + count($Classes->getClasses()) * 3 + count($Teachers->getTeachers()) * 2;
            $godziny = Isf2::Connect()->Select('lek_godziny')
                            ->Execute()->fetchAll();
            foreach ($dni as $dzien) {
                echo "<tr class=\"a_even centerize\"><td colspan={$colspan}>{$dzien}</td></tr>";
                foreach ($godziny as $id => $col) {
                    echo "<tr><td>{$col["lekcja"]}</td><td>{$col["godzina"]}</td>";

                    echo "</tr>";
                }
            }
            ?>
        </table>
    </body>
</html>