<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/funciones.php';

requerirAdministrador();

$tituloPagina = 'Boletines';
$baseUrl = '../../';


/*
 * =========================================================
 * OBTENER BOLETINES
 * =========================================================
 */

$stmt = $pdo->query("
    SELECT
        b.id,
        b.numero_boletin,
        b.resumen,
        b.foto_portada,
        b.archivo_pdf,
        b.fecha_publicacion,
        b.usuario_id,

        CONCAT(
            u.nombres,
            ' ',
            u.ap_paterno
        ) AS nombre_usuario

    FROM boletines b

    LEFT JOIN usuarios u
        ON b.usuario_id = u.id

    ORDER BY b.id DESC
");

$boletines = $stmt->fetchAll();


/*
 * =========================================================
 * MENSAJES
 * =========================================================
 */

$mensaje = $_GET['mensaje'] ?? '';
$error = $_GET['error'] ?? '';

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
                    Boletines
                </h1>

                <p class="text-muted mb-0">
                    Gestiona los boletines de la revista.
                </p>

            </div>


            <a
                href="crear.php"
                class="btn btn-primary"
            >

                <i class="bi bi-plus-lg me-1"></i>

                Nuevo boletín

            </a>

        </div>


        <!-- MENSAJES DE ÉXITO -->

        <?php if ($mensaje === 'creado'): ?>

            <div class="alert alert-success alert-dismissible fade show">

                <i class="bi bi-check-circle me-2"></i>

                El boletín se creó correctamente.

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>

            </div>

        <?php elseif ($mensaje === 'editado'): ?>

            <div class="alert alert-success alert-dismissible fade show">

                <i class="bi bi-check-circle me-2"></i>

                El boletín se actualizó correctamente.

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>

            </div>

        <?php elseif ($mensaje === 'eliminado'): ?>

            <div class="alert alert-success alert-dismissible fade show">

                <i class="bi bi-check-circle me-2"></i>

                El boletín se eliminó correctamente.

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>

            </div>

        <?php endif; ?>


        <!-- MENSAJES DE ERROR -->

        <?php if ($error === 'numero_existente'): ?>

            <div class="alert alert-danger">

                <i class="bi bi-exclamation-triangle me-2"></i>

                Ya existe un boletín con ese número.

            </div>

        <?php elseif ($error === 'no_encontrado'): ?>

            <div class="alert alert-danger">

                El boletín solicitado no existe.

            </div>

        <?php elseif ($error !== ''): ?>

            <div class="alert alert-danger">

                <i class="bi bi-exclamation-triangle me-2"></i>

                Ocurrió un error al realizar la operación.

            </div>

        <?php endif; ?>


        <!-- TABLA -->

        <div class="card shadow-sm">

            <div class="card-header">

                <h5 class="mb-0">

                    <i class="bi bi-journal-text me-2"></i>

                    Lista de boletines

                </h5>

            </div>


            <div class="card-body p-0">

                <?php if (empty($boletines)): ?>

                    <div class="text-center py-5">

                        <i
                            class="bi bi-journal-x fs-1 text-muted"
                        ></i>

                        <p class="text-muted mt-3 mb-3">

                            No existen boletines registrados.

                        </p>


                        <a
                            href="crear.php"
                            class="btn btn-primary"
                        >

                            <i class="bi bi-plus-lg me-1"></i>

                            Crear primer boletín

                        </a>

                    </div>

                <?php else: ?>

                    <div class="table-responsive">

                        <table
                            class="table table-hover align-middle mb-0"
                        >

                            <thead class="table-light">

                                <tr>

                                    <th>
                                        ID
                                    </th>

                                    <th>
                                        Número
                                    </th>

                                    <th>
                                        Portada
                                    </th>

                                    <th>
                                        Resumen
                                    </th>

                                    <th>
                                        Fecha
                                    </th>

                                    <th>
                                        Usuario
                                    </th>

                                    <th>
                                        PDF
                                    </th>

                                    <th class="text-center">
                                        Acciones
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                <?php foreach (
                                    $boletines
                                    as $boletin
                                ): ?>

                                    <tr>

                                        <!-- ID -->

                                        <td>
                                            <?= e($boletin['id']) ?>
                                        </td>


                                        <!-- NÚMERO -->

                                        <td>

                                            <span
                                                class="badge bg-primary"
                                            >
                                                Nº
                                                <?= e(
                                                    $boletin['numero_boletin']
                                                ) ?>
                                            </span>

                                        </td>


                                        <!-- PORTADA -->

                                        <td>

                                            <?php if (
                                                !empty(
                                                    $boletin['foto_portada']
                                                )
                                            ): ?>

                                                <img
                                                    src="<?= e('../../' . $boletin['foto_portada']) ?>"
                                                    alt="Portada del boletín"
                                                    class="img-thumbnail"
                                                    style="
                                                        width: 80px;
                                                        height: 100px;
                                                        object-fit: cover;
                                                    "
                                                >

                                            <?php else: ?>

                                                <span
                                                    class="text-muted"
                                                >
                                                    Sin portada
                                                </span>

                                            <?php endif; ?>

                                        </td>


                                        <!-- RESUMEN -->

                                        <td>

                                            <div
                                                style="
                                                    max-width: 300px;
                                                "
                                            >

                                                <?= e(
                                                    $boletin['resumen']
                                                ) ?>

                                            </div>

                                        </td>


                                        <!-- FECHA -->

                                        <td>

                                            <?= e(
                                                $boletin[
                                                    'fecha_publicacion'
                                                ]
                                            ) ?>

                                        </td>


                                        <!-- USUARIO -->

                                        <td>

                                            <?= e(
                                                $boletin[
                                                    'nombre_usuario'
                                                ] ??
                                                'Sin usuario'
                                            ) ?>

                                        </td>


                                        <!-- PDF -->

                                        <td>

                                            <?php if (
                                                !empty(
                                                    $boletin['archivo_pdf']
                                                )
                                            ): ?>

                                                <a
                                                    href="<?= e('../../' . $boletin['archivo_pdf']) ?>"
                                                    target="_blank"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Ver PDF"
                                                >

                                                    <i
                                                        class="bi bi-file-earmark-pdf"
                                                    ></i>

                                                </a>

                                            <?php else: ?>

                                                <span
                                                    class="text-muted"
                                                >
                                                    Sin PDF
                                                </span>

                                            <?php endif; ?>

                                        </td>


                                        <!-- ACCIONES -->

                                        <td>

                                            <div
                                                class="d-flex flex-wrap gap-1 justify-content-center"
                                            >

                                                <a
                                                    href="editar.php?id=<?= e($boletin['id']) ?>"
                                                    class="btn btn-sm btn-outline-primary"
                                                    title="Editar"
                                                >

                                                    <i
                                                        class="bi bi-pencil"
                                                    ></i>

                                                </a>


                                                <form
                                                    method="POST"
                                                    action="eliminar.php"
                                                    class="d-inline"
                                                    onsubmit="return confirm('¿Estás seguro de eliminar este boletín?');"
                                                >

                                                    <input
                                                        type="hidden"
                                                        name="id"
                                                        value="<?= e($boletin['id']) ?>"
                                                    >

                                                    <button
                                                        type="submit"
                                                        class="btn btn-sm btn-outline-danger"
                                                        title="Eliminar"
                                                    >

                                                        <i
                                                            class="bi bi-trash"
                                                        ></i>

                                                    </button>

                                                </form>

                                            </div>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </div>

</main>


<?php

require_once __DIR__ .
    '/../../includes/admin-footer.php';

?>