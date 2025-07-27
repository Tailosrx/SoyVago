<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$filtro_completado = $_GET['completado'] ?? 'todas'; // 'todas', 'completadas', 'pendientes'
$filtro_importancia = $_GET['importancia'] ?? 'todas'; // 'todas', '1', '2', '3'
$orden = $_GET['orden'] ?? 'fecha_creacion_desc'; // 'fecha_creacion_asc', 'fecha_creacion_desc', 'fecha_limite_asc', 'importancia_desc'


function importanciaTexto($nivel) {
    switch($nivel) {
        case 1: return 'Poco importante';
        case 2: return 'Importante';
        case 3: return 'Muy importante';
        default: return 'Importancia no definida';
    }
}


$sql = "SELECT * FROM metas WHERE usuario_id = :usuario_id";
$params = ['usuario_id' => $_SESSION['usuario_id']];

if ($filtro_completado !== 'todas') {
    $sql .= " AND completado = :completado";
    $params['completado'] = $filtro_completado === 'completadas' ? 1 : 0;
}

if ($filtro_importancia !== 'todas') {
    $sql .= " AND importancia = :importancia";
    $params['importancia'] = $filtro_importancia;
}

switch ($orden) {
    case 'fecha_creacion_asc':
        $sql .= " ORDER BY fecha_creacion ASC";
        break;
    case 'fecha_limite_asc':
        $sql .= " ORDER BY fecha_limite ASC";
        break;
    case 'importancia_desc':
        $sql .= " ORDER BY importancia DESC";
        break;
    default:
        $sql .= " ORDER BY fecha_creacion DESC";
}

$stmt = $connection->prepare($sql);
$stmt->execute($params);
$metas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mis Metas - SoyVago</title>
    <link rel="stylesheet" href="public/css/styles.css">
</head>
<body>
    <div class="app-container">
    <aside class="vertical-sidebar"> 
    <nav>
        <section class="sidebar__wrapper">
        <ul class="sidebar__list list--primary">
    <li class="sidebar__item"> 
        <a class="sidebar__link" href="dashboard.php" data-tooltip="Dashboard"> 
            <span class="icon"> 
                <svg width="16" height="16" fill="currentColor" class="bi bi-house-door" viewBox="0 0 16 16">
                    <path d="M8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4.5a.5.5 0 0 0 .5-.5v-4h2v4a.5.5 0 0 0 .5.5H14a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146zM2.5 14V7.707l5.5-5.5 5.5 5.5V14H10v-4a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5v4H2.5z"/>
                </svg> 
            </span> 
            <span class="text">Dashboard</span> 
        </a> 
    </li>
    <li class="sidebar__item"> 
        <a class="sidebar__link" href="mis_metas.php" data-tooltip="Mis Metas"> 
            <span class="icon"> 
                <svg width="16" height="16" fill="currentColor" class="bi bi-bullseye" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                    <path d="M8 13A5 5 0 1 1 8 3a5 5 0 0 1 0 10zm0 1A6 6 0 1 0 8 2a6 6 0 0 0 0 12z"/>
                    <path d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm0 1a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/>
                    <path d="M9.5 8a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                </svg> 
            </span> 
            <span class="text">Mis Metas</span> 
        </a> 
    </li>
    <li class="sidebar__item"> 
        <a class="sidebar__link" href="#" data-tooltip="Tareas/Hábitos"> 
            <span class="icon"> 
                <svg width="16" height="16" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                    <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                </svg> 
            </span> 
            <span class="text">Tareas/Hábitos</span> 
        </a> 
    </li>
    <li class="sidebar__item"> 
        <a class="sidebar__link" href="#" data-tooltip="Calendario"> 
            <span class="icon"> 
                <svg width="16" height="16" fill="currentColor" class="bi bi-calendar" viewBox="0 0 16 16">
                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                </svg> 
            </span> 
            <span class="text">Calendario</span> 
        </a> 
    </li>
</ul>
            <hr>
            <ul class="sidebar__list list--secondary">
                <li class="sidebar__item"> <a class="sidebar__link" href="#" data-tooltip="Profile"> <span class="icon"> <svg width="16" height="16" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                                <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                                <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
                            </svg> </span> <span class="text">Mi Cuenta</span> </a> </li>
                <li class="sidebar__item"> <a class="sidebar__link" href="#" data-tooltip="Settings"> <span class="icon"> <svg width="16" height="16" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16">
                                <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0" />
                                <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z" />
                            </svg> </span> <span class="text">Ajustes</span> </a> </li>
                <li class="sidebar__item"> <a class="sidebar__link" href="logout.php" data-tooltip="Logout"> <span class="icon"> <svg width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z" />
                                <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z" />
                            </svg> </span> <span class="text">Salir</span> </a> </li>
            </ul>
        </section>
    </nav>
</aside>
      


