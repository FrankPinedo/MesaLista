<?php
class LoginController
{
    public function index()
    {
        require_once __DIR__ . '/../../config/config.php';
        session_start();

        unset($_SESSION['usuario_recuperar']);
        unset($_SESSION['token_recuperar']);

        $ip = $_SERVER['REMOTE_ADDR'];
        $_SESSION['ip_actual'] = $ip;

        $rateLimitKey = 'login_attempts_' . $ip;
        $timeFrame = 120;
        $maxAttempts = 5;

        $blocked = false;
        $remainingTime = 0;

        if (
            isset($_SESSION[$rateLimitKey]) &&
            $_SESSION[$rateLimitKey]['count'] >= $maxAttempts &&
            $_SESSION[$rateLimitKey]['time'] + $timeFrame > time()
        ) {

            $blocked = true;
            $remainingTime = $_SESSION[$rateLimitKey]['time'] + $timeFrame - time();
            $_SESSION['error_login'] = "Demasiados intentos. Por favor, espera " . ceil($remainingTime / 60) . " minutos.";
        } elseif (isset($_SESSION[$rateLimitKey]) && $_SESSION[$rateLimitKey]['time'] + $timeFrame <= time()) {
            unset($_SESSION[$rateLimitKey]);
            unset($_SESSION['error_login']);
        }

        require_once __DIR__ . '/../views/login/login.php';
    }

    public function validar()
    {
        require_once __DIR__ . '/../../config/config.php';
        require_once __DIR__ . '/../models/UsuarioModel.php';
        session_start();

        $ip = $_SERVER['REMOTE_ADDR'];
        $rateLimitKey = 'login_attempts_' . $ip;
        $maxAttempts = 5;
        $timeFrame = 120;

        if (isset($_SESSION[$rateLimitKey])) {
            if (
                $_SESSION[$rateLimitKey]['count'] >= $maxAttempts &&
                $_SESSION[$rateLimitKey]['time'] + $timeFrame > time()
            ) {

                $remainingTime = $_SESSION[$rateLimitKey]['time'] + $timeFrame - time();
                $_SESSION['error_login'] = "Demasiados intentos. Por favor, espera " . ceil($remainingTime / 60) . " minutos.";
                header("Location: " . BASE_URL . "/login");
                exit;
            } elseif ($_SESSION[$rateLimitKey]['time'] + $timeFrame <= time()) {
                unset($_SESSION[$rateLimitKey]);
                unset($_SESSION['error_login']);
            }
        }

        $correo = $_POST['correo'];
        $clave = $_POST['clave'];

        if (empty($correo) || empty($clave)) {
            $_SESSION['error_login'] = "Correo y contraseña son requeridos.";
            header("Location: " . BASE_URL . "/login");
            exit;
        }

        if (preg_match('/\s/', $correo)) {
            $_SESSION['error_login'] = "El correo no debe contener espacios.";
            header("Location: " . BASE_URL . "/login");
            exit;
        }

        try {
            $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $usuarioModel = new UsuarioModel($pdo);
            $user = $usuarioModel->obtenerUsuarioPorCorreo($correo);

    if ($user && password_verify($clave, $user['contraseña'])) {
    $_SESSION['usuario'] = $user;
    $_SESSION['rol'] = $user['rol'];  // Agregado
    unset($_SESSION[$rateLimitKey]);

    switch ($user['rol']) {
        case 'admin':
            header("Location: " . BASE_URL . "/admin");
            break;
        case 'mozo':
            header("Location: " . BASE_URL . "/mozo");
            break;
        case 'cocinero':
            header("Location: " . BASE_URL . "/cocina");
            break;
        default:
            echo "Rol no reconocido.";
    }
    exit;
}

            else {
                if (!isset($_SESSION[$rateLimitKey])) {
                    $_SESSION[$rateLimitKey] = ['count' => 1, 'time' => time()];
                } else {
                    $_SESSION[$rateLimitKey]['count']++;
                }

                $_SESSION['error_login'] = "Correo o contraseña incorrectos.";

                if ($_SESSION[$rateLimitKey]['count'] >= $maxAttempts) {
                    $remainingTime = $timeFrame;
                    $_SESSION['error_login'] = "Demasiados intentos. Por favor, espera " . ceil($remainingTime / 60) . " minutos.";
                }

                header("Location: " . BASE_URL . "/login");
                exit;
            }
        } catch (PDOException $e) {
            $_SESSION['error_login'] = "Error en el sistema. Por favor, intente más tarde.";
            header("Location: " . BASE_URL . "/login");
            exit;
        }
    }
}
