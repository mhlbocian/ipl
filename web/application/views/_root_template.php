<?php
/*
 * Szablon systemu Internetowy Planu Lekcji
 * 
 * @author Michal Bocian <mhl.bocian@gmail.com>
 * @package views
 */
/**
 * Instrukcje dotyczace zmiennych w szablone, gdy
 * nie sa zdefiniowane, ustawia je jako puste (null)
 */
if (!isset($content))
    $content = null;
if (!isset($_SESSION['token']))
    $_SESSION['token'] = null;
if (!isset($script))
    $script = null;
if (!isset($bodystr))
    $bodystr = null;
$appver = App_Globals::getRegistryKey('app_ver');
if (Core_Tools::is_mobile() && !isset($_COOKIE['_nomobile'])) {
    Kohana_Request::factory()->redirect('mobile/index');
    exit;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Plan lekcji - <?php echo App_Globals::getRegistryKey('nazwa_szkoly'); ?></title>
        <?php echo $script; ?>
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base() ?>lib/css/style.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base() ?>lib/css/themes/<?php echo $_SESSION['app_theme']; ?>.css"/>
        <?php
        $isf = new Kohana_Isf();
        $isf->IE9_faviconset();
        $isf->IE9_WebAPP('Internetowy Plan Lekcji', 'Uruchom IPL', APP_PATH);
        $isf->IE9_apptask('Logowanie', 'index.php/admin/login', URL::base() . 'lib/images/ie_login.ico');
        if (App_Globals::getSysLv() == 3) {
            $isf->IE9_apptask('Zestawienie planów', 'index.php/podglad/zestawienie', URL::base() . 'lib/images/ie_timetable.ico');
            $isf->IE9_apptask('Zastępstwa', 'index.php/zastepstwa/index');
        }
        echo $isf->IE9_make();
        ?>
        <script type="text/javascript" src="<?php echo URL::base(); ?>lib/ipl.js">
        </script>
    </head>
    <body onLoad="initTemplate();" onResize="resizeContent()">
        <!-- kontener -->
        <div id="container">
            <div id="container1">
                <div id="pnlLeft">
                    <div class="app_info" id="app_info">
                        <a href="<?php echo URL::site('default/index'); ?>">
                            <img src="<?php echo URL::base(); ?>lib/icons/home.png" alt=""/></a>
                        Plan Lekcji
                    </div>
                    <div id="sidebar_menu" style="padding-left: 10px;" class="a_light_menu">

                        <?php if (Core_Tools::is_mobile()): ?>
                            <h3><a href="<?php echo URL::site('mobile/index'); ?>">Wersja mobilna</a></h3>
                        <?php endif; ?>
                        <?php echo View::factory()->render('_sidebar_menu'); ?>
                        <hr/>
                        <?php echo View::factory()->render('_snippet_theme'); ?>
                    </div>
                </div>
                <div id="pnlCenter">
                    <?php echo $content; ?>
                </div>

                <?php if ($_SESSION['token'] != null): ?>
                    <button id="showpanel" onclick="togglePanel();" class="btnShowPanel">
                    </button>
                    <div id="pnlRight">
                        <?php echo View::factory()->render('_sidebar_right'); ?>
                    </div>
                <?php endif; ?>
            </div>
            <!-- koniec kontenera II -->
            <div class="divbrk"></div>
            <!-- stopka -->
            <div id="footer">
                <?php echo View::factory()->render('_panel_bottom'); ?>
            </div>
            <!-- koniec stopki -->
        </div>
        <!-- koniec kontenera -->
    </body>
</html>
