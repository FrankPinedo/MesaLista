<?php
class RecuperarController
{
    public function index()
    {
        require_once __DIR__ . '/../../config/config.php';
        session_start();

        $ip = $_SERVER['REMOTE_ADDR'];
        $_SESSION['ip_actual'] = $ip;

        $rateLimitKey = 'login_attempts_' . $ip;
        $timeFrame = 100;
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
            $_SESSION['error_recuperar'] = "Demasiados intentos. Por favor, espera " . ceil($remainingTime / 60) . " minutos.";
        } elseif (isset($_SESSION[$rateLimitKey]) && $_SESSION[$rateLimitKey]['time'] + $timeFrame <= time()) {
            unset($_SESSION[$rateLimitKey]);
            unset($_SESSION['error_recuperar']);
        }

        require_once __DIR__ . '/../views/login/recuperar.php';
    }
    public function validar()
    {
        require_once __DIR__ . '/../../config/config.php';
        require_once __DIR__ . '/../models/UsuarioModel.php';
        session_start();


        $ip = $_SERVER['REMOTE_ADDR'];
        $rateLimitKey = 'login_attempts_' . $ip;
        $maxAttempts = 5;
        $timeFrame = 100;

        if (isset($_SESSION[$rateLimitKey])) {
            if (
                $_SESSION[$rateLimitKey]['count'] >= $maxAttempts &&
                $_SESSION[$rateLimitKey]['time'] + $timeFrame > time()
            ) {

                $remainingTime = $_SESSION[$rateLimitKey]['time'] + $timeFrame - time();
                $_SESSION['error_recuperar'] = "Demasiados intentos. Por favor, espera " . ceil($remainingTime / 60) . " minutos.";
                header("Location: " . BASE_URL . "/recuperar");
                exit;
            } elseif ($_SESSION[$rateLimitKey]['time'] + $timeFrame <= time()) {
                unset($_SESSION[$rateLimitKey]);
                unset($_SESSION['error_recuperar']);
            }
        }

        $dni = $_POST['dni'];
        $correo = $_POST['correo'];

        if (!preg_match('/^\d{8}$/', $dni)) {
            $_SESSION['error_recuperar'] = "El DNI debe tener exactamente 8 números.";
            header("Location: " . BASE_URL . "/recuperar");
            exit;
        }

        try {
            $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $usuarioModel = new UsuarioModel($pdo);
            $usuario = $usuarioModel->obtenerPorDniYCorreo($dni, $correo);

            if ($usuario) {

                $_SESSION['usuario_recuperar'] = $usuario;
                $_SESSION['token_recuperar'] = bin2hex(random_bytes(16));

                unset($_SESSION[$rateLimitKey]);

                header("Location: " . BASE_URL . "/restablecer");

                exit;
                
            } else {
                if (!isset($_SESSION[$rateLimitKey])) {
                    $_SESSION[$rateLimitKey] = ['count' => 1, 'time' => time()];
                } else {
                    $_SESSION[$rateLimitKey]['count']++;
                }

                $_SESSION['error_recuperar'] = "DNI o correo incorrectos.";

                if ($_SESSION[$rateLimitKey]['count'] >= $maxAttempts) {
                    $remainingTime = $timeFrame;
                    $_SESSION['error_recuperar'] = "Demasiados intentos. Por favor, espera " . ceil($remainingTime / 60) . " minutos.";
                }
                header("Location: " . BASE_URL . "/recuperar");
                exit;
            }
        } catch (PDOException $e) {
            $_SESSION['error_recuperar'] = "Error en el sistema. Por favor, intente más tarde.";
            header("Location: " . BASE_URL . "/recuperar");
            exit;
        }
    }
}
