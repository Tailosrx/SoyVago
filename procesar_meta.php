<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['meta_id'], $_POST['accion'])) {
    $metaId = $_POST['meta_id'];
    $accion = $_POST['accion'];
    $usuarioId = $_SESSION['usuario_id'];

    if ($accion === 'completar') {
        $stmt = $connection->prepare("UPDATE metas SET completado = TRUE, racha = racha + 1 WHERE id = :id AND usuario_id = :usuario_id");
        $stmt->execute(['id' => $metaId, 'usuario_id' => $usuarioId]);
    }

    if ($accion === 'eliminar') {
        $stmt = $connection->prepare("DELETE FROM metas WHERE id = :id AND usuario_id = :usuario_id");
        $stmt->execute(['id' => $metaId, 'usuario_id' => $usuarioId]);
        header('Location: dashboard.php');
        exit();
    }
}

header('Location: mis_metas.php');
exit();
