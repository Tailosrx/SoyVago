<?php
session_start();
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Determinar si es login o registro
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';
    
    try {
        if ($accion === 'registro') {
            procesarRegistro($connection);
        } elseif ($accion === 'login') {
            procesarLogin($connection);
        } else {
            header('Location: login.php?error=accion_invalida');
            exit();
        }
    } catch (PDOException $e) {
        error_log('Error de base de datos: ' . $e->getMessage());
        header('Location: login.php?error=db_error');
        exit();
    }
}

function procesarRegistro($connection) {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmar = trim($_POST['confirmar']);

    // Validaciones
    if (empty($nombre) || empty($email) || empty($password) || empty($confirmar)) {
        header('Location: registro.php?error=campos_vacios');
        exit();
    }

    if ($password !== $confirmar) {
        header('Location: registro.php?error=password_mismatch');
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: registro.php?error=email_invalido');
        exit();
    }

    // Verificar si el email ya existe
    $stmt = $connection->prepare("SELECT id FROM usuarios WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    if ($stmt->fetch()) {
        header('Location: registro.php?error=email_existente');
        exit();
    }

    // Hash de la contraseña
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Insertar nuevo usuario
    $stmt = $connection->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (:nombre, :email, :password)");
    $stmt->execute([
        'nombre' => $nombre,
        'email' => $email,
        'password' => $hash
    ]);
    
    // Establecer sesión y redirigir
    $_SESSION['usuario_id'] = $connection->lastInsertId();
    $_SESSION['nombre'] = $nombre;
    $_SESSION['email'] = $email;

    $_SESSION['onboarding_pendiente'] = true;
        header('Location: onboarding.php');
    
    exit();
}

function procesarLogin($connection) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        header('Location: login.php?error=campos_vacios');
        exit();
    }

    $stmt = $connection->prepare("SELECT id, nombre, password FROM usuarios WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($password, $usuario['password'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['email'] = $email;
        
        header('Location: dashboard.php');
        exit();
    } else {
        header('Location: login.php?error=credenciales');
        exit();
    }
}