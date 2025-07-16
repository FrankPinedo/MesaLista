<?php if (!defined('BASE_URL')) require_once __DIR__ . '/../../../config/config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MesaLista - Cuenta Mesa <?= htmlspecialchars($mesa['nombre']) ?></title>
    <link rel="icon" href="<?= BASE_URL ?>/public/assets/img/logo_1.png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            .container { max-width: 100% !important; }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-3">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="bi bi-receipt"></i> Cuenta - Mesa <?= htmlspecialchars($mesa['nombre']) ?>
                </h4>
            </div>
            <div class="card-body">
                <?php if (empty($comandas)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                        <h5 class="mt-3">No hay comandas activas para esta mesa</h5>
                        <p class="text-muted">Esta mesa no tiene pedidos pendientes de pago.</p>
                    </div>
                <?php else: ?>
                    <div class="text-center mb-4">
                        <h5>RESUMEN DE CONSUMO</h5>
                        <p class="text-muted"><?= date('d/m/Y H:i') ?></p>
                    </div>
                    
                    <?php 
                    // Debug: Mostrar información de comandas
                    $debugInfo = [];
                    foreach ($comandas as $idx => $cmd) {
                        $debugInfo[] = "Comanda " . ($idx + 1) . ": " . count($cmd['items']) . " items";
                    }
                    
                    // Agrupar items por producto, evitando duplicados
                    $itemsAgrupados = [];
                    $itemsDetalle = []; // Para el debug
                    
                    foreach ($comandas as $comanda) {
                        if (!empty($comanda['items'])) {
                            foreach ($comanda['items'] as $item) {
                                // Para debug
                                $itemsDetalle[] = $item['nombre'] . " (Cant: " . $item['cantidad'] . ")";
                                
                                // Usar nombre y precio como clave para agrupar
                                $key = $item['nombre'] . '|' . $item['precio'];
                                
                                if (!isset($itemsAgrupados[$key])) {
                                    $itemsAgrupados[$key] = [
                                        'nombre' => $item['nombre'],
                                        'precio' => $item['precio'],
                                        'cantidad' => 0
                                    ];
                                }
                                $itemsAgrupados[$key]['cantidad'] += $item['cantidad'];
                            }
                        }
                    }
                    ?>
                    
                    <!-- Debug info (comentar en producción) -->
                    <!--
                    <div class="alert alert-info small">
                        <strong>Debug:</strong><br>
                        <?= implode('<br>', $debugInfo) ?><br>
                        <strong>Items totales:</strong> <?= count($itemsDetalle) ?><br>
                        <strong>Items:</strong> <?= implode(', ', $itemsDetalle) ?>
                    </div>
                    -->
                    
                    <div class="mb-3">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Cant</th>
                                    <th>Descripción</th>
                                    <th class="text-end">P. Unit</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $totalCalculado = 0;
                                foreach ($itemsAgrupados as $item): 
                                    $subtotal = $item['cantidad'] * $item['precio'];
                                    $totalCalculado += $subtotal;
                                ?>
                                    <tr>
                                        <td><?= $item['cantidad'] ?></td>
                                        <td><?= htmlspecialchars($item['nombre']) ?></td>
                                        <td class="text-end">S/ <?= number_format($item['precio'], 2) ?></td>
                                        <td class="text-end">S/ <?= number_format($subtotal, 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Subtotal:</th>
                                    <th class="text-end">S/ <?= number_format($totalCalculado, 2) ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <h4>TOTAL A PAGAR:</h4>
                        <h4>S/ <?= number_format($total, 2) ?></h4>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer no-print">
                <div class="d-flex justify-content-between">
                    <a href="<?= BASE_URL ?>/mozo" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                    <?php if (!empty($comandas)): ?>
                        <button onclick="window.print()" class="btn btn-primary">
                            <i class="bi bi-printer"></i> Imprimir
                        </button>
                        <button onclick="procesarPago()" class="btn btn-success">
                            <i class="bi bi-cash"></i> Procesar Pago
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function procesarPago() {
            if (confirm('¿Confirmar el pago de S/ <?= number_format($total, 2) ?>?')) {
                // Procesar pago completo
                fetch('<?= BASE_URL ?>/mozo/procesarPagoCompleto', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        mesa_id: <?= $mesaId ?>
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('✅ Pago exitoso');
                        window.location.href = '<?= BASE_URL ?>/mozo';
                    } else {
                        alert('Error al procesar el pago');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error de conexión');
                });
            }
        }
    </script>
</body>
</html>