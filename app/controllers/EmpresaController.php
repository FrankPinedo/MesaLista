<?php
require_once __DIR__ . '/../models/EmpresaModel.php';

class EmpresaController
{
    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();
        header("Location: " . BASE_URL . "/login");
        exit();
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $empresaModel = new EmpresaModel();

            $nombre = $_POST['nombre'];
            $telefono = $_POST['telefono'];
            $direccion = $_POST['direccion'];
            $logo = null;

            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {

                $directorio = __DIR__ . '/../../public/uploads/';

                $nombreLogo = uniqid() . '_' . basename($_FILES['logo']['name']);
                $rutaDestino = $directorio . $nombreLogo;
                $nombreLogo;
                move_uploaded_file($_FILES['logo']['tmp_name'], $rutaDestino);
                $logo = $nombreLogo;
            }

            $empresaModel->guardar($nombre, $telefono, $direccion, $logo);

            header('Location: ' . BASE_URL . '/admin/empresa');
            exit;
        }
    }
}
