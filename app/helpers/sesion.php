<?php
function verificarSesion()
{
    session_start();

    if (!isset($_SESSION['usuario'])) {
        require_once __DIR__ . '/../controllers/ErrorController.php';
        $error = new ErrorController();
        $error->index('401');
        exit();
    }

    return $_SESSION['usuario'];
}