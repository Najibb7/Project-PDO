<?php

function getPDO()
{
    try {

        $pdo = new PDO(DSN, DB_USER, DB_PWD, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    } catch (PDOException $e) {
        die('Erreur de connexion à la base : ' . $e->getMessage());
    }

    return $pdo;
}

function makeRequest($sql, $params = [])
{

    $pdo = getPDO();

    if (empty($params)) {
        // pas besoin de la méthode execute() car la méthode query() execute directement la requete passée en parametre
        return $pdo->query($sql);
    } else {

        if (($request = $pdo->prepare($sql)) !== false) {

            foreach ($params as $key => $value) {
                if (($request->bindValue($key, $value == '' ? null : $value)) === false) {
                    return false;
                }
            }

            if ($request->execute()) {
                return $request;
            } else {
                return false;
            }
        }
    }
}

function makeSelect($sql, $params = [])
{

    $request = makeRequest($sql, $params);

    $results = $request->fetchAll(PDO::FETCH_ASSOC);
    $request->closeCursor();

    // On gère le cas ou on a qu'un seul résultat
    if (($results !== false) && (count($results) < 2)) {
        return $results[0];
    }

    return $results;
}


function dump($element)
{
    echo "<div style='border:1px solid #ccc; padding:10px; margin:10px;background-color: #fff;'>";
    echo "<strong>Type:</strong> " . gettype($element) . "<br>";
    echo "<strong>Content:</strong> <pre>";

    if (is_array($element) || is_object($element)) {
        echo htmlspecialchars(print_r($element, true));
    } else {
        echo htmlspecialchars(var_export($element, true));
    }

    echo "</pre></div>";
}
