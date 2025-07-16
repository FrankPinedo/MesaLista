<?php
require_once __DIR__ . '/../../config/config.php';

class ProductoModel
{
    private $db;

    public function __construct()
    {
        $this->db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
    }
    public function listarTiposProducto()
    {
        $stmt = $this->db->query("SELECT * FROM tipo_producto");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function listarTamanos()
    {
        $stmt = $this->db->query("SELECT * FROM tamano");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function listarTiposBebida()
    {
        $stmt = $this->db->query("SELECT * FROM tipo_bebida");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function listarTiposPlato()
    {
        $stmt = $this->db->query("SELECT * FROM tipo_plato");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function listarGuarnicionesActivas()
    {
        $stmt = $this->db->query("SELECT * FROM guarnicion WHERE estado = 1");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function listarGuarniciones()
    {
        $stmt = $this->db->query("SELECT * FROM guarnicion ORDER BY id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function contarGuarniciones()
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM guarnicion");
        return $stmt->fetchColumn();
    }
    public function insertarGuarnicion($data)
    {
        $stmt = $this->db->prepare("INSERT INTO guarnicion (nombre, descripcion, precio, estado, stock, imagen) 
                                VALUES (:nombre, :descripcion, :precio, :estado, :stock, :imagen)");
        return $stmt->execute([
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion'],
            ':precio' => $data['precio'],
            ':estado' => $data['estado'],
            ':stock' => $data['stock'],
            ':imagen' => $data['imagen'] ?? 'sin imagen.jpg'
        ]);
    }
    public function obtenerGuarnicionesPaginados($offset, $limite)
    {
        $sql = "SELECT id, nombre, descripcion, precio, stock, estado, imagen
            FROM guarnicion
            ORDER BY id ASC
            LIMIT :limite OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obtenerProductos()
    {
        $sql = "SELECT p.id, p.nombre, p.descripcion, p.precio, p.stock, p.estado, p.imagen
            FROM producto p
            ORDER BY p.id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obtenerProductosPaginados($offset, $limite)
    {
        $sql = "SELECT id, nombre, descripcion, precio, stock, estado, imagen
            FROM producto
            ORDER BY id ASC
            LIMIT :limite OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function contarProductos()
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM producto");
        return $stmt->fetchColumn();
    }
    public function cambiarEstado($id, $estado)
    {
        $stmt = $this->db->prepare("UPDATE producto SET estado = :estado WHERE id = :id");
        $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function cambiarEstadoGuarnicion($id, $estado)
    {
        $stmt = $this->db->prepare("UPDATE guarnicion SET estado = :estado WHERE id = :id");
        $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function insertarProducto($data)
    {
        $stmt = $this->db->prepare("INSERT INTO producto (nombre, descripcion, precio, stock, tipo_producto_id, tamano_id, imagen) 
                                VALUES (:nombre, :descripcion, :precio, :stock, :tipo_producto_id, :tamano_id, :imagen)");
        $stmt->execute([
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion'],
            ':precio' => $data['precio'],
            ':stock' => $data['stock'],
            ':tipo_producto_id' => $data['tipo_producto_id'],
            ':tamano_id' => $data['tamano_id'],
            ':imagen' => $data['imagen']
        ]);

        $productoId = $this->db->lastInsertId();

        // Inserciones relacionadas
        if ($data['tipo_producto_id'] == 1 && !empty($data['tipo_bebida_id'])) {
            $stmt = $this->db->prepare("INSERT INTO producto_bebida (producto_id, tipo_bebida_id) VALUES (:producto_id, :tipo_bebida_id)");
            $stmt->execute([
                ':producto_id' => $productoId,
                ':tipo_bebida_id' => $data['tipo_bebida_id']
            ]);
        }

        if ($data['tipo_producto_id'] == 2 && !empty($data['tipo_plato_id'])) {
            $stmt = $this->db->prepare("INSERT INTO producto_plato (producto_id, tipo_plato_id) VALUES (:producto_id, :tipo_plato_id)");
            $stmt->execute([
                ':producto_id' => $productoId,
                ':tipo_plato_id' => $data['tipo_plato_id']
            ]);
        }

        if ($data['tipo_producto_id'] == 2 && !empty($data['guarniciones'])) {
            $stmt = $this->db->prepare("INSERT INTO producto_guarnicion (producto_id, guarnicion_id) VALUES (:producto_id, :guarnicion_id)");
            foreach ($data['guarniciones'] as $guarnicionId) {
                $stmt->execute([
                    ':producto_id' => $productoId,
                    ':guarnicion_id' => $guarnicionId
                ]);
            }
        }

        return $productoId;
    }
    public function insertarComponentesCombo($comboId, $componentes)
    {
        try {
            $this->db->beginTransaction();

            $deleteStmt = $this->db->prepare("DELETE FROM combo_componentes WHERE combo_id = ?");
            $deleteStmt->execute([$comboId]);

            $insertStmt = $this->db->prepare("
            INSERT INTO combo_componentes 
            (combo_id, producto_id, obligatorio, cantidad, grupo) 
            VALUES (?, ?, ?, ?, ?)
        ");

            foreach ($componentes as $componente) {
                $insertStmt->execute([
                    $comboId,
                    $componente['producto_id'],
                    $componente['obligatorio'] ? 1 : 0,
                    $componente['cantidad'],
                    $componente['grupo']
                ]);
            }

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error al insertar componentes del combo: " . $e->getMessage());
            return false;
        }
    }
    public function guardarImagen($archivo)
    {
        $carpetaDestino = __DIR__ . '/../../public/uploads/';
        $nombreOriginal = basename($archivo['name']);
        $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
        $tiposPermitidos = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($extension, $tiposPermitidos)) {
            return false; // O lanzar excepción personalizada
        }

        $esImagen = getimagesize($archivo['tmp_name']);
        if ($esImagen === false) {
            return false;
        }

        $hash = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $nombreOriginal);
        $rutaFinal = $carpetaDestino . $hash;

        if (move_uploaded_file($archivo['tmp_name'], $rutaFinal)) {
            return $hash;
        } else {
            return false;
        }
    }
    public function guardarTipoBebida($nombre, $estado)
    {
        $stmt = $this->db->prepare("INSERT INTO tipo_bebida (nombre, estado) VALUES (:nombre, :estado)");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function guardarTipoPlato($nombre, $estado)
    {
        $stmt = $this->db->prepare("INSERT INTO tipo_plato (nombre, estado) VALUES (:nombre, :estado)");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function guardarTamano($nombre, $estado)
    {
        $stmt = $this->db->prepare("INSERT INTO tamano (nombre, estado) VALUES (:nombre, :estado)");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function cambiarEstadoVar($id, $estado, $tabla)
    {
        $validTables = ['tipo_bebida', 'tipo_plato', 'tamanos'];
        if (!in_array($tabla, $validTables)) {
            throw new Exception("Tabla no válida");
        }

        $stmt = $this->db->prepare("UPDATE $tabla SET estado = :estado WHERE id = :id");
        $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function obtenerTiposBebida()
    {
        $stmt = $this->db->query("SELECT * FROM tipo_bebida");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obtenerTiposPlato()
    {
        $stmt = $this->db->query("SELECT * FROM tipo_plato");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obtenerTamanos()
    {
        $stmt = $this->db->query("SELECT * FROM tamano");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // ---
    public function obtenerProductosPorTipo($tipoId, $soloActivos = false)
    {
        $sql = "SELECT p.*, p.id as id_plato FROM producto p 
            WHERE p.tipo_producto_id = :tipo";

        if ($soloActivos) {
            $sql .= " AND p.estado = 1";
        }

        $sql .= " ORDER BY p.nombre";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['tipo' => $tipoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function verificarStock($productoId, $cantidad)
    {
        $stmt = $this->db->prepare("SELECT stock FROM producto WHERE id = ?");
        $stmt->execute([$productoId]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        return $producto && $producto['stock'] >= $cantidad;
    }
    public function actualizarStock($productoId, $cantidad)
    {
        $stmt = $this->db->prepare("UPDATE producto SET stock = stock + ? WHERE id = ?");
        return $stmt->execute([$cantidad, $productoId]);
    }
}
