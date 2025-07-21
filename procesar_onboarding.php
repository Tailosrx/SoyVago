<?php
session_start();
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // 1. Insertar primera meta
        $stmt = $connection->prepare("INSERT INTO metas 
            (usuario_id, titulo, tipo_recordatorio, fecha_creacion) 
            VALUES (:usuario_id, :titulo, :recordatorio, NOW())");
        
        $stmt->execute([
            'usuario_id' => $_SESSION['usuario_id'],
            'titulo' => $_POST['objetivo'],
            'recordatorio' => $_POST['recordatorio']
        ]);
        
        // 2. Marcar onboarding como completo
        $_SESSION['onboarding_complete'] = true;
        
        // 3. Redirigir al dashboard
        header('Location: dashboard.php');
        exit();
        
    } catch (PDOException $e) {
        error_log("Error en onboarding: " . $e->getMessage());
        header('Location: onboarding.php?error=1');
        exit();
    }
}