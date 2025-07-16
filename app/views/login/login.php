<?php if (!defined('BASE_URL')) require_once __DIR__ . '/../../../config/config.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MesaLista - Login</title>

    <!-- BOOTSTRAP -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7"
        crossorigin="anonymous" />
    <!-- LOGO -->
    <link rel="icon" href="<?= BASE_URL ?>/public/assets/img/logo_1.png" />

    <!-- ESTILOS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/login/inicio.css" />


    <!-- ICONOS -->
    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</head>

<body>
    <section>
        <div class="container d-flex justify-content-center align-items-center">
            <div
                class="ventana d-flex flex-column justify-content-center align-items-center m-0 gap-1">
                <div class="ventana_1 d-flex flex-column bg-white p-3">
                    <div class="header_1 d-flex flex-column align-items-center">
                        <img src="<?= BASE_URL ?>/public/assets/img/logo_1.png" alt="" />
                        <span class="h3">Inicia sesión</span>
                    </div>
                    <div class="body_1 px-5 py-4">
                        <form
                            method="POST"
                            action="<?= BASE_URL ?>/login/validar"
                            class="w-100 d-flex justify-content-center align-items-center flex-column">

                            <?php if (isset($_SESSION['mensaje_confirmacion'])): ?>
                                <div id="mensaje-confirmacion" class="alert alert-success text-center w-100">
                                    <?= $_SESSION['mensaje_confirmacion']; ?>
                                </div>
                                <?php unset($_SESSION['mensaje_confirmacion']); ?>
                            <?php endif; ?>

                            <?php if (isset($_SESSION['error_login'])): ?>
                                <div id="mensaje_error" class="alert alert-danger w-100 mb-4">
                                    <?= $_SESSION['error_login'] ?>
                                </div>
                                <?php unset($_SESSION['error_login']); ?>
                            <?php endif; ?>

                            <div id="error-email" class="alert alert-danger w-100 mt-2 d-none"></div>

                            <div class="form-group mb-4">
                                <input
                                    type="email"
                                    id="email"
                                    class="form-input"
                                    placeholder=" "
                                    name="correo" />
                                <label for="email" class="form-label">Correo</label>
                            </div>

                            <div class="form-group mb-3">
                                <input
                                    type="password"
                                    id="password"
                                    class="form-input"
                                    placeholder=" "
                                    name="clave"
                                    required />
                                <label for="password" class="form-label">Contraseña</label>
                            </div>

                            <div class="form-check  mb-4">
                                <input class="form-check-input" type="checkbox" value="" id="checkDefault">
                                <label class="form-check-label" for="checkDefault">
                                    Mostrar contraseña
                                </label>
                            </div>

                            <div class="form-group d-flex justify-content-end  mb-4">
                                <button class="btn btn-primary w-100" type="submit">Siguiente</button>
                            </div>

                            <div class="form-group d-flex justify-content-end">
                                <a
                                    href="<?= BASE_URL; ?>/recuperar"
                                    class="mt-1 d-flex text-decoration-none fw-medium"
                                    style="font-size: 14px">¿Has olvidado tu contraseña?</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($blocked) && $blocked): ?>
                const form = document.querySelector('form');
                const inputs = form.querySelectorAll('input, button');
                inputs.forEach(element => {
                    element.disabled = true;
                });

                let remainingTime = <?php echo $remainingTime; ?>;
                const errorElement = document.querySelector('.error-message');
                const originalMessage = errorElement.textContent;

                const timer = setInterval(function() {
                    remainingTime--;
                    const minutes = Math.ceil(remainingTime / 60);
                    errorElement.textContent = originalMessage.replace(/\d+ minutos/, minutes + ' minutos');

                    if (remainingTime <= 0) {
                        clearInterval(timer);
                        inputs.forEach(element => {
                            element.disabled = false;
                        });
                        errorElement.textContent = '';
                        window.location.reload();
                    }
                }, 1200);
            <?php endif; ?>
        });
    </script>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
        crossorigin="anonymous">
    </script>

    <script src="<?= BASE_URL ?>/public/assets/js/login/login.js"></script>

</body>

</html>