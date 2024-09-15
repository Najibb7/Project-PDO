<?php
require '../admin/inc/head.php';
require_once '../admin/config/config.php';

try {

    $pdo = new PDO(DSN, DB_USER, DB_PWD, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    if (isset($_GET['selectCity']) && !empty($_GET['selectCity'])) {
        $cityId = $_GET['selectCity'];
    }

    $queryCity = 'SELECT DISTINCT v.v_id, v.v_nom, v.v_superficie, n.n_nom, n.n_id
    FROM ville v
    LEFT JOIN nain n ON v.v_id = n.n_ville_fk
    WHERE v.v_id = :selectCity';

    $request = $pdo->prepare($queryCity);
    $request->bindValue('selectCity', $cityId);
    $request->execute();
    $city = $request->fetchAll(PDO::FETCH_ASSOC);

    $queryTavernes = "SELECT DISTINCT taverne.t_nom, taverne.t_id AS id_taverne
        FROM ville
        LEFT JOIN nain ON v_id = n_ville_fk
        LEFT JOIN taverne ON v_id = t_ville_fk
        LEFT JOIN tunnel ON v_id = t_villedepart_fk
        WHERE v_id = :selectCity";
    $req = $pdo->prepare($queryTavernes);
    $req->bindValue('selectCity', $cityId);
    $req->execute();
    $tavernes = $req->fetchAll(PDO::FETCH_ASSOC);

    $queryTunnel = 'SELECT tunnel.*, depart.v_nom AS v_depart, arrivee.v_nom AS v_arrivee
                        FROM tunnel
                        JOIN ville AS depart ON t_villedepart_fk = depart.v_id
                        JOIN ville AS arrivee ON t_villearrivee_fk = arrivee.v_id
                        WHERE t_villedepart_fk = :idDepart 
                        OR t_villearrivee_fk = :idArrivee';
    $stmt = $pdo->prepare($queryTunnel);
    $stmt->bindValue('idDepart', $cityId);
    $stmt->bindValue('idArrivee', $cityId);
    $stmt->execute();
    $tunnels = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die($e->getMessage());
}

?>

<div class="container py-5">
    <div class="card d-flex mx-auto shadow p-3 mb-5 bg-body-tertiary rounded" style="width: 24rem;">
        <div class="card-body d-flex mx-auto flex-column ">
            <h5 class="card-title shadow p-3 mb-5 bg-body-tertiary rounded">Nom : <?= $city[0]['v_nom'] ?></h5>
            <p class="card-title">Superficie : <?= $city[0]['v_superficie']?> km²</p>
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
                            <?php foreach ($city as $value) : ?>
                                <?php if ($value['v_id'] == $cityId) : ?>
                                    <p>
                                        <a href="./dwarfPage.php?selectNain=<?= $value['n_id'] ?>">
                                            <?= $value['n_nom'] ?>
                                        </a>
                                    </p>
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
            <p class="card-text">
                Taverne :
            <ul>

                <?php foreach ($tavernes as $taverne) : ?>
                    <li>
                        <a href="./pubPage.php?selectPub=<?= $taverne['id_taverne'] ?>">
                            <?= $taverne['t_nom'] ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            </p>
            <p class="card-text"></p>
        </div>
        <a href="../homePage.php" class="link d'flex text-center">Retour a la page d'accueil</a>
    </div>
    <div class="card shadow p-3 mb-5 bg-body-tertiary rounded">
        <h5 class="card-title shadow p-3 mb-5 bg-body-tertiary rounded"> Etats des tunnels : </h5>
        <div class="card-body">
            <div class="card-text">
                <?php
                foreach ($tunnels as $tunnel) {

                    if ($cityId == $tunnel['t_villedepart_fk']) {
                        $idVille = $tunnel['t_villearrivee_fk'];
                        $nomVille = $tunnel['v_arrivee'];
                    } else {
                        $idVille = $tunnel['t_villedepart_fk'];
                        $nomVille = $tunnel['v_depart'];
                    }

                    if ($tunnel['t_progres'] == 100) {
                        $progres = '<span class="btn btn-success" 
        style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">Ouvert</span>';
                    } else {
                        $progres = '<span class="btn btn-warning" disabled
        style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">' . $tunnel['t_progres'] . '%</span>';
                    }

                    echo '<p>Tunnel vers <a href="ville.php?id=' . $idVille . '"> ' . $nomVille . '</a> : ' . $progres . ' </p> ';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php
include '../admin/inc/foot.php';
?>