<?php
$title = 'Gurdil';
require 'admin/inc/head.php';
require_once 'controller/dwarfController.php';
require_once 'admin/config/config.php';
require_once 'controller/pubController.php';
require_once 'controller/crewController.php';
require_once 'controller/cityController.php';
?>

<div class="container py-5 d-flex">
    <form action="views/dwarfPage.php" method="get">
            <div class="card p-3 me-4">
        <div class="card-body">
            <h5 class="card-title">Choisir un nain :</h5>
            <select class="form-select" aria-label="Default select example" name="selectNain">
                <?php foreach($users as $user ) : ?>
                <option value="<?= $user['n_id'] ?>"><?=$user['n_nom']?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary mt-2 ms-5 px-4">Valider</button>
        </div>
    </div>
    </form>
<form action="views/cityPage.php" method="get">
        <div class="card p-3 me-4">
        <div class="card-body">
            <h5 class="card-title">Choisir un ville :</h5>
            <select class="form-select" aria-label="Default select example" name="selectCity">
            <?php foreach($cities as $city ) : ?>
                <option value="<?= $city['v_id'] ?>"><?=$city['v_nom']?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary mt-2 ms-5 px-4">Valider</button>
        </div>
    </div>
</form>
<form action="views/crewPage.php" method="get">
        <div class="card p-3 me-4">
        <div class="card-body">
            <h5 class="card-title">Choisir un groupe :</h5>
            <select class="form-select" aria-label="Default select example" name="selectCrew">
            <?php foreach($crews as $crew ) : ?>
                <option value="<?= $crew['g_id'] ?>"><?=$crew['g_id']?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary mt-2 ms-5 px-4">Valider</button>
        </div>
    </div>
</form>

<form action="views/pubPage.php" method="get">
        <div class="card p-3 me-4">
        <div class="card-body">
            <h5 class="card-title">Choisir une taverne :</h5>
            <select class="form-select" aria-label="Default select example" name="selectPub">
                <?php foreach($taverns as $tavern ) : ?>
                <option value="<?= $tavern['t_id'] ?>"><?=$tavern['t_nom']?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary mt-2 ms-5 px-4">Valider</button>
        </div>
    </div>
</form>

</div>

<?php
require 'admin/inc/foot.php';
?>