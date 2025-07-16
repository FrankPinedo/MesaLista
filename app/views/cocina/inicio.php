<?php if (!defined('BASE_URL')) require_once __DIR__ . '/../../../config/config.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MesaLista - Cocina</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT"
        crossorigin="anonymous" />
    <!-- LOGO -->
    <link rel="icon" href="<?= BASE_URL ?>/public/assets/img/logo_1.png" />

    <!-- ESTILOS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/cocina/inicio.css" />

</head>

<body class="bg-gray-100">

    <nav style="background-color: #1E262C;" class="shadow-sm sticky top-0 z-50">
        <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 text-white">
                <div class="flex items-center">
                    <span class="text-lg font-bold whitespace-nowrap">
                        COMANDAS
                    </span>
                </div>

                <div class="flex items-center">
                    <span id="current-time" class="font-bold text-lg whitespace-nowrap">
                        Cargando...
                    </span>
                </div>

                <div class="flex items-center md:hidden">
                    <button id="menu-toggle" class="text-gray-300 hover:text-white">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>

                <div class="hidden md:flex items-center space-x-4">
                    <button class="px-3 py-1 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-500 transition">
                        Platos
                    </button>
                    <button class="px-3 py-1 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-green-500 transition">
                        Bebidas
                    </button>
                    <div class="relative">
                        <button id="profile-button" class="px-3 py-1 bg-purple-600 text-white rounded-md text-sm font-medium hover:bg-purple-500 transition flex items-center">
                            Perfil
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="profile-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50">
                            <form action="<?= BASE_URL ?>/cocina/logout">
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-red-500 rounded-md font-bold hover:text-white">
                                    Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Menú móvil -->
            <div id="mobile-menu" class="mobile-menu md:hidden bg-[#2C343A] text-white">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                    <button class="block w-full text-left px-3 py-2 bg-blue-600 hover:bg-blue-500 rounded-md text-sm font-medium">
                        Platos
                    </button>
                    <button class="block w-full text-left px-3 py-2 bg-green-600 hover:bg-green-500 rounded-md text-sm font-medium">
                        Bebidas
                    </button>
                    <div class="relative">
                        <button id="mobile-profile-button" class="block w-full text-left px-3 py-2 bg-purple-600 hover:bg-purple-500 rounded-md text-sm font-medium flex justify-between items-center">
                            Perfil
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="mobile-profile-menu" class="hidden pl-4 mt-1">
                            <form action="<?= BASE_URL ?>/cocina/logout">
                                <button type="submit" class="block w-full text-left px-3 py-2 bg-red-600 hover:bg-red-500 text-white rounded-md text-sm">
                                    Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="mx-auto p-4 pt-6">
        <div
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">

        </div>
    </div>

    <!-- Modal para cancelar item -->
    <div id="cancelItemModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-lg shadow-lg w-80 p-4">
            <h2 class="text-lg font-bold text-gray-800 mb-3">¿Cancelar producto?</h2>
            <p class="text-sm text-gray-600 mb-4">
                ¿Estás seguro de que quieres cancelar este producto?<br>
                Esta acción no se puede deshacer.
            </p>
            <div class="flex justify-end gap-2">
                <button onclick="closeCancelItemModal()"
                    class="px-4 py-2 text-sm rounded bg-gray-200 hover:bg-gray-300 text-gray-700">
                    No
                </button>
                <button id="confirmCancelItem"
                    class="px-4 py-2 text-sm rounded bg-red-500 hover:bg-red-600 text-white">
                    Sí, cancelar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal para cancelar comanda -->
    <div id="cancelOrderModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-lg shadow-lg w-80 p-4">
            <h2 class="text-lg font-bold text-gray-800 mb-3">¿Cancelar comanda completa?</h2>
            <p class="text-sm text-gray-600 mb-4">
                ¿Estás seguro de que quieres cancelar toda la comanda?<br>
                Esta acción no se puede deshacer.
            </p>
            <div class="flex justify-end gap-2">
                <button onclick="closeCancelOrderModal()"
                    class="px-4 py-2 text-sm rounded bg-gray-200 hover:bg-gray-300 text-gray-700">
                    No
                </button>
                <button id="confirmCancelOrder"
                    class="px-4 py-2 text-sm rounded bg-red-500 hover:bg-red-600 text-white">
                    Sí, cancelar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal para notificar sobre comanda -->
    <div id="notificationCom" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-lg shadow-lg w-96 p-4">
            <h2 class="text-xl font-semibold text-gray-800 mb-3">¡Tiempo casi agotado!</h2>
            <p class="text-sm text-gray-700 mb-4">
                La comanda <span id="comandaIdSpan" class="font-bold text-red-500">#123</span> será cancelada automáticamente en 5 minutos.
                Puedes extender el tiempo o cancelarla ahora.
            </p>
            <div class="flex justify-end gap-2">
                <button class="px-4 py-2 text-sm rounded bg-blue-500 hover:bg-blue-600 text-white">
                    Dar Extensión
                </button>
                <button class="px-4 py-2 text-sm rounded bg-red-500 hover:bg-red-600 text-white">
                    Cancelar Comanda
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación de cancelación -->
    <div id="sucessCancelCom" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-lg shadow-lg w-80 p-4 text-center">
            <h2 class="text-lg font-bold text-gray-800 mb-2">Comanda cancelada</h2>
            <p class="text-sm text-gray-600 mb-4">La comanda ha sido cancelada exitosamente.</p>
            <button class="px-4 py-2 text-sm rounded bg-green-500 hover:bg-green-600 text-white">
                Aceptar
            </button>
        </div>
    </div>

    <div id="notificacionCancelacion" class="alert alert-danger" style="display: none; position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>
    <script src="<?= BASE_URL ?>/public/assets/js/cocina/header.js"></script>
    <script src="<?= BASE_URL ?>/public/assets/js/cocina/timeSync.js"></script>
    <script src="<?= BASE_URL ?>/public/assets/js/cocina/comanda.js"></script>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
</body>

</html>