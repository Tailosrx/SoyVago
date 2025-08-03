<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

// Verificar si se recibió un ID de meta válido
if (!isset($_GET['id'])) {
    header('Location: mis_metas.php');
    exit();
}

$meta_id = $_GET['id'];
$usuario_id = $_SESSION['usuario_id'];

// Obtener la meta a editar
$stmt = $connection->prepare("SELECT * FROM metas WHERE id = :id AND usuario_id = :usuario_id");
$stmt->execute(['id' => $meta_id, 'usuario_id' => $usuario_id]);
$meta = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$meta) {
    header('Location: mis_metas.php');
    exit();
}

// Procesar el formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $importancia = intval($_POST['importancia']);
    $fecha_limite = !empty($_POST['fecha_limite']) ? $_POST['fecha_limite'] : null;
    $color = $_POST['color'] ?? '#4C6B9F';

    // Validaciones básicas
    if (empty($titulo)) {
        $error = "El título no puede estar vacío";
    } else {
        // Actualizar la meta en la base de datos
        $sql = "UPDATE metas SET 
                titulo = :titulo, 
                descripcion = :descripcion, 
                importancia = :importancia, 
                fecha_limite = :fecha_limite,
                color = :color
                WHERE id = :id AND usuario_id = :usuario_id";

        $stmt = $connection->prepare($sql);
        $stmt->execute([
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'importancia' => $importancia,
            'fecha_limite' => $fecha_limite,
            'color' => $color,
            'id' => $meta_id,
            'usuario_id' => $usuario_id
        ]);

        // Redirigir a mis_metas.php con mensaje de éxito
        $_SESSION['mensaje'] = "Meta actualizada correctamente";
        header('Location: mis_metas.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Meta - SoyVago</title>
    <link rel="stylesheet" href="public/css/styles.css">
    <style>
        .form-editar-meta {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: #2a2a2a;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .form-editar-meta label {
            display: block;
            margin-bottom: 5px;
            color: #f3ecec;
        }
        
        .form-editar-meta input[type="text"],
        .form-editar-meta textarea,
        .form-editar-meta select,
        .form-editar-meta input[type="date"],
        .form-editar-meta input[type="color"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            background: #3a3a3a;
            border: 1px solid #634b4b;
            border-radius: 5px;
            color: #f3ecec;
        }
        
        .form-editar-meta textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .btn-guardar {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }
        
        .btn-guardar:hover {
            background: #45a049;
        }
        
        .color-preview {
            width: 30px;
            height: 30px;
            display: inline-block;
            border-radius: 50%;
            margin-left: 10px;
            vertical-align: middle;
            border: 2px solid #fff;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <aside class="vertical-sidebar">
            <!-- Incluir el mismo sidebar que en mis_metas.php -->
            <?php include 'partials/sidebar.php'; ?>
        </aside>
        
        <main class="main-content">
            <h2 class="center-title">Editar Meta</h2>
            
            <?php if (isset($error)): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form method="POST" class="form-editar-meta">
                <div>
                    <label for="titulo">Título:</label>
                    <input type="text" id="titulo" name="titulo" value="<?= htmlspecialchars($meta['titulo']) ?>" required>
                </div>
                
                <div>
                    <label for="descripcion">Descripción:</label>
                    <textarea id="descripcion" name="descripcion"><?= htmlspecialchars($meta['descripcion']) ?></textarea>
                </div>
                
                <div>
                    <label for="importancia">Importancia:</label>
                    <select id="importancia" name="importancia" required>
                        <option value="1" <?= $meta['importancia'] == 1 ? 'selected' : '' ?>>Poco importante</option>
                        <option value="2" <?= $meta['importancia'] == 2 ? 'selected' : '' ?>>Importante</option>
                        <option value="3" <?= $meta['importancia'] == 3 ? 'selected' : '' ?>>Muy importante</option>
                    </select>
                </div>
                
                <div>
                    <label for="fecha_limite">Fecha límite (opcional):</label>
                    <input type="date" id="fecha_limite" name="fecha_limite" 
                           value="<?= $meta['fecha_limite'] ? date('Y-m-d', strtotime($meta['fecha_limite'])) : '' ?>">
                </div>
                
                <div>
                    <label for="color">Color de la tarjeta:</label>
                    <input type="color" id="color" name="color" value="<?= $meta['color'] ?? '#4C6B9F' ?>">
                    <span class="color-preview" style="background: <?= $meta['color'] ?? '#4C6B9F' ?>"></span>
                </div>
                
                <button type="submit" class="btn-guardar">Guardar Cambios</button>
                <a href="mis_metas.php" style="color: #c47d7f; margin-left: 15px;">Cancelar</a>
            </form>
        </main>
    </div>

    <script>
        // Actualizar el preview del color cuando cambie
        document.getElementById('color').addEventListener('input', function() {
            document.querySelector('.color-preview').style.background = this.value;
        });
    </script>
</body>
</html>