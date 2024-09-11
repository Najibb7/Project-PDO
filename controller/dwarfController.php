<?php
require_once 'admin/config/config.php';

try {

    $pdo = new PDO(DSN, DB_USER, DB_PWD, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    $q = 'SELECT n_id, n_nom, n_barbe, n_groupe_fk AS groupe, v_nom, t_nom, g_debuttravail, g_fintravail 
    FROM nain
    JOIN ville ON n_ville_fk = v_id
    LEFT JOIN groupe ON n_groupe_fk = g_id
    LEFT JOIN taverne ON g_taverne_fk = t_id
    ORDER BY n_id ASC
    ';

    $stmt = $pdo->query($q);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);


} catch (PDOException $e) {
    die($e->getMessage());
}
