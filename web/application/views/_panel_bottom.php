<div class="app_ver">
    <b>Plan lekcji </b><?php echo App_Globals::getRegistryKey('app_ver'); ?>
    <br/>
    <?php echo App_Globals::getRegistryKey('nazwa_szkoly'); ?>
    <br/>
    <a href="http://sites.google.com/site/internetowyplanlekcji" target="_blank">strona projektu Plan Lekcji</a>
    &emsp;<a href="<?php echo URL::site('default/about'); ?>">informacje o systemie</a>
    <?php if (!isset($_SESSION['token'])): ?>
        &emsp;
        <a href="<?php echo URL::site('admin/login'); ?>">
    	<img src="<?php echo URL::base(); ?>lib/icons/adminlogin.png" alt=""/> Logowanie
        </a>
    <?php endif; ?>
</div>