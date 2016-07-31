<?php if (defined('ldap_enable') && ldap_enable == "true"): ?>
    <p>
        Moduł użytkowników w trybie LDAP jest niedostępny
    </p>
    <hr/>
<?php else: ?>
    <p>
        <img src="<?php echo URL::base(); ?>lib/icons/user.png" alt=""/>
        <a href="<?php echo URL::site('admin/users'); ?>" >Użytkownicy</a>
    </p>
<?php endif; ?>
<p>
    <img src="<?php echo URL::base(); ?>lib/icons/registry.png" alt=""/>
    <a href="<?php echo URL::site('regedit'); ?>" >Podgląd rejestru</a>
</p>
<?php /*
  <p>
  <img src="<?php echo URL::base(); ?>lib/icons/file.png" alt=""/>
  <a href="<?php echo URL::site('admin/logs'); ?>" >Podgląd dzienników</a>
  </p>
 */
?>
<p><hr/></p>