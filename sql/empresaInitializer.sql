CREATE TABLE
    IF NOT EXISTS `empresa` (
        `id` INT (11) NOT NULL AUTO_INCREMENT,
        `nombre` VARCHAR(100) NOT NULL,
        `telefono` VARCHAR(20) NOT NULL,
        `direccion` VARCHAR(255) DEFAULT NULL,
        `logo` VARCHAR(255) DEFAULT NULL,
        `fecha_registro` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO
    `empresa` (`nombre`, `telefono`, `direccion`, `logo`)
VALUES
    (
        'ANDERSON HAROLD',
        '987625242',
        'ooolljj',
        'uploads/login2.jpg'
    );