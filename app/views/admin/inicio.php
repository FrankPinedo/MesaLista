<?php if (!defined('BASE_URL')) require_once __DIR__ . '/../../../config/config.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MesaLista - Panel Administrativo</title>

    <!-- Logo -->
    <link rel="icon" href="<?= BASE_URL ?>/public/assets/img/logo_1.png" />

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT"
        crossorigin="anonymous" />

    <!-- Estilos -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/admin/inicio.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/admin/fragment.css" />

    <!-- Iconos -->
    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

</head>

<body>
    <header>
        <div class="page-loader flex-column" id="page-loader">
            <div
                class="d-flex flex-column align-items-center justify-content-center">
                <span class="spinner-border text-dark" role="status"></span>
                <span class="text-muted fs-6 fw-semibold mt-4">Cargando...</span>
            </div>
        </div>

        <div class="container-fluid p-0 flex-column" id="headerDesktop">
            <div class="linkHome_desktop p-2 py-3 w-100">
                <img src="<?= BASE_URL ?>/public/assets/img/logo.png" class="iconHome" />
                <span>Panel de Control</span>
            </div>

            <div class="profile_desktop my-2" id="btnProfile">
                <div class="btnProfile">
                    <div class="imgProfile_desktop">
                        <img
                            src="<?= BASE_URL ?>/public/assets/img/perfil_defect.jpg"
                            alt=""
                            class="iconHome"
                            style="filter: invert(1)" />
                    </div>
                    <div class="dateProfile">
                        <span class="nameAdmin"><?= htmlspecialchars($usuario['nombres'] . ' ' . $usuario['apellidos']) ?></span>
                        <span class="roleAdmin">Administrador</span>
                    </div>
                </div>

                <div id="containProfile" class="hidden">
                    <div class="container-fluid p-0 h-100 flex-column d-flex">
                        <div class="d-flex contain_DataProfile">
                            <div class="contain_BackProfile"></div>
                            <div class="containImgProfile">
                                <img
                                    src="<?= BASE_URL ?>/public/assets/img/perfil_defect.jpg"
                                    alt=""
                                    class="iconHome"
                                    style="filter: invert(1)" />
                            </div>
                        </div>
                        <div class="d-flex flex-column py-1">
                            <div class="d-inline w-100 text-center">
                                <span><?= htmlspecialchars($usuario['nombres'] . ' ' . $usuario['apellidos']) ?></span>
                            </div>
                            <div class="d-inline w-100 text-center">
                                <span class="fw-semibold">Administrador</span>
                            </div>
                        </div>
                        <div class="formProfile p-2">
                            <form action="<?= BASE_URL ?>/admin/logout" method="post" class="px-3 py-2">
                                <button type="submit" class="btn btn-danger btn-sm w-100">
                                    Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="menuItems_desktop">
                <ul class="listItems p-0 m-0">
                    <li>
                        <a href="<?= BASE_URL; ?>/admin" class="navlink">
                            <div class="line"></div>
                            <span class="material-symbols-outlined"> home </span>
                            <span class="nameItem">Inicio</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL; ?>/admin/empresa" class="navlink">
                            <div class="line"></div>
                            <span class="material-symbols-outlined"> storefront </span>
                            <span class="nameItem">Empresa</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL; ?>/admin/platos" class="navlink">
                            <div class="line"></div>
                            <span class="material-symbols-outlined"> flatware </span>
                            <span class="nameItem">Platos</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL; ?>/admin/bebidas" class="navlink">
                            <div class="line"></div>
                            <span class="material-symbols-outlined"> liquor </span>
                            <span class="nameItem">Bebidas</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL; ?>/admin/usuarios" class="navlink">
                            <div class="line"></div>
                            <span class="material-symbols-outlined"> groups </span>
                            <span class="nameItem">Usuarios</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="footer_desktop mt-auto">
                <button id="btn_Desktop" class="btn">
                    <span class="material-symbols-outlined"> keyboard_tab_rtl </span>
                </button>
            </div>
        </div>

        <div class="container-fluid p-0" id="headerMobile">
            <div class="contenedorResponsivo">
                <div class="linkHome_Mobile">
                    <img src="<?= BASE_URL ?>/public/assets/img/logo.png" alt="" class="iconHome" />
                    <span>Panel de Control</span>
                </div>

                <div class="ms-auto">
                    <button
                        class="btn"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#menuResponsive"
                        aria-expanded="false"
                        aria-controls="menuResponsive">
                        <span class="material-symbols-outlined m-auto"> menu </span>
                    </button>
                </div>
            </div>

            <div class="collapse w-100" id="menuResponsive">
                <div class="profile_desktop my-2">
                    <div class="btnProfile">
                        <div class="imgProfile_desktop">
                            <img
                                src="<?= BASE_URL ?>/public/assets/img/perfil_defect.jpg"
                                alt=""
                                class="iconHome"
                                style="filter: invert(1)" />
                        </div>
                        <div class="dateProfile">
                            <span class="nameAdmin"><?= htmlspecialchars($usuario['nombres'] . ' ' . $usuario['apellidos']) ?></span>
                            <span class="roleAdmin">Administrador</span>
                        </div>
                    </div>
                </div>

                <div class="menuItems_desktop">
                    <ul class="listItems p-0 m-0">
                        <li>
                            <a href="<?= BASE_URL; ?>/admin" class="navlink">
                                <div class="line"></div>
                                <span class="material-symbols-outlined"> home </span>
                                <span class="nameItem">Inicio</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL; ?>/admin/empresa" class="navlink">
                                <div class="line"></div>
                                <span class="material-symbols-outlined"> storefront </span>
                                <span class="nameItem">Empresa</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL; ?>/admin/platos" class="navlink">
                                <div class="line"></div>
                                <span class="material-symbols-outlined"> flatware </span>
                                <span class="nameItem">Platos</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL; ?>/admin/bebidas" class="navlink">
                                <div class="line"></div>
                                <span class="material-symbols-outlined"> liquor </span>
                                <span class="nameItem">Bebidas</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL; ?>/admin/usuarios" class="navlink">
                                <div class="line"></div>
                                <span class="material-symbols-outlined"> groups </span>
                                <span class="nameItem">Usuarios</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <main>
        <div class="container-fluid p-3 m-0 h-100 d-flex flex-column">
            <!-- Breadcrumb y Título -->
            <div class="row w-100 containMain mb-3">
                <nav aria-label="breadcrumb">
                    <ol style="--bs-breadcrumb-divider: '・';" class="breadcrumb mb-1">
                        <li class="breadcrumb-item" aria-current="page">
                            <a href="<?= BASE_URL; ?>/admin" class="link text-body-secondary">Inicio</a>
                        </li>
                    </ol>
                    <h1 class="fs-4 mb-0 fw-semibold">Panel Administrativo</h1>
                </nav>
            </div>

            <!-- Tarjetas -->
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
                <div class="col">
                    <a href="<?= BASE_URL ?>/admin/empresa" class="text-decoration-none">
                        <div class="card text-center shadow-sm h-100">
                            <div class="card-body">
                                <i class="bi bi-building fs-1 text-primary"></i>
                                <p class="mt-2 fw-semibold">Empresa</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="<?= BASE_URL ?>/admin/productos" class="text-decoration-none">
                        <div class="card text-center shadow-sm h-100">
                            <div class="card-body">
                                <i class="bi bi-egg-fried fs-1 text-success"></i>
                                <i class="bi bi-cup-straw fs-1 text-warning"></i>
                                <p class="mt-2 fw-semibold">Productos</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col">
                    <a href="<?= BASE_URL ?>/admin/usuarios" class="text-decoration-none">
                        <div class="card text-center shadow-sm h-100">
                            <div class="card-body">
                                <i class="bi bi-people fs-1 text-danger"></i>
                                <p class="mt-2 fw-semibold">Usuarios</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Ayuda -->
            <div class="mt-auto text-center">
                ¿Necesitas ayuda? <a href="<?= BASE_URL ?>/admin/contacto" class="ayuda-link">Contáctanos</a>
            </div>
        </div>
    </main>

    <script src="<?= BASE_URL ?>/public/assets/js/admin/fragment.js"></script>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
</body>

</html>