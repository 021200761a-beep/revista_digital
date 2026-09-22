<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/funciones.php';
require_once __DIR__ . '/../../includes/uploads.php';

requerirAdministrador();

$tituloPagina = 'Nuevo reportaje';
$baseUrl = '../../';

$errores = [];


/*
 * Obtener autores disponibles.
 */
$sqlAutores = "
    SELECT
        id,
        nombres,
        ap_paterno,
        ap_materno,
        nickname,
        es_nickname
    FROM autores
    ORDER BY nombres ASC, ap_paterno ASC
";

$stmtAutores = $pdo->prepare($sqlAutores);
$stmtAutores->execute();

$autores = $stmtAutores->fetchAll();


/*
 * Valores iniciales del formulario.
 */
$titulo = '';
$resumen_corto = '';
$desarrollo = '';
$foto_principal = '';
$pdf_adjunto = '';
$fecha_publicacion = date('Y-m-d');
$es_destacado = 0;
$autor_id = '';


/*
 * Procesar formulario.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titulo = trim($_POST['titulo'] ?? '');
    $resumen_corto = trim($_POST['resumen_corto'] ?? '');
    $desarrollo = trim($_POST['desarrollo'] ?? '');
    $foto_principal = null;
    $pdf_adjunto = trim($_POST['pdf_adjunto'] ?? '');
    $fecha_publicacion = trim($_POST['fecha_publicacion'] ?? '');
    $es_destacado = isset($_POST['es_destacado']) ? 1 : 0;
    $autor_id = trim($_POST['autor_id'] ?? '');


    /*
     * Validaciones.
     */

    if ($titulo === '') {
        $errores[] = 'El título es obligatorio.';
    }

    if ($resumen_corto === '') {
        $errores[] = 'El resumen corto es obligatorio.';
    }

    if ($desarrollo === '') {
        $errores[] = 'El desarrollo del reportaje es obligatorio.';
    }

    if ($fecha_publicacion === '') {
        $errores[] = 'La fecha de publicación es obligatoria.';
    }

    if ($autor_id === '') {
        $errores[] = 'Debes seleccionar un autor.';
    }


    /*
     * Comprobar que el autor realmente exista.
     */
    if ($autor_id !== '') {

        $stmtAutor = $pdo->prepare(
            "SELECT id FROM autores WHERE id = :id LIMIT 1"
        );

        $stmtAutor->execute([
            ':id' => $autor_id
        ]);

        if (!$stmtAutor->fetch()) {
            $errores[] = 'El autor seleccionado no existe.';
        }
    }

    if (
        isset($_FILES['foto_principal']) &&
        $_FILES['foto_principal']['error'] !== UPLOAD_ERR_NO_FILE
    ) {

        try {

            $foto_principal = subirImagenReportaje(
                $_FILES['foto_principal']
            );

        } catch (RuntimeException $e) {

            $errores[] = $e->getMessage();
        }
    }


    /*
     * Insertar si no existen errores.
     */
    if (empty($errores)) {

        try {

            $sql = "
                INSERT INTO reportajes
                (
                    titulo,
                    resumen_corto,
                    desarrollo,
                    foto_principal,
                    pdf_adjunto,
                    fecha_publicacion,
                    es_destacado,
                    autor_id,
                    usuario_id
                )
                VALUES
                (
                    :titulo,
                    :resumen_corto,
                    :desarrollo,
                    :foto_principal,
                    :pdf_adjunto,
                    :fecha_publicacion,
                    :es_destacado,
                    :autor_id,
                    :usuario_id
                )
            ";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':titulo' => $titulo,
                ':resumen_corto' => $resumen_corto,
                ':desarrollo' => $desarrollo,
                ':foto_principal' => $foto_principal !== ''
                    ? $foto_principal
                    : null,
                ':pdf_adjunto' => $pdf_adjunto !== ''
                    ? $pdf_adjunto
                    : null,
                ':fecha_publicacion' => $fecha_publicacion,
                ':es_destacado' => $es_destacado,
                ':autor_id' => (int) $autor_id,
                ':usuario_id' => (int) $_SESSION['usuario']['id']
            ]);


            /*
             * Volver al listado.
             */
            header('Location: index.php');
            exit;

        } catch (PDOException $e) {

            $errores[] =
                'No se pudo guardar el reportaje.';

        }

    }

}


require_once __DIR__ . '/../../includes/admin-header.php';
require_once __DIR__ . '/../../includes/admin-sidebar.php';

?>

