<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/funciones.php';

requerirAdministrador();

/*
|--------------------------------------------------------------------------
| Obtener noticias
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query("
    SELECT
        n.id,
        n.titulo,
        n.foto,
        n.link_externo,
        n.fecha_publicacion,
        n.usuario_id,
        CONCAT(
            u.nombres,
            ' ',
            u.ap_paterno
        ) AS nombre_usuario
    FROM noticias n
    LEFT JOIN usuarios u
        ON n.usuario_id = u.id
    ORDER BY n.fecha_publicacion DESC, n.id DESC
");

$noticias = $stmt->fetchAll();

$tituloPagina = 'Noticias';
$baseUrl = '../../';

require_once __DIR__ . '/../../includes/admin-header.php';
require_once __DIR__ . '/../../includes/admin-sidebar.php';

?>

<main class="admin-main">

    <div class="container-fluid py-4">

        <!-- Encabezado -->

        <div
            class="d-flex justify-content-between align-items-center mb-4"
        >

            <div>

                <h1 class="h3 mb-1">
                    Noticias
                </h1>

                <p class="text-muted mb-0">
                    Administra las noticias de la revista digital.
                </p>

            </div>

            <a
                href="crear.php"
                class="btn btn-primary"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Nueva noticia
            </a>

        </div>


        <!-- Mensajes -->

        <?php if (isset($_GET['creado'])): ?>

            <div class="alert alert-success alert-dismissible fade show">

                <i class="bi bi-check-circle me-2"></i>

                La noticia fue creada correctamente.

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>

            </div>

        <?php endif; ?>


        <?php if (isset($_GET['editado'])): ?>

            <div class="alert alert-success alert-dismissible fade show">

                <i class="bi bi-check-circle me-2"></i>

                La noticia fue actualizada correctamente.

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>

            </div>

        <?php endif; ?>


        <?php if (isset($_GET['eliminado'])): ?>

            <div class="alert alert-success alert-dismissible fade show">

                <i class="bi bi-check-circle me-2"></i>

                La noticia fue eliminada correctamente.

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>

            </div>

        <?php endif; ?>


        <?php if (isset($_GET['error'])): ?>

            <div class="alert alert-danger alert-dismissible fade show">

                <i class="bi bi-exclamation-triangle me-2"></i>

                Ocurrió un problema al procesar la noticia.

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>

            </div>

        <?php endif; ?>


        <!-- Tabla -->

        <div class="card shadow-sm">

            <div class="card-header">

                <div
                    class="d-flex justify-content-between align-items-center"
                >

                    <h5 class="mb-0">
                        Lista de noticias
                    </h5>

                    <span class="badge bg-primary">
                        <?= count($noticias) ?>
                        noticia(s)
                    </span>

                </div>

            </div>


            <div class="card-body p-0">

                <?php if (empty($noticias)): ?>

                    <div class="text-center py-5 px-3">

                        <i
                            class="bi bi-newspaper"
                            style="font-size: 3rem;"
                        ></i>

                        <h5 class="mt-3">
                            No hay noticias registradas
                        </h5>

                        <p class="text-muted">
                            Comienza creando la primera noticia.
                        </p>

                        <a
                            href="crear.php"
                            class="btn btn-primary"
                        >
                            <i class="bi bi-plus-lg me-1"></i>
                            Crear noticia
                        </a>

                    </div>

                <?php else: ?>

                    <div class="table-responsive">

                        <table
                            class="table table-hover align-middle mb-0"
                        >

                            <thead>

                                <tr>

                                    <th>
                                        ID
                                    </th>

                                    <th>
                                        Imagen
                                    </th>

                                    <th>
                                        Título
                                    </th>

                                    <th>
                                        Fecha
                                    </th>

                                    <th>
                                        Usuario
                                    </th>

                                    <th>
                                        Enlace
                                    </th>

                                    <th class="text-end">
                                        Acciones
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php foreach ($noticias as $noticia): ?>

                                    <tr>

                                        <td>
                                            <?= (int) $noticia['id'] ?>
                                        </td>


                                        <!-- Imagen -->

                                        <td>

                                            <?php if (!empty($noticia['foto'])): ?>

                                                <img
                                                    src="<?= e(
                                                        '../../' .
                                                        $noticia['foto']
                                                    ) ?>"
                                                    alt="Imagen de noticia"
                                                    class="img-thumbnail"
                                                    style="
                                                        width: 80px;
                                                        height: 60px;
                                                        object-fit: cover;
                                                    "
                                                >

                                            <?php else: ?>

                                                <div
                                                    class="bg-light rounded d-flex align-items-center justify-content-center"
                                                    style="
                                                        width: 80px;
                                                        height: 60px;
                                                    "
                                                >

                                                    <i
                                                        class="bi bi-image text-muted"
                                                    ></i>

                                                </div>

                                            <?php endif; ?>

                                        </td>


                                        <!-- Título -->

                                        <td>

                                            <div class="fw-semibold">
                                                <?= e(
                                                    $noticia['titulo']
                                                ) ?>
                                            </div>

                                        </td>


                                        <!-- Fecha -->

                                        <td>

                                            <?= e(
                                                substr(
                                                    (string)
                                                    $noticia[
                                                        'fecha_publicacion'
                                                    ],
                                                    0,
                                                    10
                                                )
                                            ) ?>

                                        </td>


                                        <!-- Usuario -->

                                        <td>

                                            <?= e(
                                                $noticia[
                                                    'nombre_usuario'
                                                ] ?? 'Sin usuario'
                                            ) ?>

                                        </td>


                                        <!-- Enlace -->

                                        <td>

                                            <?php if (
                                                !empty(
                                                    $noticia['link_externo']
                                                )
                                            ): ?>

                                                <a
                                                    href="<?= e(
                                                        $noticia[
                                                            'link_externo'
                                                        ]
                                                    ) ?>"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="btn btn-sm btn-outline-secondary"
                                                >
                                                    <i
                                                        class="bi bi-box-arrow-up-right"
                                                    ></i>
                                                </a>

                                            <?php else: ?>

                                                <span
                                                    class="text-muted"
                                                >
                                                    Sin enlace
                                                </span>

                                            <?php endif; ?>

                                        </td>


                                        <!-- Acciones -->

                                        <td>

                                            <div
                                                class="d-flex justify-content-end gap-1 flex-wrap"
                                            >

                                                <a
                                                    href="editar.php?id=<?= (int) $noticia['id'] ?>"
                                                    class="btn btn-sm btn-outline-primary"
                                                >
                                                    <i
                                                        class="bi bi-pencil me-1"
                                                    ></i>
                                                    Editar
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
                                                        value="<?= (int) $noticia['id'] ?>"
                                                    >

                                                    <button
                                                        type="submit"
                                                        class="btn btn-sm btn-outline-danger"
                                                    >
                                                        <i
                                                            class="bi bi-trash me-1"
                                                        ></i>
                                                        Eliminar
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


<script>

function confirmarEliminacion() {

    return confirm(
        '¿Estás seguro de que deseas eliminar esta noticia?\n\n' +
        'Esta acción no se puede deshacer.'
    );
}

</script>


<?php require_once __DIR__ . '/../../includes/admin-footer.php'; ?>