<?php if (!defined('BASE_URL')) require_once __DIR__ . '/../../../config/config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MesaLista - Comanda Enviada</title>

    <!-- Logo -->
    <link rel="icon" href="<?= BASE_URL ?>/public/assets/img/logo_1.png" />

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT"
        crossorigin="anonymous" />

    <!-- Iconos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        .pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }
        
        .status-badge {
            font-size: 1.2rem;
            padding: 10px 20px;
        }
        
        .timer {
            font-size: 2rem;
            font-weight: bold;
            color: #0d6efd;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg">
                    <div class="card-header bg-success text-white text-center py-4">
                        <h3 class="mb-0">
                            <i class="bi bi-check-circle-fill"></i> 
                            COMANDA #<?= $comanda['id'] ?> - ENVIADA A COCINA
                        </h3>
                    </div>
                    
                    <div class="card-body text-center py-5">
                        <!-- Información de la mesa/delivery -->
                        <div class="mb-4">
                            <h4 class="text-muted">
                                <?php if ($comanda['mesa_id']): ?>
                                    <i class="bi bi-geo-alt-fill"></i> Mesa: <?= htmlspecialchars($comanda['mesa_nombre']) ?>
                                <?php else: ?>
                                    <i class="bi bi-bag-check-fill"></i> Pedido para Llevar
                                <?php endif; ?>
                            </h4>
                        </div>
                        
                        <!-- Estado de la comanda -->
                        <div class="mb-4">
                            <span class="badge status-badge bg-<?= $comanda['estado'] == 'pendiente' ? 'warning' : ($comanda['estado'] == 'recibido' ? 'info' : 'success') ?> pulse">
                                <?php 
                                    $estados = [
                                        'pendiente' => 'Esperando confirmación de cocina',
                                        'recibido' => 'En preparación',
                                        'listo' => 'Listo para servir'
                                    ];
                                    echo $estados[$comanda['estado']] ?? ucfirst($comanda['estado']);
                                ?>
                            </span>
                        </div>
                        
                        <!-- Timer -->
                        <div class="mb-5">
                            <p class="text-muted mb-1">Tiempo transcurrido:</p>
                            <div class="timer" id="timer">00:00</div>
                        </div>
                        
                        <!-- Botones de acción -->
                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <?php if ($comanda['estado'] == 'pendiente'): ?>
                                <a href="<?= BASE_URL ?>/mozo/editarComanda/<?= $comanda['id'] ?>" 
                                   class="btn btn-primary btn-lg">
                                    <i class="bi bi-pencil-square"></i> Editar Comanda
                                </a>
                            <?php else: ?>
                                <button class="btn btn-secondary btn-lg" disabled>
                                    <i class="bi bi-lock-fill"></i> Comanda en Preparación
                                </button>
                            <?php endif; ?>
                            
                            <?php if ($comanda['mesa_id']): ?>
                                <button onclick="nuevaComanda(<?= $comanda['mesa_id'] ?>)" 
                                        class="btn btn-success btn-lg">
                                    <i class="bi bi-plus-circle"></i> Nueva Comanda
                                </button>
                            <?php endif; ?>
                            
                            <a href="<?= BASE_URL ?>/mozo" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-arrow-left"></i> Volver al Panel
                            </a>
                        </div>
                        
                        <!-- Información adicional -->
                        <?php if ($comanda['estado'] == 'recibido'): ?>
                            <div class="alert alert-info mt-4">
                                <i class="bi bi-info-circle"></i> 
                                La comanda está siendo preparada. No es posible editarla en este momento.
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-footer text-center text-muted">
                        <small>
                            Comanda creada: <?= date('H:i', strtotime($comanda['fecha'])) ?> | 
                            Mozo: <?= htmlspecialchars($usuario['nombres']) ?>
                        </small>
                    </div>
                </div>
                
                <!-- Resumen de la comanda -->
                <div class="card mt-3 shadow">
                    <div class="card-header">
                        <h5 class="mb-0">Resumen de la Comanda</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Cant</th>
                                        <th>Producto</th>
                                        <th>Precio</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="resumen-items">
                                    <!-- Se llenará con JavaScript -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">Total:</th>
                                        <th id="total-resumen">S/ 0.00</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const BASE_URL = '<?= BASE_URL ?>';
        const comandaId = <?= $comanda['id'] ?>;
        const fechaComanda = new Date('<?= $comanda['fecha'] ?>');
        
        // Actualizar timer
        function actualizarTimer() {
            const ahora = new Date();
            const diferencia = ahora - fechaComanda;
            const minutos = Math.floor(diferencia / 60000);
            const segundos = Math.floor((diferencia % 60000) / 1000);
            
            document.getElementById('timer').textContent = 
                `${minutos.toString().padStart(2, '0')}:${segundos.toString().padStart(2, '0')}`;
        }
        
        // Cargar resumen de comanda
        function cargarResumen() {
            fetch(`${BASE_URL}/mozo/obtenerComanda/${comandaId}`)
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('resumen-items');
                    tbody.innerHTML = '';
                    let total = 0;
                    
                    data.detalles.forEach(item => {
                        const subtotal = item.precio * item.cantidad;
                        total += subtotal;
                        
                        tbody.innerHTML += `
                            <tr>
                                <td>${item.cantidad}</td>
                                <td>
                                    ${item.nombre}
                                    ${item.comentario ? `<small class="text-muted d-block">${item.comentario}</small>` : ''}
                                </td>
                                <td>S/ ${parseFloat(item.precio).toFixed(2)}</td>
                                <td>S/ ${subtotal.toFixed(2)}</td>
                            </tr>
                        `;
                    });
                    
                    document.getElementById('total-resumen').textContent = `S/ ${total.toFixed(2)}`;
                });
        }
        
        // Nueva comanda
        function nuevaComanda(mesaId) {
            if (confirm('¿Deseas crear una nueva comanda para esta mesa?')) {
                // Crear nueva comanda
                fetch(`${BASE_URL}/mozo/crearNuevaComanda`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        mesa_id: mesaId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = `${BASE_URL}/mozo/comanda/${mesaId}`;
                    }
                });
            }
        }
        
        // Actualizar estado cada 5 segundos
        function verificarEstado() {
            fetch(`${BASE_URL}/mozo/verificarEstadoComanda/${comandaId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.estado !== '<?= $comanda['estado'] ?>') {
                        location.reload();
                    }
                });
        }
        
        // Inicializar
        setInterval(actualizarTimer, 1000);
        setInterval(verificarEstado, 5000);
        cargarResumen();
        actualizarTimer();
    </script>
</body>
</html>