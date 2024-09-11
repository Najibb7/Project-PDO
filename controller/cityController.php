<?php
require_once 'admin/config/config.php';

try {
    $pdo = new PDO(DSN, DB_USER, DB_PWD, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $q = "SELECT v_id, v_nom
        FROM ville
        ORDER BY v_id";
        $stmt = $pdo->query($q);
        $cities = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die($e->getMessage());
}