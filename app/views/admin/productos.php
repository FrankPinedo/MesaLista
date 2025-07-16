<?php if (!defined('BASE_URL')) require_once __DIR__ . '/../../../config/config.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MesaLista - Productos</title>

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
        <div class="container mx-auto p-4 md:p-6">
            <!-- Botones de acción -->
            <div class="flex flex-col md:flex-row gap-4 mb-6">
                <a href="<?= BASE_URL; ?>/admin/agregar_producto" class="btn btn-primary flex-1 py-3 px-4 text-center rounded-lg shadow hover:shadow-md transition">
                    <i class="fas fa-plus-circle mr-2"></i> Agregar Productos
                </a>
                <a href="<?= BASE_URL; ?>/admin/agregar_guarnicion" class="btn btn-success flex-1 py-3 px-4 text-center rounded-lg shadow hover:shadow-md transition">
                    <i class="fas fa-utensils mr-2"></i> Agregar Guarniciones
                </a>
                <a href="<?= BASE_URL; ?>/admin/variaciones" class="btn btn-info flex-1 py-3 px-4 text-center rounded-lg shadow hover:shadow-md transition">
                    <i class="fas fa-ruler-combined mr-2"></i> Variaciones
                </a>
            </div>

            <!-- Tabla de productos -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Imagen</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($productos as $producto): ?>
                                <tr>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm font-medium"><?= $producto['id'] ?></td>
                                    <td class="px-6 py-2 whitespace-nowrap">
                                        <img src="<?= htmlspecialchars(BASE_URL . '/public/uploads/' . ($producto['imagen'] ?? '')) ?: 'https://static.vecteezy.com/system/resources/previews/004/141/669/non_2x/no-photo-or-blank-image-icon-loading-images-or-missing-image-mark-image-not-available-or-image-coming-soon-sign-simple-nature-silhouette-in-frame-isolated-illustration-vector.jpg' ?>"
                                            alt="<?= htmlspecialchars($producto['nombre']) ?>"
                                            class="h-10 w-10 rounded-full">

                                    </td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm"><?= htmlspecialchars($producto['nombre']) ?></td>
                                    <td class="px-6 py-2 text-sm max-w-xs truncate"><?= htmlspecialchars($producto['descripcion']) ?></td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm">S/ <?= number_format($producto['precio'], 2) ?></td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm"><?= $producto['stock'] ?></td>
                                    <td class="px-6 py-2 whitespace-nowrap">
                                        <?php if ($producto['estado']): ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Habilitado</span>
                                        <?php else: ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Deshabilitado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm font-medium">
                                        <?php if ($producto['estado']): ?>
                                            <button class="text-red-600 hover:text-red-900" data-bs-toggle="modal" data-bs-target="#disableModal" data-product-id="<?= $producto['id'] ?>" data-product-name="<?= htmlspecialchars($producto['nombre']) ?>">
                                                Deshabilitar
                                            </button>
                                        <?php else: ?>
                                            <button class="text-green-600 hover:text-green-900" data-bs-toggle="modal" data-bs-target="#enableModal" data-product-id="<?= $producto['id'] ?>" data-product-name="<?= htmlspecialchars($producto['nombre']) ?>">
                                                Habilitar
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Paginación -->
            <nav class="mt-4 flex items-center justify-between">
                <div class="hidden sm:block">
                    <p class="text-sm text-gray-700">
                        Mostrando <span class="font-medium"><?= $offset + 1 ?></span>
                        a <span class="font-medium"><?= min($offset + count($productos), $totalProductos) ?></span>
                        de <span class="font-medium"><?= $totalProductos ?></span> resultados
                    </p>
                </div>
                <div class="flex-1 flex justify-between sm:justify-end">
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
            </nav>
        </div>

    </main>

    <!-- Modal para deshabilitar producto -->
    <div class="modal fade" id="disableModal" tabindex="-1" aria-labelledby="disableModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="disableModalLabel">Deshabilitar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro que deseas deshabilitar el producto <span id="disableProductName" class="font-semibold"></span>?</p>
                    <p class="text-red-500 mt-2">Este producto ya no estará disponible para la venta.</p>
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
                    <h5 class="modal-title" id="enableModalLabel">Habilitar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro que deseas habilitar el producto <span id="enableProductName" class="font-semibold"></span>?</p>
                    <p class="text-green-500 mt-2">Este producto estará disponible para la venta.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success">Confirmar Habilitar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= BASE_URL ?>/public/assets/js/admin/fragment.js"></script>

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

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>

</body>

</html>