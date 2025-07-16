<?php
class AdminController
{
    private $usuario;
    public function __construct()
    {
        require_once __DIR__ . '/../../config/config.php';
        require_once __DIR__ . '/../helpers/sesion.php';
        $this->usuario = verificarSesion();
        session_regenerate_id(true);

        if ($this->usuario['rol'] !== 'admin') {
            require_once __DIR__ . '/../controllers/ErrorController.php';
            (new ErrorController())->index('403');
            exit();
        }
    }
    public function index()
    {
        $usuario = $this->usuario;
        require_once __DIR__ . '/../views/admin/inicio.php';
    }
    public function empresa()
    {
        $usuario = $this->usuario;
        require_once __DIR__ . '/../views/admin/empresa.php';
    }
    public function contacto()
    {
        $usuario = $this->usuario;
        require_once __DIR__ . '/../views/admin/contacto.php';
    }
    public function productos()
    {
        $usuario = $this->usuario;
        require_once __DIR__ . '/../models/ProductoModel.php';
        $productoModel = new ProductoModel();

        // Procesar acciones
        if (isset($_GET['accion'], $_GET['id'])) {
            $id = intval($_GET['id']);
            if ($_GET['accion'] === 'habilitar') {
                $productoModel->cambiarEstado($id, 1);
            } elseif ($_GET['accion'] === 'deshabilitar') {
                $productoModel->cambiarEstado($id, 0);
            }
            header("Location: " . BASE_URL . "/admin/productos");
            exit;
        }

        $limite = 20;
        $pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
        $offset = ($pagina - 1) * $limite;

        $totalProductos = $productoModel->contarProductos();
        $productos = $productoModel->obtenerProductosPaginados($offset, $limite);
        $totalPaginas = ceil($totalProductos / $limite);

        require_once __DIR__ . '/../views/admin/productos.php';
    }
    public function usuarios()
    {
        $usuario = $this->usuario;
        require_once __DIR__ . '/../views/admin/usuarios.php';
    }
    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();
        header("Location: " . BASE_URL . "/login");
        exit();
    }
    public function agregar_producto()
    {
        $usuario = $this->usuario;
        require_once __DIR__ . '/../models/ProductoModel.php';
        $productoModel = new ProductoModel();
        $tiposProducto = $productoModel->listarTiposProducto();
        $tamanos = $productoModel->listarTamanos();
        $tiposBebida = $productoModel->listarTiposBebida();
        $tiposPlato = $productoModel->listarTiposPlato();
        $guarniciones = $productoModel->listarGuarnicionesActivas();

        // Procesar acciones
        if (isset($_GET['accion'], $_GET['id'])) {
            $id = intval($_GET['id']);
            if ($_GET['accion'] === 'habilitar') {
                $productoModel->cambiarEstado($id, 1);
            } elseif ($_GET['accion'] === 'deshabilitar') {
                $productoModel->cambiarEstado($id, 0);
            }
            header("Location: " . BASE_URL . "/admin/agregar_producto");
            exit;
        }

        // Lógica de paginación...
        $limite = 20;
        $pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
        $offset = ($pagina - 1) * $limite;

        $totalProductos = $productoModel->contarProductos();
        $productos = $productoModel->obtenerProductosPaginados($offset, $limite);
        $totalPaginas = ceil($totalProductos / $limite);
        require_once __DIR__ . '/../views/admin/agregarPro.php';
    }
    public function guardarProducto()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            session_start();
            require_once __DIR__ . '/../models/ProductoModel.php';
            $productoModel = new ProductoModel();

            $nombre = $_POST['nombre'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $precio = $_POST['precio'] ?? 0;
            $stock = $_POST['stock'] ?? 0;
            $tipo_producto_id = $_POST['tipo_producto_id'] ?? null;
            $tamano_id = $_POST['tamano_id'] ?? null;

            $_SESSION['error_producto'] = null;

            if (empty($nombre)) {
                $_SESSION['error_producto'] = 'El campo Nombre no puede estar vacío.';
                header("Location: " . BASE_URL . "/admin/agregar_producto");
                exit;
            }
            if (strlen($nombre) > 100) {
                $_SESSION['error_producto'] = 'El campo Nombre no puede exceder los 100 caracteres.';
                header("Location: " . BASE_URL . "/admin/agregar_producto");
                exit;
            }
            if (preg_match('/[@\/_]/', $nombre)) {
                $_SESSION['error_producto'] = 'El campo Nombre no puede contener caracteres especiales como @, / o _.';
                header("Location: " . BASE_URL . "/admin/agregar_producto");
                exit;
            }

            if (empty($descripcion)) {
                $_SESSION['error_producto'] = 'El campo Descripción no puede estar vacío.';
                header("Location: " . BASE_URL . "/admin/agregar_producto");
                exit;
            }
            if (strlen($descripcion) > 200) {
                $_SESSION['error_producto'] = 'El campo Descripción no puede exceder los 200 caracteres.';
                header("Location: " . BASE_URL . "/admin/agregar_producto");
                exit;
            }

            if ($precio === '') {
                $_SESSION['error_producto'] = 'El campo Precio no puede estar vacío.';
                header("Location: " . BASE_URL . "/admin/agregar_producto");
                exit;
            }
            if (!is_numeric($precio) || floatval($precio) <= 0) {
                $_SESSION['error_producto'] = 'El campo Precio debe ser mayor a 0.';
                header("Location: " . BASE_URL . "/admin/agregar_producto");
                exit;
            }

            if ($stock === '') {
                $_SESSION['error_producto'] = 'El campo Stock no puede estar vacío.';
                header("Location: " . BASE_URL . "/admin/agregar_producto");
                exit;
            }
            if (!is_numeric($stock) || intval($stock) < 0) {
                $_SESSION['error_producto'] = 'El campo Stock no puede ser menor a 0.';
                header("Location: " . BASE_URL . "/admin/agregar_producto");
                exit;
            }

            if (empty($tipo_producto_id)) {
                $_SESSION['error_producto'] = 'Debe seleccionar un Tipo de Producto.';
                header("Location: " . BASE_URL . "/admin/agregar_producto");
                exit;
            }

            if (empty($tamano_id)) {
                $_SESSION['error_producto'] = 'Debe seleccionar un Tamaño.';
                header("Location: " . BASE_URL . "/admin/agregar_producto");
                exit;
            }

            $imagenNombre = null;
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $validImageTypes = ['image/jpeg', 'image/png', 'image/webp'];
                $imageType = $_FILES['imagen']['type'];
                if (!in_array($imageType, $validImageTypes)) {
                    $_SESSION['error_producto'] = 'La imagen debe ser de tipo JPG, PNG o WEBP.';
                    header("Location: " . BASE_URL . "/admin/agregar_producto");
                    exit;
                }
                $imagenNombre = $productoModel->guardarImagen($_FILES['imagen']);
                if (!$imagenNombre) {
                    $_SESSION['error_producto'] = 'Solo se permiten archivos de imagen válidos.';
                    header("Location: " . BASE_URL . "/admin/agregar_producto");
                    exit;
                }
            }

            $data = [
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'precio' => $precio,
                'stock' => $stock,
                'tipo_producto_id' => $tipo_producto_id,
                'tamano_id' => $tamano_id,
                'imagen' => $imagenNombre,
            ];

            if ($tipo_producto_id == 1) {
                $data['tipo_bebida_id'] = $_POST['tipo_bebida_id'] ?? null;
            } elseif ($tipo_producto_id == 2) {
                $data['tipo_plato_id'] = $_POST['tipo_plato_id'] ?? null;
                $data['guarniciones'] = $_POST['guarniciones'] ?? [];
            }

            $productoId = $productoModel->insertarProducto($data);

            if ($tipo_producto_id == 4 && isset($_POST['componentes'])) {
                $componentes = [];

                foreach ($_POST['componentes'] as $comp) {
                    if (!empty($comp['producto_id'])) {
                        $componentes[] = [
                            'producto_id' => $comp['producto_id'],
                            'cantidad' => $comp['cantidad'] ?? 1,
                            'obligatorio' => isset($comp['obligatorio']),
                            'grupo' => $comp['grupo'] ?? ''
                        ];
                    }
                }

                if (!empty($componentes)) {
                    $productoModel->insertarComponentesCombo($productoId, $componentes);
                }
            }

            $_SESSION['success_producto'] = 'Producto guardado exitosamente.';

            unset($_SESSION['error_producto']);
            header("Location: " . BASE_URL . "/admin/agregar_producto");
            exit();
        }
    }
    public function agregar_guarnicion()
    {
        $usuario = $this->usuario;
        require_once __DIR__ . '/../models/ProductoModel.php';
        $productoModel = new ProductoModel();
        $guarniciones = $productoModel->listarGuarniciones();

        // Procesar acciones
        if (isset($_GET['accion'], $_GET['id'])) {
            $id = intval($_GET['id']);
            if ($_GET['accion'] === 'habilitar') {
                $productoModel->cambiarEstadoGuarnicion($id, 1);
            } elseif ($_GET['accion'] === 'deshabilitar') {
                $productoModel->cambiarEstadoGuarnicion($id, 0);
            }
            header("Location: " . BASE_URL . "/admin/agregar_guarnicion");
            exit;
        }

        $limite = 20;
        $pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
        $offset = ($pagina - 1) * $limite;

        $totalGuarniciones = $productoModel->contarGuarniciones();
        $productos = $productoModel->obtenerGuarnicionesPaginados($offset, $limite);
        $totalPaginas = ceil($totalGuarniciones / $limite);
        require_once __DIR__ . '/../views/admin/agregarGua.php';
    }
    public function guardarGuarnicion()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            session_start();
            require_once __DIR__ . '/../models/ProductoModel.php';
            $productoModel = new ProductoModel();

            $nombre = trim($_POST['nombre']);
            $descripcion = trim($_POST['descripcion']);
            $precio = floatval($_POST['precio']);
            $estado = isset($_POST['estado']) ? intval($_POST['estado']) : 1;
            $stock = isset($_POST['stock']) ? intval($_POST['stock']) : 0;

            $error = '';
            if (empty($nombre) || empty($descripcion) || empty($_POST['precio']) || empty($_POST['stock']) || $estado === null) {
                $error = 'Todos los campos obligatorios deben estar completos.';
            } elseif (!preg_match('/^[a-zA-Z0-9\s]*$/', $nombre)) {
                $error = 'El nombre no debe contener caracteres especiales como @, / o _.';
            } elseif (strlen($descripcion) > 200) {
                $error = 'La descripción no debe exceder los 200 caracteres.';
            } elseif ($precio <= 0) {
                $error = 'El precio debe ser un número positivo mayor que cero.';
            } elseif ($stock <= 0) {
                $error = 'El stock debe ser un número positivo mayor que cero.';
            } elseif (!in_array($estado, [0, 1])) {
                $error = 'El estado debe ser seleccionado (Habilitado o Deshabilitado).';
            } elseif (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $validImageTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
                $imageType = mime_content_type($_FILES['imagen']['tmp_name']);
                if (!in_array($imageType, $validImageTypes)) {
                    $error = 'La imagen debe ser un archivo válido (jpg, png, webp, gif).';
                }
            }

            if ($error) {
                $_SESSION['error_guarnicion'] = $error;
                header("Location: " . BASE_URL . "/admin/agregar_guarnicion");
                exit;
            }
            // Imagen
            $imagenNombre = 'sin_imagen.jpg';
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $imagenSubida = $productoModel->guardarImagen($_FILES['imagen']);
                if ($imagenSubida !== false) {
                    $imagenNombre = $imagenSubida;
                } else {
                    $_SESSION['error_guarnicion'] = 'Error al procesar la imagen.';
                    header("Location: " . BASE_URL . "/admin/agregar_guarnicion");
                    exit;
                }
            }

            $data = [
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'precio' => $precio,
                'estado' => $estado,
                'stock' => $stock,
                'imagen' => $imagenNombre
            ];

            if ($productoModel->insertarGuarnicion($data)) {
                unset($_SESSION['error_guarnicion']);
                $_SESSION['success_guarnicion'] = 'Guarnición guardada correctamente.';
                header("Location: " . BASE_URL . "/admin/agregar_guarnicion");
            } else {
                $_SESSION['error_guarnicion'] = 'Error al guardar la guarnición.';
                header("Location: " . BASE_URL . "/admin/agregar_guarnicion");
            }
            exit;
        }
    }
    public function variaciones()
    {
        require_once __DIR__ . '/../models/ProductoModel.php';
        $model = new ProductoModel();

        $tiposBebida = $model->listarTiposBebida();
        $tiposPlato = $model->listarTiposPlato();
        $tamanos = $model->listarTamanos();

        $usuario = $this->usuario;

        require_once __DIR__ . '/../views/admin/variaciones.php';
    }
    public function guardarBebida()
    {
        $usuario = $this->usuario;
        require_once __DIR__ . '/../models/ProductoModel.php';
        $model = new ProductoModel();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre']) && isset($_POST['estado'])) {
            $nombre = trim($_POST['nombre']);
            $estado = (int)$_POST['estado'];
            if (!empty($nombre)) {
                $model->guardarTipoBebida($nombre, $estado);
            }
        }
        header("Location: " . BASE_URL . "/admin/variaciones");
        exit;
    }
    public function guardarPlato()
    {
        $usuario = $this->usuario;
        require_once __DIR__ . '/../models/ProductoModel.php';
        $model = new ProductoModel();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre']) && isset($_POST['estado'])) {
            $nombre = trim($_POST['nombre']);
            $estado = (int)$_POST['estado'];
            if (!empty($nombre)) {
                $model->guardarTipoPlato($nombre, $estado);
            }
        }
        header("Location: " . BASE_URL . "/admin/variaciones");
        exit;
    }
    public function guardarTamano()
    {
        $usuario = $this->usuario;
        require_once __DIR__ . '/../models/ProductoModel.php';
        $model = new ProductoModel();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre']) && isset($_POST['estado'])) {
            $nombre = trim($_POST['nombre']);
            $estado = (int)$_POST['estado'];
            if (!empty($nombre)) {
                $model->guardarTamano($nombre, $estado);
            }
        }

        header("Location: " . BASE_URL . "/admin/variaciones");
        exit;
    }
    public function gestionarProductos()
    {
        require_once __DIR__ . '/../models/ProductoModel.php';
        $productoModel = new ProductoModel();

        if (
            $_SERVER['REQUEST_METHOD'] === 'POST' &&
            isset($_POST['accion'], $_POST['id'], $_POST['tabla'])
        ) {

            header('Content-Type: application/json');

            $id = intval($_POST['id']);
            $tabla = $_POST['tabla'];
            $accion = $_POST['accion'];

            try {
                if ($accion === 'habilitar') {
                    $productoModel->cambiarEstadoVar($id, 1, $tabla);
                } elseif ($accion === 'deshabilitar') {
                    $productoModel->cambiarEstadoVar($id, 0, $tabla);
                }
                echo json_encode(['success' => true]);
                exit;
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                exit;
            }
        }

        $tiposBebida = $productoModel->obtenerTiposBebida();
        $tiposPlato = $productoModel->obtenerTiposPlato();
        $tamanos = $productoModel->obtenerTamanos();

        require_once __DIR__ . '/../views/admin/variaciones.php';
    }
}
