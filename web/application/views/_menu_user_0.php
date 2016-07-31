<?php $isf = new Kohana_Isf(); ?>
<?php $isf->Connect(APP_DBSYS); ?>
<table border="0" width="100%">
    <thead class="a_even" style="text-align: center;">
        <tr>
            <td colspan="2">
                Edycja planów
            </td>
        </tr>
        <tr>
            <td colspan="2">
                [ <a href="<?php echo URL::site('admin/doSaveTimetables'); ?>">Zamknij edycję planów</a> ]
            </td>
        </tr>
    </thead>
    <?php foreach ($isf->DbSelect('klasy', array('klasa'), 'order by klasa asc') as $r => $c): ?>
        <tr valign="center">
            <td class="a_even" style="text-align: center;"><b><?php echo $c['klasa']; ?></b></td>
            <td>
                <a href="<?php echo URL::site('plan/klasa/' . $c['klasa']); ?>">Plan wspólny</a><br/>
                <?php
                $grp = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'ilosc_grup\'');
                ?>
                <?php if ($grp[0]['wartosc'] > 0): ?>
                    <a href="<?php echo URL::site('plan/grupy/' . $c['klasa']); ?>">Plan grupowy</a><br/>
                <?php endif; ?>
		    <b><a href="<?php echo URL::site('podglad/klasa/' . $c['klasa']); ?>">Podgląd planu</a></b>
            </td>
        </tr>
    <?php endforeach; ?>
</table>