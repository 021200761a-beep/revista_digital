<?php

/**
 * Sistema de autenticación
 * Revista Digital
 */

/*
 * Iniciar sesión solamente si todavía
 * no existe una sesión activa.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/**
 * Verifica si existe una sesión iniciada.
 */
function estaAutenticado(): bool
{
    return isset($_SESSION['usuario']);
}


/**
 * Verifica si el usuario actual tiene
 * permisos de administrador.
 */
function esAdministrador(): bool
{
    return isset($_SESSION['usuario'])
        && isset($_SESSION['usuario']['rol'])
        && $_SESSION['usuario']['rol'] === 'admin';
}


/**
 * Obliga al usuario a iniciar sesión.
 */
function requerirAutenticacion(): void
{
    if (!estaAutenticado()) {
        header('Location: ../login.php');
        exit;
    }
}


/**
 * Obliga al usuario a tener rol admin.
 */
function requerirAdministrador(): void
{
    if (!esAdministrador()) {
        header('Location: ../login.php');
        exit;
    }
}