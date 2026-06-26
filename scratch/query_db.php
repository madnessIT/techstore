<?php
try {
    $pdo = new PDO("mysql:host=localhost;port=3306;dbname=techstore;charset=utf8mb4", "root", "M4nd4m4s");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "--- CLIENTES ---\n";
    $stmt = $pdo->prepare("SELECT id, nombre, apellido, email, password, activo, verificado FROM clientes WHERE email = ?");
    $stmt->execute(['joaquin@techstore.com']);
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($clientes);

    echo "\n--- USUARIOS ---\n";
    $stmt = $pdo->prepare("SELECT id, nombre, apellido, email, password, activo, rol FROM usuarios WHERE email = ?");
    $stmt->execute(['joaquin@techstore.com']);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($usuarios);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
