-- Tabla tipo_producto
CREATE TABLE
    IF NOT EXISTS tipo_producto (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(50) NOT NULL
    );

-- Tabla tamano
CREATE TABLE
    IF NOT EXISTS tamano (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(50) NOT NULL,
        estado TINYINT (1) DEFAULT 1
    );

-- Tabla producto
CREATE TABLE
    IF NOT EXISTS producto (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        descripcion TEXT,
        estado TINYINT (1) DEFAULT 1,
        precio DECIMAL(10, 2) NOT NULL,
        stock INT,
        tipo_producto_id INT,
        tamano_id INT DEFAULT NULL,
        imagen VARCHAR(255) DEFAULT 'sin imagen.jpg',
        FOREIGN KEY (tipo_producto_id) REFERENCES tipo_producto (id),
        FOREIGN KEY (tamano_id) REFERENCES tamano (id)
    );

-- Tabla tipo_bebida
CREATE TABLE
    IF NOT EXISTS tipo_bebida (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(50) NOT NULL,
        estado TINYINT (1) DEFAULT 1
    );

-- Tabla tipo_plato
CREATE TABLE
    IF NOT EXISTS tipo_plato (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(50) NOT NULL,
        estado TINYINT (1) DEFAULT 1
    );

-- Tabla producto_bebida
CREATE TABLE
    IF NOT EXISTS producto_bebida (
        producto_id INT PRIMARY KEY,
        tipo_bebida_id INT,
        FOREIGN KEY (producto_id) REFERENCES producto (id) ON DELETE CASCADE,
        FOREIGN KEY (tipo_bebida_id) REFERENCES tipo_bebida (id)
    );

-- Tabla producto_plato
CREATE TABLE
    IF NOT EXISTS producto_plato (
        producto_id INT PRIMARY KEY,
        tipo_plato_id INT,
        FOREIGN KEY (producto_id) REFERENCES producto (id) ON DELETE CASCADE,
        FOREIGN KEY (tipo_plato_id) REFERENCES tipo_plato (id)
    );

-- Tabla guarnicion
CREATE TABLE
    IF NOT EXISTS guarnicion (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        descripcion TEXT,
        precio DECIMAL(10, 2),
        estado TINYINT (1) DEFAULT 1,
        stock INT,
        imagen VARCHAR(255) DEFAULT 'sin imagen.jpg'
    );

-- Tabla producto_guarnicion
CREATE TABLE
    IF NOT EXISTS producto_guarnicion (
        producto_id INT,
        guarnicion_id INT,
        PRIMARY KEY (producto_id, guarnicion_id),
        FOREIGN KEY (producto_id) REFERENCES producto (id) ON DELETE CASCADE,
        FOREIGN KEY (guarnicion_id) REFERENCES guarnicion (id)
    );

-- Tabla combo_componentes
CREATE TABLE
    IF NOT EXISTS combo_componentes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        combo_id INT,
        producto_id INT,
        obligatorio TINYINT (1) DEFAULT 1,
        cantidad INT DEFAULT 1,
        grupo VARCHAR(50),
        FOREIGN KEY (combo_id) REFERENCES producto (id) ON DELETE CASCADE,
        FOREIGN KEY (producto_id) REFERENCES producto (id)
    );

-- Tabla tipo_entrega
CREATE TABLE
    IF NOT EXISTS tipo_entrega (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(50) NOT NULL
    );

-- Tabla comanda (CORREGIDA)
CREATE TABLE
    IF NOT EXISTS comanda (
        id INT AUTO_INCREMENT PRIMARY KEY,
        mesa_id INT DEFAULT NULL,
        usuario_id INT,
        estado ENUM (
            'nueva',
            'pendiente',
            'recibido',
            'listo',
            'cancelado'
        ) DEFAULT 'nueva',
        tipo_entrega_id INT,
        fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        extensiones INT DEFAULT 0,
        FOREIGN KEY (mesa_id) REFERENCES mesas (id) ON DELETE SET NULL,
        FOREIGN KEY (usuario_id) REFERENCES usuario (id_user),
        FOREIGN KEY (tipo_entrega_id) REFERENCES tipo_entrega (id)
    );