<div class="main-wrapper">

    <!-- NAVBAR -->

    <header class="top-navbar">

        <div>

            <h5 class="mb-0">
                Nuevo reportaje
            </h5>

        </div>


        <div class="user-info">

            <div class="text-end">

                <div class="fw-semibold">
                    <?= e(nombreUsuarioActual()) ?>
                </div>

                <small class="text-muted">
                    Administrador
                </small>

            </div>


            <div class="user-avatar">

                <?= e(
                    strtoupper(
                        substr(
                            $usuario['nombres'],
                            0,
                            1
                        )
                    )
                ) ?>

            </div>

        </div>

    </header>


    <!-- CONTENIDO -->

    <main class="page-content">

        <div class="container-fluid">


            <!-- CABECERA -->

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h2 class="fw-bold mb-1">
                        Nuevo reportaje
                    </h2>

                    <p class="text-muted mb-0">
                        Registra un nuevo reportaje.
                    </p>

                </div>


                <a
                    href="index.php"
                    class="btn btn-outline-secondary"
                >

                    <i class="bi bi-arrow-left me-2"></i>

                    Volver

                </a>

            </div>


            <!-- ERRORES -->

            <?php if (!empty($errores)): ?>

                <div class="alert alert-danger">

                    <h6 class="fw-bold">
                        No se pudo guardar el reportaje:
                    </h6>

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

            <div class="card dashboard-card">

                <div class="card-body">

                    <form
                        method="POST"
                        enctype="multipart/form-data"
                    >


                        <!-- TÍTULO -->

                        <div class="mb-4">

                            <label
                                for="titulo"
                                class="form-label fw-semibold"
                            >
                                Título *
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="titulo"
                                name="titulo"
                                value="<?= e($titulo) ?>"
                                maxlength="255"
                                required
                            >

                        </div>


                        <!-- RESUMEN -->

                        <div class="mb-4">

                            <label
                                for="resumen_corto"
                                class="form-label fw-semibold"
                            >
                                Resumen corto *
                            </label>

                            <textarea
                                class="form-control"
                                id="resumen_corto"
                                name="resumen_corto"
                                rows="4"
                                required
                            ><?= e($resumen_corto) ?></textarea>

                        </div>


                        <!-- DESARROLLO -->

                        <div class="mb-4">

                            <label
                                for="desarrollo"
                                class="form-label fw-semibold"
                            >
                                Desarrollo *
                            </label>

                            <textarea
                                class="form-control"
                                id="desarrollo"
                                name="desarrollo"
                                rows="12"
                                required
                            ><?= e($desarrollo) ?></textarea>

                        </div>


                        <!-- AUTOR -->

                        <div class="mb-4">

                            <label
                                for="autor_id"
                                class="form-label fw-semibold"
                            >
                                Autor *
                            </label>

                            <select
                                class="form-select"
                                id="autor_id"
                                name="autor_id"
                                required
                            >

                                <option value="">
                                    Seleccionar autor
                                </option>


                                <?php foreach ($autores as $autor): ?>

                                    <?php

                                    $nombreAutor =
                                        trim(
                                            $autor['nombres']
                                            . ' '
                                            . $autor['ap_paterno']
                                            . ' '
                                            . ($autor['ap_materno'] ?? '')
                                        );


                                    if (
                                        !empty($autor['es_nickname'])
                                        && !empty($autor['nickname'])
                                    ) {

                                        $nombreAutor .=
                                            ' (' . $autor['nickname'] . ')';

                                    }

                                    ?>

                                    <option
                                        value="<?= e($autor['id']) ?>"
                                        <?= (string) $autor_id === (string) $autor['id']
                                            ? 'selected'
                                            : ''
                                        ?>
                                    >

                                        <?= e($nombreAutor) ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <!-- FOTO -->

                        <div class="mb-4">

                            <label
                                for="foto_principal"
                                class="form-label fw-semibold"
                            >
                                Foto principal
                            </label>

                            <div class="mb-3">

                                <label
                                    for="foto_principal"
                                    class="form-label"
                                >
                                    Foto principal
                                </label>

                                <input
                                    type="file"
                                    class="form-control"
                                    id="foto_principal"
                                    name="foto_principal"
                                    accept="image/jpeg,image/png,image/webp,image/gif"
                                >

                                <div class="form-text">
                                    Formatos permitidos: JPG, PNG, WEBP y GIF.
                                    Tamaño máximo: 5 MB.
                                </div>

                            </div>
                            

                        </div>


                        <!-- PDF -->

                        <div class="mb-4">

                            <label
                                for="pdf_adjunto"
                                class="form-label fw-semibold"
                            >
                                PDF adjunto
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="pdf_adjunto"
                                name="pdf_adjunto"
                                value="<?= e($pdf_adjunto) ?>"
                                placeholder="Ruta o URL del PDF"
                            >

                            <div class="form-text">
                                En esta primera versión guardaremos la ruta o URL.
                            </div>

                        </div>


                        <!-- FECHA -->

                        <div class="mb-4">

                            <label
                                for="fecha_publicacion"
                                class="form-label fw-semibold"
                            >
                                Fecha de publicación *
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


                        <!-- DESTACADO -->

                        <div class="form-check mb-4">

                            <input
                                type="checkbox"
                                class="form-check-input"
                                id="es_destacado"
                                name="es_destacado"
                                value="1"
                                <?= $es_destacado === 1 ? 'checked' : '' ?>
                            >

                            <label
                                class="form-check-label"
                                for="es_destacado"
                            >

                                Marcar como reportaje destacado

                            </label>

                        </div>


                        <!-- BOTONES -->

                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >

                                <i class="bi bi-save me-2"></i>

                                Guardar reportaje

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

    require_once __DIR__ . '/../../includes/admin-footer.php';

    ?>

</div>