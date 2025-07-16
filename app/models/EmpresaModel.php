<?php
class EmpresaModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->conn->connect_error) {
            die('Conexión fallida: ' . $this->conn->connect_error);
        }
    }

    public function guardar($nombre, $telefono, $direccion, $logo)
    {
        $sql = "INSERT INTO empresa (nombre, telefono, direccion, logo) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssss", $nombre, $telefono, $direccion, $logo);
        $stmt->execute();
        $stmt->close();
    }
}
