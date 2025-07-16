<?php if (!defined('BASE_URL')) require_once __DIR__ . '/../../../config/config.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MesaLista - Notificaciones</title>
    <link rel="icon" href="<?= BASE_URL ?>/public/assets/img/logo_1.png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-3">
        <div class="card shadow">
            <div class="card-header bg-warning">
                <h4 class="mb-0">
                    <i class="bi bi-bell-fill"></i> Notificaciones
                </h4>
            </div>
            <div class="card-body">
                <div id="lista-notificaciones">
                    <div class="text-center py-4">
                        <div class="spinner-border text-warning" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="<?= BASE_URL ?>/mozo" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <script>
        const BASE_URL = '<?= BASE_URL ?>';

        function cargarNotificaciones() {
            fetch(`${BASE_URL}/mozo/verificarComandasListas`)
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('lista-notificaciones');

                    if (data.comandasListas && data.comandasListas.length > 0) {
                        let html = '<div class="list-group">';
                        data.comandasListas.forEach(comanda => {
                            html += `
                                <div class="list-group-item list-group-item-action" ondblclick="eliminarNotificacion(this, ${comanda.id})" style="cursor: pointer;">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">Comanda #${comanda.id}</h5>
                                        <span class="badge bg-success">LISTA</span>
                                    </div>
                                    <p class="mb-1">Mesa: ${comanda.mesa}</p>
                                    <small class="text-muted">Doble clic para marcar como entregada</small>
                                </div>
                            `;
                        });
                        html += '</div>';
                        container.innerHTML = html;
                    } else {
                        mostrarSinNotificaciones();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('lista-notificaciones').innerHTML =
                        '<div class="alert alert-danger">Error al cargar notificaciones</div>';
                });
        }

        function mostrarSinNotificaciones() {
            document.getElementById('lista-notificaciones').innerHTML = `
                <div class="text-center py-4">
                    <i class="bi bi-bell-slash fs-1 text-muted"></i>
                    <p class="text-muted mt-2">No hay notificaciones pendientes</p>
                </div>
            `;
        }

        // Función para eliminar notificación permanentemente
        function eliminarNotificacion(elemento, comandaId) {

            if (!confirm('¿Ya entregaste este pedido a la mesa?')) {
                return;
            }
            // Animación de desvanecimiento
            elemento.style.transition = 'all 0.3s ease';
            elemento.style.transform = 'translateX(-100%)';
            elemento.style.opacity = '0';

            // Marcar como entregada en el servidor
            fetch(`${BASE_URL}/mozo/marcarEntregada`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        comanda_id: comandaId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remover el elemento después de la animación
                        setTimeout(() => {
                            elemento.remove();

                            // Verificar si quedan notificaciones
                            const notificacionesRestantes = document.querySelectorAll('.list-group-item').length;
                            if (notificacionesRestantes === 0) {
                                mostrarSinNotificaciones();
                            }
                        }, 300);
                    } else {
                        // Si hay error, restaurar el elemento
                        elemento.style.transition = '';
                        elemento.style.transform = '';
                        elemento.style.opacity = '';
                        alert('Error al marcar como entregada');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Restaurar elemento si hay error
                    elemento.style.transition = '';
                    elemento.style.transform = '';
                    elemento.style.opacity = '';
                });
        }

        // Cargar al inicio
        cargarNotificaciones();

        // Actualizar cada 10 segundos
        setInterval(cargarNotificaciones, 10000);
    </script>
</body>

</html>