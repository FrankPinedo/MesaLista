    <?php
    class UsuarioModel
    {
        private $pdo;

        public function __construct($pdo)
        {
            $this->pdo = $pdo;
        }

        public function obtenerUsuarioPorCorreo($correo)
        {
            $sql = "SELECT u.*, r.nombre AS rol
                    FROM usuario u
                    JOIN user_rol ur ON u.id_user = ur.id_user
                    JOIN rol r ON ur.id_rol = r.id_rol
                    WHERE u.correo = :correo AND u.estado = 1";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['correo' => $correo]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function obtenerPorDniYCorreo($dni, $correo)
        {
            $sql = "SELECT * FROM usuario WHERE dni = :dni AND correo = :correo AND estado = 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'dni' => $dni,
                'correo' => $correo
            ]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        public function obtenerPorDni($dni)
        {
            $sql = "SELECT * FROM usuario WHERE dni = :dni AND estado = 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['dni' => $dni]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
