<?php if (!defined('BASE_URL')) require_once __DIR__ . '/../../../config/config.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MesaLista - Agregar guarniciones</title>

    <!-- Logo -->
    <link rel="icon" href="<?= BASE_URL ?>/public/assets/img/logo_1.png" />

    <!-- Estilos -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/admin/fragment.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/admin/platos/productos.css" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT"
        crossorigin="anonymous" />

    <!-- Iconos -->
    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

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
                                <span class="nameAdmin"><?= htmlspecialchars($usuario['nombres'] . ' ' . $usuario['apellidos']) ?></span>
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
                        <a href="<?= BASE_URL; ?>/admin/productos" class="navlink">
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
                            <a href="<?= BASE_URL; ?>/admin/productos" class="navlink">
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
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">
                Gestión de Guarniciones
            </h1>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Formulario de Guarnición -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">
                        Agregar/Editar Guarnición
                    </h2>

                    <!-- Envío exitoso -->
                    <?php if (isset($_SESSION['success_guarnicion'])): ?>
                        <div class="fixed top-4 right-4 z-50" id="success-alert">
                            <div class="flex items-center p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
                                <svg class="flex-shrink-0 inline w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM13.707 7.707a1 1 0 0 1-1.414 1.414L9 7.414l-1.293 1.293a1 1 0 0 1-1.414-1.414L7.586 6 6.293 4.707a1 1 0 0 1 1.414-1.414L9 4.586l1.293-1.293a1 1 0 0 1 1.414 1.414L10.414 6l1.293 1.707Z" />
                                </svg>
                                <span class="sr-only">Éxito</span>
                                <div>
                                    <span class="font-medium">¡Éxito!</span> <?= htmlspecialchars($_SESSION['success_guarnicion']) ?>
                                </div>
                            </div>
                        </div>
                        <?php unset($_SESSION['success_guarnicion']); ?>
                        <script>
                            setTimeout(() => {
                                const alert = document.getElementById('success-alert');
                                if (alert) {
                                    alert.style.transition = 'opacity 0.5s ease';
                                    alert.style.opacity = '0';
                                    setTimeout(() => alert.remove(), 500);
                                }
                            }, 3000);
                        </script>
                    <?php endif; ?>

                    <!-- Alerta backend -->
                    <?php if (isset($_SESSION['error_guarnicion'])): ?>
                        <div id="mensaje_error" class="alert alert-danger w-100 mb-4">
                            <strong>Atención:</strong> <?= htmlspecialchars($_SESSION['error_guarnicion']) ?>
                        </div>
                        <?php unset($_SESSION['error_guarnicion']); ?>
                    <?php endif; ?>

                    <!-- Alerta fronted -->
                    <div class="alert alert-danger alert-dismissible fade show hidden_2" role="alert" id="message_error">
                        <strong>Atención:</strong> Este es un mensaje de advertencia.
                    </div>

                    <form id="guarnicion-form" method="POST" enctype="multipart/form-data" action="<?= BASE_URL ?>/admin/guardarGuarnicion" class="space-y-4">
                        <input type="hidden" name="id" id="guarnicion-id">

                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre:</label>
                                <input
                                    type="text"
                                    name="nombre"
                                    id="nombre"

                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción:</label>
                                <textarea
                                    name="descripcion"
                                    id="descripcion"
                                    rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Precio:</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">S/</span>
                                    </div>
                                    <input
                                        type="number"
                                        name="precio"
                                        id="precio"
                                        required
                                        class="block w-full pl-7 pr-12 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" />
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Stock:</label>
                                <input
                                    type="number"
                                    name="stock"
                                    id="stock"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estado:</label>
                            <select
                                name="estado"
                                id="estado"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="1">Habilitado</option>
                                <option value="0">Deshabilitado</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Imagen:</label>
                            <div class="mt-1 flex items-center">
                                <input type="file" name="imagen" id="imagen" accept="image/*" class="py-2" />
                                <div id="imagen-actual" class="ml-4 hidden">
                                    <span class="text-sm text-gray-500">Imagen actual:</span>
                                    <img id="imagen-preview" src="" class="h-10 w-10 rounded-full">
                                </div>
                            </div>
                        </div>

                        <div class="pt-4">
                            <button
                                type="submit"
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Guardar Guarnición
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tabla de Guarniciones -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">
                        Lista de Guarniciones
                    </h2>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ID
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Imagen
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nombre
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Descripción
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Precio
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Stock
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Estado
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="guarniciones-list">
                                <?php foreach ($guarniciones as $guarnicion): ?>
                                    <tr>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm font-medium"><?= $guarnicion['id'] ?></td>
                                        <td class="px-6 py-2 whitespace-nowrap">
                                            <img src="<?= htmlspecialchars(BASE_URL . '/public/uploads/' . ($guarnicion['imagen'] ?? 'sin imagen.jpg')) ?>"
                                                alt="<?= htmlspecialchars($guarnicion['nombre']) ?>"
                                                class="h-10 w-10 rounded-full">
                                        </td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm"><?= htmlspecialchars($guarnicion['nombre']) ?></td>
                                        <td class="px-6 py-2 text-sm max-w-xs truncate"><?= htmlspecialchars($guarnicion['descripcion']) ?></td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm">S/ <?= number_format($guarnicion['precio'], 2) ?></td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm"><?= $guarnicion['stock'] ?></td>
                                        <td class="px-6 py-2 whitespace-nowrap">
                                            <?php if ($guarnicion['estado']): ?>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Habilitado</span>
                                            <?php else: ?>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Deshabilitado</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm font-medium space-x-2">
                                            <button onclick="editarGuarnicion(<?= htmlspecialchars(json_encode($guarnicion), ENT_QUOTES, 'UTF-8') ?>)"
                                                class="text-indigo-600 hover:text-indigo-900">
                                                Editar
                                            </button>

                                            <?php if ($guarnicion['estado']): ?>
                                                <button class="text-red-600 hover:text-red-900" data-bs-toggle="modal" data-bs-target="#disableModal" data-product-id="<?= $guarnicion['id'] ?>" data-product-name="<?= htmlspecialchars($guarnicion['nombre']) ?>">
                                                    Deshabilitar
                                                </button>
                                            <?php else: ?>
                                                <button class="text-green-600 hover:text-green-900" data-bs-toggle="modal" data-bs-target="#enableModal" data-product-id="<?= $guarnicion['id'] ?>" data-product-name="<?= htmlspecialchars($guarnicion['nombre']) ?>">
                                                    Habilitar
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="mt-4 flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            Mostrando <span class="font-medium"><?= $offset + 1 ?></span>
                            a <span class="font-medium"><?= min($offset + count($guarniciones), $totalGuarniciones) ?></span>
                            de <span class="font-medium"><?= $totalGuarniciones ?></span> resultados
                        </div>
                        <div class="flex space-x-2">
                            <?php if ($pagina > 1): ?>
                                <a href="?pagina=<?= $pagina - 1 ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Anterior
                                </a>
                            <?php endif; ?>
                            <?php if ($pagina < $totalPaginas): ?>
                                <a href="?pagina=<?= $pagina + 1 ?>" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Siguiente
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal para deshabilitar producto -->
    <div class="modal fade" id="disableModal" tabindex="-1" aria-labelledby="disableModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="disableModalLabel">Deshabilitar Guarnición</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro que deseas deshabilitar la guarnición <span id="disableProductName" class="font-semibold"></span>?</p>
                    <p class="text-red-500 mt-2">Esta guarnición ya no estará disponible para la venta.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger">Confirmar Deshabilitar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para habilitar producto -->
    <div class="modal fade" id="enableModal" tabindex="-1" aria-labelledby="enableModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="enableModalLabel">Habilitar Guarnición</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro que deseas habilitar la guarnicióon <span id="enableProductName" class="font-semibold"></span>?</p>
                    <p class="text-green-500 mt-2">Esta guarnición estará disponible para la venta.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success">Confirmar Habilitar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Deshabilitar
            const disableModal = document.getElementById('disableModal');
            disableModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const productId = button.getAttribute('data-product-id');
                const productName = button.getAttribute('data-product-name');
                disableModal.querySelector('#disableProductName').textContent = productName;

                // Guardar ID en botón de confirmar
                const confirmBtn = disableModal.querySelector('.btn-danger');
                confirmBtn.onclick = function() {
                    window.location.href = `?accion=deshabilitar&id=${productId}`;
                };
            });

            // Habilitar
            const enableModal = document.getElementById('enableModal');
            enableModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const productId = button.getAttribute('data-product-id');
                const productName = button.getAttribute('data-product-name');
                enableModal.querySelector('#enableProductName').textContent = productName;

                // Guardar ID en botón de confirmar
                const confirmBtn = enableModal.querySelector('.btn-success');
                confirmBtn.onclick = function() {
                    window.location.href = `?accion=habilitar&id=${productId}`;
                };
            });
        });
    </script>

    <script src="<?= BASE_URL ?>/public/assets/js/admin/fragment.js"></script>


    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>

</body>

</html>