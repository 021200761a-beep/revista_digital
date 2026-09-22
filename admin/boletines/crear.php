<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/funciones.php';
require_once __DIR__ . '/../../includes/uploads.php';

requerirAdministrador();

$tituloPagina = 'Nuevo boletín';
$baseUrl = '../../';

$errores = [];

$numero_boletin = '';
$resumen = '';
$fecha_publicacion = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $numero_boletin = trim(
        $_POST['numero_boletin'] ?? ''
    );

    $resumen = trim(
        $_POST['resumen'] ?? ''
    );

    $fecha_publicacion = trim(
        $_POST['fecha_publicacion'] ?? ''
    );

    /*
     * Validar número de boletín.
     */
    if ($numero_boletin === '') {
        $errores[] =
            'El número de boletín es obligatorio.';
    }

    /*
     * Validar fecha.
     */
    if ($fecha_publicacion === '') {
        $errores[] =
            'La fecha de publicación es obligatoria.';
    }

    /*
     * Validar que la fecha tenga formato correcto.
     */
    if ($fecha_publicacion !== '') {

        $fechaValida = DateTime::createFromFormat(
            'Y-m-d',
            $fecha_publicacion
        );

        if (
            !$fechaValida ||
            $fechaValida->format('Y-m-d') !==
            $fecha_publicacion
        ) {
            $errores[] =
                'La fecha de publicación no es válida.';
        }
    }

    /*
     * Verificar que el número de boletín
     * todavía no exista.
     */
    if ($numero_boletin !== '') {

        $stmt = $pdo->prepare(
            "SELECT id
             FROM boletines
             WHERE numero_boletin = ?
             LIMIT 1"
        );

        $stmt->execute([
            $numero_boletin
        ]);

        if ($stmt->fetch()) {
            $errores[] =
                'Ya existe un boletín con ese número.';
        }
    }

    /*
     * Variables para controlar archivos
     * subidos durante la operación.
     */
    $rutaPortada = '';
    $rutaPdf = '';

    try {

        /*
         * Subir portada solamente si se seleccionó.
         */
        if (
            isset($_FILES['foto_portada']) &&
            $_FILES['foto_portada']['error'] !==
            UPLOAD_ERR_NO_FILE
        ) {
            $rutaPortada =
                subirPortadaBoletin(
                    $_FILES['foto_portada']
                );
        }

        /*
         * Subir PDF solamente si se seleccionó.
         */
        if (
            isset($_FILES['archivo_pdf']) &&
            $_FILES['archivo_pdf']['error'] !==
            UPLOAD_ERR_NO_FILE
        ) {
            $rutaPdf =
                subirPdfBoletin(
                    $_FILES['archivo_pdf']
                );
        }

        /*
         * Si no hay errores, guardar en MySQL.
         */
        if (empty($errores)) {

            $usuarioId =
                (int) $_SESSION['usuario']['id'];

            $stmt = $pdo->prepare(
                "INSERT INTO boletines (
                    numero_boletin,
                    resumen,
                    foto_portada,
                    archivo_pdf,
                    fecha_publicacion,
                    usuario_id
                ) VALUES (?, ?, ?, ?, ?, ?)"
            );

            $stmt->execute([
                $numero_boletin,
                $resumen,
                $rutaPortada,
                $rutaPdf,
                $fecha_publicacion,
                $usuarioId
            ]);

            header(
                'Location: index.php?mensaje=creado'
            );

            exit;
        }

    } catch (PDOException $e) {

        /*
         * Si MySQL rechaza la operación,
         * eliminar los archivos recién subidos.
         */
        if ($rutaPortada !== '') {
            eliminarArchivoBoletin(
                $rutaPortada
            );
        }

        if ($rutaPdf !== '') {
            eliminarArchivoBoletin(
                $rutaPdf
            );
        }

        if (
            $e->errorInfo[0] === '23000'
        ) {
            $errores[] =
                'Ya existe un boletín con ese número.';
        } else {
            $errores[] =
                'No se pudo guardar el boletín.';
        }

    } catch (RuntimeException $e) {

        /*
         * Si falla la subida de algún archivo,
         * eliminar cualquier archivo que ya
         * haya sido subido durante esta operación.
         */
        if ($rutaPortada !== '') {
            eliminarArchivoBoletin(
                $rutaPortada
            );
        }

        if ($rutaPdf !== '') {
            eliminarArchivoBoletin(
                $rutaPdf
            );
        }

        $errores[] =
            $e->getMessage();
    }
}

require_once __DIR__ . '/../../includes/admin-header.php';
require_once __DIR__ . '/../../includes/admin-sidebar.php';
?>

