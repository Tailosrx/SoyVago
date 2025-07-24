<?php
session_start();
require_once 'config/db.php';
require_once 'helpers.php';

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
$stmt = $connection->prepare("SELECT COUNT(*) as completadas FROM metas WHERE usuario_id = :usuario_id AND completado = TRUE");
$stmt->execute(['usuario_id' => $_SESSION['usuario_id']]);
$progreso = $stmt->fetch(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - SoyVago</title>
    <link rel="stylesheet" href="public\css\styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<h1>Bienvenido, <?= htmlspecialchars($_SESSION['nombre']) ?></h1>

<div class="app-container">
<!--Eliminaremos el aside menu para poner el menu arriba del todo -->
<aside class="vertical-sidebar"> 
    <input type="checkbox" role="switch" id="checkbox-input" class="checkbox-input" checked />
    <nav>
        <header>
            <div class="sidebar__toggle-container"> <label tabindex="0" for="checkbox-input" id="label-for-checkbox-input" class="nav__toggle"> <span class="toggle--icons" aria-hidden="true"> <svg width="24" height="24" viewBox="0 0 24 24" class="toggle-svg-icon toggle--open">
                            <path d="M3 5a1 1 0 1 0 0 2h18a1 1 0 1 0 0-2zM2 12a1 1 0 0 1 1-1h18a1 1 0 1 1 0 2H3a1 1 0 0 1-1-1M2 18a1 1 0 0 1 1-1h18a1 1 0 1 1 0 2H3a1 1 0 0 1-1-1"> </path>
                        </svg> <svg width="24" height="24" viewBox="0 0 24 24" class="toggle-svg-icon toggle--close">
                            <path d="M18.707 6.707a1 1 0 0 0-1.414-1.414L12 10.586 6.707 5.293a1 1 0 0 0-1.414 1.414L10.586 12l-5.293 5.293a1 1 0 1 0 1.414 1.414L12 13.414l5.293 5.293a1 1 0 0 0 1.414-1.414L13.414 12z"> </path>
                        </svg> </span> </label> </div>
            <figure> <img class="codepen-logo" src="https://blog.codepen.io/wp-content/uploads/2023/09/logo-black.png" alt="" />
                <figcaption>
                    <p class="user-id">SoyVago</p>
                    <p class="user-role">Kevin</p>
                </figcaption>
            </figure>
        </header>
        <section class="sidebar__wrapper">
            <ul class="sidebar__list list--primary">
                <li class="sidebar__item item--heading">
                    <h2 class="sidebar__item--heading">general</h2>
                </li>
                <li class="sidebar__item"> <a class="sidebar__link" href="#" data-tooltip="Inbox"> <span class="icon"> <svg width="16" height="16" fill="currentColor" class="bi bi-inbox" viewBox="0 0 16 16">
                                <path d="M4.98 4a.5.5 0 0 0-.39.188L1.54 8H6a.5.5 0 0 1 .5.5 1.5 1.5 0 1 0 3 0A.5.5 0 0 1 10 8h4.46l-3.05-3.812A.5.5 0 0 0 11.02 4zm9.954 5H10.45a2.5 2.5 0 0 1-4.9 0H1.066l.32 2.562a.5.5 0 0 0 .497.438h12.234a.5.5 0 0 0 .496-.438zM3.809 3.563A1.5 1.5 0 0 1 4.981 3h6.038a1.5 1.5 0 0 1 1.172.563l3.7 4.625a.5.5 0 0 1 .105.374l-.39 3.124A1.5 1.5 0 0 1 14.117 13H1.883a1.5 1.5 0 0 1-1.489-1.314l-.39-3.124a.5.5 0 0 1 .106-.374z" />
                            </svg> </span> <span class="text">Dashboard</span> </a> </li>
                <li class="sidebar__item"> <a class="sidebar__link" href="#" data-tooltip="Favourite"> <span class="icon"> <svg width="16" height="16" fill="currentColor" class="bi bi-star" viewBox="0 0 16 16">
                                <path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894a.5.5 0 0 0-.461 0z" />
                            </svg> </span> <span class="text">Mis Metas</span> </a> </li>
                <li class="sidebar__item"> <a class="sidebar__link" href="#" data-tooltip="Sent"> <span class="icon"> <svg width="16" height="16" fill="currentColor" class="bi bi-send" viewBox="0 0 16 16">
                                <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576zm6.787-8.201L1.591 6.602l4.339 2.76z" />
                            </svg> </span> <span class="text">Tareas/Habitos</span> </a> </li>
                            <li class="sidebar__item"> <a class="sidebar__link" href="#" data-tooltip="Sent"> <span class="icon"> <svg width="16" height="16" fill="currentColor" class="bi bi-send" viewBox="0 0 16 16">
                                <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576zm6.787-8.201L1.591 6.602l4.339 2.76z" />
                            </svg> </span> <span class="text">Calendario</span> </a> </li>
            </ul>
            <ul class="sidebar__list list--secondary">
                <li class="sidebar__item item--heading">
                    <h2 class="sidebar__item--heading">general</h2>
                </li>
                <li class="sidebar__item"> <a class="sidebar__link" href="#" data-tooltip="Profile"> <span class="icon"> <svg width="16" height="16" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                                <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                                <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
                            </svg> </span> <span class="text">Mi Cuenta</span> </a> </li>
                <li class="sidebar__item"> <a class="sidebar__link" href="#" data-tooltip="Settings"> <span class="icon"> <svg width="16" height="16" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16">
                                <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0" />
                                <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z" />
                            </svg> </span> <span class="text">Ajustes</span> </a> </li>
                <li class="sidebar__item"> <a class="sidebar__link" href="#" data-tooltip="Logout"> <span class="icon"> <svg width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z" />
                                <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z" />
                            </svg> </span> <span class="text">Salir</span> </a> </li>
            </ul>
        </section>
    </nav>
</aside>

<main class="main-content">

<div class="semana-calendario">
    <?php 
    $dias = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
    $hoy = date('w');
    for ($i = 0; $i < 7; $i++):
        $dia = date('d', strtotime("+$i day"));
        $clase = ($i == 0) ? 'hoy' : '';
    ?>
        <div class="dia <?= $clase ?>">
            <span><?= $dias[(date('w') + $i) % 7] ?></span>
            <strong><?= $dia ?></strong>
        </div>
    <?php endfor; ?>
</div>

<!-- Reemplaza la sección streaks con este código -->
<section class="streaks">
    <h2>Rachas</h2>
    <div class="streak-grid">
        <?php foreach ($metas as $meta): ?>
            <div class="streak-card <?= $meta['completado'] ? 'completada' : '' ?>" style="--card-color: <?= $meta['color'] ?>; --card-accent: <?= ajustarBrillo($meta['color'], -20) ?>">
                <h3><?= htmlspecialchars($meta['titulo']) ?></h3>
                <div class="streak-progress">
                    <div class="progress-bar" style="width: <?= min(100, ($meta['racha'] ?? 0) * 10) ?>%"></div>
                </div>
                <p>Racha: <?= $meta['racha'] ?? 0 ?> días</p>
                <?php if ($meta['fecha_limite']): ?>
                    <p class="fecha-limite">📅 <?= date('d/m/Y', strtotime($meta['fecha_limite'])) ?></p>
                <?php endif; ?>
                <form method="POST" action="procesar_meta.php" onsubmit="return confirm('¿Eliminar esta meta?')">
                <input type="hidden" name="meta_id" value="<?= $meta['id'] ?>">
                <input type="hidden" name="accion" value="eliminar">
                <button type="submit" class="delete-btn">🗑️</button>
            </form>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="hoy">
    <h2>Tareas de Hoy</h2>
    <ul class="metas-list">
        <?php foreach ($metas as $meta): ?>
            <li class="<?= $meta['completado'] ? 'completada' : '' ?>">
                <label>
                    <form action="procesar_meta.php" method="POST" class="meta-actions">
                        <input type="hidden" name="meta_id" value="<?= $meta['id'] ?>">
                        <input type="checkbox" <?= $meta['completado'] ? 'checked disabled' : '' ?> 
                            onchange="this.form.submit()"
                            name="accion" value="completar">
                        <?= htmlspecialchars($meta['titulo']) ?>
                    </form>
                </label>
            </li>
        <?php endforeach; ?>
    </ul>
</section>

<a href="nueva_meta.php" class="add-button">
    <span>＋</span></a>
</main>

</div>
















</body>
</html>