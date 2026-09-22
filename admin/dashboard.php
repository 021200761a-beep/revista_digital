<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/funciones.php';

requerirAdministrador();


/*
 * Título de la página.
 */
$tituloPagina = 'Dashboard';


/*
 * Ruta base del proyecto.
 *
 * Esto permite que los enlaces del sidebar
 * funcionen correctamente.
 */
$baseUrl = '../';


/*
 * Obtener estadísticas generales.
 */
$totalReportajes = 0;
$totalNoticias = 0;
$totalBoletines = 0;
$totalPodcasts = 0;
$totalVideos = 0;
$totalUsuarios = 0;
$totalAutores = 0;


try {

    $totalReportajes = (int) $pdo
        ->query("SELECT COUNT(*) FROM reportajes")
        ->fetchColumn();


    $totalNoticias = (int) $pdo
        ->query("SELECT COUNT(*) FROM noticias")
        ->fetchColumn();


    $totalBoletines = (int) $pdo
        ->query("SELECT COUNT(*) FROM boletines")
        ->fetchColumn();


    $totalPodcasts = (int) $pdo
        ->query("SELECT COUNT(*) FROM podcasts")
        ->fetchColumn();


    $totalVideos = (int) $pdo
        ->query("SELECT COUNT(*) FROM videos")
        ->fetchColumn();


    $totalUsuarios = (int) $pdo
        ->query("SELECT COUNT(*) FROM usuarios")
        ->fetchColumn();


    $totalAutores = (int) $pdo
        ->query("SELECT COUNT(*) FROM autores")
        ->fetchColumn();

} catch (PDOException $e) {

    /*
     * En esta fase mostramos un mensaje sencillo.
     * Más adelante implementaremos un sistema
     * de mensajes/errores más completo.
     */
    $errorDashboard =
        'No se pudieron cargar algunas estadísticas.';

}


/*
 * Cargar header.
 */
require_once __DIR__ . '/../includes/admin-header.php';


/*
 * Cargar sidebar.
 */
require_once __DIR__ . '/../includes/admin-sidebar.php';

?>

<div class="main-wrapper">

    <!-- NAVBAR -->

    <header class="top-navbar">

        <div>

            <h5 class="mb-0">
                Dashboard
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


            <!-- TÍTULO -->

            <div class="mb-4">

                <h2 class="fw-bold">
                    Dashboard
                </h2>

                <p class="text-muted mb-0">

                    Resumen general de D&D Noticias.

                </p>

            </div>


            <?php if (isset($errorDashboard)): ?>

                <div class="alert alert-warning">

                    <?= e($errorDashboard) ?>

                </div>

            <?php endif; ?>


            <!-- TARJETAS -->

            <div class="row g-4 mb-4">


                <!-- REPORTAJES -->

                <div class="col-xl-3 col-md-6">

                    <div class="card dashboard-card h-100">

                        <div class="card-body">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <p class="text-muted mb-2">
                                        Reportajes
                                    </p>

                                    <h3 class="fw-bold mb-0">

                                        <?= e($totalReportajes) ?>

                                    </h3>

                                </div>

                                <div class="dashboard-icon">

                                    <i class="bi bi-file-earmark-text"></i>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- NOTICIAS -->

                <div class="col-xl-3 col-md-6">

                    <div class="card dashboard-card h-100">

                        <div class="card-body">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <p class="text-muted mb-2">
                                        Noticias
                                    </p>

                                    <h3 class="fw-bold mb-0">

                                        <?= e($totalNoticias) ?>

                                    </h3>

                                </div>

                                <div class="dashboard-icon">

                                    <i class="bi bi-newspaper"></i>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- BOLETINES -->

                <div class="col-xl-3 col-md-6">

                    <div class="card dashboard-card h-100">

                        <div class="card-body">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <p class="text-muted mb-2">
                                        Boletines
                                    </p>

                                    <h3 class="fw-bold mb-0">

                                        <?= e($totalBoletines) ?>

                                    </h3>

                                </div>

                                <div class="dashboard-icon">

                                    <i class="bi bi-journal-text"></i>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- USUARIOS -->

                <div class="col-xl-3 col-md-6">

                    <div class="card dashboard-card h-100">

                        <div class="card-body">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <p class="text-muted mb-2">
                                        Usuarios
                                    </p>

                                    <h3 class="fw-bold mb-0">

                                        <?= e($totalUsuarios) ?>

                                    </h3>

                                </div>

                                <div class="dashboard-icon">

                                    <i class="bi bi-people"></i>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- SEGUNDA FILA -->

            <div class="row g-4">


                <!-- CONTENIDO -->

                <div class="col-lg-8">

                    <div class="card dashboard-card">

                        <div class="card-body">

                            <h5 class="fw-bold mb-4">
                                Contenido de la revista
                            </h5>


                            <div class="table-responsive">

                                <table class="table align-middle">

                                    <thead>

                                        <tr>

                                            <th>
                                                Tipo
                                            </th>

                                            <th>
                                                Cantidad
                                            </th>

                                            <th>
                                                Estado
                                            </th>

                                        </tr>

                                    </thead>


                                    <tbody>

                                        <tr>

                                            <td>
                                                <i class="bi bi-mic me-2"></i>
                                                Podcasts
                                            </td>

                                            <td>
                                                <?= e($totalPodcasts) ?>
                                            </td>

                                            <td>

                                                <span class="badge bg-success">
                                                    Registrados
                                                </span>

                                            </td>

                                        </tr>


                                        <tr>

                                            <td>
                                                <i class="bi bi-camera-video me-2"></i>
                                                Videos
                                            </td>

                                            <td>
                                                <?= e($totalVideos) ?>
                                            </td>

                                            <td>

                                                <span class="badge bg-success">
                                                    Registrados
                                                </span>

                                            </td>

                                        </tr>


                                        <tr>

                                            <td>
                                                <i class="bi bi-person-vcard me-2"></i>
                                                Autores
                                            </td>

                                            <td>
                                                <?= e($totalAutores) ?>
                                            </td>

                                            <td>

                                                <span class="badge bg-success">
                                                    Registrados
                                                </span>

                                            </td>

                                        </tr>

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- ACCIONES -->

                <div class="col-lg-4">

                    <div class="card dashboard-card">

                        <div class="card-body">

                            <h5 class="fw-bold mb-4">
                                Acciones rápidas
                            </h5>


                            <div class="d-grid gap-2">


                                <a
                                    href="<?= e($baseUrl) ?>admin/reportajes/"
                                    class="btn btn-primary"
                                >

                                    <i class="bi bi-plus-circle me-2"></i>

                                    Gestionar reportajes

                                </a>


                                <a
                                    href="<?= e($baseUrl) ?>admin/noticias/"
                                    class="btn btn-outline-primary"
                                >

                                    <i class="bi bi-newspaper me-2"></i>

                                    Gestionar noticias

                                </a>


                                <a
                                    href="<?= e($baseUrl) ?>admin/boletines/"
                                    class="btn btn-outline-primary"
                                >

                                    <i class="bi bi-journal-text me-2"></i>

                                    Gestionar boletines

                                </a>


                                <a
                                    href="<?= e($baseUrl) ?>admin/usuarios/"
                                    class="btn btn-outline-secondary"
                                >

                                    <i class="bi bi-people me-2"></i>

                                    Gestionar usuarios

                                </a>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


        </div>

    </main>


    <!-- FOOTER -->

    <?php

    require_once __DIR__ . '/../includes/admin-footer.php';

    ?>

</div>