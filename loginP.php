<?php
/**
 * loginP.php
 * ----------
 * Login and registration page for the Portalia web application.
 *
 * Displays a centered card form on a gradient background.
 * Shows either the login or registration form depending on
 * which button was pressed on the index page ($_GET["ini"] or $_GET["reg"]).
 *
 * Form submits to exists.php for credential validation.
 */

// Determine which form to show based on the GET parameter
$isLogin = isset($_GET["ini"]);
$formTitle = $isLogin ? "Iniciar sesión" : "Registrarse";
$submitName  = $isLogin ? "exist" : "new";
$submitLabel = $isLogin ? "Iniciar sesión" : "Registrarse";
$submitClass = $isLogin ? "sBtt blue bold" : "sBtt green bold";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portalia — <?= htmlspecialchars($formTitle) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- ── Header ───────────────────────────────────────────────────────── -->
    <div class="header">
        <div class="home">
            <a href="index.php">
                <img src="Images/portalia.png" alt="Portalia logo">
            </a>
        </div>
    </div>

    <!-- ── Auth Card ─────────────────────────────────────────────────────── -->
    <div class="start">
        <form action="exists.php" method="post" class="sData">

            <h1><?= htmlspecialchars($formTitle) ?></h1>

            <label for="user">Usuario</label>
            <input type="text" id="user" name="user" placeholder="Ingresa tu usuario" required>

            <label for="pass" style="margin-top:0.8rem;">Contraseña</label>
            <input type="password" id="pass" name="pass" placeholder="Ingresa tu contraseña" required>

            <div style="margin-top:1.4rem; text-align:center;">
                <input type="submit" name="<?= $submitName ?>" value="<?= $submitLabel ?>"
                       class="<?= $submitClass ?>" style="width:100%; padding:0.7rem 1rem; font-size:1rem;">
            </div>

            <!-- Link to switch between login and register -->
            <p style="text-align:center; margin-top:1rem; font-size:0.85rem; color:var(--gray-text);">
                <?php if ($isLogin): ?>
                    ¿No tienes cuenta?
                    <a href="loginP.php?reg=1">Regístrate aquí</a>
                <?php else: ?>
                    ¿Ya tienes cuenta?
                    <a href="loginP.php?ini=1">Inicia sesión</a>
                <?php endif; ?>
            </p>

        </form>
    </div>

    <!-- ── Footer ────────────────────────────────────────────────────────── -->
    <footer class="footer">
        <div class="footer-brand">
            <img src="Images/portalia.png" alt="Portalia" style="height:32px; filter:brightness(10);">
            <p>La comunidad donde personas creativas comparten proyectos, construyen portafolios y crecen juntas.</p>
        </div>
        <div class="footer-col">
            <h4>Plataforma</h4>
            <a href="#">Acerca de nosotros</a>
            <a href="#">Contacto</a>
        </div>
        <div class="footer-col">
            <h4>Legal</h4>
            <a href="#">Términos y condiciones</a>
            <a href="#">Política de privacidad</a>
            <a href="#">Manuales de uso</a>
        </div>
        <div class="footer-col">
            <h4>Síguenos</h4>
            <a href="#">Instagram</a>
            <a href="#">LinkedIn</a>
            <a href="#">X</a>
        </div>
        <div class="footer-bottom">© 2024 Portalia. Todos los derechos reservados.</div>
    </footer>

</body>
</html>
