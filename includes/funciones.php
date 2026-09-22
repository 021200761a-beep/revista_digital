<?php

/**
 * Funciones generales
 * Revista Digital
 */


/**
 * Escapar texto para mostrarlo
 * de forma segura en HTML.
 */
function e($valor): string
{
    return htmlspecialchars(
        (string) $valor,
        ENT_QUOTES,
        'UTF-8'
    );
}


/**
 * Obtener el nombre completo
 * del usuario autenticado.
 */
function nombreUsuarioActual(): string
{
    if (!isset($_SESSION['usuario'])) {
        return '';
    }

    $usuario = $_SESSION['usuario'];

    $nombre = $usuario['nombres'] ?? '';
    $paterno = $usuario['ap_paterno'] ?? '';
    $materno = $usuario['ap_materno'] ?? '';

    return trim(
        $nombre . ' ' . $paterno . ' ' . $materno
    );
}