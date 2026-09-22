<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';


/*
 * Si ya existe una sesión de administrador,
 * no tiene sentido mostrar nuevamente el login.
 */
if (esAdministrador()) {
    header('Location: admin/dashboard.php');
    exit;
}


$error = '';


/*
 * Procesar formulario.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';


    /*
     * Validación básica.
     */
    if ($email === '' || $password === '') {

        $error = 'Debes ingresar el correo y la contraseña.';

    } else {

        try {

            /*
             * Buscar usuario por correo.
             */
            $sql = "
                SELECT
                    id,
                    nombres,
                    ap_paterno,
                    ap_materno,
                    email,
                    password_hash,
                    rol
                FROM usuarios
                WHERE email = :email
                LIMIT 1
            ";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':email' => $email
            ]);

            $usuario = $stmt->fetch();


            /*
             * Comprobar contraseña.
             */
            if (
                $usuario &&
                password_verify($password, $usuario['password_hash'])
            ) {

                /*
                 * Solamente permitimos entrar
                 * al panel si el usuario es admin.
                 */
                if ($usuario['rol'] !== 'admin') {

                    $error = 'No tienes permisos de administrador.';

                } else {

                    /*
                     * Regenerar ID de sesión
                     * después de autenticarse.
                     */
                    session_regenerate_id(true);


                    /*
                     * Guardar solamente los datos
                     * necesarios en la sesión.
                     */
                    $_SESSION['usuario'] = [
                        'id' => $usuario['id'],
                        'nombres' => $usuario['nombres'],
                        'ap_paterno' => $usuario['ap_paterno'],
                        'ap_materno' => $usuario['ap_materno'],
                        'email' => $usuario['email'],
                        'rol' => $usuario['rol']
                    ];


                    /*
                     * Enviar al panel.
                     */
                    header('Location: admin/dashboard.php');
                    exit;
                }

            } else {

                $error = 'Correo o contraseña incorrectos.';
            }

        } catch (PDOException $e) {

            $error = 'Ocurrió un error al procesar el inicio de sesión.';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Iniciar sesión - D&D Noticias</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e1e2f 0%, #2d2d44 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: #333;
        }

        h1 {
            color: #f0f0f0;
            margin-bottom: 25px;
            font-size: 2rem;
            text-align: center;
            letter-spacing: 1px;
        }

        .error {
            background-color: #ff4d4f;
            color: #fff;
            padding: 12px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
            width: 100%;
            max-width: 400px;
            text-align: center;
            font-size: 0.95rem;
            box-shadow: 0 4px 10px rgba(255, 77, 79, 0.3);
        }

        form {
            background-color: #ffffff;
            padding: 35px 30px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
            width: 100%;
            max-width: 400px;
        }

        form div {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
            font-size: 0.95rem;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            outline: none;
            font-family: inherit;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #6c63ff;
            box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.2);
        }

        button[type="submit"] {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #6c63ff 0%, #4834d4 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            letter-spacing: 0.5px;
            margin-top: 5px;
        }

        button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(108, 99, 255, 0.45);
        }

        button[type="submit"]:active {
            transform: translateY(0);
        }

        body > a {
            margin-top: 25px;
            color: #c9c9e0;
            text-decoration: none;
            font-size: 0.95rem;
            transition: color 0.3s ease;
        }

        body > a:hover {
            color: #ffffff;
            text-decoration: underline;
        }

        /* Elimina los <br> visuales dentro del form ya que usamos márgenes */
        form br {
            display: none;
        }
    </style>

</head>

<body>

    <h1>Iniciar sesión</h1>

    <?php if ($error !== ''): ?>

        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>


    <form method="POST" action="">

        <div>

            <label for="email">
                Correo electrónico
            </label>

            <input
                type="email"
                id="email"
                name="email"
                required
                autocomplete="email"
                placeholder="tu@correo.com"
            >

        </div>

        <div>

            <label for="password">
                Contraseña
            </label>

            <input
                type="password"
                id="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="••••••••"
            >

        </div>

        <button type="submit">
            Iniciar sesión
        </button>

    </form>

    <a href="index.php">
        Volver a la página principal
    </a>

</body>

</html>