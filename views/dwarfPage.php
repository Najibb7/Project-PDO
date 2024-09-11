<?php
// session_start();
$title = 'Dwarf Page';
require '../admin/inc/head.php';
require_once '../admin/config/config.php';

try {

    $pdo = new PDO(DSN, DB_USER, DB_PWD, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    if (isset($_GET['selectNain']) && !empty($_GET['selectNain'])) {
        $nainId = $_GET['selectNain'];
    }

    $q = 'SELECT n_id, n_nom, n_barbe, n_groupe_fk, v_nom, t_nom, g_debuttravail, g_fintravail,t_villedepart_fk,t_villearrivee_fk 
    FROM nain
    JOIN ville ON n_ville_fk = v_id
    LEFT JOIN groupe ON n_groupe_fk = g_id
    LEFT JOIN taverne ON g_taverne_fk = t_id
    LEFT JOIN tunnel ON v_id = t_villedepart_fk
    WHERE n_id = :selectNain
    ';

    $request = $pdo->prepare($q);
    $request->bindValue('selectNain', $nainId);
    $request->execute();
    $nain = $request->fetch(PDO::FETCH_ASSOC);

    $pdo = new PDO(DSN, DB_USER, DB_PWD, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $q = "SELECT g_id
    FROM groupe
    ORDER BY g_id";
    $stmt = $pdo->query($q);
    $crews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $_GET['selectGroupe'] ?? '';

    $qUpdate = 'UPDATE nain SET n_groupe_fk = :up_groupe WHERE n_id = :idNain';
    $reqUpdate = $pdo->prepare($qUpdate);
    $reqUpdate->bindValue('up_groupe', $_POST['selectGroup']);
    $reqUpdate->bindValue('idNain', $nainId);
    $reqUpdate->execute();
} catch (PDOException $e) {
    die($e->getMessage());
}
?>

<div class="container py-5">
    <div class="card d-flex mx-auto" style="width: 24rem;">
        <div class="card-body d-flex text-center flex-column ">
            <h5 class="card-title">Name : <?= $nain['n_nom'] ?></h5>
            <p class="card-text">City : <?= $nain['v_nom'] ?></p>
            <p class="card-text">Longueur de barbe : <?= $nain['n_barbe'] ?></p>
            <p class="card-text <?= $nain['t_nom'] == NULL ? 'd-none' : '' ?>">
                Taverne : <?= $nain['t_nom'] ?>
            </p>

            <p class="card-text ">
                <?= $nain['n_groupe_fk'] == NULL ? 'Aucun groupe' : ("Membre du groupe nº" . $nain['n_groupe_fk']) ?>
            </p>

            <p class="card-text <?= $nain['n_groupe_fk'] == NULL ? 'd-none' : '' ?>">
                Travaille de <?= $nain['g_debuttravail'] ?> à <?= $nain['g_fintravail'] ?> de <?= $nain['t_villedepart_fk'] ?> à <?= $nain['t_villearrivee_fk'] ?>
            </p>

            <!-- Button trigger modal -->
            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#updateGroupe">
                Changer de groupe
            </button>

            <!-- Modal -->
            <div class="modal fade" id="updateGroupe" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="post">
                                <select name="selectGroup" id="selectGroup" class="form-select" aria-label="Default select example">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                </select>
                                <button type="submit" class="btn btn-danger my-2">Valider</button>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <a href="../homePage.php" class="link d'flex text-center">Retour a la page d'accueil</a>
    </div>
</div>


<!-- <div class="container py-5">
    <div class="card d-flex mx-auto" style="width: 24rem;">
        <div class="card-body d-flex mx-auto flex-column ">
            <h5 class="card-title"></h5>
            <p class="card-text"></p>
            <p class="card-text"></p>
            <p class="card-text "></p>
            <p class="card-text "></p>
            <p class="card-text "></p>
        </div>
    </div>
</div> -->


<?php
require '../admin/inc/foot.php';
?>