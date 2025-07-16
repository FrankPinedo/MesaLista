<?php
class ComandaModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->conn->connect_error) {
            die('Conexión fallida: ' . $this->conn->connect_error);
        }
    }

    // Crear nueva comanda con mesa y usuario
    public function crearComanda($mesaId, $usuarioId)
    {
        // Primero verificar si ya existe una comanda activa para esta mesa
        $sql = "SELECT id FROM comanda WHERE mesa_id = ? AND estado IN ('nueva', 'pendiente', 'recibido') LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $mesaId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['id'];
        }

        // Si no existe, crear nueva comanda con estado 'nueva'
        $sql = "INSERT INTO comanda (mesa_id, usuario_id, estado, tipo_entrega_id, fecha) 
            VALUES (?, ?, 'nueva', 3, NOW())"; // Estado 'nueva' en lugar de 'pendiente'
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $mesaId, $usuarioId);
        $stmt->execute();
        return $this->conn->insert_id;
    }

    // NUEVO: Crear comanda siempre nueva (para múltiples comandas)
    public function crearNuevaComanda($mesaId, $usuarioId)
    {
        $sql = "INSERT INTO comanda (mesa_id, usuario_id, estado, tipo_entrega_id, fecha) 
                VALUES (?, ?, 'nueva', 3, NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $mesaId, $usuarioId);
        $stmt->execute();
        return $this->conn->insert_id;
    }

    // Obtener comanda activa por mesa
    public function obtenerComandaActivaPorMesa($mesaId)
    {
        $sql = "SELECT * FROM comanda 
        WHERE mesa_id = ? AND estado IN ('nueva', 'pendiente', 'recibido', 'listo', 'entregado') 
        ORDER BY fecha DESC LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $mesaId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // NUEVO: Obtener TODAS las comandas activas de una mesa
    public function obtenerComandasActivasPorMesa($mesaId)
    {
        $sql = "SELECT * FROM comanda 
                WHERE mesa_id = ? 
                AND estado IN ('nueva', 'pendiente', 'recibido', 'listo', 'entregado') 
                ORDER BY fecha ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $mesaId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $comandas = [];
        while ($row = $result->fetch_assoc()) {
            $comandas[] = $row;
        }
        
        return $comandas;
    }

    // NUEVO: Verificar si se puede editar una comanda
    public function puedeEditarComanda($comandaId)
    {
        $sql = "SELECT estado FROM comanda WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $comandaId);
        $stmt->execute();
        $result = $stmt->get_result();
        $comanda = $result->fetch_assoc();
        
        // Solo se puede editar si está en estado 'nueva' o 'pendiente'
        return $comanda && in_array($comanda['estado'], ['nueva', 'pendiente']);
    }

    // Obtener detalles de comanda con información completa
    public function obtenerDetallesComandaCompletos($comandaId)
    {
        $sql = "SELECT dc.id as id_detalle, dc.cantidad, dc.comentario, dc.cancelado,
                p.id as id_plato, p.nombre, p.precio, p.descripcion
                FROM detalle_comanda dc
                JOIN producto p ON dc.producto_id = p.id
                WHERE dc.comanda_id = ? AND dc.cancelado = 0";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $comandaId);
        $stmt->execute();
        $result = $stmt->get_result();

        $detalles = [];
        while ($row = $result->fetch_assoc()) {
            $detalles[] = $row;
        }

        return $detalles;
    }

    // Agregar item a la comanda
    public function agregarItemComanda($comandaId, $productoId, $cantidad, $comentario = '')
    {
        // NUEVO: Verificar si se puede editar
        if (!$this->puedeEditarComanda($comandaId)) {
            return false;
        }
        
        // Para comandas en estado 'pendiente', marcar como "con cambios pendientes"
        $comanda = $this->obtenerComandaPorId($comandaId);
        if ($comanda && $comanda['estado'] === 'pendiente') {
            // Aquí podrías agregar un campo en la BD para marcar cambios pendientes
            // Por ahora, simplemente agregamos el item
        }
        
        $sql = "INSERT INTO detalle_comanda (comanda_id, producto_id, cantidad, comentario, cancelado) 
                VALUES (?, ?, ?, ?, 0)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iiis", $comandaId, $productoId, $cantidad, $comentario);
        return $stmt->execute();
    }

    // Obtener detalle específico
    public function obtenerDetalleComanda($detalleId)
    {
        $sql = "SELECT * FROM detalle_comanda WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $detalleId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Actualizar comentario
    public function actualizarComentarioDetalle($detalleId, $comentario)
    {
        $sql = "UPDATE detalle_comanda SET comentario = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $comentario, $detalleId);
        return $stmt->execute();
    }

    // Eliminar item de comanda
    public function eliminarDetalleComanda($detalleId)
    {
        // NUEVO: Obtener comanda_id y verificar si se puede editar
        $sql = "SELECT comanda_id FROM detalle_comanda WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $detalleId);
        $stmt->execute();
        $result = $stmt->get_result();
        $detalle = $result->fetch_assoc();
        
        if (!$detalle || !$this->puedeEditarComanda($detalle['comanda_id'])) {
            return false;
        }
        
        $sql = "DELETE FROM detalle_comanda WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $detalleId);
        return $stmt->execute();
    }

    // Actualizar estado de comanda
    public function actualizarEstadoComanda($comandaId, $estado)
    {
        $sql = "UPDATE comanda SET estado = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $estado, $comandaId);
        return $stmt->execute();
    }

    /* Métodos para cocina */
    /* Obtiene las comandas pendientes con sus detalles */
    public function obtenerComandasPendientes()
    {
        $sql = "SELECT c.id, c.estado, c.mesa_id, te.nombre as tipo_entrega, 
            DATE_FORMAT(c.fecha, '%H:%i') as hora, 
            TIMESTAMPDIFF(MINUTE, c.fecha, NOW()) as minutos_transcurridos
            FROM comanda c
            JOIN tipo_entrega te ON c.tipo_entrega_id = te.id
            WHERE c.estado IN ('pendiente', 'recibido')  -- No incluir 'nueva'
            ORDER BY c.fecha ASC";

        $result = $this->conn->query($sql);
        if (!$result) {
            error_log("Error en obtenerComandasPendientes: " . $this->conn->error);
            return [];
        }

        $comandas = [];
        while ($row = $result->fetch_assoc()) {
            $comandaId = $row['id'];
            $row['items'] = $this->obtenerDetallesComanda($comandaId);
            $comandas[] = $row;
        }

        return $comandas;
    }

    /* Obtiene los detalles de una comanda específica para cocina */
private function obtenerDetallesComanda($comandaId)
{
    $items = [];
    $sql = "SELECT dc.id, p.nombre, p.descripcion, dc.cantidad, dc.comentario,
                   tp.nombre as tipo_producto, t.nombre as tamano
            FROM detalle_comanda dc
            JOIN producto p ON dc.producto_id = p.id
            JOIN tipo_producto tp ON p.tipo_producto_id = tp.id
            LEFT JOIN tamano t ON p.tamano_id = t.id
            WHERE dc.comanda_id = ? 
            AND dc.cancelado = 0
            AND dc.es_cambio_pendiente = FALSE"; // Solo mostrar confirmados

    $stmt = $this->conn->prepare($sql);
    if (!$stmt) {
        error_log("Error preparando obtenerDetallesComanda: " . $this->conn->error);
        return [];
    }

    $stmt->bind_param("i", $comandaId);
    if (!$stmt->execute()) {
        error_log("Error ejecutando obtenerDetallesComanda: " . $stmt->error);
        return [];
    }

    $result = $stmt->get_result();
    while ($item = $result->fetch_assoc()) {
        $itemId = $item['id'];
        $item['guarniciones'] = $this->obtenerGuarnicionesItem($itemId);
        $item['opciones_combo'] = $this->obtenerOpcionesCombo($itemId);
        $items[] = $item;
    }

    return $items;
}
    /* Obtiene las guarniciones asociadas a un item de comanda */
    private function obtenerGuarnicionesItem($detalleComandaId)
    {
        $sql = "SELECT g.nombre, g.descripcion
                FROM detalle_comanda_guarnicion dcg
                JOIN guarnicion g ON dcg.guarnicion_id = g.id
                WHERE dcg.detalle_comanda_id = ?";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt || !$stmt->bind_param("i", $detalleComandaId) || !$stmt->execute()) {
            error_log("Error en obtenerGuarnicionesItem: " . ($stmt ? $stmt->error : $this->conn->error));
            return [];
        }

        $result = $stmt->get_result();
        $guarniciones = [];
        while ($row = $result->fetch_assoc()) {
            $guarniciones[] = $row;
        }

        return $guarniciones;
    }

    /* Obtiene las opciones de combo asociadas a un item de comanda */
    private function obtenerOpcionesCombo($detalleComandaId)
    {
        $sql = "SELECT p.nombre, p.descripcion
                FROM detalle_comanda_combo_opciones dcco
                JOIN producto p ON dcco.producto_id = p.id
                WHERE dcco.detalle_comanda_id = ?";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt || !$stmt->bind_param("i", $detalleComandaId) || !$stmt->execute()) {
            error_log("Error en obtenerOpcionesCombo: " . ($stmt ? $stmt->error : $this->conn->error));
            return [];
        }

        $result = $stmt->get_result();
        $opciones = [];
        while ($row = $result->fetch_assoc()) {
            $opciones[] = $row;
        }

        return $opciones;
    }

    /* Cancela un item específico de una comanda */
    public function cancelarItem($itemId)
    {
        $sql = "UPDATE detalle_comanda SET cancelado = 1 WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt || !$stmt->bind_param("i", $itemId) || !$stmt->execute()) {
            error_log("Error en cancelarItem: " . ($stmt ? $stmt->error : $this->conn->error));
            return false;
        }
        return true;
    }

    /* Recupera un item cancelado */
    public function recuperarItem($itemId)
    {
        $sql = "UPDATE detalle_comanda SET cancelado = 0 WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt || !$stmt->bind_param("i", $itemId) || !$stmt->execute()) {
            error_log("Error en recuperarItem: " . ($stmt ? $stmt->error : $this->conn->error));
            return false;
        }
        return true;
    }

    /* Cancela una comanda completa */
    public function cancelarComanda($comandaId)
    {
        $this->conn->begin_transaction();
        try {
            // Primero cancelamos todos los items
            $sqlItems = "UPDATE detalle_comanda SET cancelado = 1 WHERE comanda_id = ?";
            $stmtItems = $this->conn->prepare($sqlItems);
            if (!$stmtItems || !$stmtItems->bind_param("i", $comandaId) || !$stmtItems->execute()) {
                throw new Exception("Error cancelando items: " . ($stmtItems ? $stmtItems->error : $this->conn->error));
            }

            // Luego cancelamos la comanda
            $sqlComanda = "UPDATE comanda SET estado = 'cancelado' WHERE id = ?";
            $stmtComanda = $this->conn->prepare($sqlComanda);
            if (!$stmtComanda || !$stmtComanda->bind_param("i", $comandaId) || !$stmtComanda->execute()) {
                throw new Exception("Error cancelando comanda: " . ($stmtComanda ? $stmtComanda->error : $this->conn->error));
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log($e->getMessage());
            return false;
        }
    }

    /* Obtiene el tiempo transcurrido desde la creación de la comanda */
    public function obtenerTiempoComanda($comandaId)
    {
        $sql = "SELECT TIMESTAMPDIFF(MINUTE, fecha, NOW()) as minutos 
                FROM comanda WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt || !$stmt->bind_param("i", $comandaId) || !$stmt->execute()) {
            error_log("Error en obtenerTiempoComanda: " . ($stmt ? $stmt->error : $this->conn->error));
            return 0;
        }

        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        return $data ? $data['minutos'] : 0;
    }
    
    public function crearComandaDelivery($usuarioId)
    {
        // Crear comanda sin mesa asociada, con tipo de entrega "para llevar" (id=2)
        $sql = "INSERT INTO comanda (mesa_id, usuario_id, estado, tipo_entrega_id, fecha) 
                VALUES (NULL, ?, 'nueva', 2, NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $usuarioId);
        $stmt->execute();
        return $this->conn->insert_id;
    }

    public function obtenerComandaPorId($comandaId)
    {
        $sql = "SELECT c.*, m.nombre as mesa_nombre 
            FROM comanda c
            LEFT JOIN mesas m ON c.mesa_id = m.id
            WHERE c.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $comandaId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Obtener total de todas las comandas de una mesa
    public function obtenerTotalMesa($mesaId)
    {
        $sql = "SELECT SUM(dc.cantidad * p.precio) as total
            FROM comanda c
            JOIN detalle_comanda dc ON c.id = dc.comanda_id
            JOIN producto p ON dc.producto_id = p.id
            WHERE c.mesa_id = ? 
            AND c.estado IN ('nueva', 'pendiente', 'recibido', 'listo', 'entregado')
            AND dc.cancelado = 0";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $mesaId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['total'] ?? 0;
    }

    // Obtener todas las comandas activas de una mesa
    public function obtenerComandasMesa($mesaId)
    {
        $sql = "SELECT c.*, DATE_FORMAT(c.fecha, '%H:%i') as hora
            FROM comanda c
            WHERE c.mesa_id = ? 
            AND c.estado IN ('nueva', 'pendiente', 'recibido', 'listo', 'entregado')
            ORDER BY c.fecha ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $mesaId);
        $stmt->execute();
        $result = $stmt->get_result();

        $comandas = [];
        while ($row = $result->fetch_assoc()) {
            $comandas[] = $row;
        }

        return $comandas;
    }

    // En ComandaModel.php - línea ~461
    public function obtenerComandasListas()
    {
        $sql = "SELECT c.id, m.nombre as mesa
            FROM comanda c
            LEFT JOIN mesas m ON c.mesa_id = m.id
            WHERE c.estado = 'listo'";  // Solo mostrar 'listo', no 'entregado'

        $result = $this->conn->query($sql);
        $comandas = [];

        while ($row = $result->fetch_assoc()) {
            $comandas[] = [
                'id' => $row['id'],
                'mesa' => $row['mesa'] ?? 'Delivery'
            ];
        }

        return $comandas;
    }

    public function finalizarComandasMesa($mesaId)
    {
        $sql = "UPDATE comanda 
            SET estado = 'pagado' 
            WHERE mesa_id = ? 
            AND estado IN ('nueva', 'pendiente', 'recibido', 'listo', 'entregado')";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $mesaId);
        return $stmt->execute();
    }

    // NUEVO: Procesar pago completo - elimina todas las comandas y libera la mesa
    public function procesarPagoCompleto($mesaId)
    {
        $this->conn->begin_transaction();
        
        try {
            // Eliminar todas las comandas de la mesa
            $sql = "DELETE FROM comanda WHERE mesa_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $mesaId);
            $stmt->execute();
            
            // Liberar la mesa
            $sql2 = "UPDATE mesas SET estado = 'libre' WHERE id = ?";
            $stmt2 = $this->conn->prepare($sql2);
            $stmt2->bind_param("i", $mesaId);
            $stmt2->execute();
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    // Agregar estos métodos a la clase ComandaModel

// Agregar item como cambio pendiente
public function agregarItemComandaPendiente($comandaId, $productoId, $cantidad, $comentario = '')
{
    // Verificar si la comanda está en estado que permite cambios pendientes
    $comanda = $this->obtenerComandaPorId($comandaId);
    if (!$comanda || !in_array($comanda['estado'], ['pendiente', 'recibido'])) {
        return false;
    }
    
    // Marcar la comanda como con cambios pendientes
    $sql = "UPDATE comanda SET tiene_cambios_pendientes = TRUE WHERE id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $comandaId);
    $stmt->execute();
    
    // Agregar el item como cambio pendiente
    $sql = "INSERT INTO detalle_comanda (comanda_id, producto_id, cantidad, comentario, cancelado, es_cambio_pendiente) 
            VALUES (?, ?, ?, ?, 0, TRUE)";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("iiis", $comandaId, $productoId, $cantidad, $comentario);
    return $stmt->execute();
}

// Obtener detalles separando los pendientes
public function obtenerDetallesComandaConPendientes($comandaId)
{
    $sql = "SELECT dc.id as id_detalle, dc.cantidad, dc.comentario, dc.cancelado, dc.es_cambio_pendiente,
            p.id as id_plato, p.nombre, p.precio, p.descripcion
            FROM detalle_comanda dc
            JOIN producto p ON dc.producto_id = p.id
            WHERE dc.comanda_id = ? AND dc.cancelado = 0
            ORDER BY dc.es_cambio_pendiente, dc.id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $comandaId);
    $stmt->execute();
    $result = $stmt->get_result();

    $detalles = [
        'confirmados' => [],
        'pendientes' => []
    ];
    
    while ($row = $result->fetch_assoc()) {
        if ($row['es_cambio_pendiente']) {
            $detalles['pendientes'][] = $row;
        } else {
            $detalles['confirmados'][] = $row;
        }
    }

    return $detalles;
}

// Confirmar cambios pendientes
public function confirmarCambiosPendientes($comandaId)
{
    $this->conn->begin_transaction();
    
    try {
        // Marcar todos los items pendientes como confirmados
        $sql = "UPDATE detalle_comanda 
                SET es_cambio_pendiente = FALSE 
                WHERE comanda_id = ? AND es_cambio_pendiente = TRUE";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $comandaId);
        $stmt->execute();
        
        // Actualizar la comanda
        $sql = "UPDATE comanda 
                SET tiene_cambios_pendientes = FALSE,
                    ultima_actualizacion = NOW()
                WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $comandaId);
        $stmt->execute();
        
        $this->conn->commit();
        return true;
    } catch (Exception $e) {
        $this->conn->rollback();
        return false;
    }
}

