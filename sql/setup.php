<?php
require_once '../config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Crear base de datos
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    echo "Base de datos '" . DB_NAME . "' creada o ya existe.<br>";

    // Conectarse a la nueva base
    $pdo->exec("USE " . DB_NAME);

    // Listar
    $sqlFiles = [
        './userInitializer.sql',
        './empresaInitializer.sql',
        './mesasInitializer.sql',
        './comandaInitializer.sql'
    ];

    // Crear tablas
    foreach ($sqlFiles as $file) {
        if (file_exists($file)) {
            echo "Ejecutando $file...<br>";
            $sql = file_get_contents($file);
            $queries = array_filter(array_map('trim', explode(";", $sql)));

            foreach ($queries as $query) {
                if (!empty($query)) {
                    $pdo->exec($query);
                }
            }
            echo "$file ejecutado correctamente.<br>";
        } else {
            echo "Archivo $file no encontrado.<br>";
        }
    }

    echo "Todas las tablas fueron creadas correctamente.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
