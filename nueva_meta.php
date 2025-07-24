<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $fecha_limite = !empty($_POST['fecha_limite']) ? $_POST['fecha_limite'] : null;
    $importancia = $_POST['importancia'] ?? 3;
    $colores = ['#4C6B9F', '#588157', '#9C3D54', '#6B4226', '#3E6C7D', '#7E4A35', '#486966', '#8A5E3B'];
    $color_aleatorio = $colores[array_rand($colores)];

    if (empty($titulo)) {
        die('El título es obligatorio.');
    }

    $stmt = $connection->prepare("
    INSERT INTO metas (usuario_id, titulo, descripcion, completado, fecha_creacion, fecha_limite, importancia, color)
    VALUES (:usuario_id, :titulo, :descripcion, FALSE, NOW(), :fecha_limite, :importancia, :color)
");

    $stmt->execute([
    'usuario_id' => $_SESSION['usuario_id'],
    'titulo' => $titulo,
    'descripcion' => $descripcion,
    'importancia' => $importancia,
    'fecha_limite' => $fecha_limite,
    'color' => $color_aleatorio
    ]);


    switch ($importancia) {
        case 1:
            $puntos = 10;
            break;
        case 2:
            $puntos = 20;
            break;
        default:
            $puntos = 30;
    }

    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Nueva Meta - SoyVago</title>
    <link rel="stylesheet" href="public/css/styles.css">
</head>
<body>
    <div class="form-container">
        <h1>Crear Nueva Meta</h1>
        <form method="POST" action="">
            <label for="titulo">Título *</label>
            <input type="text" name="titulo" id="titulo" required>

            <label for="descripcion">Descripción</label>
            <textarea name="descripcion" id="descripcion" rows="3"></textarea>

            <label for="fecha_limite">Fecha Límite</label>
            <input type="date" name="fecha_limite" id="fecha_limite">
            <label><input type="checkbox" name="sin_fecha_limite" id="sin_fecha"> Sin fecha límite</label>

            <?php 
            $fecha_limite = isset($_POST['fecha_limite']) ? $_POST['fecha_limite'] : null;
            ?>

            <label>¿Qué tan importante es esta meta para ti?</label>
            <div class="importancia-opciones">
             <label><input type="radio" name="importancia" value="1"> 🌱 Poco importante</label>
             <label><input type="radio" name="importancia" value="2"> 💪 Importante</label>
             <label><input type="radio" name="importancia" value="3" checked> 🚀 Muy importante</label>
            </div>

            


            <button type="submit">Guardar Meta</button>
        </form>
        <a href="dashboard.php">← Volver al Dashboard</a>
    </div>
</body>
</html>
