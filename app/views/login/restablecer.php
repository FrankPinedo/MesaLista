<?php if (!defined('BASE_URL')) require_once __DIR__ . '/../../../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MesaLista - Restablecer Contraseña</title>

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

    <!--  -->
    <style>
        .form-input {
            padding: 1.2rem 2.5rem 0.4rem 0.5rem;
        }

        .material-symbols-outlined {
            font-family: "Material Symbols Outlined";
            font-variation-settings: "FILL" 0, "wght" 400, "GRAD" 0, "opsz" 24;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 0.5rem;
            transform: translateY(-50%);
            cursor: pointer;
            color: #777;
            user-select: none;
        }

        .ventana {
            height: auto !important;
            width: auto;
            min-width: 500px;
        }

        section {
            width: auto;
        }

        @media (max-width: 600px) {
            .body_1 {
                padding: 4% 1% !important;
            }

            .container {
                padding: 0% !important;
                width: 100vw;
            }

            .ventana {
                min-width: 100%;
            }
        }
    </style>
</head>

<body>
    <section>
        <div class="container d-flex justify-content-center align-items-center">
            <div
                class="ventana d-flex flex-column justify-content-center align-items-center m-0 gap-1">
                <div class="ventana_1 d-flex flex-column bg-white p-3">
                    <div class="header_1 d-flex flex-column align-items-center">
                        <img src="<?= BASE_URL ?>/public/assets/img/logo_1.png" alt="" />
                        <span class="h3">Restablecer Contraseña</span>
                    </div>
                    <div class="body_1 px-5 py-4">
                        <form
                            method="POST"
                            action="<?= BASE_URL ?>/restablecer/actualizar"
                            class="w-100 d-flex justify-content-center align-items-center flex-column">


                            <?php if (isset($_SESSION['error_restaurar'])): ?>
                                <div class="alert alert-danger w-100" role="alert">
                                    <?= $_SESSION['error_restaurar'] ?>
                                </div>
                                <?php unset($_SESSION['error_restaurar']); ?>
                            <?php endif; ?>

                            <div class="d-flex mb-4 gap-1 w-100">
                                <div class="form-group w-50">
                                    <input
                                        type="text"
                                        id="dni"
                                        name="dni"
                                        class="form-input"
                                        placeholder=" "
                                        pattern="\d{8}"
                                        maxlength="8"
                                        inputmode="numeric"
                                        required />
                                    <label for="dni" class="form-label">Documento de identidad</label>
                                </div>
                                <div class="form-group w-50">
                                    <input
                                        type="text"
                                        id="digito"
                                        name="digito"
                                        class="form-input"
                                        placeholder=" "
                                        pattern="\d{1}"
                                        maxlength="1"
                                        inputmode="numeric"
                                        required />
                                    <label for="digito" class="form-label">Dígito verificador</label>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <input
                                    type="password"
                                    id="clave"
                                    name="nueva_contraseña"
                                    class="form-input"
                                    placeholder=" "
                                    required />
                                <label for="clave" class="form-label">Contraseña nueva</label>
                                <span
                                    class="material-symbols-outlined toggle-password"
                                    onclick="togglePassword(this)">visibility_off</span>
                            </div>

                            <div class="form-group mb-3">
                                <ul id="password-suggestions" class="m-0 list-inline ps-2">
                                    <li id="length">Mínimo 8 caracteres</li>
                                    <li id="uppercase">Al menos una mayúscula</li>
                                    <li id="lowercase">Al menos una minúscula</li>
                                    <li id="number">Al menos un número</li>
                                    <li id="special">
                                        Al menos un carácter especial (@, #, $, %, etc.)
                                    </li>
                                </ul>
                            </div>

                            <div class="form-group mb-4">
                                <input
                                    type="password"
                                    id="confirmar-clave"
                                    name="confirmar_contraseña"
                                    class="form-input"
                                    placeholder=" "
                                    required />
                                <label for="confirmar-clave" class="form-label">Confirmar nueva contraseña</label>
                                <span
                                    class="material-symbols-outlined toggle-password"
                                    onclick="togglePassword(this)">visibility_off</span>
                            </div>
                            <input type="hidden" name="token_recuperar" value="<?= $token_recuperar ?>">

                            <div class="form-group d-flex justify-content-end mb-0 gap-1">
                                <a href="<?= BASE_URL ?>/restablecer/cancelar" class="btn btn-danger w-50">Cancelar</a>

                                <button class="btn btn-primary w-50" type="submit">
                                    Continuar
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="<?= BASE_URL ?>/public/assets/js/login/restablecer.js"></script>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
        crossorigin="anonymous">
    </script>

</body>

</html>