-- Tabla detalle_comanda
CREATE TABLE
    IF NOT EXISTS detalle_comanda (
        id INT AUTO_INCREMENT PRIMARY KEY,
        comanda_id INT,
        producto_id INT,
        cantidad INT NOT NULL,
        comentario TEXT,
        cancelado TINYINT (1) DEFAULT 0,
        FOREIGN KEY (comanda_id) REFERENCES comanda (id) ON DELETE CASCADE,
        FOREIGN KEY (producto_id) REFERENCES producto (id)
    );

-- Tabla detalle_comanda_guarnicion
CREATE TABLE
    IF NOT EXISTS detalle_comanda_guarnicion (
        id INT AUTO_INCREMENT PRIMARY KEY,
        detalle_comanda_id INT,
        guarnicion_id INT,
        FOREIGN KEY (detalle_comanda_id) REFERENCES detalle_comanda (id) ON DELETE CASCADE,
        FOREIGN KEY (guarnicion_id) REFERENCES guarnicion (id)
    );

-- Tabla detalle_comanda_combo_opciones
CREATE TABLE
    IF NOT EXISTS detalle_comanda_combo_opciones (
        id INT AUTO_INCREMENT PRIMARY KEY,
        detalle_comanda_id INT,
        producto_id INT,
        FOREIGN KEY (detalle_comanda_id) REFERENCES detalle_comanda (id) ON DELETE CASCADE,
        FOREIGN KEY (producto_id) REFERENCES producto (id)
    );

-- Insertar datos iniciales
INSERT INTO
    tipo_producto (nombre)
VALUES
    ('bebida'),
    ('plato'),
    ('postre'),
    ('combo');

INSERT INTO
    tipo_bebida (nombre)
VALUES
    ('refresco natural'),
    ('gaseosa'),
    ('jugo'),
    ('agua mineral'),
    ('cerveza'),
    ('café');

INSERT INTO
    tipo_plato (nombre)
VALUES
    ('entrada'),
    ('ronda'),
    ('fondo'),
    ('ensalada'),
    ('sopa'),
    ('postre');

INSERT INTO
    tipo_entrega (nombre)
VALUES
    ('delivery'),
    ('para llevar'),
    ('comedor');

INSERT INTO
    tamano (nombre)
VALUES
    ('250 ml'),
    ('330 ml'),
    ('500 ml'),
    ('1 litro'),
    ('1.5 litros'),
    ('3 litros'),
    ('individual'),
    ('mediano'),
    ('grande');

-- Insertar guarniciones
INSERT INTO
    guarnicion (nombre, descripcion, precio, estado, stock)
VALUES
    (
        'Papas fritas',
        'Papas cortadas en bastones y fritas',
        3.50,
        1,
        100
    ),
    (
        'Ensalada fresca',
        'Mezcla de lechuga, tomate y zanahoria',
        4.00,
        1,
        50
    ),
    (
        'Arroz blanco',
        'Arroz cocido al estilo tradicional',
        2.50,
        1,
        80
    ),
    (
        'Yuca frita',
        'Yuca cortada y frita hasta dorar',
        3.80,
        1,
        60
    ),
    (
        'Plátano frito',
        'Rodajas de plátano dulce fritas',
        3.00,
        1,
        70
    );

-- Insertar productos (INCLUYENDO LOS DE LA TABLA PLATOS)
INSERT INTO
    producto (
        nombre,
        descripcion,
        precio,
        stock,
        tipo_producto_id,
        tamano_id,
        imagen
    )
