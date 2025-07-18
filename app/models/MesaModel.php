<?php
class MesaModel
{
    private $db;

    public function __construct()
    {
        try {
            // Usar las constantes de configuración
            $this->db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    // Agrega una nueva mesa con nombre automático y estado 'libre'
    public function agregarMesa()
{
    try {
        $this->db->beginTransaction();
        
        // Obtener todas las mesas ordenadas por ID
        $stmt = $this->db->query("SELECT nombre FROM mesas WHERE nombre REGEXP '^M[0-9]+$' ORDER BY id ASC");
        $nombres = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Debug: ver qué nombres tenemos
        error_log("Nombres actuales: " . json_encode($nombres));
        
        $numerosUsados = [];
        foreach ($nombres as $nombre) {
            if (preg_match('/^M(\d+)$/', $nombre, $match)) {
                $numerosUsados[] = (int)$match[1];
            }
        }
        
        // Eliminar duplicados y ordenar
        $numerosUsados = array_unique($numerosUsados);
        sort($numerosUsados);
        
        // Encontrar el primer número disponible
        $nuevoNumero = 1;
        foreach ($numerosUsados as $numero) {
            if ($numero != $nuevoNumero) {
                break;
            }
            $nuevoNumero++;
        }
        
        $nuevoNombre = 'M' . $nuevoNumero;
        
        // Verificar que no exista ya una mesa con ese nombre
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM mesas WHERE nombre = ?");
        $stmt->execute([$nuevoNombre]);
        if ($stmt->fetchColumn() > 0) {
            // Si ya existe, buscar el siguiente número disponible
            $maxStmt = $this->db->query("SELECT MAX(CAST(SUBSTRING(nombre, 2) AS UNSIGNED)) FROM mesas WHERE nombre REGEXP '^M[0-9]+$'");
            $maxNumero = $maxStmt->fetchColumn();
            $nuevoNumero = ($maxNumero ? $maxNumero : 0) + 1;
            $nuevoNombre = 'M' . $nuevoNumero;
        }
        
        error_log("Creando mesa: " . $nuevoNombre);
        
        $stmt = $this->db->prepare("INSERT INTO mesas (nombre, estado) VALUES (?, 'libre')");
        $stmt->execute([$nuevoNombre]);
        
        $this->db->commit();
        return true;
    } catch (Exception $e) {
        $this->db->rollBack();
        error_log("Error al agregar mesa: " . $e->getMessage());
        return false;
    }
}

    public function juntarMesas($id1, $id2)
    {
        try {
            $this->db->beginTransaction();

            // Verificar que ambas mesas existan y estén libres
            $stmt = $this->db->prepare("SELECT id, nombre, estado FROM mesas WHERE id = ? AND estado = 'libre'");
            $stmt->execute([$id1]);
            $mesa1 = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt->execute([$id2]);
            $mesa2 = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$mesa1 || !$mesa2) {
                $this->db->rollBack();
                return false;
            }

            // Unir nombres visualmente con el símbolo |
            $nuevoNombre = $mesa1['nombre'] . ' | ' . $mesa2['nombre'];

            // Actualizar la primera mesa con nuevo nombre y estado "combinada"
            $sql1 = "UPDATE mesas SET nombre = ?, estado = 'combinada' WHERE id = ?";
            $this->db->prepare($sql1)->execute([$nuevoNombre, $id1]);

            // Eliminar la segunda mesa
            $sql2 = "DELETE FROM mesas WHERE id = ?";
            $this->db->prepare($sql2)->execute([$id2]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al juntar mesas: " . $e->getMessage());
            return false;
        }
    }

    // Nuevo método para juntar múltiples mesas a una ya combinada
    public function juntarMesasMultiple($idCombinada, $idNueva)
    {
        try {
            $this->db->beginTransaction();

            // Obtener las mesas
            $stmt = $this->db->prepare("SELECT id, nombre, estado FROM mesas WHERE id = ?");
            $stmt->execute([$idCombinada]);
            $mesaCombinada = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $this->db->prepare("SELECT id, nombre, estado FROM mesas WHERE id = ? AND estado = 'libre'");
            $stmt->execute([$idNueva]);
            $mesaNueva = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$mesaCombinada || !$mesaNueva) {
                $this->db->rollBack();
                return false;
            }

            // Agregar la nueva mesa al nombre combinado
            $nuevoNombre = $mesaCombinada['nombre'] . ' | ' . $mesaNueva['nombre'];

            // Actualizar la mesa combinada
            $sql1 = "UPDATE mesas SET nombre = ? WHERE id = ?";
            $this->db->prepare($sql1)->execute([$nuevoNombre, $idCombinada]);

            // Eliminar la mesa nueva
            $sql2 = "DELETE FROM mesas WHERE id = ?";
            $this->db->prepare($sql2)->execute([$idNueva]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al agregar mesa a combinación: " . $e->getMessage());
            return false;
        }
    }

    // Eliminar una mesa
    public function eliminarMesa($id)
    {
        try {
            // Verificar que la mesa no tenga comandas activas
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM comanda WHERE mesa_id = ? AND estado IN ('nueva', 'pendiente', 'recibido')");
            $stmt->execute([$id]);
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                return false; // No se puede eliminar mesa con comandas activas
            }

            $sql = "DELETE FROM mesas WHERE id = ?";
            $this->db->prepare($sql)->execute([$id]);
            return true;
        } catch (Exception $e) {
            error_log("Error al eliminar mesa: " . $e->getMessage());
            return false;
        }
    }

   
    // En el archivo app/models/MesaModel.php, actualiza el método obtenerMesas:

public function obtenerMesas()
{
    try {
        // Consulta simple sin DISTINCT y con ordenamiento correcto
        $sql = "SELECT id, nombre, estado FROM mesas ORDER BY id ASC";
        $stmt = $this->db->query($sql);
        $mesas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Eliminar duplicados por nombre (por si acaso)
        $mesasUnicas = [];
        $nombresVistos = [];
        
        foreach ($mesas as $mesa) {
            if (!in_array($mesa['nombre'], $nombresVistos)) {
                $mesasUnicas[] = $mesa;
                $nombresVistos[] = $mesa['nombre'];
            }
        }
        
        // Ordenar por número extraído del nombre
        usort($mesasUnicas, function($a, $b) {
            // Extraer números de los nombres (M1, M2, etc.)
            preg_match('/\d+/', $a['nombre'], $numA);
            preg_match('/\d+/', $b['nombre'], $numB);
            
            $numeroA = isset($numA[0]) ? intval($numA[0]) : 0;
            $numeroB = isset($numB[0]) ? intval($numB[0]) : 0;
            
            return $numeroA - $numeroB;
        });
        
        return $mesasUnicas;
    } catch (Exception $e) {
        error_log("Error al obtener mesas: " . $e->getMessage());
        return [];
    }
}

    public function separarMesa($nombreCombinado)
    {
        try {
            $this->db->beginTransaction();

            // Eliminar mesa combinada
            $stmt = $this->db->prepare("DELETE FROM mesas WHERE nombre = ?");
            $stmt->execute([$nombreCombinado]);

            // Extraer cada número: "M5 | M6" => ["5", "6"]
            preg_match_all('/M(\d+)/', $nombreCombinado, $matches);
            $numeros = $matches[1];

            foreach ($numeros as $n) {
                $nombre = 'M' . $n;
                $stmt = $this->db->prepare("INSERT INTO mesas (nombre, estado) VALUES (?, 'libre')");
                $stmt->execute([$nombre]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al separar mesas: " . $e->getMessage());
            return false;
        }
    }

    public function cambiarEstado($id, $nuevoEstado)
{
    try {
        // Asegurarse de que 'ocupada' esté en la lista
        $estadosValidos = ['libre', 'reservado', 'ocupada', 'esperando', 'atendido', 'combinada'];
        
        if (!in_array($nuevoEstado, $estadosValidos)) {
            error_log("Estado no válido: " . $nuevoEstado);
            return false;
        }

        $stmt = $this->db->prepare("UPDATE mesas SET estado = ? WHERE id = ?");
        $resultado = $stmt->execute([$nuevoEstado, $id]);
        
        if (!$resultado) {
            error_log("Error al ejecutar UPDATE: " . implode(", ", $stmt->errorInfo()));
        }
        
        return $resultado;
    } catch (Exception $e) {
        error_log("Error al cambiar estado de mesa: " . $e->getMessage());
        return false;
    }
}

    // Obtener mesa por ID
    public function obtenerMesaPorId($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM mesas WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error al obtener mesa: " . $e->getMessage());
            return null;
        }
    }
}
