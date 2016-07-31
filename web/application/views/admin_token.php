<!doctype html>
<html>
    <head>
        <meta charset="UTF-8"/>
        <?php
        $db = new Kohana_Isf();
        $db->Connect(APP_DBSYS);
        $r = App_Globals::getRegistryKey('nazwa_szkoly');
        insert_log('admin.token', 'Uzytkownik ' . $_SESSION['user'] . ' generuje tokeny dla uzytkownika ' . $id);
        ?>
        <title>System RAND_TOKEN</title>
        <style>
            body{
                font: 80% sans-serif;
                margin: 5px;
                max-width: 24cm;
            }
            table.przed {
                border-width: 0px;
                border-spacing: 0px;
                border-style: solid;
                border-color: green;
                border-collapse: collapse;
            }
            table.przed th {
                border-width: 1px;
                padding: 3px;
                border-style: solid;
                border-color: black;
            }
            table.przed td {
                border-width: 1px;
                padding: 3px;
                border-style: solid;
                border-color: black;
            }
        </style>
    </head>
    <body>
        <h1>
            <a href="#" onClick="window.print();"><img border="0" src="<?php echo URL::base() ?>lib/images/printer.png" alt="[drukuj]"/></a>
            Lista tokenów jednorazowych
        </h1>
        <h3>
            <a href="#" onClick="window.close();">Zamknij okno</a>&emsp;<?php echo $r; ?>
        </h3>
        <table class="przed" style="max-width: 80%">
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <tr valign="top">
                    <?php
                    $randev = rand(0, 1000);
                    $sum = sha1(time() + $randev);
                    $s2 = md5(rand(1000, 10000));
                    $ret = substr($sum, 0, 3) . substr($s2, 0, 3);
                    $chk = $db->DbSelect('tokeny', array('*'), 'where token=\'' . $ret . '\'');
                    $l = $db->DbSelect('uzytkownicy', array('*'), 'where uid=\'' . $id . '\'');
                    if (count($l) == 0) {
                        echo 'Uzytkownik nie istnieje!';
                        exit;
                    }
                    $db->DbInsert('tokeny', array('login' => $l[0]['login'], 'token' => md5('plan' . $ret)));
                    ?>
                    <td style="width:10cm">
                        <p>Użytkownik <b><?php echo $l[0]['login']; ?></b></p>
                        <h1><?php echo $ret; ?></h1>
                    </td>
                    <?php
                    rand1:
                    $randev = rand(0, 1000);
                    $sum = sha1(time() + $randev);
                    $s2 = md5(rand(1000, 10000));
                    $ret = substr($sum, 0, 3) . substr($s2, 0, 3);
                    $l = $db->DbSelect('uzytkownicy', array('*'), 'where uid=\'' . $id . '\'');
                    if (count($l) == 0) {
                        echo 'Uzytkownik nie istnieje!';
                        exit;
                    }
                    $db->DbInsert('tokeny', array('login' => $l[0]['login'], 'token' => md5('plan' . $ret)));
                    ?>
                    <td style="width:10cm">
                        <p>Użytkownik <b><?php echo $l[0]['login']; ?></b></p>
                        <h1><?php echo $ret; ?></h1>
                    </td>
                </tr>
            <?php endfor; ?>
        </table>
        <p>Wygenerowano aplikacją <b>Internetowy Plan Lekcji</b></p>
    </body>
</html>
