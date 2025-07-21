<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - SoyVago</title>
    <link rel="stylesheet" href="public\css\styles.css">
</head>
<body>
    <div class="login-container">
        <h1>SoyVago.com</h1>
        <h2>Crear cuenta</h2>
        <form action="procesar_user.php" method="POST">
            <input type="hidden" name="accion" value="registro">


            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="email">Correo electrónico:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <label for="confirmar">Confirmar contraseña:</label>
            <input type="password" id="confirmar" name="confirmar" required>

            <button type="submit">Registrarme</button>
        </form>
        <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
    </div>
</body>
</html>
