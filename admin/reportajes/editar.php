<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/funciones.php';
require_once __DIR__ . '/../../includes/uploads.php';

requerirAdministrador();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    header('Location: index.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| Cargar reportaje
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        id,
        titulo,
        resumen_corto,
        desarrollo,
        foto_principal,
        pdf_adjunto,
        fecha_publicacion,
        es_destacado,
        autor_id
    FROM reportajes
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$id]);

$reportaje = $stmt->fetch();

if (!$reportaje) {
    header('Location: index.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| Cargar autores
|--------------------------------------------------------------------------
*/

$stmtAutores = $pdo->query("
    SELECT
        id,
        nombres,
        ap_paterno,
        ap_materno,
        nickname,
        es_nickname
    FROM autores
    ORDER BY nombres ASC, ap_paterno ASC
");

$autores = $stmtAutores->fetchAll();

/*
|--------------------------------------------------------------------------
| Variables del formulario
|--------------------------------------------------------------------------
*/

$errores = [];

$titulo = $reportaje['titulo'];
$resumen_corto = $reportaje['resumen_corto'];
$desarrollo = $reportaje['desarrollo'];
$foto_principal = $reportaje['foto_principal'];
$pdf_adjunto = $reportaje['pdf_adjunto'];
$fecha_publicacion = $reportaje['fecha_publicacion'];
$es_destacado = (int) $reportaje['es_destacado'];
$autor_id = (int) $reportaje['autor_id'];

/*
|--------------------------------------------------------------------------
| Procesar formulario
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titulo = trim($_POST['titulo'] ?? '');
    $resumen_corto = trim($_POST['resumen_corto'] ?? '');
    $desarrollo = trim($_POST['desarrollo'] ?? '');
   
    $pdf_adjunto = trim($_POST['pdf_adjunto'] ?? '');
    $fecha_publicacion = trim($_POST['fecha_publicacion'] ?? '');
    $autor_id = filter_input(
        INPUT_POST,
        'autor_id',
        FILTER_VALIDATE_INT
    );

    $es_destacado = isset($_POST['es_destacado']) ? 1 : 0;

    /*
    |--------------------------------------------------------------------------
    | Validaciones
    |--------------------------------------------------------------------------
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

    if (!$autor_id || $autor_id <= 0) {
        $errores[] = 'Debes seleccionar un autor.';
    }

    if ($fecha_publicacion === '') {
        $errores[] = 'La fecha de publicación es obligatoria.';
    }

    /*
    |--------------------------------------------------------------------------
    | Verificar autor
    |--------------------------------------------------------------------------
    */

    if ($autor_id && $autor_id > 0) {

        $stmtAutor = $pdo->prepare("
            SELECT id
            FROM autores
            WHERE id = ?
            LIMIT 1
        ");

        $stmtAutor->execute([$autor_id]);

        if (!$stmtAutor->fetch()) {
            $errores[] = 'El autor seleccionado no existe.';
        }
    }
    /*
    |--------------------------------------------------------------------------
    | Subir nueva imagen
    |--------------------------------------------------------------------------
    */

    $nuevaFoto = null;

    if (
        isset($_FILES['foto_principal']) &&
        $_FILES['foto_principal']['error'] !== UPLOAD_ERR_NO_FILE
    ) {

        try {

            $nuevaFoto = subirImagenReportaje(
                $_FILES['foto_principal']
            );

        } catch (RuntimeException $e) {

            $errores[] = $e->getMessage();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Actualizar reportaje
    |--------------------------------------------------------------------------
    */

    if (empty($errores)) {

        $fotoParaGuardar = $foto_principal;

        if ($nuevaFoto !== null) {
            $fotoParaGuardar = $nuevaFoto;
        }

        try {

            $stmtUpdate = $pdo->prepare("
                UPDATE reportajes
                SET
                    titulo = ?,
                    resumen_corto = ?,
                    desarrollo = ?,
                    foto_principal = ?,
                    pdf_adjunto = ?,
                    fecha_publicacion = ?,
                    es_destacado = ?,
                    autor_id = ?
                WHERE id = ?
            ");

            $stmtUpdate->execute([
                $titulo,
                $resumen_corto,
                $desarrollo,
                $fotoParaGuardar,
                $pdf_adjunto !== '' ? $pdf_adjunto : null,
                $fecha_publicacion,
                $es_destacado,
                $autor_id,
                $id
            ]);

            /*
            |--------------------------------------------------------------------------
            | Eliminar imagen anterior
            |--------------------------------------------------------------------------
            */

            if ($nuevaFoto !== null) {
                eliminarImagenReportaje($foto_principal);
            }

            header('Location: index.php?editado=1');
            exit;

        } catch (PDOException $e) {

            $errores[] = 'No se pudo actualizar el reportaje.';
        }
    }
}

$tituloPagina = 'Editar reportaje';
$baseUrl = '../../';

require_once __DIR__ . '/../../includes/admin-header.php';
require_once __DIR__ . '/../../includes/admin-sidebar.php';

?>

<main class="admin-main">

    <div class="container-fluid py-4">

        <!-- Encabezado -->

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h1 class="h3 mb-1">
                    Editar reportaje
                </h1>

                <p class="text-muted mb-0">
                    Modifica la información del reportaje seleccionado.
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


        <!-- Errores -->

        <?php if (!empty($errores)): ?>

            <div class="alert alert-danger">

                <div class="fw-bold mb-2">
                    No se pudo actualizar el reportaje:
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


        <!-- Formulario -->

        <div class="card shadow-sm">

            <div class="card-header">
                <h5 class="mb-0">
                    Información del reportaje
                </h5>
            </div>

            <div class="card-body">

                <form
                    method="POST"
                    enctype="multipart/form-data"
                >

                    <!-- Título -->

                    <div class="mb-3">

                        <label
                            for="titulo"
                            class="form-label"
                        >
                            Título
                            <span class="text-danger">*</span>
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


                    <!-- Resumen -->

                    <div class="mb-3">

                        <label
                            for="resumen_corto"
                            class="form-label"
                        >
                            Resumen corto
                            <span class="text-danger">*</span>
                        </label>

                        <textarea
                            class="form-control"
                            id="resumen_corto"
                            name="resumen_corto"
                            rows="4"
                            required
                        ><?= e($resumen_corto) ?></textarea>

                    </div>


                    <!-- Desarrollo -->

                    <div class="mb-3">

                        <label
                            for="desarrollo"
                            class="form-label"
                        >
                            Desarrollo
                            <span class="text-danger">*</span>
                        </label>

                        <textarea
                            class="form-control"
                            id="desarrollo"
                            name="desarrollo"
                            rows="10"
                            required
                        ><?= e($desarrollo) ?></textarea>

                    </div>


                    <div class="row">

                        <!-- Autor -->

                        <div class="col-md-6 mb-3">

                            <label
                                for="autor_id"
                                class="form-label"
                            >
                                Autor
                                <span class="text-danger">*</span>
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

                                    $nombreAutor = trim(
                                        $autor['nombres'] . ' ' .
                                        $autor['ap_paterno'] . ' ' .
                                        ($autor['ap_materno'] ?? '')
                                    );

                                    if (
                                        !empty($autor['es_nickname'])
                                        && !empty($autor['nickname'])
                                    ) {
                                        $nombreAutor .=
                                            ' (@' .
                                            $autor['nickname'] .
                                            ')';
                                    }

                                    ?>

                                    <option
                                        value="<?= (int) $autor['id'] ?>"
                                        <?= $autor_id === (int) $autor['id']
                                            ? 'selected'
                                            : '' ?>
                                    >
                                        <?= e($nombreAutor) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <!-- Fecha -->

                        <div class="col-md-6 mb-3">

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
                                value="<?= e(
                                    substr(
                                        (string) $fecha_publicacion,
                                        0,
                                        10
                                    )
                                ) ?>"
                                required
                            >

                        </div>

                    </div>


                    <!-- Foto principal -->

                    <!-- Foto principal -->

                    <div class="mb-4">

                        <label
                            for="foto_principal"
                            class="form-label"
                        >
                            Foto principal
                        </label>

                        <?php if (!empty($foto_principal)): ?>

                            <div class="mb-3">

                                <p class="small text-muted mb-2">
                                    Imagen actual:
                                </p>

                                <div
                                    class="border rounded p-2"
                                    style="max-width: 400px;"
                                >

                                    <img
                                        src="<?= e('../../' . $foto_principal) ?>"
                                        alt="Imagen actual del reportaje"
                                        class="img-fluid rounded"
                                        style="max-height: 250px; object-fit: contain;"
                                    >

                                </div>

                            </div>

                        <?php endif; ?>


                        <input
                            type="file"
                            class="form-control"
                            id="foto_principal"
                            name="foto_principal"
                            accept="image/jpeg,image/png,image/webp,image/gif"
                        >

                        <div class="form-text">

                            <?php if (!empty($foto_principal)): ?>

                                Selecciona una nueva imagen solamente si
                                deseas reemplazar la actual.

                            <?php else: ?>

                                Selecciona una imagen para este reportaje.

                            <?php endif; ?>

                            Formatos: JPG, PNG, WEBP y GIF.
                            Máximo: 5 MB.

                        </div>

                    </div>


                    <!-- PDF -->

                    <div class="mb-3">

                        <label
                            for="pdf_adjunto"
                            class="form-label"
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
                            Por ahora se guarda como texto.
                        </div>

                    </div>


                    <!-- Destacado -->

                    <div class="form-check mb-4">

                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="es_destacado"
                            name="es_destacado"
                            value="1"
                            <?= $es_destacado === 1
                                ? 'checked'
                                : '' ?>
                        >

                        <label
                            class="form-check-label"
                            for="es_destacado"
                        >
                            Mostrar como reportaje destacado
                        </label>

                    </div>


                    <!-- Botones -->

                    <div class="d-flex gap-2">

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            <i class="bi bi-save me-1"></i>
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

<?php require_once __DIR__ . '/../../includes/admin-footer.php'; ?>