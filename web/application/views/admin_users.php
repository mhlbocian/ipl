<?php
$isf = Isf2::Connect();
$res = $isf->Select('uzytkownicy')
		->Where(array('login::!=' => 'root', 'haslo::!=' => 'ldap_login'))
		->Execute()->fetchAll();
?>
<table style="width: 100%;">
    <thead class="a_odd">
        <tr>
            <td>ID</td>
            <td>login (dostępne tokeny)</td>
            <td>Stan konta</td>
            <td>Zalogowany</td>
            <td>Akcja</td>
        </tr>
    </thead>
    <tr>
        <td colspan="5" style="text-align: center;">
            <a href="<?php echo URL::site('admin/adduser'); ?>" id="btnCUser" style="margin: 5px;">
                Dodaj użytkownika
            </a>
        </td>
    </tr>
    <?php $i = 0; ?>
    <?php foreach ($res as $rowid => $rowcol): ?>
	<?php $i++; ?>
	<?php if ($i % 2 != 0): ?>
	    <tr class="a_even">
	    <?php else: ?>
	    <tr>
	    <?php endif; ?>
    	<td><?php echo $rowcol['uid']; ?></td>
    	<td>
		<?php echo $rowcol['login']; ?>
    	    (
		<?php
		echo count($isf->Select('tokeny', array('*'))->Where(array(
				    'login' => $rowcol['login']
				))
				->Execute()->fetchAll());
		?>
    	    )
    	</td>
	    <?php if ($rowcol['ilosc_prob'] >= 3): ?>
		<td><p class="error">█ zablokowany</p></td>
	    <?php else: ?>
		<td><p class="notice">█ aktywny</p></td>
	    <?php endif; ?>
	    <?php if ($rowcol['webapi_timestamp'] < time()): ?>
		<td><p class="error">█ niezalogowany</p></td>
	    <?php else: ?>
		<td><p class="notice">█ zalogowany</p></td>
	    <?php endif; ?>
    	<td>
    	    &bull; <a href="#" onClick="deluser(<?php echo $rowcol['uid']; ?>);">usuń</a><br/>
		<?php if ($rowcol['ilosc_prob'] >= 3): ?>
		    &bull; <a href="<?php echo URL::site('admin/userublock/' . $rowcol['uid']); ?>">odblokuj</a><br/>
		<?php endif; ?>
    	    &bull; <a href="<?php echo URL::site('admin/token/' . $rowcol['uid']); ?>" target="_blank">generuj tokeny</a>
    	</td>
        </tr>
    <?php endforeach; ?>
</table>
<script type="text/javascript">
    function deluser(n){
        var answer = confirm("Czy napewno chcesz usunąć użytkownika nr "+n+"?");
        if(answer){
            document.location.href = "<?php echo URL::site('admin/userdel/"+n+"'); ?>";
        }else{
        }
    }
</script>
