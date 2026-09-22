<?php

$paginaActual = basename($_SERVER['PHP_SELF']);

?>

<aside class="sidebar">

    <a href="<?= e($baseUrl ?? '../') ?>admin/dashboard.php"
       class="sidebar-brand">

        <span>D&D</span> Noticias

    </a>


    <nav class="sidebar-menu">

        <div class="sidebar-section">
            Principal
        </div>


        <a
            href="<?= e($baseUrl ?? '../') ?>admin/dashboard.php"
            class="<?= $paginaActual === 'dashboard.php' ? 'active' : '' ?>"
        >

            <i class="bi bi-speedometer2"></i>

            <span>Dashboard</span>

        </a>


        <div class="sidebar-section">
            Contenido
        </div>


        <a href="<?= e($baseUrl ?? '../') ?>admin/reportajes/">

            <i class="bi bi-file-earmark-text"></i>

            <span>Reportajes</span>

        </a>


        <a href="<?= e($baseUrl ?? '../') ?>admin/noticias/">

            <i class="bi bi-newspaper"></i>

            <span>Noticias</span>

        </a>


        <a href="<?= e($baseUrl ?? '../') ?>admin/boletines/">

            <i class="bi bi-journal-text"></i>

            <span>Boletines</span>

        </a>


        <a href="<?= e($baseUrl ?? '../') ?>admin/podcasts/">

            <i class="bi bi-mic"></i>

            <span>Podcasts</span>

        </a>


        <a href="<?= e($baseUrl ?? '../') ?>admin/videos/">

            <i class="bi bi-camera-video"></i>

            <span>Videos</span>

        </a>


        <div class="sidebar-section">
            Administración
        </div>


        <a href="<?= e($baseUrl ?? '../') ?>admin/autores/">

            <i class="bi bi-person-vcard"></i>

            <span>Autores</span>

        </a>


        <a href="<?= e($baseUrl ?? '../') ?>admin/usuarios/">

            <i class="bi bi-people"></i>

            <span>Usuarios</span>

        </a>


        <div class="sidebar-section">
            Sistema
        </div>


        <a href="<?= e($baseUrl ?? '../') ?>index.php"
           target="_blank">

            <i class="bi bi-globe"></i>

            <span>Ver página web</span>

        </a>


        <a href="<?= e($baseUrl ?? '../') ?>logout.php">

            <i class="bi bi-box-arrow-right"></i>

            <span>Cerrar sesión</span>

        </a>

    </nav>

</aside>