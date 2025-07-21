<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['onboarding_complete']) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Bienvenido a SoyVago</title>
    <link rel="stylesheet" href="public\css\styles.css">
</head>
<body>
    <div class="onboarding-container">
        <h1>👋 ¡Bienvenido, <?= htmlspecialchars($_SESSION['nombre']) ?>!</h1>
        
        <form action="procesar_onboarding.php" method="POST">
            <h2>Crea tu primera meta</h2>
            <input type="text" name="objetivo" placeholder="Ej: Hacer ejercicio 3 veces por semana" required>
            
            <h2>¿Qué tipo de recordatorios prefieres?</h2>
            <select name="recordatorio" required>
                <option value="email">Email</option>
                <option value="notificacion">Notificaciones en la app</option>
                <option value="ambos">Ambos</option>
            </select>
            
            <button type="submit">Comenzar</button>
        </form>
    </div>
</body>
</html>