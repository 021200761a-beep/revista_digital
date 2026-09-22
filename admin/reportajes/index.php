<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/funciones.php';

requerirAdministrador();

$tituloPagina = 'Reportajes';
$baseUrl = '../../';


/*
 * Obtener todos los reportajes.
 *
 * Se relacionan:
 * - reportajes
 * - autores
 * - usuarios
 */
$sql = "
    SELECT
        r.id,
        r.titulo,
        r.resumen_corto,
        r.foto_principal,
        r.fecha_publicacion,
        r.es_destacado,
        r.autor_id,
        r.usuario_id,
        r.created_at,
        r.updated_at,

        CONCAT(
            a.nombres,
            ' ',
            a.ap_paterno,
            CASE
                WHEN a.ap_materno IS NOT NULL
                     AND a.ap_materno <> ''
                THEN CONCAT(' ', a.ap_materno)
                ELSE ''
            END
        ) AS nombre_autor,

        CONCAT(
            u.nombres,
            ' ',
            u.ap_paterno
        ) AS nombre_usuario

    FROM reportajes r

    LEFT JOIN autores a
        ON r.autor_id = a.id

    LEFT JOIN usuarios u
        ON r.usuario_id = u.id

    ORDER BY r.id DESC
";


$stmt = $pdo->prepare($sql);
$stmt->execute();

$reportajes = $stmt->fetchAll();

require_once __DIR__ . '/../../includes/admin-header.php';
require_once __DIR__ . '/../../includes/admin-sidebar.php';

?>

<div class="main-wrapper">

    <header class="top-navbar">

        <div>

            <h5 class="mb-0">
                Reportajes
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


    <main class="page-content">

        <div class="container-fluid">


            <!-- CABECERA -->

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h2 class="fw-bold mb-1">
                        Reportajes
                    </h2>

                    <p class="text-muted mb-0">
                        Administración de los reportajes de D&D Noticias.
                    </p>

                </div>


                <a
                    href="crear.php"
                    class="btn btn-primary"
                >

                    <i class="bi bi-plus-circle me-2"></i>

                    Nuevo reportaje

                </a>

            </div>


            <!-- TABLA -->

            <div class="card dashboard-card">

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-hover align-middle">

                            <thead>

                                <tr>

                                    <th>
                                        ID
                                    </th>

                                    <th>
                                        Reportaje
                                    </th>

                                    <th>
                                        Autor
                                    </th>

                                    <th>
                                        Publicación
                                    </th>

                                    <th>
                                        Destacado
                                    </th>

                                    <th>
                                        Acciones
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                            <?php if (empty($reportajes)): ?>

                                <tr>

                                    <td
                                        colspan="6"
                                        class="text-center py-5"
                                    >

                                        <i
                                            class="bi bi-file-earmark-text fs-1 text-muted"
                                        ></i>

                                        <p class="mt-3 mb-0 text-muted">
                                            No existen reportajes registrados.
                                        </p>

                                    </td>

                                </tr>

                            <?php else: ?>


                                <?php foreach ($reportajes as $reportaje): ?>

                                    <tr>

                                        <td>
                                            <?= e($reportaje['id']) ?>
                                        </td>


                                        <td>

                                            <div class="d-flex align-items-center">

                                                <?php if (!empty($reportaje['foto_principal'])): ?>

                                                    <img
                                                        src="<?= e($reportaje['foto_principal']) ?>"
                                                        alt="<?= e($reportaje['titulo']) ?>"
                                                        style="
                                                            width:60px;
                                                            height:45px;
                                                            object-fit:cover;
                                                            border-radius:6px;
                                                        "
                                                    >

                                                <?php else: ?>

                                                    <div
                                                        class="bg-light d-flex align-items-center justify-content-center"
                                                        style="
                                                            width:60px;
                                                            height:45px;
                                                            border-radius:6px;
                                                        "
                                                    >

                                                        <i class="bi bi-image text-muted"></i>

                                                    </div>

                                                <?php endif; ?>


                                                <div class="ms-3">

                                                    <div class="fw-semibold">

                                                        <?= e($reportaje['titulo']) ?>

                                                    </div>


                                                    <?php if (!empty($reportaje['resumen_corto'])): ?>

                                                        <small class="text-muted">

                                                            <?= e(
                                                                mb_strimwidth(
                                                                    $reportaje['resumen_corto'],
                                                                    0,
                                                                    70,
                                                                    '...'
                                                                )
                                                            ) ?>

                                                        </small>

                                                    <?php endif; ?>

                                                </div>

                                            </div>

                                        </td>


                                        <td>

                                            <?= e(
                                                $reportaje['nombre_autor']
                                                ?? 'Sin autor'
                                            ) ?>

                                        </td>


                                        <td>

                                            <?= e(
                                                $reportaje['fecha_publicacion']
                                                ?? ''
                                            ) ?>

                                        </td>


                                        <td>

                                            <?php if ((int) $reportaje['es_destacado'] === 1): ?>

                                                <span class="badge bg-warning text-dark">

                                                    <i class="bi bi-star-fill me-1"></i>

                                                    Destacado

                                                </span>

                                            <?php else: ?>

                                                <span class="badge bg-secondary">

                                                    Normal

                                                </span>

                                            <?php endif; ?>

                                        </td>


                                        <td>

                                            <div class="btn-group">

                                                <a
                                                    href="editar.php?id=<?= e($reportaje['id']) ?>"
                                                    class="btn btn-sm btn-outline-primary"
                                                    title="Editar"
                                                >

                                                    <i class="bi bi-pencil"></i>

                                                </a>


                                                <form
                                                    method="POST"
                                                    action="eliminar.php"
                                                    class="d-inline"
                                                    onsubmit="return confirmarEliminacion();"
                                                >
                                                    <input
                                                        type="hidden"
                                                        name="id"
                                                        value="<?= (int) $reportaje['id'] ?>"
                                                    >

                                                    <button
                                                        type="submit"
                                                        class="btn btn-sm btn-outline-danger"
                                                    >
                                                        <i class="bi bi-trash me-1"></i>
                                                        
                                                    </button>
                                                </form>

                                            </div>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </main>

    <script>
    function confirmarEliminacion() {

        return confirm(
            '¿Estás seguro de que deseas eliminar este reportaje?\n\n' +
            'Esta acción no se puede deshacer.'
        );
    }
    </script>


    <?php

    require_once __DIR__ . '/../../includes/admin-footer.php';

    ?>

</div>