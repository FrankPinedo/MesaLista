<?php
class ErrorController
{
    public function index($errorCode = '500')
    {
        $errors = [
            '404' => 'Oops! La página que estás buscando no existe.',
            '403' => 'Acceso denegado. No tienes permisos para ver esta página.',
            '500' => 'Error interno del servidor. Algo salió mal.',
            '401' => 'No autorizado. Debes iniciar sesión.',
        ];

        $errorMessage = $errors[$errorCode] ?? 'Ha ocurrido un error inesperado.';

        require_once __DIR__ . '/../views/error.php';
    }
}
