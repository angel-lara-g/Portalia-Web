<?php
/**
 * index.php
 * ---------
 * Entry point and welcome page for the Portalia web application.
 *
 * Initializes the database (via createDB.php) on first load,
 * then displays the landing page with a hero section,
 * feature highlights, and navigation to login or register.
 */
include 'createDB.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portalia — Bienvenido</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- ── Header ───────────────────────────────────────────────────────── -->
    <div class="header">
        <div class="home">
            <img src="Images/portalia.png" alt="Portalia logo">
        </div>
        <div class="options">
            <form action="loginP.php" method="get" class="hor">
                <input type="submit" name="ini" value="Iniciar Sesión" class="sBtt blue bold">
                <input type="submit" name="reg" value="Registrarse"    class="sBtt green bold">
            </form>
        </div>
    </div>

    <!-- ── Hero Section ─────────────────────────────────────────────────── -->
    <div class="welcome-hero">
        <h2>Tu espacio para <span style="color:var(--blue)">crear</span>,
            <span style="color:var(--green)">compartir</span> y
            <span style="color:var(--red)">crecer</span>
        </h2>
        <p>
            Portalia es la plataforma donde estudiantes, profesionistas y autodidactas
            comparten proyectos, construyen portafolios digitales y se inspiran mutuamente.
        </p>

        <!-- Decorative welcome images -->
        <div class="welcome-images">
            <img src="Images/w1.jpg" alt="Proyecto destacado 1">
            <img src="Images/w2.jpg" alt="Proyecto destacado 2">
            <img src="Images/w3.jpg" alt="Proyecto destacado 3">
        </div>
    </div>

    <!-- ── Feature Highlights ────────────────────────────────────────────── -->
    <div style="max-width:1100px; margin:0 auto; padding:0 2rem 3rem;">
        <div style="text-align:center; margin-bottom:2rem;">
            <span class="section-label">Beneficios</span>
            <h2 style="font-size:1.8rem; margin-top:0.5rem;">Todo lo que necesitas para destacar tu creatividad</h2>
            <p style="color:var(--gray-text); margin-top:0.4rem;">
                Herramientas diseñadas para que puedas enfocarte en crear, no en gestionar.
            </p>
        </div>

        <!-- Feature cards grid -->
        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px,1fr)); gap:1.2rem;">
            <?php
            // Feature list — label and description pairs
            $features = [
                ['🗂️ Portafolio digital',        'Presenta tu trabajo con un perfil editable y profesional.'],
                ['🔍 Descubre proyectos',          'Explora un feed de ideas y encuentra inspiración.'],
                ['⭐ Favoritos',                   'Guarda los proyectos que más te inspiran.'],
                ['🤝 Comunidad respetuosa',        'Un entorno libre de publicidad y juicio.'],
                ['📤 Publicación sencilla',        'Sube imágenes, videos o documentos en segundos.'],
                ['🔒 Seguridad y privacidad',      'Tus datos y contenido están protegidos.'],
            ];
            foreach ($features as [$title, $desc]):
            ?>
            <div class="b" style="margin-bottom:0;">
                <h3 style="margin-bottom:0.4rem;"><?= $title ?></h3>
                <p style="color:var(--gray-text); font-size:0.88rem;"><?= $desc ?></p>
            </div>
            <?php endforeach; ?>
        </div>
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