<main class="main-content">
<h2 class="center-title">Mis Metas</h2>
        
        <!-- Filtros -->
        <section class="filtros-container">
            <h2 class="filtros-title">Opciones de Filtrado</h2>
            <form method="GET" class="filtro-form">
                <div class="filtro-grupo">
                    <label for="completado">Estado:</label>
                    <select name="completado" id="completado">
                        <option value="todas" <?= $filtro_completado === 'todas' ? 'selected' : '' ?>>Todas las metas</option>
                        <option value="completadas" <?= $filtro_completado === 'completadas' ? 'selected' : '' ?>>Solo completadas</option>
                        <option value="pendientes" <?= $filtro_completado === 'pendientes' ? 'selected' : '' ?>>Solo pendientes</option>
                    </select>
                </div>
                
                <div class="filtro-grupo">
                    <label for="importancia">Importancia:</label>
                    <select name="importancia" id="importancia">
                        <option value="todas" <?= $filtro_importancia === 'todas' ? 'selected' : '' ?>>Todas</option>
                        <option value="1" <?= $filtro_importancia === '1' ? 'selected' : '' ?>>Poco importante</option>
                        <option value="2" <?= $filtro_importancia === '2' ? 'selected' : '' ?>>Importante</option>
                        <option value="3" <?= $filtro_importancia === '3' ? 'selected' : '' ?>>Muy importante</option>
                    </select>
                </div>
                
                <div class="filtro-grupo">
                    <label for="orden">Ordenar por:</label>
                    <select name="orden" id="orden">
                        <option value="fecha_creacion_desc" <?= $orden === 'fecha_creacion_desc' ? 'selected' : '' ?>>Más recientes primero</option>
                        <option value="fecha_creacion_asc" <?= $orden === 'fecha_creacion_asc' ? 'selected' : '' ?>>Más antiguas primero</option>
                        <option value="fecha_limite_asc" <?= $orden === 'fecha_limite_asc' ? 'selected' : '' ?>>Fecha límite (próximas)</option>
                        <option value="importancia_desc" <?= $orden === 'importancia_desc' ? 'selected' : '' ?>>Más importantes primero</option>
                    </select>
                </div>
                
                <div class="filtro-grupo">
                    <button type="submit" class="btn-filtrar">Aplicar Filtros</button>
                    <a href="mis_metas.php" class="btn-limpiar">Limpiar Filtros</a>
                </div>
            </form>
        </section>
        
        <div class="resultados-count">
            Mostrando <?= count($metas) ?> meta(s) que coinciden con tus filtros
        </div>
        
        <!-- Sección de streaks adaptada del dashboard -->
        <section class="streaks">
            <h2>Mis Metas</h2>
            <div class="streak-grid">
                <?php if (empty($metas)): ?>
                    <p class="sin-metas">No hay metas que coincidan con tus criterios de filtrado.</p>
                <?php else: ?>
                    <?php foreach ($metas as $meta): ?>
                        <div class="streak-card" data-importancia="<?= $meta['importancia'] ?>" style="--card-color: <?= $meta['color'] ?>;">
                            <div class="importancia-indicador" title="Importancia: <?= importanciaTexto($meta['importancia']) ?>">
                                <h5><?= importanciaTexto($meta['importancia']) ?></h5>
                            </div>

                            <h3><?= htmlspecialchars($meta['titulo']) ?></h3>
                            
                            <?php if (!empty($meta['descripcion'])): ?>
                                <p class="meta-descripcion">
                                    <?php 
                                    $descripcion = htmlspecialchars($meta['descripcion']);
                                    if (strlen($descripcion) > 100) {
                                        $descripcion = substr($descripcion, 0, 100) . '...';
                                    }
                                    echo nl2br($descripcion);
                                    ?>
                                </p>
                            <?php endif; ?>
                            
                            <div class="streak-progress">
        <div class="progress-bar" style="width: <?= min(100, ($meta['racha'] ?? 0) * 10) ?>%"></div>
    </div>
    <p>Racha: <?= $meta['racha'] ?? 0 ?> días</p>
    
    <?php if ($meta['fecha_limite']): ?>
        <div class="fecha-container">
            <p class="fecha-limite">📅 <?= date('d/m/Y', strtotime($meta['fecha_limite'])) ?></p>
            <?php 
            $hoy = new DateTime();
            $fecha_lim = new DateTime($meta['fecha_limite']);
            if ($fecha_lim < $hoy && !$meta['completado']) {
                echo '<span class="vencido">(Vencido)</span>';
            }
            ?>
        </div>
    <?php endif; ?>
                            
    <div class="card-footer">
        <div class="meta-acciones">
            <a href="ver_meta.php?id=<?= $meta['id'] ?>" class="btn-ver">
                <svg class="icon-ver" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M12 9a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3m0 8a5 5 0 0 1-5-5 5 5 0 0 1 5-5 5 5 0 0 1 5 5 5 5 0 0 1-5 5m0-12.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5z"/>
                </svg>
                Ver detalles
            </a>
            <a href="editar_meta.php?id=<?= $meta['id'] ?>" class="btn-editar">
                <svg class="icon-editar" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                </svg>
            </a>
        </div>
        
        <form method="POST" action="procesar_meta.php" class="delete-form">
            <input type="hidden" name="meta_id" value="<?= $meta['id'] ?>">
            <input type="hidden" name="accion" value="eliminar">
            <button type="submit" class="delete-btn">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20" height="20">
                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                </svg>
            </button>
        </form>
    </div>
</div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
</main>










         
       
</body>
</html>