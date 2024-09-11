<?php
require_once 'admin/config/config.php';

try {
    $pdo = new PDO(DSN, DB_USER, DB_PWD, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $q = "SELECT t_id, t_nom
        FROM taverne";
        $stmt = $pdo->query($q);
        $taverns = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die($e->getMessage());
}
