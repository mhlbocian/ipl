<?php if (App_Globals::getSysLv() == 1): ?>
    <p class="grptxt">
        Bardzo nam przykro, ale system jest aktualnie niedostępny.
    </p>
<?php else: ?>
    <?php if (App_Globals::getSysLv() == 3): //gdy system jest calkowicie otwarty bez edycji sal, czy planow ?>
	<p class="grptxt">
	    <img src="<?php echo URL::base(); ?>lib/icons/zastepstwa.png" alt=""/>
	    <a href="<?php echo URL::site('zastepstwa/index'); ?>">
		Zastępstwa
	    </a>
	</p>
	<p class="grptxt">
	    <img src="<?php echo URL::base(); ?>lib/icons/zestawienie.png" alt=""/>
	    <a href="<?php echo URL::site('podglad/zestawienie'); ?>" target="_blank">
		Zestawienie planów
	    </a>
	</p>
    <hr/>
	<?php echo View::factory('_menu_plany')->render(); ?>
    <?php else: ?>
	<p class="grptxt">
	    Bardzo nam przykro, ale system jest aktualnie niedostępny.
	</p>
    <?php endif; ?>
<?php endif; ?>