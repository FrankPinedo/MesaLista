CREATE TABLE IF NOT EXISTS mesas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    estado ENUM('libre', 'reservado', 'ocupada', 'esperando', 'atendido', 'combinada') NOT NULL DEFAULT 'libre'
);

-- Insertar mesas iniciales
INSERT INTO mesas (nombre, estado) VALUES
('M1', 'libre'),
('M2', 'libre'),
('M3', 'libre'),
('M4', 'libre'),
('M5', 'libre'),
('M6', 'libre'),
('M7', 'libre'),
('M8', 'libre');