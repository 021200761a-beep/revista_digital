
<?php

/**
 * Conexión a la base de datos
 * Proyecto: Revista Digital
 * Motor: MySQL
 * XAMPP
 */

$host = 'sql206.infinityfree.com';
$dbname = 'if0_42984043_revista';
$username = 'if0_42984043';
$password = 'pgDkMXDpfrtx98';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {

    $pdo = new PDO(
        $dsn,
        $username,
        $password,
        $options
    );

} catch (PDOException $e) {

    die(
        'Error de conexión a la base de datos: '
        . $e->getMessage()
    );

}

