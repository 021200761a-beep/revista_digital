<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/funciones.php';
require_once __DIR__ . '/../../includes/uploads.php';

requerirAdministrador();

$tituloPagina = 'Editar noticia';
$baseUrl = '../../';

$errores = [];

/*
 * =========================================================
 * OBTENER ID
 * =========================================================
 */

$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$id || $id <= 0) {
    header('Location: index.php?error=id_invalido');
    exit;
}


/*
 * =========================================================
 * BUSCAR NOTICIA
 * =========================================================
 */

$stmt = $pdo->prepare("
    SELECT
        id,
        titulo,
        foto,
        link_externo,
        fecha_publicacion,
        usuario_id
    FROM noticias
    WHERE id = :id
    LIMIT 1
");

$stmt->execute([
    ':id' => $id
]);

$noticia = $stmt->fetch();

if (!$noticia) {
    header('Location: index.php?error=no_encontrada');
    exit;
}


/*
 * =========================================================
 * DATOS DEL FORMULARIO
 * =========================================================
 */

$titulo = $noticia['titulo'];
$foto = $noticia['foto'];
$link_externo = $noticia['link_externo'];
$fecha_publicacion = $noticia['fecha_publicacion'];


/*
 * =========================================================
 * PROCESAR FORMULARIO
 * =========================================================
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titulo = trim(
        $_POST['titulo'] ?? ''
    );

    $link_externo = trim(
        $_POST['link_externo'] ?? ''
    );

    $fecha_publicacion = trim(
        $_POST['fecha_publicacion'] ?? ''
    );


    /*
     * -----------------------------------------------------
     * VALIDAR TÍTULO
     * -----------------------------------------------------
     */

    if ($titulo === '') {

        $errores[] =
            'El título de la noticia es obligatorio.';
    }


    /*
     * -----------------------------------------------------
     * VALIDAR FECHA
     * -----------------------------------------------------
     */

    if ($fecha_publicacion === '') {

        $errores[] =
            'La fecha de publicación es obligatoria.';
    }


    /*
     * -----------------------------------------------------
     * VALIDAR ENLACE
     * -----------------------------------------------------
     */

    if ($link_externo !== '') {

        if (
            !filter_var(
                $link_externo,
                FILTER_VALIDATE_URL
            )
        ) {

            $errores[] =
                'El enlace externo no tiene un formato válido.';

        } else {

            $esHttp = str_starts_with(
                strtolower($link_externo),
                'http://'
            );

            $esHttps = str_starts_with(
                strtolower($link_externo),
                'https://'
            );

            if (!$esHttp && !$esHttps) {

                $errores[] =
                    'El enlace debe comenzar con http:// o https://.';
            }
        }
    }


    /*
     * =====================================================
     * IMAGEN
     * =====================================================
     */

    $nuevaFoto = null;


    if (
        isset($_FILES['foto']) &&
        $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE
    ) {

        try {

            $nuevaFoto = subirImagenNoticia(
                $_FILES['foto']
            );

        } catch (RuntimeException $e) {

            $errores[] = $e->getMessage();
        }
    }


    /*
     * =====================================================
     * ACTUALIZAR BASE DE DATOS
     * =====================================================
     */

    if (empty($errores)) {

        /*
         * Por defecto conservamos
         * la imagen existente.
         */

        $fotoParaGuardar = $foto;


        /*
         * Si se subió una nueva imagen,
         * utilizamos la nueva.
         */

        if ($nuevaFoto !== null) {

            $fotoParaGuardar = $nuevaFoto;
        }


        try {

            $sql = "
                UPDATE noticias
                SET
                    titulo = :titulo,
                    foto = :foto,
                    link_externo = :link_externo,
                    fecha_publicacion = :fecha_publicacion
                WHERE id = :id
            ";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':titulo' =>
                    $titulo,

                ':foto' =>
                    $fotoParaGuardar,

                ':link_externo' =>
                    $link_externo !== ''
                        ? $link_externo
                        : null,

                ':fecha_publicacion' =>
                    $fecha_publicacion,

                ':id' =>
                    $id
            ]);


            /*
             * -------------------------------------------------
             * ELIMINAR IMAGEN ANTERIOR
             * -------------------------------------------------
             *
             * Solamente si realmente se reemplazó
             * por una imagen nueva.
             */

            if (
                $nuevaFoto !== null &&
                $foto !== null &&
                $foto !== ''
            ) {

                eliminarImagenNoticia($foto);
            }


            header(
                'Location: index.php?mensaje=editada'
            );

            exit;


        } catch (PDOException $e) {

            /*
             * Si MySQL falla después de haber subido
             * una nueva imagen, eliminamos la nueva.
             */

            if ($nuevaFoto !== null) {

                eliminarImagenNoticia(
                    $nuevaFoto
                );
            }


            $errores[] =
                'No se pudo actualizar la noticia.';
        }
    }
}


