<?php

class RestablecerController
{
    public function index()
    {
        session_start();
        
        session_regenerate_id(true);
        
        $usuario = $_SESSION['usuario_recuperar'];
        $token_recuperar = $_SESSION['token_recuperar'];

        if (!isset($_SESSION['usuario_recuperar']) || !isset($_SESSION['token_recuperar'])) {
            header("Location: " . BASE_URL . "/login");
            exit;
        }

        require_once __DIR__ . '/../views/login/restablecer.php';
    }

    public function actualizar()
    {
        require_once __DIR__ . '/../../config/config.php';
        require_once __DIR__ . '/../models/UsuarioModel.php';
        session_start();

        if (!isset($_SESSION['usuario_recuperar']) || !isset($_SESSION['token_recuperar'])) {
            header("Location: " . BASE_URL . "/login");
            exit;
        }

        $dni = trim($_POST['dni']);
        $digito = trim($_POST['digito']);
        $nueva_contraseña = $_POST['nueva_contraseña'];
        $confirmar_contraseña = $_POST['confirmar_contraseña'];
        $token_recuperar = $_POST['token_recuperar'];

        // Validación de token
        if ($token_recuperar !== $_SESSION['token_recuperar']) {
            $_SESSION['error_restaurar'] = "Token no válido. Por favor, intente de nuevo.";
            header("Location: " . BASE_URL . "/restablecer");
            exit;
        }

        // Validación de DNI y dígito
        if (!preg_match('/^\d{8}$/', $dni) || !preg_match('/^\d{1}$/', $digito)) {
            $_SESSION['error_restaurar'] = "DNI o dígito verificador inválido.";
            header("Location: " . BASE_URL . "/restablecer");
            exit;
        }

        // Verificación contra sesión del usuario recuperado
        $usuario_recuperado = $_SESSION['usuario_recuperar'];
        if ($dni !== $usuario_recuperado['dni'] || $digito !== $usuario_recuperado['digito']) {
            $_SESSION['error_restaurar'] = "El DNI o dígito verificador no coinciden con el usuario verificado.";
            header("Location: " . BASE_URL . "/restablecer");
            exit;
        }

        // Verificación de coincidencia de contraseñas
        if ($nueva_contraseña !== $confirmar_contraseña) {
            $_SESSION['error_restaurar'] = "Las contraseñas no coinciden.";
            header("Location: " . BASE_URL . "/restablecer");
            exit;
        }

        // Validación de requisitos de contraseña
        if (
            strlen($nueva_contraseña) < 8 ||
            !preg_match('/[A-Z]/', $nueva_contraseña) ||
            !preg_match('/[a-z]/', $nueva_contraseña) ||
            !preg_match('/[0-9]/', $nueva_contraseña) ||
            !preg_match('/[@#$%^&*(),.?":{}|<>]/', $nueva_contraseña)
        ) {
            $_SESSION['error_restaurar'] = "La contraseña no cumple con los requisitos de seguridad.";
            header("Location: " . BASE_URL . "/restablecer");
            exit;
        }

        try {
            $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $usuarioModel = new UsuarioModel($pdo);
            $usuario_actual = $usuarioModel->obtenerPorDni($dni);

            if (!$usuario_actual) {
                $_SESSION['error_restaurar'] = "Usuario no encontrado.";
                header("Location: " . BASE_URL . "/restablecer");
                exit;
            }

            if (password_verify($nueva_contraseña, $usuario_actual['contraseña'])) {
                $_SESSION['error_restaurar'] = "La nueva contraseña no puede ser igual a la anterior.";
                header("Location: " . BASE_URL . "/restablecer");
                exit;
            }

            $hashed_password = password_hash($nueva_contraseña, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("UPDATE usuario SET contraseña = :clave WHERE dni = :dni");
            $stmt->execute([
                'clave' => $hashed_password,
                'dni' => $dni
            ]);

            unset($_SESSION['usuario_recuperar']);
            unset($_SESSION['token_recuperar']);

            $_SESSION['mensaje_confirmacion'] = "Contraseña actualizada exitosamente.";
            header("Location: " . BASE_URL . "/login");
            exit;
        } catch (PDOException $e) {
            echo "Error al actualizar la contraseña: " . $e->getMessage();
        }
    }

    public function cancelar()
    {
        session_start();
        unset($_SESSION['usuario_recuperar']);
        unset($_SESSION['token_recuperar']);

        header("Location: " . BASE_URL . "/login");
        exit;
    }
}
