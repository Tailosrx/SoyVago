<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

// Obtener metas del usuario
$stmt = $connection->prepare("SELECT * FROM metas 
    WHERE usuario_id = :usuario_id 
    ORDER BY fecha_creacion DESC LIMIT 5");
$stmt->execute(['usuario_id' => $_SESSION['usuario_id']]);
$metas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener progreso
$stmt = $connection->prepare("SELECT * FROM metas WHERE usuario_id = :usuario_id AND completado = TRUE");
$stmt->execute(['usuario_id' => $_SESSION['usuario_id']]);
$progreso = $stmt->fetch();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - SoyVago</title>
    <link rel="stylesheet" href="public\css\styles.css">
</head>
<body>
    <div class="dashboard">
        <h1>Tus Metas</h1>
        
        <div class="stats">
            <div class="stat-card">
                <h3>Metas activas</h3>
                <p><?= count($metas) ?></p>
            </div>
            <div class="stat-card">
                <h3>Completadas</h3>
                <p><?= $progreso['completadas'] ?></p>
            </div>
        </div>
        
        <ul class="metas-list">
            <?php foreach ($metas as $meta): ?>
            <li class="<?= $meta['completada'] ? 'completada' : '' ?>">
                <h3><?= htmlspecialchars($meta['titulo']) ?></h3>
                <p>Creada el: <?= date('d/m/Y', strtotime($meta['fecha_creacion'])) ?></p>
                <form action="procesar_meta.php" method="POST" class="meta-actions">
                    <input type="hidden" name="meta_id" value="<?= $meta['id'] ?>">
                    <?php if (!$meta['completada']): ?>
                        <button type="submit" name="accion" value="completar">✅ Completar</button>
                    <?php endif; ?>
                    <button type="submit" name="accion" value="eliminar">🗑️ Eliminar</button>
                </form>
            </li>
            <?php endforeach; ?>
        </ul>
        
        <a href="nueva_meta.php" class="add-button">+ Nueva Meta</a>
    </div>
</body>
</html>