/*
 * =========================================================
 * INTERFAZ
 * =========================================================
 */

require_once __DIR__ .
    '/../../includes/admin-header.php';

require_once __DIR__ .
    '/../../includes/admin-sidebar.php';

?>

<main class="admin-main">

    <div class="container-fluid py-4">


        <!-- ENCABEZADO -->

        <div
            class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4"
        >

            <div>

                <h1 class="h3 mb-1">
                    Editar noticia
                </h1>

                <p class="text-muted mb-0">
                    Modifica la información de la noticia.
                </p>

            </div>


            <a
                href="index.php"
                class="btn btn-outline-secondary"
            >

                <i class="bi bi-arrow-left me-1"></i>

                Volver

            </a>

        </div>


        <!-- ERRORES -->

        <?php if (!empty($errores)): ?>

            <div class="alert alert-danger">

                <div class="fw-bold mb-2">
                    No se pudo actualizar la noticia:
                </div>

                <ul class="mb-0">

                    <?php foreach ($errores as $error): ?>

                        <li>
                            <?= e($error) ?>
                        </li>

                    <?php endforeach; ?>

                </ul>

            </div>

        <?php endif; ?>


        <!-- FORMULARIO -->

        <div class="card shadow-sm">

            <div class="card-header">

                <h5 class="mb-0">

                    <i class="bi bi-pencil-square me-2"></i>

                    Información de la noticia

                </h5>

            </div>


            <div class="card-body">

                <form
                    method="POST"
                    enctype="multipart/form-data"
                >


                    <div class="row g-4">


                        <!-- TÍTULO -->

                        <div class="col-12">

                            <label
                                for="titulo"
                                class="form-label fw-semibold"
                            >
                                Título
                            </label>

                            <input
                                type="text"
                                id="titulo"
                                name="titulo"
                                class="form-control"
                                value="<?= e($titulo) ?>"
                                maxlength="255"
                                required
                            >

                        </div>


                        <!-- IMAGEN ACTUAL -->

                        <div class="col-12 col-lg-6">

                            <label
                                class="form-label fw-semibold"
                            >
                                Imagen actual
                            </label>


                            <?php if (!empty($foto)): ?>

                                <div class="border rounded p-2">

                                    <img
                                        src="<?= e('../../' . $foto) ?>"
                                        alt="Imagen de la noticia"
                                        class="img-fluid rounded"
                                        style="max-height: 250px; object-fit: contain;"
                                    >

                                </div>

                            <?php else: ?>

                                <div
                                    class="border rounded p-4 text-center text-muted"
                                >

                                    <i class="bi bi-image fs-1"></i>

                                    <div>
                                        Esta noticia no tiene imagen.
                                    </div>

                                </div>

                            <?php endif; ?>

                        </div>


                        <!-- NUEVA IMAGEN -->

                        <div class="col-12 col-lg-6">

                            <label
                                for="foto"
                                class="form-label fw-semibold"
                            >
                                Reemplazar imagen
                            </label>

                            <input
                                type="file"
                                id="foto"
                                name="foto"
                                class="form-control"
                                accept="image/jpeg,image/png,image/webp,image/gif"
                            >

                            <div class="form-text">

                                Déjalo vacío para conservar
                                la imagen actual.

                                <br>

                                JPG, PNG, WEBP o GIF.
                                Máximo 5 MB.

                            </div>

                        </div>


                        <!-- FECHA -->

                        <div class="col-12 col-lg-6">

                            <label
                                for="fecha_publicacion"
                                class="form-label fw-semibold"
                            >
                                Fecha de publicación
                            </label>

                            <input
                                type="date"
                                id="fecha_publicacion"
                                name="fecha_publicacion"
                                class="form-control"
                                value="<?= e($fecha_publicacion) ?>"
                                required
                            >

                        </div>


                        <!-- ENLACE -->

                        <div class="col-12 col-lg-6">

                            <label
                                for="link_externo"
                                class="form-label fw-semibold"
                            >

                                Enlace externo

                                <span class="text-muted fw-normal">
                                    (opcional)
                                </span>

                            </label>

                            <input
                                type="url"
                                id="link_externo"
                                name="link_externo"
                                class="form-control"
                                value="<?= e($link_externo) ?>"
                                placeholder="https://ejemplo.com/noticia"
                            >

                        </div>

                    </div>


                    <hr class="my-4">


                    <!-- BOTONES -->

                    <div class="d-flex flex-wrap gap-2">

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >

                            <i class="bi bi-check-lg me-1"></i>

                            Guardar cambios

                        </button>


                        <a
                            href="index.php"
                            class="btn btn-outline-secondary"
                        >

                            Cancelar

                        </a>

                    </div>


                </form>

            </div>

        </div>

    </div>

</main>


<?php

require_once __DIR__ .
    '/../../includes/admin-footer.php';

?>