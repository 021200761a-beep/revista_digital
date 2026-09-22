<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/funciones.php';
require_once __DIR__ . '/../../includes/uploads.php';

requerirAdministrador();

$tituloPagina = 'Crear noticia';
$baseUrl = '../../';

$errores = [];

$titulo = '';
$link_externo = '';
$fecha_publicacion = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titulo = trim($_POST['titulo'] ?? '');
    $link_externo = trim($_POST['link_externo'] ?? '');
    $fecha_publicacion = trim(
        $_POST['fecha_publicacion'] ?? ''
    );

    /*
     * VALIDAR TÍTULO
     */
    if ($titulo === '') {
        $errores[] =
            'El título de la noticia es obligatorio.';
    }

    /*
     * VALIDAR FECHA
     */
    if ($fecha_publicacion === '') {
        $errores[] =
            'La fecha de publicación es obligatoria.';
    }

    /*
     * VALIDAR URL EXTERNA
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

            $esHttp =
                str_starts_with(
                    strtolower($link_externo),
                    'http://'
                );

            $esHttps =
                str_starts_with(
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
     * SUBIR IMAGEN
     */
    $foto = null;

    if (
        isset($_FILES['foto']) &&
        $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE
    ) {

        try {

            $foto = subirImagenNoticia(
                $_FILES['foto']
            );

        } catch (RuntimeException $e) {

            $errores[] = $e->getMessage();
        }
    }

    /*
     * GUARDAR EN BASE DE DATOS
     */
    if (empty($errores)) {

        try {

            $sql = "
                INSERT INTO noticias (
                    titulo,
                    foto,
                    link_externo,
                    fecha_publicacion,
                    usuario_id
                )
                VALUES (
                    :titulo,
                    :foto,
                    :link_externo,
                    :fecha_publicacion,
                    :usuario_id
                )
            ";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':titulo' => $titulo,
                ':foto' => $foto,
                ':link_externo' =>
                    $link_externo !== ''
                        ? $link_externo
                        : null,
                ':fecha_publicacion' =>
                    $fecha_publicacion,
                ':usuario_id' =>
                    $_SESSION['usuario']['id']
            ]);

            header(
                'Location: index.php?mensaje=creada'
            );

            exit;

        } catch (PDOException $e) {

            /*
             * Si hubo un error después de subir
             * la imagen, eliminamos el archivo.
             */
            if ($foto !== null) {
                eliminarImagenNoticia($foto);
            }

            $errores[] =
                'No se pudo guardar la noticia.';
        }
    }
}

require_once __DIR__ .
    '/../../includes/admin-header.php';

require_once __DIR__ .
    '/../../includes/admin-sidebar.php';
?>

<main class="admin-main">

    <div class="container-fluid py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">

        <div>
            <h1 class="h3 mb-1">
                Crear noticia
            </h1>

            <p class="text-muted mb-0">
                Agrega una nueva noticia a la revista.
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


    <?php if (!empty($errores)): ?>

        <div class="alert alert-danger">

            <div class="fw-bold mb-2">
                No se pudo guardar la noticia:
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


    <div class="card shadow-sm">

        <div class="card-header">

            <h5 class="mb-0">
                <i class="bi bi-newspaper me-2"></i>
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


                    <!-- IMAGEN -->

                    <div class="col-12 col-lg-6">

                        <label
                            for="foto"
                            class="form-label fw-semibold"
                        >
                            Imagen
                        </label>

                        <input
                            type="file"
                            id="foto"
                            name="foto"
                            class="form-control"
                            accept="image/jpeg,image/png,image/webp,image/gif"
                        >

                        <div class="form-text">
                            Formatos permitidos:
                            JPG, PNG, WEBP y GIF.
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

                    <div class="col-12">

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

                        <div class="form-text">
                            Puedes colocar el enlace hacia
                            la noticia original o una fuente externa.
                        </div>

                    </div>

                </div>


                <hr class="my-4">


                <div class="d-flex flex-wrap gap-2">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-save me-1"></i>
                        Guardar noticia
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