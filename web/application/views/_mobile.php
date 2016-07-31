<?php
define('MOBILE_LIB_PATH', URL::base().'lib/jquery/mobile/');
?>
<!DOCTYPE html>
<html>
    <head>
	<meta name="viewport" content="width=device-width, initial-scale=1"/>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" href="<?php echo MOBILE_LIB_PATH; ?>jquery.mobile-1.0.1.min.css" />
	<script src="<?php echo MOBILE_LIB_PATH; ?>jquery-1.6.4.min.js"></script>
	<script src="<?php echo MOBILE_LIB_PATH; ?>jquery.mobile-1.0.1.min.js"></script>        
        <title>Internetowy Plan Lekcji - <?php echo App_Globals::getRegistryKey('nazwa_szkoly'); ?></title>
	<script>
            $(document).ready(function() {
            });
        </script>
    </head>
    <body>
	<div data-role="page" data-theme="c">
	    <div data-role="header">
		<a href="<?php echo URL::site('mobile/index'); ?>"
		   data-icon="home"
		   data-iconpos="notext">Home</a>
		   <?php if (!isset($title)): ?>
    		<h1><?php echo App_Globals::getRegistryKey('nazwa_szkoly'); ?></h1>
		<?php else: ?>
    		<h1><?php echo $title; ?></h1>
		<?php endif; ?>
	    </div>
	    <div data-role="content">
		<?php if (App_Globals::getRegistryKey('edycja_danych') != 3): ?>
    		<p><b>System jest akutalnie niedostępny</b></p>
		<?php else: ?>
		    <?php echo $content; ?>
		<?php endif; ?>
	    </div>
	    <div data-role="navbar">
		<ul>
		    <li>
			<a href="<?php echo URL::site('default/index/nomobile'); ?>" target="_blank">Wersja standardowa</a>
		    </li>
		</ul>
	    </div>
	    <div data-role="footer">
		<h4>Internetowy Plan Lekcji</h4>
	    </div>
	</div>
    </body>
</html>
