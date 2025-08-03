<?php

require_once 'config/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

// Procesar actualizaciones de ajustes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $notificaciones = isset($_POST['notificaciones']) ? 1 : 0;
    $modo_oscuro = isset($_POST['modo_oscuro']) ? 1 : 0;
    $color_principal = $_POST['color_principal'] ?? '#db9e1b';

    $stmt = $connection->prepare("UPDATE usuarios SET 
                                notificaciones = :notificaciones,
                                modo_oscuro = :modo_oscuro,
                                color_principal = :color_principal
                                WHERE id = :id");
    $stmt->execute([
        'notificaciones' => $notificaciones,
        'modo_oscuro' => $modo_oscuro,
        'color_principal' => $color_principal,
        'id' => $_SESSION['usuario_id']
    ]);

    $_SESSION['mensaje'] = "Ajustes actualizados correctamente";
    header('Location: ajustes.php');
    exit();
}

// Obtener ajustes actuales
$stmt = $connection->prepare("SELECT notificaciones, modo_oscuro, color_principal FROM usuarios WHERE id = :id");
$stmt->execute(['id' => $_SESSION['usuario_id']]);
$ajustes = $stmt->fetch(PDO::FETCH_ASSOC);
