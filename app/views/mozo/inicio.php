<?php if (!defined('BASE_URL')) require_once __DIR__ . '/../../../config/config.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>Panel Mozo - MesaLista</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" href="<?= BASE_URL ?>/public/assets/img/logo_1.png" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/mozo/inicio/inicio.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* Estilos adicionales para selección múltiple */
        .mesa-card.seleccionada {
            border: 3px solid #0d6efd !important;
            box-shadow: 0 0 20px rgba(13, 110, 253, 0.5) !important;
            transform: scale(1.05);
        }

        /* Agregar estos estilos dentro del tag <style> que ya creamos */
        .btn-cambiar-estado:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-cambiar-estado:disabled:hover {
            background-color: transparent;
            color: #6c757d;
        }

        .mesa-card.seleccionada-multiple {
            border: 3px solid #28a745 !important;
            box-shadow: 0 0 20px rgba(40, 167, 69, 0.5) !important;
        }

        .menu-icon-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .menu-icon-btn:disabled img {
            filter: grayscale(100%);
        }

        .mesa-combinada {
            background-color: #17a2b8 !important;
            color: white;
        }

        .mesa-nombre-combinado {
            font-size: 0.9rem;
            font-weight: bold;
        }

        #mensajeSeleccionMultiple {
            position: fixed;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1050;
            width: auto;
            padding: 12px 24px;
            font-weight: bold;
            font-size: 14px;
        }

        /* Indicador de tiempo para mesas con comanda */
        .tiempo-comanda {
            font-size: 0.85rem;
            font-weight: bold;
            display: block;
            margin-top: 5px;
        }

        .tiempo-comanda.text-warning {
            color: #ffc107 !important;
        }

        .tiempo-comanda.text-danger {
            color: #dc3545 !important;
            animation: pulse 1s infinite;
        }

        /* Estilos para botón Cerrar Cuenta */
        #btnCerrarCuenta:not(:disabled) {
            background-color: rgba(40, 167, 69, 0.1);
            border-radius: 8px;
        }

        #btnCerrarCuenta:not(:disabled):hover {
            background-color: rgba(40, 167, 69, 0.2);
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }

            100% {
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <div class="d-flex vh-100">
        <!-- MENÚ LATERAL -->
        <div class="bg-dark text-white p-4 d-flex flex-column justify-content-between sidebar-mozo">
            <div>
                <h5 class="mb-4 text-center">MENÚ</h5>
                <div class="row row-cols-2 g-3 justify-content-center">
                    <div class="col text-center">
                        <button class="menu-icon-btn" id="btnComanda" disabled>
                            <img src="<?= BASE_URL ?>/public/assets/img/comanda.png" alt="Comanda">
                            <span>Comanda</span>
                        </button>
                    </div>
                    <div class="col text-center">
                        <button class="menu-icon-btn" id="btnCerrarCuenta"
                            disabled
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="Selecciona una mesa en estado 'Atendido' para cerrar cuenta">
                            <img src="<?= BASE_URL ?>/public/assets/img/CerrarCuenta.png" alt="Cerrar Cuenta">
                            <span>Cerrar Cuenta</span>
                        </button>
                    </div>
                    <div class="col text-center">
                        <button class="menu-icon-btn" id="btnRecargar">
                            <img src="<?= BASE_URL ?>/public/assets/img/Recargar.png" alt="Recargar">
                            <span>Recargar</span>
                        </button>
                    </div>
                    <div class="col text-center">
                        <button class="menu-icon-btn" id="btnDelivery">
                            <img src="<?= BASE_URL ?>/public/assets/img/Deliviry.png" alt="Delivery">
                            <span>Delivery</span>
                        </button>
                    </div>
                    <div class="col text-center">
                        <button class="menu-icon-btn" id="btnJuntarMesas" disabled>
                            <img src="<?= BASE_URL ?>/public/assets/img/Juntar.png" alt="Juntar Mesas">
                            <span>Juntar Mesas</span>
                        </button>
                    </div>
                    <div class="col text-center">
                        <button class="menu-icon-btn" id="btnAgregarMesa">
                            <img src="<?= BASE_URL ?>/public/assets/img/Agregar.png" alt="Agregar Mesa">
                            <span>Agregar Mesa</span>
                        </button>
                    </div>
                    <div class="col text-center">
                        <button class="menu-icon-btn" id="btnQuitarMesa">
                            <img src="<?= BASE_URL ?>/public/assets/img/Quitar.png" alt="Quitar Mesa">
                            <span>Quitar Mesa</span>
                        </button>
                    </div>
                    <div class="col text-center">
                        <button type="button" class="menu-icon-btn" id="btnSepararMesas" disabled>
                            <img src="<?= BASE_URL ?>/public/assets/img/Separar.png" alt="Separar Mesas">
                            <span>Separar Mesas</span>
                        </button>
                    </div>
                </div>
            </div>
            <a href="<?= BASE_URL ?>/mozo/logout"
                class="btn btn-danger w-100 d-flex align-items-center justify-content-center gap-2 mt-4">
                Salir
            </a>
        </div>

        <!-- FORMULARIO OCULTO PARA AGREGAR -->
        <form method="post" id="formAgregarMesa" style="display: none;">
            <input type="hidden" name="agregar_mesa" value="1">
        </form>

        <!-- PANEL DE MESAS -->
        <div class="flex-grow-1 p-4 bg-light">
            <!-- TÍTULO -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>MESAS</h4>
                <i class="bi bi-bell-fill fs-3 text-warning"></i>
            </div>

            <!-- MENSAJES -->
            <div id="mensajeEliminarMesa" class="alert alert-danger text-center fw-bold d-none mb-3">
                🔴 Modo eliminación activado: haz clic en una mesa para eliminarla.
            </div>
            <div id="mensajeJuntarMesas" class="alert alert-primary text-center fw-bold d-none mb-3">
                🟡 Modo juntar activado: selecciona 2 mesas libres para combinarlas.
            </div>
            <div id="mensajeSepararMesas" class="alert alert-info text-center fw-bold d-none mb-3">
                🔵 Modo separar activado: haz clic en una mesa combinada para dividirla en mesas libres.
            </div>
            <div id="mensajeSeleccionMultiple" class="alert alert-success text-center fw-bold d-none mb-3">
                ✅ Selección múltiple activada: Selecciona las mesas que deseas juntar.
            </div>

            <div class="row g-3" id="contenedorMesas">
                <?php if (isset($mesas) && is_array($mesas)): ?>
                    <?php foreach ($mesas as $mesa): ?>
                        <?php
                        // Determinar color del badge según estado
                        $badgeColor = 'secondary';
                        if ($mesa['estado'] === 'reservado') $badgeColor = 'warning';
                        elseif ($mesa['estado'] === 'ocupada') $badgeColor = 'orange';
                        elseif ($mesa['estado'] === 'esperando') $badgeColor = 'danger';
                        elseif ($mesa['estado'] === 'atendido') $badgeColor = 'success';
                        elseif ($mesa['estado'] === 'combinada') $badgeColor = 'info';

                        // Clases para la tarjeta según estado
                        $clase = 'mesa-libre';
                        if ($mesa['estado'] === 'reservado')
                            $clase = 'mesa-reservado';
                        elseif ($mesa['estado'] === 'ocupada')
                            $clase = 'mesa-ocupada';
                        elseif ($mesa['estado'] === 'esperando')
                            $clase = 'mesa-esperando';
                        elseif ($mesa['estado'] === 'atendido')
                            $clase = 'mesa-atendido';
                        elseif ($mesa['estado'] === 'combinada')
                            $clase = 'mesa-combinada';

                        // Verificar si es combinada ANTES de usarla
                        $nombreMesa = isset($mesa['nombre']) ? htmlspecialchars($mesa['nombre']) : '';
                        $esCombinada = strpos($nombreMesa, '|') !== false;
                        ?>
                        <div class="col-6 col-sm-4 col-md-3">
                            <div class="card mesa-card shadow-sm rounded-4 animate-mesa <?= $clase ?>"
                                data-id="<?= $mesa['id'] ?>"
                                data-mesa="<?= htmlspecialchars($nombreMesa, ENT_QUOTES, 'UTF-8') ?>"
                                data-estado="<?= $mesa['estado'] ?>"
                                data-combinada="<?= $esCombinada ? 'true' : 'false' ?>">
                                <div class="card-body d-flex flex-column align-items-center justify-content-center py-4 position-relative">
                                    <?php
                                    if ($esCombinada) {
                                        // Mostrar como M1 + M2
                                        $partes = explode('|', $nombreMesa);
                                        $mesasCombinadas = '';
                                        if (count($partes) >= 2) {
                                            $mesasCombinadas = trim($partes[0]) . ' + ' . trim($partes[1]);
                                        }
                                        echo "<span class='mesa-nombre-combinado'>{$mesasCombinadas}</span>";
                                    } else {
                                        echo "<span class='fw-bold fs-4 mb-2'>{$nombreMesa}</span>";
                                    }
                                    ?>
                                    <span class="badge bg-<?= $badgeColor ?> mb-2"><?= ucfirst($mesa['estado']) ?></span>

                                    <?php if ($mesa['estado'] === 'esperando' && isset($mesa['tiempo_comanda'])): ?>
                                        <span class="tiempo-comanda" data-minutos="<?= $mesa['tiempo_comanda'] ?>">
                                            ⏱ <?= $mesa['tiempo_comanda'] ?> min
                                        </span>
                                    <?php endif; ?>

                                    <div class="d-flex gap-2">
                                        <!-- En la sección del botón cambiar estado -->
                                        <button class="btn btn-outline-secondary btn-sm btn-cambiar-estado"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalCambiarEstado"
                                            data-id="<?= $mesa['id'] ?>"
                                            data-nombre="<?= $nombreMesa ?>"
                                            data-estado="<?= $mesa['estado'] ?>"
                                            <?= (!in_array($mesa['estado'], ['libre', 'reservado', 'ocupada'])) ? 'disabled' : '' ?>>
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm btn-eliminar-mesa"
                                            data-id="<?= $mesa['id'] ?>"
                                            data-nombre="<?= $nombreMesa ?>"
                                            title="Eliminar mesa">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p>No hay mesas disponibles.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- LEYENDA -->
            <div class="mt-4 d-flex justify-content-center gap-4 flex-wrap">
                <span><span class="badge bg-secondary">&nbsp;&nbsp;</span> Libre</span>
                <span><span class="badge bg-warning">&nbsp;&nbsp;</span> Reservado</span>
                <span><span class="badge bg-orange">&nbsp;&nbsp;</span> Ocupada</span>
                <span><span class="badge bg-danger">&nbsp;&nbsp;</span> Esperando</span>
                <span><span class="badge bg-success">&nbsp;&nbsp;</span> Atendido</span>
                <span><span class="badge bg-info">&nbsp;&nbsp;</span> Combinada</span>
            </div>
        </div>
    </div>

    <!-- Modales -->
    <!-- Modal: Éxito al agregar -->
    <div class="modal fade" id="modalMesaAgregada" tabindex="-1" aria-labelledby="modalMesaAgregadaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalMesaAgregadaLabel">Éxito</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">¡Mesa agregada correctamente!</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Confirmar eliminación -->
    <div class="modal fade" id="modalEliminarMesa" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Eliminar Mesa</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro que deseas eliminar la mesa <strong id="mesaAEliminarNombre"></strong>?
                </div>
                <div class="modal-footer">
                    <form method="post" id="formEliminarMesa">
                        <input type="hidden" name="eliminar_mesa_id" id="mesaAEliminarId">
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Juntar mesas -->
    <div class="modal fade" id="modalJuntarMesas" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Confirmar Unión de Mesas</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    ¿Deseas juntar las mesas seleccionadas?
                    <div id="mesasSeleccionadasInfo" class="mt-2"></div>
                </div>
                <div class="modal-footer">
                    <form method="post" id="formJuntarMesas">
                        <input type="hidden" name="mesa_ids" id="mesaIds">
                        <button type="submit" class="btn btn-primary">Confirmar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Cambiar estado -->
    <!-- Modal: Cambiar estado (alrededor de línea 287) -->
    <div class="modal fade" id="modalCambiarEstado" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title">Cambiar Estado de Mesa</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <input type="hidden" name="cambiar_estado_id" id="cambiarEstadoId">
                        <p id="nombreMesaCambio"></p>
                        <p class="text-muted small mb-3" id="estadoActualInfo"></p>
                        <select name="nuevo_estado" id="selectNuevoEstado" class="form-select" required>
                            <!-- Las opciones se llenarán dinámicamente -->
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning">Cambiar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script src="<?= BASE_URL ?>/public/assets/js/mozo/panel.js"></script>
    <script src="<?= BASE_URL ?>/public/assets/js/mozo/mozo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Agregar al final del archivo, antes del </body>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar tooltips de Bootstrap
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
    </script>
</body>

</html>