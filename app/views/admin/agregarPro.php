<?php if (!defined('BASE_URL')) require_once __DIR__ . '/../../../config/config.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MesaLista - Agregar productos</title>

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
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">
                Gestión de Productos
            </h1>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Formulario de Producto -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-x   l font-semibold text-gray-700 mb-4">
                        Agregar/Editar Producto
                    </h2>

                    <!-- Envío exitoso -->
                    <?php if (isset($_SESSION['success_producto'])): ?>
                        <div class="fixed top-4 right-4 z-50" id="success-alert">
                            <div class="flex items-center p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
                                <svg class="flex-shrink-0 inline w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM13.707 7.707a1 1 0 0 1-1.414 1.414L9 7.414l-1.293 1.293a1 1 0 0 1-1.414-1.414L7.586 6 6.293 4.707a1 1 0 0 1 1.414-1.414L9 4.586l1.293-1.293a1 1 0 0 1 1.414 1.414L10.414 6l1.293 1.707Z" />
                                </svg>
                                <span class="sr-only">Éxito</span>
                                <div>
                                    <span class="font-medium">¡Éxito!</span> <?= htmlspecialchars($_SESSION['success_producto']) ?>
                                </div>
                            </div>
                        </div>
                        <?php unset($_SESSION['success_producto']); ?>
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
                    <?php if (isset($_SESSION['error_producto'])): ?>
                        <div id="mensaje_error" class="alert alert-danger w-100 mb-4">
                            <strong>Atención:</strong> <?= htmlspecialchars($_SESSION['error_producto']) ?>
                        </div>
                        <?php unset($_SESSION['error_producto']); ?>
                    <?php endif; ?>

                    <!-- Alerta fronted -->
                    <div class="alert alert-danger alert-dismissible fade show hidden_2" role="alert" id="message_error">
                        <strong>Atención:</strong> Este es un mensaje de advertencia.
                    </div>

                    <form id="producto-form" method="POST" enctype="multipart/form-data" action="<?= BASE_URL ?>/admin/guardarProducto" class="space-y-4">

                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre:</label>
                                <input
                                    type="text"
                                    name="nombre"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción:</label>
                                <textarea
                                    name="descripcion"
                                    rows="3"
                                    required
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
                                        required
                                        class="block w-full pl-7 pr-12 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" />
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Stock:</label>
                                <input
                                    type="number"
                                    name="stock"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Producto:</label>
                                <select
                                    name="tipo_producto_id"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($tiposProducto as $tipo): ?>
                                        <option value="<?= $tipo['id'] ?>"><?= htmlspecialchars($tipo['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tamaño:</label>
                                <select
                                    name="tamano_id"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Ninguno</option>
                                    <?php foreach ($tamanos as $tam): ?>
                                        <option value="<?= $tam['id'] ?>"><?= htmlspecialchars($tam['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Imagen:</label>
                            <div class="mt-1 flex items-center">
                                <input type="file" name="imagen" accept="image/*" class="py-2" />
                            </div>
                        </div>

                        <!-- Sección dinámica según tipo de producto -->
                        <div id="tipo-especifico" class="space-y-4">
                            <!-- Para bebidas -->
                            <div class="bebida-fields hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Bebida:</label>
                                <select
                                    name="tipo_bebida_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($tiposBebida as $bebida): ?>
                                        <option value="<?= $bebida['id'] ?>"><?= htmlspecialchars($bebida['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Para platos -->
                            <div class="plato-fields hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Plato:</label>
                                <select
                                    name="tipo_plato_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($tiposPlato as $plato): ?>
                                        <option value="<?= $plato['id'] ?>"><?= htmlspecialchars($plato['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>

                                <label class="block text-sm font-medium text-gray-700 mt-3 mb-1">Guarniciones Disponibles:</label>
                                <div class="guarniciones-list space-y-2">
                                    <?php foreach ($guarniciones as $g): ?>
                                        <div class="flex items-center">
                                            <input id="guarnicion-<?= $g['id'] ?>" name="guarniciones[]" type="checkbox" value="<?= $g['id'] ?>" class="..." />
                                            <label for="guarnicion-<?= $g['id'] ?>" class="ml-2 block text-sm text-gray-700"><?= htmlspecialchars($g['nombre']) ?></label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Para combos -->
                            <div class="combo-fields hidden">
                                <h4 class="text-lg font-medium text-gray-700">
                                    Componentes del Combo
                                </h4>
                                <div class="mt-4" id="combo-componentes"></div>
                                <template id="plantilla-componente">
                                    <div class="componente flex gap-2 items-center mt-2">
                                        <select name="componentes[][producto_id]" required class="border rounded p-1">
                                            <option value="">Seleccione un producto...</option>
                                            <?php foreach ($productos as $p): ?>
                                                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
                                            <?php endforeach; ?>
                                        </select>

                                        <input type="number" name="componentes[][cantidad]" min="1" value="1" class="w-16 border rounded p-1" placeholder="Cantidad" required />

                                        <input type="text" name="componentes[][grupo]" placeholder="Grupo (ej. Bebida, Principal)" class="border rounded p-1" />

                                        <label class="flex items-center gap-1">
                                            <input type="checkbox" name="componentes[][obligatorio]" value="1" checked />
                                            Obligatorio
                                        </label>

                                        <button type="button" class="eliminar-componente text-red-600 hover:text-red-800">✖</button>
                                    </div>
                                </template>
                                <button type="button" id="agregar-componente" class="mt-2 text-sm text-indigo-600 hover:text-indigo-800">
                                    + Agregar Componente
                                </button>
                            </div>
                        </div>

                        <div class="pt-4">
                            <button
                                type="submit"
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Guardar Producto
                            </button>
                        </div>
                    </form>

                </div>

                <!-- Tabla de Productos -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">
                        Lista de Productos
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
                                        Acción
                                    </th>
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

                    <!-- Paginación -->
                    <div class="mt-4 flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            Mostrando <span class="font-medium"><?= $offset + 1 ?></span>
                            a <span class="font-medium"><?= min($offset + count($productos), $totalProductos) ?></span>
                            de <span class="font-medium"><?= $totalProductos ?></span> resultados
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

    <script src="<?= BASE_URL ?>/public/assets/js/admin/agregarPro.js"></script>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>

</body>

</html>