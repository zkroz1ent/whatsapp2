<?php
try {
    $dbh = new PDO('mysql:host=127.0.0.1;dbname=testdb', 'root');
    echo 'Connection réussie à MySQL via PDO.';
} catch (PDOException $e) {
    echo 'Échec de la connexion : ' . $e->getMessage();
}
?>