<main class="admin-main">

    <div class="container-fluid py-4">

        <!-- ENCABEZADO -->

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h1 class="h3 mb-1">
                    Nuevo boletín
                </h1>

                <p class="text-muted mb-0">
                    Registrar un nuevo boletín digital
                </p>

            </div>

            <div>

                <a
                    href="index.php"
                    class="btn btn-outline-secondary"
                >
                    <i class="bi bi-arrow-left"></i>
                    Volver
                </a>

            </div>

        </div>


        <!-- ERRORES -->

        <?php if (!empty($errores)): ?>

            <div
                class="alert alert-danger"
                role="alert"
            >

                <strong>
                    No se pudo guardar el boletín:
                </strong>

                <ul class="mb-0 mt-2">

                    <?php foreach ($errores as $error): ?>

                        <li>
                            <?= e($error) ?>
                        </li>

                    <?php endforeach; ?>

                </ul>

            </div>

        <?php endif; ?>


        <!-- FORMULARIO -->

        <div class="card dashboard-card">

            <div class="card-body">

                <form
                    method="POST"
                    enctype="multipart/form-data"
                >

                    <div class="row g-4">

                        <!-- NÚMERO -->

                        <div class="col-12 col-md-6">

                            <label
                                for="numero_boletin"
                                class="form-label"
                            >
                                Número de boletín
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="numero_boletin"
                                name="numero_boletin"
                                value="<?= e($numero_boletin) ?>"
                                maxlength="100"
                                required
                            >

                            <div class="form-text">
                                Debe ser único.
                            </div>

                        </div>


                        <!-- FECHA -->

                        <div class="col-12 col-md-6">

                            <label
                                for="fecha_publicacion"
                                class="form-label"
                            >
                                Fecha de publicación
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="date"
                                class="form-control"
                                id="fecha_publicacion"
                                name="fecha_publicacion"
                                value="<?= e($fecha_publicacion) ?>"
                                required
                            >

                        </div>


                        <!-- RESUMEN -->

                        <div class="col-12">

                            <label
                                for="resumen"
                                class="form-label"
                            >
                                Resumen
                            </label>

                            <textarea
                                class="form-control"
                                id="resumen"
                                name="resumen"
                                rows="5"
                            ><?= e($resumen) ?></textarea>

                        </div>


                        <!-- PORTADA -->

                        <div class="col-12 col-lg-6">

                            <label
                                for="foto_portada"
                                class="form-label"
                            >
                                Foto de portada
                            </label>

                            <input
                                type="file"
                                class="form-control"
                                id="foto_portada"
                                name="foto_portada"
                                accept="image/jpeg,image/png,image/webp,image/gif"
                            >

                            <div class="form-text">
                                JPG, PNG, WEBP o GIF.
                                Máximo 5 MB.
                            </div>

                            <div
                                id="vistaPortada"
                                class="mt-3"
                            ></div>

                        </div>


                        <!-- PDF -->

                        <div class="col-12 col-lg-6">

                            <label
                                for="archivo_pdf"
                                class="form-label"
                            >
                                Archivo PDF
                            </label>

                            <input
                                type="file"
                                class="form-control"
                                id="archivo_pdf"
                                name="archivo_pdf"
                                accept="application/pdf,.pdf"
                            >

                            <div class="form-text">
                                Solo PDF.
                                Máximo 10 MB.
                            </div>

                            <div
                                id="nombrePdf"
                                class="mt-3 text-muted"
                            ></div>

                        </div>


                        <!-- BOTONES -->

                        <div class="col-12">

                            <hr>

                            <div
                                class="d-flex flex-wrap gap-2 justify-content-end"
                            >

                                <a
                                    href="index.php"
                                    class="btn btn-outline-secondary"
                                >
                                    Cancelar
                                </a>

                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                >
                                    <i class="bi bi-save"></i>
                                    Guardar boletín
                                </button>

                            </div>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>

</main>


<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {

        const inputPortada =
            document.getElementById(
                'foto_portada'
            );

        const vistaPortada =
            document.getElementById(
                'vistaPortada'
            );

        const inputPdf =
            document.getElementById(
                'archivo_pdf'
            );

        const nombrePdf =
            document.getElementById(
                'nombrePdf'
            );


        /*
         * Vista previa de portada.
         */
        inputPortada.addEventListener(
            'change',
            function () {

                vistaPortada.innerHTML = '';

                const archivo =
                    this.files[0];

                if (!archivo) {
                    return;
                }

                const imagen =
                    document.createElement(
                        'img'
                    );

                imagen.src =
                    URL.createObjectURL(
                        archivo
                    );

                imagen.alt =
                    'Vista previa de portada';

                imagen.style.maxWidth =
                    '100%';

                imagen.style.maxHeight =
                    '300px';

                imagen.style.objectFit =
                    'contain';

                imagen.className =
                    'img-thumbnail';

                vistaPortada.appendChild(
                    imagen
                );
            }
        );


        /*
         * Mostrar nombre del PDF.
         */
        inputPdf.addEventListener(
            'change',
            function () {

                nombrePdf.textContent = '';

                const archivo =
                    this.files[0];

                if (!archivo) {
                    return;
                }

                nombrePdf.innerHTML =
                    '<i class="bi bi-file-earmark-pdf"></i> ' +
                    archivo.name;
            }
        );
    }
);
</script>

<?php
require_once __DIR__ . '/../../includes/admin-footer.php';
?>