<?php
class MozoController
{
    private $usuario;
    private $comandaModel;
    private $mesaModel;

    public function __construct()
    {
        require_once __DIR__ . '/../../config/config.php';
        require_once __DIR__ . '/../helpers/sesion.php';
        require_once __DIR__ . '/../models/ComandaModel.php';
        require_once __DIR__ . '/../models/MesaModel.php';

        $this->usuario = verificarSesion();
        session_regenerate_id(true);

        if ($this->usuario['rol'] !== 'mozo') {
            require_once __DIR__ . '/../controllers/ErrorController.php';
            (new ErrorController())->index('403');
            exit();
        }

        $this->comandaModel = new ComandaModel();
        $this->mesaModel = new MesaModel();
    }

    public function index()
    {
        // Procesar acciones POST primero
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->procesarAccionesMesa();
            return;
        }

        $usuario = $this->usuario;
        $mesas = $this->mesaModel->obtenerMesas();

        // Actualizar tiempo de comandas para cada mesa
        foreach ($mesas as &$mesa) {
            if ($mesa['estado'] === 'esperando') {
                $comandaActiva = $this->comandaModel->obtenerComandaActivaPorMesa($mesa['id']);
                if ($comandaActiva) {
                    $mesa['tiempo_comanda'] = $this->comandaModel->obtenerTiempoComanda($comandaActiva['id']);
                }
            }
        }

        require_once __DIR__ . '/../views/mozo/inicio.php';
    }

    // En el archivo app/controllers/MozoController.php, actualiza el método comanda:

public function comanda($mesaId = null)
{
    $usuario = $this->usuario;

    // Si es delivery/para llevar
    if (!$mesaId || $mesaId === 'delivery') {
        $comandaId = $this->comandaModel->crearComandaDelivery($usuario['id_user']);
        $comanda = $this->comandaModel->obtenerComandaPorId($comandaId);
        $mesa = 'Delivery/Para Llevar';
        $mesaId = null;
    } else {
        // Verificar si la mesa existe
        $mesa = $this->mesaModel->obtenerMesaPorId($mesaId);
        if (!$mesa) {
            header("Location: " . BASE_URL . "/mozo");
            exit();
        }

        // Buscar comandas activas de la mesa
        $comandasActivas = $this->comandaModel->obtenerComandasActivasPorMesa($mesaId);

        // Buscar una comanda editable (nueva o pendiente)
        $comandaEditable = null;
        foreach ($comandasActivas as $cmd) {
            if (in_array($cmd['estado'], ['nueva', 'pendiente'])) {
                $comandaEditable = $cmd;
                break;
            }
        }

        if ($comandaEditable) {
            // Usar la comanda editable existente
            $comanda = $comandaEditable;
        } else {
            // No hay comanda editable, crear una nueva
            $comandaId = $this->comandaModel->crearComanda($mesaId, $usuario['id_user']);
            $comanda = $this->comandaModel->obtenerComandaPorId($comandaId);
            
            // Solo cambiar estado de mesa si no hay otras comandas activas
            if (empty($comandasActivas)) {
                $this->mesaModel->cambiarEstado($mesaId, 'ocupada');
            }
        }

        $mesa = $mesa['nombre'];
    }

    // Determinar si se puede editar
    $puedeEditar = in_array($comanda['estado'], ['nueva', 'pendiente']);

    // IMPORTANTE: Siempre obtener los detalles, independientemente del estado
    $detalles = [];
    $detallesPendientes = [];
    $tieneCambiosPendientes = false;

    if (in_array($comanda['estado'], ['pendiente', 'recibido'])) {
        // Para comandas pendientes o recibidas, obtener tanto confirmados como pendientes
        $detallesConPendientes = $this->comandaModel->obtenerDetallesComandaConPendientes($comanda['id']);
        $detalles = $detallesConPendientes['confirmados'] ?? [];
        $detallesPendientes = $detallesConPendientes['pendientes'] ?? [];
        $tieneCambiosPendientes = $this->comandaModel->tieneCambiosPendientes($comanda['id']);
    } else {
        // Para comandas nuevas, obtener todos los detalles
        $detalles = $this->comandaModel->obtenerDetallesComandaCompletos($comanda['id']);
        $detallesPendientes = [];
        $tieneCambiosPendientes = false;
    }

    // Calcular total
    $total = 0;
    foreach ($detalles as $detalle) {
        $total += $detalle['precio'] * $detalle['cantidad'];
    }
    if (!empty($detallesPendientes)) {
        foreach ($detallesPendientes as $detalle) {
            $total += $detalle['precio'] * $detalle['cantidad'];
        }
    }

    // Obtener productos disponibles
    require_once __DIR__ . '/../models/ProductoModel.php';
    $productoModel = new ProductoModel();
    $platos = $productoModel->obtenerProductosPorTipo(2, true); // tipo 2 = platos
    $bebidas = $productoModel->obtenerProductosPorTipo(1, true); // tipo 1 = bebidas
    $combos = $productoModel->obtenerProductosPorTipo(4, true); // tipo 4 = combos

    // Obtener comandas anteriores de la mesa
    $comandasAnteriores = [];
    $totalMesa = $total;
    if ($mesaId) {
        $todasLasComandas = $this->comandaModel->obtenerComandasActivasPorMesa($mesaId);
        foreach ($todasLasComandas as $cmd) {
            if ($cmd['id'] != $comanda['id']) {
                $comandasAnteriores[] = $cmd;
            }
        }
        $totalMesa = $this->comandaModel->obtenerTotalMesa($mesaId);
    }

    $numeroComanda = count($comandasAnteriores) + 1;

    require_once __DIR__ . '/../views/mozo/comanda.php';
}

    public function agregarItem()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit();
        }

        $data = json_decode(file_get_contents('php://input'), true);

        $comandaId = $data['id_comanda'] ?? null;
        $productoId = $data['id_plato'] ?? null;
        $cantidad = $data['cantidad'] ?? 1;
        $comentario = $data['comentario'] ?? '';

        if (!$comandaId || !$productoId) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            exit();
        }

        // Verificar estado de la comanda
        $comanda = $this->comandaModel->obtenerComandaPorId($comandaId);

        if ($comanda['estado'] === 'nueva') {
            // Si es nueva, agregar normalmente
            $resultado = $this->comandaModel->agregarItemComanda($comandaId, $productoId, $cantidad, $comentario);
        } else if (in_array($comanda['estado'], ['pendiente'])) {
            // Si está pendiente, agregar como cambio pendiente
            $resultado = $this->comandaModel->agregarItemComandaPendiente($comandaId, $productoId, $cantidad, $comentario);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se puede modificar esta comanda']);
            exit();
        }

        if (is_array($resultado)) {
            echo json_encode($resultado);
        } else {
            echo json_encode(['success' => $resultado, 'pendiente' => $comanda['estado'] !== 'nueva']);
        }
        exit();
    }

    


    public function obtenerStockActualizado()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit();
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $productoIds = $data['producto_ids'] ?? [];

        if (empty($productoIds)) {
            echo json_encode(['success' => false, 'message' => 'No se proporcionaron IDs de productos']);
            exit();
        }

        require_once __DIR__ . '/../models/ProductoModel.php';
        $productoModel = new ProductoModel();
        
        // Obtener stocks actuales
        $stocks = [];
        foreach ($productoIds as $productoId) {
            $producto = $productoModel->obtenerProductoPorId($productoId);
            if ($producto) {
                $stocks[$productoId] = $producto['stock'];
            }
        }

        echo json_encode(['success' => true, 'stocks' => $stocks]);
        exit();
    }

    // Agregar método para confirmar cambios
    public function confirmarCambios()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit();
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $comandaId = $data['id_comanda'] ?? null;

        if (!$comandaId) {
            echo json_encode(['success' => false, 'message' => 'ID de comanda requerido']);
            exit();
        }

        $resultado = $this->comandaModel->confirmarCambiosPendientes($comandaId);

        echo json_encode(['success' => $resultado]);
        exit();
    }

    // Agregar método para cancelar cambios
    public function cancelarCambios()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit();
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $comandaId = $data['id_comanda'] ?? null;

        if (!$comandaId) {
            echo json_encode(['success' => false, 'message' => 'ID de comanda requerido']);
            exit();
        }

        $resultado = $this->comandaModel->cancelarCambiosPendientes($comandaId);

        echo json_encode(['success' => $resultado]);
        exit();
    }

    public function actualizarComentario()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit();
        }

        $data = json_decode(file_get_contents('php://input'), true);

        $detalleId = $data['id_detalle'] ?? null;
        $comentario = $data['comentario'] ?? '';

        if (!$detalleId) {
            echo json_encode(['success' => false, 'message' => 'ID de detalle requerido']);
            exit();
        }

        $resultado = $this->comandaModel->actualizarComentarioDetalle($detalleId, $comentario);

        echo json_encode(['success' => $resultado]);
        exit();
    }

    public function eliminarItem()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit();
        }

        $data = json_decode(file_get_contents('php://input'), true);

        $detalleId = $data['id_detalle'] ?? null;

        if (!$detalleId) {
            echo json_encode(['success' => false, 'message' => 'ID de detalle requerido']);
            exit();
        }

        $resultado = $this->comandaModel->eliminarDetalleComanda($detalleId);

        echo json_encode(['success' => $resultado]);
        exit();
    }

    public function obtenerComanda($comandaId)
    {
        $detalles = $this->comandaModel->obtenerDetallesComandaCompletos($comandaId);

        echo json_encode(['detalles' => $detalles]);
        exit();
    }

    // Agregar este método al archivo app/controllers/MozoController.php:

public function obtenerComandaConPendientes($comandaId)
{
    header('Content-Type: application/json');
    
    try {
        // Obtener detalles separados (confirmados y pendientes)
        $resultado = $this->comandaModel->obtenerDetallesComandaConPendientes($comandaId);
        
        // Asegurarse de que siempre devuelva arrays
        $response = [
            'detalles' => $resultado['confirmados'] ?? [],
            'detallesPendientes' => $resultado['pendientes'] ?? []
        ];
        
        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode([
            'detalles' => [],
            'detallesPendientes' => [],
            'error' => $e->getMessage()
        ]);
    }
    exit();
}
    public function enviarComanda()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit();
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $comandaId = $data['id_comanda'] ?? null;

        if (!$comandaId) {
            echo json_encode(['success' => false, 'message' => 'ID de comanda requerido']);
            exit();
        }

        // Verificar que la comanda tenga items
        $detalles = $this->comandaModel->obtenerDetallesComandaCompletos($comandaId);
        if (empty($detalles)) {
            echo json_encode(['success' => false, 'message' => 'La comanda está vacía']);
            exit();
        }

        // Cambiar estado a pendiente (enviar a cocina)
        $resultado = $this->comandaModel->actualizarEstadoComanda($comandaId, 'pendiente');

        if ($resultado) {
            // Actualizar estado de la mesa si existe
            $comanda = $this->comandaModel->obtenerComandaPorId($comandaId);
            if ($comanda['mesa_id']) {
                $this->mesaModel->cambiarEstado($comanda['mesa_id'], 'esperando');
            }
        }

        echo json_encode(['success' => $resultado]);
        exit();
    }

    public function verificarEstadoComanda($comandaId)
    {
        $comanda = $this->comandaModel->obtenerComandaPorId($comandaId);

        echo json_encode([
            'success' => true,
            'estado' => $comanda['estado'] ?? null
        ]);
        exit();
    }

    public function verificarComandasListas()
    {
        $comandasListas = $this->comandaModel->obtenerComandasListas();

        echo json_encode([
            'comandasListas' => $comandasListas
        ]);
        exit();
    }

    public function marcarEntregada()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit();
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $comandaId = $data['comanda_id'] ?? null;

        if (!$comandaId) {
            echo json_encode(['success' => false, 'message' => 'ID de comanda requerido']);
            exit();
        }

        $resultado = $this->comandaModel->actualizarEstadoComanda($comandaId, 'entregado');

        if ($resultado) {
            $comanda = $this->comandaModel->obtenerComandaPorId($comandaId);
            if ($comanda['mesa_id']) {
                $this->mesaModel->cambiarEstado($comanda['mesa_id'], 'atendido');
            }
        }

        echo json_encode(['success' => $resultado]);
        exit();
    }

    public function notificaciones()
    {
        $usuario = $this->usuario;
        require_once __DIR__ . '/../views/mozo/notificaciones.php';
    }

    public function mostrarCuenta($mesaId)
    {
        $usuario = $this->usuario;
        $mesa = $this->mesaModel->obtenerMesaPorId($mesaId);

        if (!$mesa) {
            header("Location: " . BASE_URL . "/mozo");
            exit();
        }

        $comandas = $this->comandaModel->obtenerComandasMesa($mesaId);
        $total = $this->comandaModel->obtenerTotalMesa($mesaId);

        // Obtener detalles de cada comanda
        foreach ($comandas as &$comanda) {
            $comanda['items'] = $this->comandaModel->obtenerDetallesComandaCompletos($comanda['id']);
        }

        require_once __DIR__ . '/../views/mozo/cuenta.php';
    }

    public function procesarPagoCompleto()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit();
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $mesaId = $data['mesa_id'] ?? null;

        if (!$mesaId) {
            echo json_encode(['success' => false, 'message' => 'ID de mesa requerido']);
            exit();
        }

        $resultado = $this->comandaModel->procesarPagoCompleto($mesaId);

        echo json_encode(['success' => $resultado]);
        exit();
    }

    public function verificarComandasMesa($mesaId)
    {
        $comandas = $this->comandaModel->obtenerComandasMesa($mesaId);

        echo json_encode([
            'tieneComandas' => !empty($comandas)
        ]);
        exit();
    }

    public function procesarAccionesMesa()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Agregar mesa
            if (isset($_POST['agregar_mesa'])) {
                $this->mesaModel->agregarMesa();
                header("Location: " . BASE_URL . "/mozo?mesa_agregada=1");
                exit();
            }

            // Eliminar mesa
            if (isset($_POST['eliminar_mesa_id'])) {
                $mesaId = intval($_POST['eliminar_mesa_id']);
                $this->mesaModel->eliminarMesa($mesaId);
                header("Location: " . BASE_URL . "/mozo");
                exit();
            }

            // Juntar mesas
            if (isset($_POST['mesa_ids'])) {
                $mesaIds = json_decode($_POST['mesa_ids'], true);
                if (count($mesaIds) >= 2) {
                    $idBase = array_shift($mesaIds);
                    foreach ($mesaIds as $id) {
                        $this->mesaModel->juntarMesas($idBase, $id);
                    }
                }
                header("Location: " . BASE_URL . "/mozo");
                exit();
            }

            // Separar mesa
            if (isset($_POST['separar_mesa_nombre'])) {
                $this->mesaModel->separarMesa($_POST['separar_mesa_nombre']);
                header("Location: " . BASE_URL . "/mozo");
                exit();
            }

            // Cambiar estado
            if (isset($_POST['cambiar_estado_id']) && isset($_POST['nuevo_estado'])) {
                $mesaId = intval($_POST['cambiar_estado_id']);
                $nuevoEstado = $_POST['nuevo_estado'];
                $this->mesaModel->cambiarEstado($mesaId, $nuevoEstado);
                header("Location: " . BASE_URL . "/mozo");
                exit();
            }
        }
    }

    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();
        header("Location: " . BASE_URL . "/login");
        exit();
    }
}