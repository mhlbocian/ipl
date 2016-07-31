<?php $isf = new Kohana_Isf(); ?>
<?php $isf->Connect(APP_DBSYS); ?>
<?php if (App_Globals::getSysLv() == 3): // gdy edycja planow zamknieta     ?>
    <h3>Oddziały</h3>
    <p class="a_klasy">
        <?php foreach ($isf->DbSelect('klasy', array('*'), 'order by klasa asc') as $rw => $rc): ?>
            <a href="<?php echo URL::site('podglad/klasa/' . $rc['klasa']); ?>"><?php echo $rc['klasa']; ?></a>&emsp;
        <?php endforeach; ?>
    </p>
    <h3>Sale</h3>
    <p class="a_klasy">
        <?php foreach ($isf->DbSelect('sale', array('*'), 'order by sala asc') as $rw => $rc): ?>
            <a href="<?php echo URL::site('podglad/sala/' . $rc['sala']); ?>"><?php echo $rc['sala']; ?></a>&emsp;
        <?php endforeach; ?>    
    </p>
    <h3>Nauczyciele</h3>
    <p class="a_klasy">
        <?php foreach ($isf->DbSelect('nauczyciele', array('*'), 'order by skrot asc') as $rw => $rc): ?>
            <?php echo $rc['skrot']; ?>-<a href="<?php echo URL::site('podglad/nauczyciel/' . $rc['skrot']); ?>"><?php echo $rc['imie_naz']; ?></a><br/>
        <?php endforeach; ?>    
    </p>
<?php else: ?>
    <p class="info">Podgląd planów będzie dostępny po zamknięciu edycji planów zajęć.</p>
<?php endif; ?>