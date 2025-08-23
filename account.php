<?php
session_start();
require_once 'config/db.php';
require_once 'controllers/get_ajustes.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$userId = (int) $_SESSION['usuario_id'];

/* --- Datos del usuario --- */
$sqlUser = "SELECT id, nombre, email, nivel, puntos, 
                   fecha_registro, avatar,      
                   notificaciones, modo_oscuro, color_principal
            FROM usuarios
            WHERE id = :id";

$stmt = $connection->prepare($sqlUser);
$stmt->execute(['id' => $userId]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

$usuario['avatar'] = !empty($usuario['avatar']) ? $usuario['avatar'] : 'public/img/default.png';


if (!$usuario) {
    die('Usuario no encontrado.');
}

/* --- Estadísticas rápidas de metas --- */
$sqlStats = "SELECT
               COUNT(*) FILTER (WHERE completado = FALSE) AS activas,
               COUNT(*) FILTER (WHERE completado = TRUE)  AS completadas
             FROM metas
             WHERE usuario_id = :id";
$stmt = $connection->prepare($sqlStats);
$stmt->execute(['id' => $userId]);
$stats = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['activas' => 0, 'completadas' => 0];

/* --- Logros del usuario (opcional: si aún no creaste las tablas, esto se omite sin romper) --- */
$logros = [];
try {
    $sqlLogros = "SELECT l.id, l.nombre, l.descripcion, l.icono, ul.fecha_desbloqueo
                  FROM usuario_logros ul
                  JOIN logros l ON l.id = ul.logro_id
                  WHERE ul.usuario_id = :id
                  ORDER BY ul.fecha_desbloqueo DESC
                  LIMIT 12";
    $st = $connection->prepare($sqlLogros);
    $st->execute(['id' => $userId]);
    $logros = $st->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Si las tablas de logros aún no existen, seguimos sin logros
}

/* --- Preferencias visuales --- */
$modoOscuro = !empty($usuario['modo_oscuro']);
$colorPrincipal = $usuario['color_principal'] ?: '#db9e1b';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Cuenta - SoyVago</title>
    <link rel="stylesheet" href="public/css/styles.css">
    <style>
    .perfil-container{max-width:900px;margin:24px auto;padding:24px;background:#fff;border-radius:16px;box-shadow:0 8px 24px rgba(0,0,0,.08)}
    body.modo-oscuro .perfil-container{background:#1f1f1f;color:#eee}
    .perfil-header{display:flex;gap:16px;align-items:center}
    .avatar{width:96px;height:96px;border-radius:50%;background:#ddd;display:block;object-fit:cover}
    .perfil-badges{display:flex;gap:12px;margin-top:8px}
    .badge{background:rgba(0,0,0,.06);padding:6px 10px;border-radius:999px;font-size:.9rem}
    body.modo-oscuro .badge{background:rgba(255,255,255,.1)}
    .perfil-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-top:20px}
    .card{padding:16px;border-radius:12px;background:rgba(0,0,0,.03)}
    body.modo-oscuro .card{background:rgba(255,255,255,.06)}
    .logros{display:grid;grid-template-columns:repeat(6,1fr);gap:12px;margin-top:16px}
    .logro{display:flex;flex-direction:column;align-items:center;gap:6px;padding:10px;border-radius:10px;background:rgba(0,0,0,.03);text-align:center}
    body.modo-oscuro .logro{background:rgba(255,255,255,.06)}
    .color-chip{display:inline-block;width:16px;height:16px;border-radius:4px;border:1px solid rgba(0,0,0,.15);vertical-align:middle}
  </style>
</head>
<body class="<?= $ajustes['modo_oscuro'] ? 'modo-oscuro' : 'modo-diurno' ?>">
<div class="app-container">
<aside class="vertical-sidebar">
            <nav>
                <section class="sidebar__wrapper">
                    <ul class="sidebar__list list--primary">
                        <li class="sidebar__item">
                            <a class="sidebar__link" href="dashboard.php" data-tooltip="Dashboard">
                                <span class="icon">
                                    <svg width="16" height="16" fill="currentColor" class="bi bi-house-door"
                                        viewBox="0 0 16 16">
                                        <path
                                            d="M8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4.5a.5.5 0 0 0 .5-.5v-4h2v4a.5.5 0 0 0 .5.5H14a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146zM2.5 14V7.707l5.5-5.5 5.5 5.5V14H10v-4a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5v4H2.5z" />
                                    </svg>
                                </span>
                                <span class="text">Dashboard</span>
                            </a>
                        </li>
                        <li class="sidebar__item">
                            <a class="sidebar__link" href="mis_metas.php" data-tooltip="Mis Metas">
                                <span class="icon">
                                    <svg width="16" height="16" fill="currentColor" class="bi bi-bullseye"
                                        viewBox="0 0 16 16">
                                        <path
                                            d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                        <path
                                            d="M8 13A5 5 0 1 1 8 3a5 5 0 0 1 0 10zm0 1A6 6 0 1 0 8 2a6 6 0 0 0 0 12z" />
                                        <path d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm0 1a4 4 0 1 0 0-8 4 4 0 0 0 0 8z" />
                                        <path d="M9.5 8a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z" />
                                    </svg>
                                </span>
                                <span class="text">Mis Metas</span>
                            </a>
                        </li>
                        <li class="sidebar__item">
                            <a class="sidebar__link" href="logros.php" data-tooltip="Mis Metas">
                                <span class="icon">
                                    <svg width="16" height="16" fill="currentColor" class="bi bi-trophy"
                                        viewBox="0 0 16 16">
                                        <path
                                            d="M3 1a1 1 0 0 0-1 1v2a3 3 0 0 0 2.5 2.959V6a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1v.959A3 3 0 0 0 14 4V2a1 1 0 0 0-1-1H3zm10 2a2 2 0 0 1-1 1.732V2h1v1zm-1 3.1A3.001 3.001 0 0 1 8 8a3.001 3.001 0 0 1-4-1.9V2h8v3.1zM2 2v1.732A2 2 0 0 1 3 2H2zm6 7a4 4 0 0 0 4-4h1a5 5 0 0 1-5 5v1h1a1 1 0 0 1 1 1v1H5v-1a1 1 0 0 1 1-1h1v-1a5 5 0 0 1-5-5h1a4 4 0 0 0 4 4z" />
                                    </svg>
                                </span>
                                <span class="text">Logros</span>
                            </a>
                        </li>
                        <li class="sidebar__item">
                            <a class="sidebar__link" href="#" data-tooltip="Calendario">
                                <span class="icon">
                                    <svg width="16" height="16" fill="currentColor" class="bi bi-calendar"
                                        viewBox="0 0 16 16">
                                        <path
                                            d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z" />
                                    </svg>
                                </span>
                                <span class="text">Calendario</span>
                            </a>
                        </li>
                    </ul>
                    <hr>
                    <ul class="sidebar__list list--secondary">
                        <li class="sidebar__item"> <a class="sidebar__link" href="#" data-tooltip="Profile"> <span
                                    class="icon"> <svg width="16" height="16" fill="currentColor"
                                        class="bi bi-person-circle" viewBox="0 0 16 16">
                                        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                                        <path fill-rule="evenodd"
                                            d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
                                    </svg> </span> <span class="text">Mi Cuenta</span> </a> </li>
                        <li class="sidebar__item"> <a class="sidebar__link" href="ajustes.php" data-tooltip="Settings">
                                <span class="icon"> <svg width="16" height="16" fill="currentColor" class="bi bi-gear"
                                        viewBox="0 0 16 16">
                                        <path
                                            d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0" />
                                        <path
                                            d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z" />
                                    </svg> </span> <span class="text">Ajustes</span> </a> </li>
                        <li class="sidebar__item"> <a class="sidebar__link" href="logout.php" data-tooltip="Logout">
                                <span class="icon"> <svg width="16" height="16" fill="currentColor"
                                        class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                            d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z" />
                                        <path fill-rule="evenodd"
                                            d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z" />
                                    </svg> </span> <span class="text">Salir</span> </a> </li>
                    </ul>
                </section>
            </nav>
        </aside>


<div class="perfil-container">
  <div class="perfil-header">
    <!-- Si aún no tienes avatar, muestra Tigri -->
    <img class="avatar" src="<?= htmlspecialchars($usuario['avatar'] )?>" alt="Avatar">    <div>
        <h2><?= htmlspecialchars($usuario['nombre']) ?></h2>
        <div class="perfil-badges">
            <span class="badge">Nivel <?= (int)$usuario['nivel'] ?></span>
            <span class="badge"><?= (int)$usuario['puntos'] ?> pts</span>
            <?php if (!empty($usuario['fecha_registro'])): ?>
                <span class="badge">Desde <?= date('d/m/Y', strtotime($usuario['fecha_registro'])) ?></span>
            <?php endif; ?>
        </div>
    </div>
</div>


<!-- Formulario para subir imagen -->
<div class="card" style="margin-top: 20px;">
    <h4>Subir imagen de perfil</h4>
    <form action="subir_avatar.php" method="POST" enctype="multipart/form-data">
        <label for="avatar">Selecciona una imagen:</label>
        <input type="file" name="avatar" id="avatar" accept="image/*" required>
        <button type="submit">Subir</button>
    </form>
</div>

  <div class="perfil-grid">
    <div class="card">
      <h4>Preferencias</h4>
      <p>Modo oscuro: <?= $modoOscuro ? 'Sí' : 'No' ?></p>
      <p>Notificaciones: <?= !empty($usuario['notificaciones']) ? 'Sí' : 'No' ?></p>
      <p>Color principal: <span class="color-chip" style="background: <?= htmlspecialchars($colorPrincipal) ?>"></span></p>
    </div>
    <div class="card">
      <h4>Metas</h4>
      <p>Activas: <?= (int)$stats['activas'] ?></p>
      <p>Completadas: <?= (int)$stats['completadas'] ?></p>
      <a style="color: white" href="mis_metas.php">Ver mis metas</a>
    </div>
    <div class="card">
      <h4>Cuenta</h4>
      <p>Email: <?//= htmlspecialchars($usuario['email']) ?> Y a ti que te importa</p>
      <a style="color: white" href="ajustes.php">Ajustes</a>
    </div>
  </div>

  <div class="card" style="margin-top:20px">
    <h4>Logros recientes</h4>
    <?php if (count($logros)): ?>
      <div class="logros">
        <?php foreach ($logros as $lg): ?>
          <div class="logro">
            <img src="<?= htmlspecialchars($lg['icono'] ?: 'public/img/logro.png') ?>" alt="" width="36" height="36">
            <small><strong><?= htmlspecialchars($lg['nombre']) ?></strong></small>
            <small><?= date('d/m', strtotime($lg['fecha_desbloqueo'])) ?></small>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p>Aún no has desbloqueado logros. ¡Completa metas para conseguirlos!</p>
    <?php endif; ?>
  </div>
</div>
</div>

</body>
</html>