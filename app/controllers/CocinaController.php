<?php
class CocinaController
{
    private $usuario;
    private $comandaModel;

    public function __construct()
    {
        require_once __DIR__ . '/../../config/config.php';
        require_once __DIR__ . '/../helpers/sesion.php';
        require_once __DIR__ . '/../models/ComandaModel.php';

        $this->usuario = verificarSesion();
        session_regenerate_id(true);

        if ($this->usuario['rol'] !== 'cocinero') {
            $this->enviarRespuestaJson(['error' => 'Acceso no autorizado'], 403);
            exit();
        }

        $this->comandaModel = new ComandaModel();
    }

    public function index()
    {
        require_once __DIR__ . '/../views/cocina/inicio.php';
    }

    public function obtenerComandas()
    {
        try {
            header('Content-Type: application/json');

            if ($this->usuario['rol'] !== 'cocinero') {
                http_response_code(403);
                echo json_encode(['error' => 'Acceso no autorizado']);
                exit();
            }

            $comandas = $this->comandaModel->obtenerComandasPendientes();
            echo json_encode($comandas);
            exit();
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
            exit();
        }
    }

    public function actualizarEstado()
    {
        try {
            $data = $this->obtenerDatosPeticion();

            if (empty($data['comanda_id']) || empty($data['estado'])) {
                throw new Exception('Datos incompletos');
            }

            $exito = $this->comandaModel->actualizarEstadoComanda(
                $data['comanda_id'],
                $data['estado']
            );

            $this->enviarRespuestaJson(['success' => $exito]);
        } catch (Exception $e) {
            $this->enviarRespuestaJson(['error' => $e->getMessage()], 400);
        }
    }

    public function cancelarItem()
    {
        try {
            $data = $this->obtenerDatosPeticion();

            if (empty($data['item_id'])) {
                throw new Exception('ID de ítem no proporcionado');
            }

            $exito = $this->comandaModel->cancelarItem($data['item_id']);
            $this->enviarRespuestaJson(['success' => $exito]);
        } catch (Exception $e) {
            $this->enviarRespuestaJson(['error' => $e->getMessage()], 400);
        }
    }

    public function recuperarItem()
    {
        try {
            $data = $this->obtenerDatosPeticion();

            if (empty($data['item_id'])) {
                throw new Exception('ID de ítem no proporcionado');
            }

            $exito = $this->comandaModel->recuperarItem($data['item_id']);
            $this->enviarRespuestaJson(['success' => $exito]);
        } catch (Exception $e) {
            $this->enviarRespuestaJson(['error' => $e->getMessage()], 400);
        }
    }

    public function cancelarComanda()
    {
        try {
            $data = $this->obtenerDatosPeticion();

            if (empty($data['comanda_id'])) {
                throw new Exception('ID de comanda no proporcionado');
            }

            $exito = $this->comandaModel->cancelarComanda($data['comanda_id']);
            $this->enviarRespuestaJson(['success' => $exito]);
        } catch (Exception $e) {
            $this->enviarRespuestaJson(['error' => $e->getMessage()], 400);
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

    private function obtenerDatosPeticion()
    {
        $json = file_get_contents('php://input');
        return json_decode($json, true);
    }

    private function enviarRespuestaJson($data, $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit();
    }
}
