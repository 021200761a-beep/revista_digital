<?php

require_once __DIR__ . '/config/database.php';

/*
 * Datos del administrador
 */
$nombres = 'Administrador';
$ap_paterno = 'Principal';
$ap_materno = '';
$email = 'admin@revista.com';
$password = 'Admin12345';

/*
 * Generar contraseña segura
 */
$password_hash = password_hash($password, PASSWORD_DEFAULT);

try {

    /*
     * Verificar si el correo ya existe
     */
    $sql = "SELECT id FROM usuarios WHERE email = :email LIMIT 1";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':email' => $email
    ]);

    $usuarioExistente = $stmt->fetch();

    if ($usuarioExistente) {

        echo '<h2>⚠️ El usuario ya existe</h2>';

        echo '<p>ID: '
            . htmlspecialchars($usuarioExistente['id'])
            . '</p>';

        echo '<p>Correo: '
            . htmlspecialchars($email)
            . '</p>';

        exit;
    }


    /*
     * Insertar administrador
     */
    $sql = "
        INSERT INTO usuarios
        (
            nombres,
            ap_paterno,
            ap_materno,
            email,
            password_hash,
            rol
        )
        VALUES
        (
            :nombres,
            :ap_paterno,
            :ap_materno,
            :email,
            :password_hash,
            'admin'
        )
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':nombres' => $nombres,
        ':ap_paterno' => $ap_paterno,
        ':ap_materno' => $ap_materno,
        ':email' => $email,
        ':password_hash' => $password_hash
    ]);


    echo '<h1>✅ Administrador creado correctamente</h1>';

    echo '<p><strong>Correo:</strong> '
        . htmlspecialchars($email)
        . '</p>';

    echo '<p><strong>Contraseña:</strong> '
        . htmlspecialchars($password)
        . '</p>';

    echo '<p><strong>Rol:</strong> admin</p>';

    echo '<hr>';

    echo '<p>';
    echo '<strong>IMPORTANTE:</strong> elimina el archivo ';
    echo '<code>crear_admin.php</code> después de realizar esta prueba.';
    echo '</p>';

    echo '<p>';
    echo '<a href="login.php">Ir al inicio de sesión</a>';
    echo '</p>';

} catch (PDOException $e) {

    echo '<h1>❌ Error</h1>';

    echo '<p>'
        . htmlspecialchars($e->getMessage())
        . '</p>';
}