VALUES
    -- Productos originales del sistema
    (
        'Ceviche de pescado',
        'Pescado fresco marinado en limón y especias',
        25.00,
        30,
        2,
        NULL,
        'productos/ceviche.jpg'
    ),
    (
        'Tallarines rojos',
        'Tallarines con salsa de tomate y especias',
        18.00,
        50,
        2,
        NULL,
        'productos/tallarines_rojos.jpg'
    ),
    (
        'Arroz con mariscos',
        'Arroz con una variedad de mariscos frescos',
        35.00,
        20,
        2,
        NULL,
        'productos/arroz_con_mariscos.jpg'
    ),
    (
        'Pollo a la brasa',
        'Pollo asado al estilo peruano con papas fritas',
        30.00,
        15,
        2,
        NULL,
        'productos/pollo_a_la_brasa.jpg'
    ),
    (
        'Chicha morada',
        'Bebida tradicional de maíz morado',
        5.00,
        100,
        1,
        NULL,
        'productos/chicha_morada.jpg'
    ),
    (
        'Causa rellena',
        'Puré de papa amarilla con relleno de atún o pollo',
        12.00,
        40,
        2,
        NULL,
        'productos/causa_rellena.jpg'
    ),
    (
        'Ronda criolla',
        'Variedad de platos criollos para compartir',
        40.00,
        20,
        2,
        NULL,
        'sin imagen.jpg'
    ),
    (
        'Sudado de pescado',
        'Pescado cocido en caldo con verduras',
        28.00,
        25,
        2,
        NULL,
        'sin imagen.jpg'
    ),
    (
        'Ensalada fresca',
        'Lechuga, tomate, zanahoria y aderezo especial',
        15.00,
        50,
        2,
        7,
        'sin imagen.jpg'
    ),
    (
        'Gaseosa Coca-Cola 330 ml',
        'Bebida gaseosa en botella pequeña',
        4.50,
        100,
        1,
        2,
        'sin imagen.jpg'
    ),
    (
        'Gaseosa Coca-Cola 1.5 litros',
        'Bebida gaseosa en botella grande',
        10.00,
        50,
        1,
        5,
        'sin imagen.jpg'
    ),
    (
        'Agua mineral 500 ml',
        'Agua mineral natural embotellada',
        3.00,
        80,
        1,
        3,
        'sin imagen.jpg'
    ),
    (
        'Cerveza Pilsen 330 ml',
        'Cerveza lager nacional',
        6.00,
        70,
        1,
        2,
        'sin imagen.jpg'
    ),
    (
        'Café americano',
        'Café filtrado de alta calidad',
        5.00,
        40,
        1,
        NULL,
        'sin imagen.jpg'
    ),
    (
        'Lomo saltado',
        'Carne de res salteada con cebolla y tomate',
        30.00,
        25,
        2,
        NULL,
        'sin imagen.jpg'
    ),
    (
        'Arroz chaufa',
        'Arroz frito estilo chino-peruano',
        20.00,
        30,
        2,
        7,
        'sin imagen.jpg'
    ),
    (
        'Alfajor de maicena',
        'Dulce tradicional relleno de manjar',
        3.50,
        100,
        3,
        NULL,
        'sin imagen.jpg'
    ),
    (
        'Helado de vainilla',
        'Postre frío de vainilla natural',
        7.00,
        40,
        3,
        NULL,
        'sin imagen.jpg'
    ),
    (
        'Combo familiar',
        'Incluye 2 rondas criollas + 4 bebidas',
        100.00,
        15,
        4,
        NULL,
        'sin imagen.jpg'
    ),
    (
        'Jugo de maracuyá 500 ml',
        'Jugo natural de maracuyá',
        6.00,
        60,
        1,
        3,
        'sin imagen.jpg'
    ),
    (
        'Sopa de pollo',
        'Caldo con pollo y verduras',
        18.00,
        40,
        2,
        NULL,
        'sin imagen.jpg'
    ),
    (
        'Ensalada mediana',
        'Porción mediana de ensalada fresca',
        25.00,
        35,
        2,
        8,
        'sin imagen.jpg'
    ),
    (
        'Yuca frita',
        'Yuca dorada y crujiente',
        12.00,
        50,
        2,
        NULL,
        'sin imagen.jpg'
    ),
    (
        'Cerveza Cusqueña 1 litro',
        'Cerveza premium en botella grande',
        12.00,
        30,
        1,
        4,
        'sin imagen.jpg'
    ),
    (
        'Postre tres leches',
        'Bizcocho bañado en tres tipos de leche',
        8.00,
        45,
        3,
        NULL,
        'sin imagen.jpg'
    ),
    (
        'Combo Menú Diario',
        'Incluye un plato de fondo, una bebida y una entrada',
        35.00,
        20,
        4,
        NULL,
        'sin imagen.jpg'
    ),
    -- Productos de la tabla platos
    (
        'Chaufa de pollo',
        'chaufa de pollo',
        10.00,
        10,
        2,
        NULL,
        'productos/chaufa.jpeg'
    ),
    (
        'Ceviche de mero',
        'de mero',
        12.00,
        10,
        2,
        NULL,
        'productos/ceviche_de_mero.jpg'
    ),
    (
        'Caldo de gallina',
        'porción grande',
        20.00,
        12,
        2,
        NULL,
        'productos/caldo_de_gallina.jpg'
    ),
    (
        'Chicha morada',
        'litros',
        5.00,
        30,
        1,
        4,
        'productos/chicha_morada.jpg'
    ),
    (
        'Arroz con pollo',
        '',
        12.00,
        2,
        2,
        NULL,
        'productos/arroz_con_pollo.jpg'
    ),
    (
        'Chaufa especial',
        'chaufa de pollo',
        10.00,
        10,
        2,
        NULL,
        'sin imagen.jpg'
    ),
    (
        'Ceviche de pota',
        'de pota',
        12.00,
        10,
        2,
        NULL,
        'sin imagen.jpg'
    ),
    (
        'Caldo de gallina',
        'porción grande',
        20.00,
        12,
        2,
        NULL,
        '681c492841b7a_caldo de gallina.jpg'
    ),
    (
        'Chicha morada',
        'litros',
        5.00,
        30,
        1,
        4,
        '681c4cf753083_receta-de-chicha-morada-de-peru-1.jpg'
    ),
    (
        'Arroz con pollo',
        '',
        12.00,
        2,
        2,
        NULL,
        'sin imagen.jpg'
    );

