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

        //* UPDATE
        $currentGroup = $_GET['selectGroupe'] ?? '';

        if (isset($_POST['selectGroup']) && $_POST['selectGroup'] !== $currentGroup) {
            $newGroup = $_POST['selectGroup'];
    
            $qUpdate = 'UPDATE nain SET n_groupe_fk = :up_groupe WHERE n_id = :idNain';
            $reqUpdate = $pdo->prepare($qUpdate);
            $reqUpdate->bindValue('up_groupe', $newGroup);
            $reqUpdate->bindValue('idNain', $nainId);
            $reqUpdate->execute();
        }

    $q = 'SELECT n_id, origin.v_id , taverne.t_id AS id_taverne, n_nom, n_barbe, n_groupe_fk, origin.v_nom AS v_natale, t_nom, g_debuttravail, g_fintravail,t_villedepart_fk,t_villearrivee_fk, depart.v_nom AS v_depart, arrivee.v_nom AS v_arrivee 
    FROM nain
    JOIN ville AS origin ON n_ville_fk = v_id
    LEFT JOIN groupe ON n_groupe_fk = g_id
    LEFT JOIN taverne ON g_taverne_fk = t_id
    LEFT JOIN tunnel ON v_id = t_villedepart_fk
    LEFT JOIN ville AS depart ON t_villedepart_fk = depart.v_id
    LEFT JOIN ville AS arrivee ON t_villearrivee_fk = arrivee.v_id
    WHERE n_id = :selectNain';

    $request = $pdo->prepare($q);
    $request->bindValue('selectNain', $nainId);
    $request->execute();
    $nain = $request->fetch(PDO::FETCH_ASSOC);

    $q = "SELECT g_id
    FROM groupe
    ORDER BY g_id";
    $stmt = $pdo->query($q);
    $crews = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die($e->getMessage());
}
?>

<div class="container py-5">
    <div class="card d-flex mx-auto shadow p-3 mb-5 bg-body-tertiary rounded" style="width: 24rem;">
        <div class="card-body d-flex text-center flex-column ">
            <h5 class="card-title shadow p-3 mb-5 bg-body-tertiary rounded">Name : <?= $nain['n_nom'] ?></h5>
            <p class="card-text">City : <a href="./cityPage.php?selectCity=<?= $nain['v_id'] ?>">
                    <?= $nain['v_natale'] ?></a>
            </p>
            <p class="card-text">Longueur de barbe : <?= $nain['n_barbe'] ?></p>
            <p class="card-text <?= $nain['t_nom'] == NULL ? 'd-none' : '' ?>">
                Taverne :
                <a href="./pubPage.php?selectPub=<?= $nain['id_taverne'] ?>">
                    <?= $nain['t_nom'] ?>
                </a>
            </p>

            <p class="card-text ">
                <a href="./crewPage.php?selectCrew=<?= $nain['n_groupe_fk'] ?>">
                    <?= $nain['n_groupe_fk'] == NULL ? 'Aucun groupe' : ("Membre du groupe nº" . $nain['n_groupe_fk']) ?>
                </a>
            </p>

            <p class="card-text <?= $nain['n_groupe_fk'] == NULL ? 'd-none' : '' ?>">
                Travaille de <?= $nain['g_debuttravail'] ?> à <?= $nain['g_fintravail'] ?> du tunnel de
                <a href="./cityPage.php?selectCrew=<?= $nain['t_villedepart_fk'] ?>">
                    <?= $nain['v_depart'] ?>
                </a>
                à
                <a href="./cityPage.php?selectCity=<?= $nain['t_villearrivee_fk'] ?>">
                    <?= $nain['v_arrivee'] ?>
                </a>
            </p>

            <form action="" method="post">
                                <select name="selectGroup" id="selectGroup" class="form-select" aria-label="Default select example">
                                <option value="" <?= !isset($nain['n_groupe_fk']) ? 'selected' : '' ?> >Aucun</option>
                                    <?php foreach($crews as $crew) : ?>
                                    <option value="<?=$crew["g_id"]?>" <?= $nain['n_groupe_fk'] == $crew['g_id'] ? 'selected' : '' ?>><?=$crew["g_id"]?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-danger my-2">Valider</button>
                            </form>
        </div>
        <a href="../homePage.php" class="link link-danger text-center">Retour a la page d'accueil</a>
    </div>
</div>
<?php
require '../admin/inc/foot.php';
?>