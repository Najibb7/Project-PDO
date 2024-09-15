<?php
require '../admin/inc/head.php';
require_once '../admin/config/config.php';
require_once '../functions/functions.php';

try {

    $pdo = new PDO(DSN, DB_USER, DB_PWD, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    if (isset($_GET['selectCrew']) && !empty($_GET['selectCrew'])) {
        $crewId = $_GET['selectCrew'];
    }

    $tavernesLibres = makeSelect(
        'SELECT t_id, t_nom, t_ville_fk, (t_chambres - COUNT(n_id)) AS chambresLibres
    FROM taverne 
    LEFT JOIN groupe ON t_id = g_taverne_fk
    LEFT JOIN nain  ON g_id = n_groupe_fk
    GROUP BY t_id
    HAVING chambresLibres >= (SELECT COUNT(n_id) FROM nain WHERE n_groupe_fk = :crewId)',
        ['crewId' => $crewId]
    );
} catch (PDOException $e) {
    die($e->getMessage());
}

//! UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['debut']) && isset($_POST['fin']) && isset($_POST['tunnel']) && isset($_POST['taverne'])) {

        $place = false;
        if ($_POST['taverne'] === '') {
            $place = true;
        } else {

            foreach ($tavernesLibres as $taverne) {

                if ($_POST['taverne'] == $taverne['t_id']) {
                    $place = true;
                    break;
                }
            }
        }


        if ($place) {

            $result = makeRequest(
                'UPDATE groupe 
                            SET g_debuttravail = :debut, g_fintravail = :fin, g_taverne_fk = :taverne, g_tunnel_fk = :tunnel 
                            WHERE g_id = :crewId',
                ['debut' => $_POST['debut'], 'fin' => $_POST['fin'], 'taverne' => $_POST['taverne'], 'tunnel' => $_POST['tunnel'], 'crewId' => $crewId]
            );
            // header('Location: groupe.php?id='.$idUrl);
        } else {

            $error = 'Nombre de place insuffisante';
        }
    }
}



//! AFFICHAGE INFOS ET FORMULAIRE
try {

    $groupe = makeSelect(
        'SELECT groupe.*,t_nom,t_progres, t_villedepart_fk, t_villearrivee_fk, depart.v_nom AS v_depart, arrivee.v_nom AS v_arrivee
    FROM groupe 
    LEFT JOIN taverne ON g_taverne_fk = taverne.t_id
    LEFT JOIN tunnel ON g_tunnel_fk = tunnel.t_id
    LEFT JOIN ville AS depart ON t_villedepart_fk = depart.v_id
    LEFT JOIN ville AS arrivee ON t_villearrivee_fk = arrivee.v_id
    WHERE g_id = :crewId',
        ['crewId' => $crewId]
    );


    $nains = makeSelect('SELECT n_id, n_nom FROM nain WHERE n_groupe_fk = :crewId', ['crewId' => $crewId]);


    $tunnels = makeSelect('SELECT tunnel.*, depart.v_nom AS v_depart, arrivee.v_nom AS v_arrivee
                        FROM tunnel
                        JOIN ville AS depart ON t_villedepart_fk = depart.v_id
                        JOIN ville AS arrivee ON t_villearrivee_fk = arrivee.v_id');
} catch (PDOException $e) {
    die($e->getMessage());
}

?>

<div class="container py-5">
    <div class="card d-flex mx-auto shadow p-3 mb-5 bg-body-tertiary rounded" style="width: 24rem;">
        <div class="card-body d-flex text-center flex-column ">
            <h5 class="card-title shadow p-3 mb-5 bg-body-tertiary rounded">Groupe : <?= $groupe['g_id'] ?></h5>
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
                            <?php foreach ($nains as $nain) : ?>
                                <p>
                                    <a href="./dwarfPage.php?selectNain=<?= $nain['n_id'] ?>"> <?= $nain['n_nom'] ?>
                                    </a>
                                </p>
                            <?php endforeach; ?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (isset($groupe['g_taverne_fk'])) : ?>
                <p>Boivent chez : <a href="./pubPage.php?selectPub=<?= $groupe['g_taverne_fk'] ?>"> <?= $groupe['t_nom'] ?></a></p>
            <?php else: ?>
                <p>Boivent dans aucune</p>
            <?php endif; ?>

            <?php if (isset($groupe['g_tunnel_fk'])) : ?>
                <p><?= $groupe['t_progres'] == 100 ? 'Entretiennent' : 'Creusent' ?> de <?= $groupe['g_debuttravail'] ?> à <?= $groupe['g_fintravail'] ?> le tunnel de <a href="./cityPage.php?selectCity=<?= $groupe['t_villedepart_fk'] ?>"> <?= $groupe['v_depart'] ?></a> à <a href="./cityPage.php?selectCity=<?= $groupe['t_villearrivee_fk'] ?>"> <?= $groupe['v_arrivee'] ?></a> (<?= $groupe['t_progres'] ?>% )</p>
            <?php endif; ?>
        </div>
        <a href="../homePage.php" class="link link-danger text-center">Retour a la page d'accueil</a>
    </div>
    <div class="card d-flex mx-auto shadow p-3 mb-5 bg-body-tertiary rounded" style="width: 24rem;">
        <form action="" method="post">
            <div class="mb-3">
                <label class="form-label" for="changeTaverne">Tavernes :</label>
                <select class="form-select" name="taverne" id="ChangeTaverne">
                    <option value="" <?= !isset($groupe['g_taverne_fk']) ? 'selected' : '' ?>>Aucune</option>
                    <?php foreach ($tavernesLibres as $taverne) : ?>
                        <option value="<?= $taverne['t_id'] ?>" <?= $groupe['g_taverne_fk'] == $taverne['t_id'] ? 'selected' : '' ?>>
                            <?= $taverne['t_nom'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="pe-3">
                <label class="form-label" for="changeTimeDebut">Debut :</label>
                <input type="time" id="changeTimeDebut" class="form-input" name="debut" step="1" value="<?= $groupe['g_debuttravail'] ?>">
                <label class="form-label" for="changeTimeFin">Fin :</label>
                <input type="time" id="changeTimeFin" class="form-input" name="fin" step="1" value="<?= $groupe['g_fintravail'] ?>">
            </div>
            <div class="mb-3">
                <label class="form-label" for="changeTunnel">Tavernes :</label>
                <select class="form-select" name="tunnel" id="ChangeTunnel">
                    <option value="" <?= !isset($groupe['g_tunnel_fk']) ? 'selected' : '' ?>>Aucun</option>
                    <?php foreach ($tunnels as $tunnel) : ?>
                        <option value="<?= $tunnel['t_id'] ?>" <?= $groupe['g_tunnel_fk'] == $tunnel['t_id'] ? 'selected' : '' ?>><?= $tunnel['v_depart'] ?> -> <?= $tunnel['v_arrivee'] ?> (<?= $tunnel['t_progres'] ?>%)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mt-5">
                <button type="submit" class="btn btn-success">Modifier</button>
            </div>
        </form>
    </div>
</div>

<?php
dump($tavernesLibres);
include '../admin/inc/foot.php';
?>