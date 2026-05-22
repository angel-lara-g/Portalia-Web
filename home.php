<?php
/**
 * home.php
 * --------
 * Main feed page for authenticated Portalia users.
 *
 * Displays the 9 most recently uploaded projects as a card grid.
 * Each project card shows a preview of its file (image, video, audio,
 * PDF, or text) with an overlay title that links to the project page.
 *
 * Requires an active session (user must be logged in).
 */

session_start();
require_once 'config.php';

$us   = $_SESSION["user"];
$usID = $_SESSION["userID"];

// Fetch the 9 most recent projects using PDO
$pdo  = get_pdo_connection();
$sql  = "SELECT pNAME, URL_FILE FROM Projects ORDER BY PROJECT_ID DESC LIMIT 9";
$stmt = $pdo->query($sql);
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portalia — Inicio</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- ── Header ───────────────────────────────────────────────────────── -->
    <div class="header">
        <div class="home">
            <img src="Images/portalia.png" alt="Portalia logo">
        </div>
        <div class="options">
            <form action="options.php" method="post" class="hor">
                <input type="submit" name="search" value="🔎 Buscar"       class="sBtt blue bold">
                <input type="submit" name="favs"   value="⭐ Favoritos"    class="sBtt green bold">
                <input type="submit" name="prof"
                       value="👤 <?= htmlspecialchars($us) ?>"             class="sBtt purple bold">
            </form>
        </div>
    </div>

    <!-- ── Page Body ─────────────────────────────────────────────────────── -->
    <div class="cont">
        <div class="father">

            <!-- Section header row -->
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.2rem;">
                <span class="section-label">Para Ti</span>
                <form action="options.php" method="post">
                    <input type="submit" name="createP" value="＋ Crear Nuevo Proyecto" class="sBtt green bold">
                </form>
            </div>

            <!-- Project grid — populated by JavaScript below -->
            <form action="options.php" method="post">
                <div class="PRO" id="contenedor"></div>
            </form>

        </div>

        <!-- ── Footer ────────────────────────────────────────────────────── -->
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
    </div>

    <script>
        /**
         * Renders project cards into the #contenedor grid.
         *
         * Each card shows a media preview (image, video, audio, PDF, or text)
         * that fills the card area, with the project name overlaid at the bottom.
         * Clicking the name submits the form to options.php to open the project.
         *
         * Data is passed from PHP as a JSON-encoded array.
         */

        // Project data passed from PHP
        const archivos   = <?= json_encode($files) ?>;
        const contenedor = document.getElementById('contenedor');

        if (archivos.length === 0) {
            contenedor.innerHTML = '<p style="color:var(--gray-text); padding:1rem;">No hay proyectos disponibles aún.</p>';
        } else {
            archivos.forEach(({ pNAME, URL_FILE }) => {
                const ext = URL_FILE.split('.').pop().toLowerCase();

                // Outer card wrapper
                const card = document.createElement('div');
                card.className = 'archivo';

                // Build the appropriate media element for the file type
                let media;
                if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
                    media = document.createElement('img');
                    media.src = `Projects/${URL_FILE}`;
                    media.alt = pNAME;
                } else if (['mp4', 'webm', 'avi'].includes(ext)) {
                    media = document.createElement('video');
                    media.src = `Projects/${URL_FILE}`;
                    media.controls = true;
                } else if (['mp3', 'ogg', 'wav'].includes(ext)) {
                    media = document.createElement('audio');
                    media.src = `Projects/${URL_FILE}`;
                    media.controls = true;
                } else if (ext === 'pdf') {
                    media = document.createElement('iframe');
                    media.src = `Projects/${URL_FILE}`;
                } else if (ext === 'txt') {
                    // Load plain-text files asynchronously and display in a <pre>
                    media = document.createElement('pre');
                    media.style.cssText = 'white-space:pre-wrap; overflow-wrap:break-word; max-width:100%; padding:0.5rem;';
                    fetch(`Projects/${URL_FILE}`)
                        .then(r => r.text())
                        .then(text => { media.textContent = text; })
                        .catch(() => { media.textContent = 'No se pudo cargar el archivo.'; });
                } else {
                    media = document.createElement('p');
                    media.textContent = `Archivo no soportado: ${URL_FILE}`;
                }

                // Submit button acting as the project title overlay
                const titleBtn = document.createElement('input');
                titleBtn.type      = 'submit';
                titleBtn.className = 'descripcion';
                titleBtn.name      = 'project';
                titleBtn.value     = pNAME || 'Sin título';

                card.appendChild(media);
                card.appendChild(titleBtn);
                contenedor.appendChild(card);
            });
        }
    </script>

</body>
</html>
