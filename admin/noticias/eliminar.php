<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/funciones.php';
require_once __DIR__ . '/../../includes/uploads.php';

requerirAdministrador();


/*
 * =========================================================
 * VERIFICAR MÉTODO
 * =========================================================
 *
 * La eliminación solamente se permite mediante POST.
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header(
        'Location: index.php?error=metodo_invalido'
    );

    exit;
}


/*
 * =========================================================
 * OBTENER ID
 * =========================================================
 */

$id = filter_input(
    INPUT_POST,
    'id',
    FILTER_VALIDATE_INT
);


if (!$id || $id <= 0) {

    header(
        'Location: index.php?error=id_invalido'
    );

    exit;
}


/*
 * =========================================================
 * BUSCAR NOTICIA
 * =========================================================
 *
 * Primero obtenemos la imagen para poder eliminarla
 * después de borrar la noticia.
 */

$stmt = $pdo->prepare("
    SELECT
        id,
        foto
    FROM noticias
    WHERE id = :id
    LIMIT 1
");

$stmt->execute([
    ':id' => $id
]);

$noticia = $stmt->fetch();


if (!$noticia) {

    header(
        'Location: index.php?error=no_encontrada'
    );

    exit;
}


/*
 * =========================================================
 * ELIMINAR NOTICIA
 * =========================================================
 */

try {

    $stmt = $pdo->prepare("
        DELETE FROM noticias
        WHERE id = :id
    ");

    $stmt->execute([
        ':id' => $id
    ]);


    /*
     * =====================================================
     * ELIMINAR IMAGEN FÍSICA
     * =====================================================
     *
     * Solamente se elimina después de que MySQL
     * haya eliminado correctamente el registro.
     */

    if (!empty($noticia['foto'])) {

        eliminarImagenNoticia(
            $noticia['foto']
        );
    }


    header(
        'Location: index.php?mensaje=eliminada'
    );

    exit;


} catch (PDOException $e) {

    header(
        'Location: index.php?error=eliminacion'
    );

    exit;
}