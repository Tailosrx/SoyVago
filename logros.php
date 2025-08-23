<?php
session_start();
require_once 'config/db.php';
require_once 'controllers/get_ajustes.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$userId = (int) $_SESSION['usuario_id'];



try {
    $stmt = $connection->prepare("
        SELECT 
            l.id,
            l.titulo as nombre,
            l.descripcion,
            l.icono,
            l.xp as puntos_recompensa,
            l.rareza,
            c.nombre as categoria,
            c.icono as categoria_icono,
            CASE WHEN ul.usuario_id IS NOT NULL THEN TRUE ELSE FALSE END as desbloqueado,
            ul.fecha_desbloqueo
        FROM logros l
        LEFT JOIN categorias_logros c ON l.categoria_id = c.id
        LEFT JOIN usuario_logros ul ON l.id = ul.logro_id AND ul.usuario_id = :usuario_id
        ORDER BY COALESCE(c.nombre, 'Sin categoría'), l.titulo
    ");
    $stmt->execute(['usuario_id' => $userId]);
    $logros = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Agrupar por categoría (incluyendo logros sin categoría)
    $logrosPorCategoria = [];
    foreach ($logros as $logro) {
        $categoria = $logro['categoria'] ?? 'Sin categoría';
        $logrosPorCategoria[$categoria][] = $logro;
    }
} catch (PDOException $e) {
    die("Error al cargar los logros: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Logros - SoyVago</title>
    <link rel="stylesheet" href="public/css/styles.css">
    <style>
        :root{
            --logro-desbloqueado: #4CAF50;
            --logro-pendiente: #6d6d6d;
            --logro-rarity-5: #ffcc00;
            --logro-rarity-4: #b36bff;
            --logro-rarity-3: #4d94ff;
            --logro-rorder: 1px solid #3a3a3a;
        }

        h2, #des{
            text-align: center;
        }
        .logros-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }
        
        .categoria-logros {
            margin-bottom: 40px;
            border-bottom: 1px solid #3a3a3a;
            padding-bottom: 20px;
        }
        
        .categoria-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .categoria-icon {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }
        
        .categoria-title {
            font-size: 1.5rem;
            color: #f3ecec;
            margin: 0;
        }
        
        .logros-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
        }
        
        .logro-card {
            background: #2a2a2a;
            border-radius: 8px;
            padding: 20px;
            position: relative;
            overflow: hidden;
            border-left: 4px solid var(--logro-pendiente);
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        
        .logro-card.desbloqueado {
            border-left-color: var(--logro-desbloqueado);
            background: linear-gradient(90deg, rgba(76, 175, 80, 0.1) 0%, #2a2a2a 100%);
        }
        
        .logro-card.rarity-5 {
            border-left-color: var(--logro-rarity-5);
        }
        
        .logro-card.rarity-4 {
            border-left-color: var(--logro-rarity-4);
        }
        
        .logro-card.rarity-3 {
            border-left-color: var(--logro-rarity-3);
        }
        
        .logro-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .logro-name {
            font-size: 1.1rem;
            color: #f3ecec;
            margin: 0;
            font-weight: 500;
        }
        
        .logro-reward {
            background: rgba(219, 158, 27, 0.2);
            color: #db9e1b;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .logro-desc {
            color: #a3a3a3;
            font-size: 0.9rem;
            margin-bottom: 15px;
            line-height: 1.5;
        }
        
        .logro-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
        }
        
        .logro-date {
            color: #6d6d6d;
        }
        
        .logro-status {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .status-desbloqueado {
            background: var(--logro-desbloqueado);
        }
        
        .status-pendiente {
            background: var(--logro-pendiente);
        }
        
        .progress-container {
            margin-top: 15px;
            background: #1e1e1e;
            height: 4px;
            border-radius: 2px;
            overflow: hidden;
        }
        
        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #db9e1b, #c47d7f);
            border-radius: 2px;
        }
        
        .logro-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        
        @media (max-width: 768px) {
            .logros-grid {
                grid-template-columns: 1fr;
            }
            
            .logro-card {
                padding: 15px;
            }
        }
        
        .logro-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #4CAF50, #2E7D32);
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.5s forwards;
        }
        
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
</head>
<body class="<?= $ajustes['modo_oscuro'] ? 'modo-oscuro' : 'modo-diurno' ?>">
    
<div class="main-content">
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
                        <li class="sidebar__item"> <a class="sidebar__link" href="account.php" data-tooltip="Profile"> <span
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
        <div class="logros-container">
            <h2 class="center-title">Mis Logros</h2>
            
            <?php foreach ($logrosPorCategoria as $categoria => $logrosCategoria): ?>
                <div class="categoria-logros">
                    <div class="categoria-header">
                        <?php if (!empty($logrosCategoria[0]['categoria_icono'])): ?>
                            <img src="<?= htmlspecialchars($logrosCategoria[0]['categoria_icono']) ?>" alt="<?= htmlspecialchars($categoria) ?>" class="categoria-icon">
                        <?php endif; ?>
                        <h3 class="categoria-title"><?= htmlspecialchars($categoria) ?></h3>
                    </div>
                    
                    <div class="logros-grid">
                        <?php foreach ($logrosCategoria as $logro): ?>
                            <div class="logro-card <?= $logro['desbloqueado'] ? 'desbloqueado' : 'pendiente' ?>" 
                                 data-rareza="<?= $logro['rareza'] ?? 3 ?>">
                                <div class="logro-header">
                                    <h4 class="logro-name"><?= htmlspecialchars($logro['nombre']) ?></h4>
                                    <span class="logro-xp">+<?= $logro['puntos_recompensa'] ?> XP</span>
                                </div>
                                
                                <div class="logro-content">
                                    <?php if (!empty($logro['icono'])): ?>
                                        <img src="<?= htmlspecialchars($logro['icono']) ?>" alt="<?= htmlspecialchars($logro['nombre']) ?>" class="logro-icon">
                                    <?php endif; ?>
                                    <p class="logro-desc"><?= htmlspecialchars($logro['descripcion']) ?></p>
                                </div>
                                
                                <div class="logro-footer">
                                    <?php if ($logro['desbloqueado']): ?>
                                        <span class="logro-date"><?= date('d/m/Y', strtotime($logro['fecha_desbloqueo'])) ?></span>
                                        <span class="logro-status status-desbloqueado">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                                <path d="M20 6L9 17l-5-5"></path>
                                            </svg>
                                            Desbloqueado
                                        </span>
                                    <?php else: ?>
                                        <span class="logro-date">Por desbloquear</span>
                                        <span class="logro-status status-pendiente">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                                <circle cx="12" cy="12" r="10"></circle>
                                            </svg>
                                            Pendiente
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>