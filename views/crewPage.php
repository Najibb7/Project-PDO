<?php
require '../admin/inc/head.php';
require_once '../admin/config/config.php';

try {

    $pdo = new PDO(DSN, DB_USER, DB_PWD, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    if (isset($_GET['selectCrew']) && !empty($_GET['selectCrew'])) {
        $crewId = $_GET['selectCrew'];
    }

    $q = "SELECT g_id, n_nom, t_nom, g_debuttravail, g_fintravail, t_villedepart_fk AS villedepart, t_villearrivee_fk AS villearrivee, t_progres
        FROM groupe
        LEFT JOIN nain ON g_id = n_groupe_fk
        LEFT JOIN taverne ON g_taverne_fk = taverne.t_id
        LEFT JOIN tunnel ON g_tunnel_fk = tunnel.t_id;
        WHERE g_id = :selectCrew";

    $request = $pdo->prepare($q);
    $request->bindValue('selectCrew', $crewId);
    $request->execute();
    $crew = $request->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die($e->getMessage());
}

?>

<div class="container py-5">
    <div class="card d-flex mx-auto" style="width: 24rem;">
        <div class="card-body d-flex text-center flex-column ">
            <h5 class="card-title">Groupe : <?= $crewId ?></h5>
            <p class="card-text">liste nain :
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#listNain">
                    List dwarf
                </button>

                <!-- Modal -->
            <div class="modal fade" id="listNain" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">List dwarf</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <?php foreach ($crew as $value) : ?>
                                <?php if ($value['g_id'] == $crewId) : ?>
                                    <p><?=$value['n_nom']?></p>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            </p>
            <?php foreach ($crew as $value) : ?>
                <?php if ($value['g_id'] == $crewId) : ?>
                    <p class="card-text">
                        <?= ($value['t_nom'] == NULL) ? 'Aucune taverne' : ("Boivent Chez : " . $value['t_nom']) ?></p>
                    <p class="card-text">Creusent de <?= $value['g_debuttravail'] ?> a <?= $value['g_fintravail'] ?> le tunnel de <?= $value['villedepart'] ?> a <?= $value['villearrivee'] ?> <?= $value['t_progres'] == 100 ? '(Entretient)' :  ( "(" . $value['t_progres'] . "%)"); break; ?></p>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <a href="../homePage.php" class="link d'flex text-center">Retour a la page d'accueil</a>
    </div>
</div>

<?php
include '../admin/inc/foot.php';
?>