<?php
session_start();
require_once 'config/db.php';
require_once 'controllers/get_ajustes.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $fecha_limite = !empty($_POST['fecha_limite']) ? $_POST['fecha_limite'] : null;
    $importancia = $_POST['importancia'] ?? 3;
    $colores = ['#4C6B9F', '#588157', '#9C3D54', '#6B4226', '#3E6C7D', '#7E4A35', '#486966', '#8A5E3B'];
    $color_aleatorio = $colores[array_rand($colores)];

    if (empty($titulo)) {
        die('El título es obligatorio.');
    }

    $stmt = $connection->prepare("
    INSERT INTO metas (usuario_id, titulo, completado, fecha_creacion, fecha_limite, importancia, color)
    VALUES (:usuario_id, :titulo,  FALSE, NOW(), :fecha_limite, :importancia, :color)
");

    $stmt->execute([
    'usuario_id' => $_SESSION['usuario_id'],
    'titulo' => $titulo,
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
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Meta - SoyVago</title>
    <link rel="stylesheet" href="public\css\styles.css">
    <style>
        /* Estilos específicos para la página de nueva meta */
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Ubuntu';
        }

        .form-container {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 400px;
            width: 90%;
            text-align: center;
        }

        .form-container h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }

        .form-container label {
            display: block;
            font-size: 14px;
            color: #555;
            margin-bottom: 5px;
            text-align: left;
        }

        .form-container input[type="text"],
        .form-container input[type="date"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        .form-container input[type="checkbox"] {
            margin-right: 5px;
        }

        .form-container .importancia-opciones {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .form-container .importancia-opciones label {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .form-container .importancia-opciones input[type="radio"] {
            margin-right: 10px;
        }

        .form-container button {
            background-color: #4C6B9F;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-container button:hover {
            background-color: #3e5a85;
        }

        .form-container a {
            display: inline-block;
            margin-top: 15px;
            color: #4C6B9F;
            text-decoration: none;
            font-size: 14px;
        }

        .form-container a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .form-container {
                padding: 20px;
            }

            .form-container h1 {
                font-size: 20px;
            }

            .form-container button {
                font-size: 14px;
            }
        }
    </style>
</head>
<body class="<?= $ajustes['modo_oscuro'] ? 'modo-oscuro' : 'modo-diurno' ?>">
    <div class="form-container">
        <h1>Crear Nueva Meta</h1>
        <form method="POST" action="">
            <label for="titulo">Título *</label>
            <input type="text" name="titulo" id="titulo" placeholder="Escribe el título de tu meta" required>

            <label for="fecha_limite">Fecha Límite</label>
            <input type="date" name="fecha_limite" id="fecha_limite">
            <label>
                <input type="checkbox" name="sin_fecha_limite" id="sin_fecha"> Sin fecha límite
            </label>

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
