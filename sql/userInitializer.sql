-- Crear tabla de roles
CREATE TABLE IF NOT EXISTS rol (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
);

-- Crear tabla de usuarios
CREATE TABLE IF NOT EXISTS usuario (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nombres VARCHAR(100),
    apellidos VARCHAR(100),
    correo VARCHAR(100) UNIQUE,
    contraseña VARCHAR(255),
    estado BOOLEAN DEFAULT 1,
    codigo VARCHAR(8),
    dni VARCHAR(8) NOT NULL,
    digito CHAR(1) NOT NULL
);

-- Relación usuario-rol
CREATE TABLE IF NOT EXISTS user_rol (
    id_user INT,
    id_rol INT,
    PRIMARY KEY (id_user, id_rol),
    FOREIGN KEY (id_user) REFERENCES usuario (id_user),
    FOREIGN KEY (id_rol) REFERENCES rol (id_rol)
);

-- Insertar roles
INSERT INTO rol (id_rol, nombre) VALUES
(1, 'admin'),
(2, 'mozo'),
(3, 'cocinero')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

-- Insertar usuarios por defecto
INSERT INTO usuario (nombres, apellidos, correo, contraseña, estado, codigo, dni, digito) VALUES
('Administrador', 'Principal', 'admin@local.com', '$2y$10$xzvZdi.4ra9WGqowDc5.OOpGzB/dNKCZlcTuJrGFNafDLDTcUUGJK', 1, 'ADMIN01', '09999999', '9'),
('Mozo', 'Prueba', 'mozo@local.com', '$2y$10$o4eBY0TE3HsC2yMMHYw0XOOyV6K5byEaYTT4.cQXME00aWeVKdase', 1, 'MOZO02', '08888888', '8'),
('Cocinero', 'Prueba', 'cocinero@local.com', '$2y$10$TSj8UXULQ0JVxsOhgr6kluNTTq/2elLNKdKD6zD7QdNqlahnbSrH6', 1, 'COCINERO03', '07777777', '7')
ON DUPLICATE KEY UPDATE contraseña = VALUES(contraseña);

-- Asignar roles a usuarios
INSERT INTO user_rol (id_user, id_rol)
SELECT u.id_user, 1 FROM usuario u WHERE u.correo = 'admin@local.com'
ON DUPLICATE KEY UPDATE id_rol = VALUES(id_rol);

INSERT INTO user_rol (id_user, id_rol)
SELECT u.id_user, 2 FROM usuario u WHERE u.correo = 'mozo@local.com'
ON DUPLICATE KEY UPDATE id_rol = VALUES(id_rol);

INSERT INTO user_rol (id_user, id_rol)
SELECT u.id_user, 3 FROM usuario u WHERE u.correo = 'cocinero@local.com'
ON DUPLICATE KEY UPDATE id_rol = VALUES(id_rol);