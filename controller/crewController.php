<?php
require_once 'admin/config/config.php';

try {
    $pdo = new PDO(DSN, DB_USER, DB_PWD, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $q = "SELECT g_id
        FROM groupe
        ORDER BY g_id";
        $stmt = $pdo->query($q);
        $crews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die($e->getMessage());
}