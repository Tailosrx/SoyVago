<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$usuario_id = (int)$_SESSION['usuario_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $archivo = $_FILES['avatar'];
    $nombreArchivo = $archivo['name'];
    $tipoArchivo = $archivo['type'];
    $tamanoArchivo = $archivo['size'];
    $rutaTemporal = $archivo['tmp_name'];
    $errorArchivo = $archivo['error'];

    // Validar errores en la subida
    if ($errorArchivo !== UPLOAD_ERR_OK) {
        die('Error al subir el archivo.');
    }

    // Validar tipo de archivo (solo imágenes)
    $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
    $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));
    if (!in_array($extension, $extensionesPermitidas)) {
        die('Solo se permiten archivos JPG, JPEG, PNG o GIF.');
    }

    // Validar tamaño del archivo (máximo 2 MB)
    if ($tamanoArchivo > 2 * 1024 * 1024) {
        die('El archivo es demasiado grande. Máximo 2 MB.');
    }

    // Generar un nombre único para la imagen
    $nombreUnico = uniqid('avatar_', true) . '.' . $extension;

    // Ruta donde se guardará la imagen
    $rutaDestino = 'public/img/' . $nombreUnico;

    // Mover el archivo subido a la carpeta de destino
    if (!move_uploaded_file($rutaTemporal, $rutaDestino)) {
        die('Error al guardar el archivo.');
    }

    // Guardar la ruta de la imagen en la base de datos
    $sql = "UPDATE usuarios SET avatar = :avatar WHERE id = :id";
    $stmt = $connection->prepare($sql);
    $stmt->execute(['avatar' => $rutaDestino, 'id' => $usuario_id]);


    // Redirigir al perfil con un mensaje de éxito
    header('Location: account.php?mensaje=avatar_actualizado');
    exit();
}