-- Relaciones producto/bebida
INSERT INTO
    producto_bebida (producto_id, tipo_bebida_id)
VALUES
    (5, 2),
    (6, 2),
    (7, 4),
    (8, 5),
    (19, 5),
    (9, 6),
    (15, 3),
    (26, 1);

-- Relaciones producto/plato  
INSERT INTO
    producto_plato (producto_id, tipo_plato_id)
VALUES
    (1, 3),
    (2, 2),
    (3, 3),
    (4, 4),
    (10, 3),
    (11, 3),
    (16, 5),
    (17, 4),
    (18, 1),
    (22, 3),
    (23, 3),
    (24, 3),
    (25, 5),
    (27, 3);

-- Relaciones producto/guarnicion
INSERT INTO
    producto_guarnicion (producto_id, guarnicion_id)
VALUES
    (10, 1),
    (10, 3),
    (3, 4),
    (1, 5),
    (11, 2),
    (2, 1),
    (2, 3),
    (2, 5),
    (22, 3),
    (23, 3),
    (27, 1),
    (27, 2);

-- Componentes de combos
INSERT INTO
    combo_componentes (
        combo_id,
        producto_id,
        obligatorio,
        cantidad,
        grupo
    )
VALUES
    (14, 2, 1, 2, 'platos principales'),
    (14, 6, 1, 2, 'bebidas'),
    (14, 8, 0, 2, 'bebidas'),
    (21, 10, 1, 1, 'fondo'),
    (21, 4, 1, 1, 'entrada'),
    (21, 15, 1, 1, 'bebida');

    -- Modificar la tabla comanda para incluir el estado 'entregado' y 'pagado'
ALTER TABLE comanda 
MODIFY COLUMN estado ENUM('nueva', 'pendiente', 'recibido', 'listo', 'entregado', 'pagado', 'cancelado') 
DEFAULT 'nueva';

-- Agregar campos a la tabla comanda
ALTER TABLE comanda 
ADD COLUMN tiene_cambios_pendientes BOOLEAN DEFAULT FALSE,
ADD COLUMN ultima_actualizacion TIMESTAMP NULL;

-- Agregar campo a detalle_comanda para marcar items nuevos/modificados
ALTER TABLE detalle_comanda 
ADD COLUMN es_cambio_pendiente BOOLEAN DEFAULT FALSE,
ADD COLUMN version_original INT DEFAULT NULL;