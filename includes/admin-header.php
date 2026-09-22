<?php

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/funciones.php';

requerirAdministrador();

$usuario = $_SESSION['usuario'];

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title><?= e($tituloPagina ?? 'Administración') ?> - D&D Noticias</title>

    <!-- Bootstrap 5 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >

    <style>

        body {
            background-color: #f5f7fb;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .admin-wrapper {
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: #212529;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 1000;
        }

        .sidebar-brand {
            height: 70px;
            display: flex;
            align-items: center;
            padding: 0 24px;
            color: #fff;
            font-size: 20px;
            font-weight: 700;
            text-decoration: none;
            border-bottom: 1px solid rgba(255,255,255,.1);
        }

        .sidebar-brand span {
            color: #0d6efd;
            margin-right: 6px;
        }

        .sidebar-menu {
            padding: 20px 12px;
        }

        .sidebar-section {
            color: #8d96a0;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 10px 14px;
            margin-top: 10px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #ced4da;
            text-decoration: none;
            padding: 12px 14px;
            border-radius: 7px;
            margin-bottom: 4px;
            transition: .2s;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: #0d6efd;
            color: #fff;
        }

        .sidebar-menu i {
            font-size: 18px;
        }

        .main-wrapper {
            margin-left: 250px;
            min-height: 100vh;
            width: calc(100% - 250px);
            box-sizing: border-box;
        }

        .top-navbar {
            height: 70px;
            background: #fff;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
        }

        .page-content {
            padding: 30px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #0d6efd;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .dashboard-card {
            border: 0;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0,0,0,.05);
        }

        .dashboard-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            background: #e7f1ff;
            color: #0d6efd;
        }

        .admin-footer {
            padding: 20px 30px;
            color: #6c757d;
            font-size: 14px;
        }

        @media (max-width: 991px) {

            .sidebar {
                width: 250px;
            }

            .main-wrapper {
                margin-left: 250px;
                width: calc(100% - 250px);
            }

        }

        @media (max-width: 767px) {

            .sidebar {
                position: relative;
                width: 100%;
                min-height: auto;
            }

            .main-wrapper {
                margin-left: 0;
                width: 100%;
            }

            .admin-main {
                margin-left: 0;
                width: 100%;
            }

            .page-content {
                padding: 20px 15px;
            }

        }
        /* =========================================================
   RESPONSIVE DEL PANEL ADMINISTRATIVO
   ========================================================= */

/* Contenido principal cuando el sidebar está fijo */
.admin-main {
    margin-left: 250px;
    min-height: 100vh;
    width: calc(100% - 250px);
    box-sizing: border-box;
}

/* Evita que elementos anchos rompan la pantalla */
.admin-main img {
    max-width: 100%;
    height: auto;
}

/* Tablas responsivas */
.admin-main .table-responsive {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

/* Formularios */
.admin-main .form-control,
.admin-main .form-select {
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
}

/* Cards */
.admin-main .card {
    max-width: 100%;
}

/* Botones y grupos de botones */
.admin-main .d-flex {
    flex-wrap: wrap;
}


/* =========================================================
   TABLET
   ========================================================= */

@media (max-width: 991.98px) {

    .admin-main {
        margin-left: 0;
        width: 100%;
    }

}


/* =========================================================
   CELULAR
   ========================================================= */

@media (max-width: 767.98px) {

    .admin-main {
        margin-left: 0;
        width: 100%;
        padding-left: 0;
        padding-right: 0;
    }

    .admin-main .container-fluid {
        padding-left: 15px;
        padding-right: 15px;
    }

    /* Encabezados de las páginas */
    .admin-main .d-flex.justify-content-between {
        flex-direction: column;
        align-items: stretch !important;
        gap: 15px;
    }

    /* Botones */
    .admin-main .btn {
        max-width: 100%;
    }

    /* Filas del formulario */
    .admin-main .row {
        margin-left: 0;
        margin-right: 0;
    }

    /* Card */
    .admin-main .card-body {
        padding: 15px;
    }

    /* Títulos */
    .admin-main h1 {
        font-size: 1.5rem;
    }

    .admin-main h2 {
        font-size: 1.35rem;
    }

    .admin-main h3 {
        font-size: 1.2rem;
    }

}


/* =========================================================
   CELULAR PEQUEÑO
   ========================================================= */

@media (max-width: 575.98px) {

    .admin-main .container-fluid {
        padding-left: 10px;
        padding-right: 10px;
    }

    .admin-main .card-body {
        padding: 12px;
    }

    .admin-main .btn {
        width: 100%;
    }

}


    </style>

</head>

<body>

<div class="admin-wrapper">