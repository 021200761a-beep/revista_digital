<?php

require_once __DIR__ . '/../includes/auth.php';

requerirAdministrador();

header('Location: dashboard.php');
exit;