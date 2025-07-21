<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - SoyVago</title>
    <link rel="stylesheet" href="public\css\styles.css">
</head>
<body>
    <div class="login-container">
        <h1>SoyVago.com</h1>
        <h2>Iniciar sesión</h2>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="error-message">
                <?php 
                switch($_GET['error']) {
                    case 'campos_vacios':
                        echo 'Por favor, completa todos los campos.';
                        break;
                    case 'credenciales':
                        echo 'Correo o contraseña incorrectos.';
                        break;
                    default:
                        echo 'Ocurrió un error al iniciar sesión.';
                }
                ?>
            </div>
        <?php endif; ?>

        <form action="procesar_user.php" method="POST">
            <input type="hidden" name="accion" value="login">

            <label for="email">Correo electrónico:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Entrar</button>
        </form>
        <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
    </div>
</body>
</html>