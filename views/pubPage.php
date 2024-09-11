<?php
require '../admin/inc/head.php';
require_once '../admin/config/config.php';

try {

    $pdo = new PDO(DSN, DB_USER, DB_PWD, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    if (isset($_GET['selectPub']) && !empty($_GET['selectPub'])) {
        $taverneId = $_GET['selectPub'];
    }

    $q = "SELECT t_id, t_nom, v_nom, t_blonde, t_brune, t_rousse
        FROM taverne
        JOIN ville ON t_ville_fk = v_id
        WHERE t_id = :selectPub";

    $request = $pdo->prepare($q);
    $request->bindValue('selectPub', $taverneId);
    $request->execute();
    $pubs = $request->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die($e->getMessage());
}

?>

<div class="container py-5">
    <div class="card d-flex mx-auto" style="width: 24rem;">
        <div class="card-body d-flex text-center flex-column ">
            <h5 class="card-title">Name : <?= $pubs['t_nom'] ?></h5>
            <p class="card-text">City : <?= $pubs['v_nom'] ?></p>
            <p class="card-text <?= $pubs['t_blonde'] == 1 ? '' : 'd-none' ?>">Blonde : <?= $pubs['t_blonde'] == 1 ? 'DISPONIBLE' : '' ?></p>
            <p class="card-text <?= $pubs['t_brune'] == 1 ? '' : 'd-none' ?>">Brune : <?= $pubs['t_brune'] == 1 ? 'DISPONIBLE' : '' ?></p>
            <p class="card-text <?= $pubs['t_rousse'] == 1 ? '' : 'd-none' ?>">Rousse : <?= $pubs['t_rousse'] == 1 ? 'DISPONIBLE' : '' ?></p>
        </div>
        <a href="../homePage.php" class="link d'flex text-center">Retour a la page d'accueil</a>
    </div>
</div>

<?php
include '../admin/inc/foot.php';
?>