// Cancelar cambios pendientes
public function cancelarCambiosPendientes($comandaId)
{
    $this->conn->begin_transaction();
    
    try {
        // Eliminar todos los items pendientes
        $sql = "DELETE FROM detalle_comanda 
                WHERE comanda_id = ? AND es_cambio_pendiente = TRUE";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $comandaId);
        $stmt->execute();
        
        // Actualizar la comanda
        $sql = "UPDATE comanda SET tiene_cambios_pendientes = FALSE WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $comandaId);
        $stmt->execute();
        
        $this->conn->commit();
        return true;
    } catch (Exception $e) {
        $this->conn->rollback();
        return false;
    }
}

// Verificar si tiene cambios pendientes
public function tieneCambiosPendientes($comandaId)
{
    $sql = "SELECT tiene_cambios_pendientes FROM comanda WHERE id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $comandaId);
    $stmt->execute();
    $result = $stmt->get_result();
    $comanda = $result->fetch_assoc();
    
    return $comanda && $comanda['tiene_cambios_pendientes'];
}

// Modificar el método para cocina para que NO muestre items pendientes
public function obtenerDetallesComandaParaCocina($comandaId)
{
    $items = [];
    $sql = "SELECT dc.id, p.nombre, p.descripcion, dc.cantidad, dc.comentario,
                   tp.nombre as tipo_producto, t.nombre as tamano
            FROM detalle_comanda dc
            JOIN producto p ON dc.producto_id = p.id
            JOIN tipo_producto tp ON p.tipo_producto_id = tp.id
            LEFT JOIN tamano t ON p.tamano_id = t.id
            WHERE dc.comanda_id = ? 
            AND dc.cancelado = 0 
            AND dc.es_cambio_pendiente = FALSE"; // Solo mostrar items confirmados

    $stmt = $this->conn->prepare($sql);
    if (!$stmt) {
        error_log("Error preparando obtenerDetallesComandaParaCocina: " . $this->conn->error);
        return [];
    }

    $stmt->bind_param("i", $comandaId);
    if (!$stmt->execute()) {
        error_log("Error ejecutando obtenerDetallesComandaParaCocina: " . $stmt->error);
        return [];
    }

    $result = $stmt->get_result();
    while ($item = $result->fetch_assoc()) {
        $itemId = $item['id'];
        $item['guarniciones'] = $this->obtenerGuarnicionesItem($itemId);
        $item['opciones_combo'] = $this->obtenerOpcionesCombo($itemId);
        $items[] = $item;
    }

    return $items;
}

    public function __destruct()
    {
        $this->conn->close();
    }
}