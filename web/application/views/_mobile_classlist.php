<ul data-role="listview" id="swipeMe">
    <li data-role="list-divider">Klasy</li>
    <?php
    $klasy = Isf2::Connect()->Select('klasy')
		    ->OrderBy(array('klasa' => 'asc'))
		    ->Execute()->fetchAll();
    foreach ($klasy as $id => $colval):
	?>
        <li><a href="<?php echo URL::site('mobile/klasa/' . $colval['klasa']); ?>">
		<?php echo $colval['klasa']; ?></a>
        </li>
    <?php endforeach; ?>
</ul>