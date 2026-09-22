<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/funciones.php';

requerirAdministrador();

/*
|--------------------------------------------------------------------------
| Solo permitimos eliminar mediante POST
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| Obtener ID
|--------------------------------------------------------------------------
*/

$id = filter_input(
    INPUT_POST,
    'id',
    FILTER_VALIDATE_INT
);

if (!$id || $id <= 0) {
    header('Location: index.php?error=id_invalido');
    exit;
}

/*
|--------------------------------------------------------------------------
| Verificar que el reportaje exista
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT id
    FROM reportajes
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$id]);

$reportaje = $stmt->fetch();

if (!$reportaje) {
    header('Location: index.php?error=no_encontrado');
    exit;
}

/*
|--------------------------------------------------------------------------
| Eliminar reportaje
|--------------------------------------------------------------------------
|
| La tabla reportajes_fotos tiene ON DELETE CASCADE,
| por lo que sus registros relacionados se eliminarán
| automáticamente.
|
*/

try {

    $stmtDelete = $pdo->prepare("
        DELETE FROM reportajes
        WHERE id = ?
    ");

    $stmtDelete->execute([$id]);

    /*
    |--------------------------------------------------------------------------
    | Verificar que realmente se eliminó
    |--------------------------------------------------------------------------
    */

    if ($stmtDelete->rowCount() > 0) {

        header('Location: index.php?eliminado=1');
        exit;
    }

    header('Location: index.php?error=no_eliminado');
    exit;

} catch (PDOException $e) {

    /*
    |--------------------------------------------------------------------------
    | Error de base de datos
    |--------------------------------------------------------------------------
    */

    header('Location: index.php?error=bd');
    exit;
}