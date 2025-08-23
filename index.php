<?php

require_once "config/db.php"; // ajusta la ruta según tu estructura
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: registro.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Consultar tareas
$queryTareas = $connection->prepare("SELECT * FROM tareas WHERE usuario_id = :usuario_id ORDER BY fecha ASC");
$queryTareas->execute(['usuario_id' => $usuario_id]);
$tareas = $queryTareas->fetchAll(PDO::FETCH_ASSOC);

// Consultar rachas
$queryRachas = $connection->prepare("SELECT * FROM rachas WHERE usuario_id = :usuario_id");
$queryRachas->execute(['usuario_id' => $usuario_id]);
$rachas = $queryRachas->fetchAll(PDO::FETCH_ASSOC);
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SoyVago - Panel</title>
    <link rel="stylesheet" href="public\css\styles.css">
</head>
<body>
    <header>
        <h1>Hola, <?= htmlspecialchars($_SESSION['usuario_nombre']); ?> 👋</h1>
    </header>

    <section class="bloque">
        <h2>✅ Tareas</h2>
        <?php if (count($tareas) > 0): ?>
            <ul>
                <?php foreach ($tareas as $t): ?>
                    <li class="<?= $t['completada'] ? 'hecha' : ''; ?>">
                        <?= htmlspecialchars($t['descripcion']); ?> 
                        <small>(<?= $t['fecha']; ?>)</small>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No tienes tareas todavía.</p>
        <?php endif; ?>
    </section>

    <section class="bloque">
        <h2>🔥 Rachas</h2>
        <?php if (count($rachas) > 0): ?>
            <ul>
                <?php foreach ($rachas as $r): ?>
                    <li>
                        <?= htmlspecialchars($r['nombre']); ?> 
                        <small><?= $r['dias_conseguidos']; ?>/<?= $r['dias_totales']; ?> días</small>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No tienes rachas activas.</p>
        <?php endif; ?>
    </section>
</body>
</html>
