<?php
/**
 * project.php
 * -----------
 * Single project view page for the Portalia web application.
 *
 * Displays the full details of a selected project:
 *   - Project name, author, date, category, description, and comments
 *   - A media preview rendered dynamically via JavaScript
 *
 * Action buttons shown depend on the viewer's relationship to the project:
 *   - Other users: "Add to Favorites" button
 *   - Project owner or admin: "Delete Project" button
 *
 * Project data is read from session variables set by options.php.
 * Requires an active session (user must be logged in).
 */

session_start();
require_once 'config.php';

$us                 = $_SESSION["user"];
$usID               = $_SESSION["userID"];
$projectUser        = $_SESSION["projectUser"];
$projectName        = $_SESSION["projectName"];
$projectDate        = $_SESSION["projectDate"];
$projectCategory    = $_SESSION["projectCategory"];
$projectDescription = $_SESSION["projectDescription"];
$projectComments    = $_SESSION["projectComments"];
$projectURL         = $_SESSION["projectURL"];
$uid                = $_SESSION["UID"];

// Extract the file extension to pass to JavaScript for media rendering
$fileExt = pathinfo($projectURL, PATHINFO_EXTENSION);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portalia — <?= htmlspecialchars($projectName) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- ── Header ───────────────────────────────────────────────────────── -->
    <div class="header">
        <div class="home">
            <a href="home.php">
                <img src="Images/portalia.png" alt="Portalia logo">
            </a>
        </div>
        <div class="options">
            <form action="options.php" method="post" class="hor">
                <input type="submit" name="search" value="🔎 Buscar"    class="sBtt blue bold">
                <input type="submit" name="favs"   value="⭐ Favoritos" class="sBtt green bold">
                <input type="submit" name="prof"
                       value="👤 <?= htmlspecialchars($us) ?>"          class="sBtt purple bold">
            </form>
        </div>
    </div>

    <!-- ── Page Body ─────────────────────────────────────────────────────── -->
    <div class="father">

        <!-- Top action row: project title + action button -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.2rem;">
            <span class="section-label"><?= htmlspecialchars($projectName) ?></span>

            <form action="options.php" method="post" class="hor"
                  onsubmit="return confirmAction(event)">
                <?php if ($uid != $usID): ?>
                    <!-- Viewer is not the owner: show Add to Favorites -->
                    <input type="submit" name="favsAdd"
                           value="⭐ Agregar a Favoritos"
                           onclick="setConfirmMsg('¿Deseas agregar el proyecto a Favoritos?')"
                           class="sBtt green bold">
                    <?php if ($usID == 1): ?>
                        <!-- Admin can also delete any project -->
                        <input type="submit" name="deleteP"
                               value="🗑 Eliminar proyecto"
                               onclick="setConfirmMsg('¿Deseas eliminar el proyecto?')"
                               class="sBtt red bold">
                    <?php endif; ?>
                <?php else: ?>
                    <!-- Viewer is the owner: show Delete -->
                    <input type="submit" name="deleteP"
                           value="🗑 Eliminar proyecto"
                           onclick="setConfirmMsg('¿Deseas eliminar el proyecto?')"
                           class="sBtt red bold">
                <?php endif; ?>
            </form>
        </div>

        <!-- ── Project Details Card ──────────────────────────────────────── -->
        <div class="b">
            <!-- Author button — clicking it navigates to their profile -->
            <form action="options.php" method="post" style="display:inline;">
                <input type="submit" name="profile"
                       value="👤 <?= htmlspecialchars($projectUser) ?>"
                       class="sBtt purple bold" style="margin-bottom:1rem;">
            </form>

            <p><strong>Nombre del proyecto:</strong> <?= htmlspecialchars($projectName) ?></p>
            <p><strong>Fecha de elaboración:</strong> <?= htmlspecialchars($projectDate) ?></p>
            <p><strong>Categoría:</strong> <?= htmlspecialchars($projectCategory) ?></p>
            <p><strong>Descripción:</strong> <?= htmlspecialchars($projectDescription) ?></p>
            <?php if (!empty($projectComments)): ?>
            <p><strong>Comentarios:</strong> <?= htmlspecialchars($projectComments) ?></p>
            <?php endif; ?>
        </div>

        <!-- ── Media Preview ─────────────────────────────────────────────── -->
        <div class="project" id="contenedor"></div>

    </div><!-- /.father -->

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

    <script>
        /**
         * Renders the project file into the #contenedor div.
         * Supports images, videos, audio, PDFs, and plain-text files.
         * Unsupported file types show a fallback message.
         */

        const filePath = "<?= 'Projects/' . $projectURL ?>";
        const fileExt  = "<?= strtolower($fileExt) ?>";
        const container = document.getElementById('contenedor');

        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExt)) {
            container.innerHTML = `<img src="${filePath}" alt="Imagen del proyecto" style="max-width:100%; border-radius:10px;">`;

        } else if (['mp4', 'webm', 'ogg'].includes(fileExt)) {
            container.innerHTML = `<video controls style="max-width:100%;">
                                       <source src="${filePath}" type="video/${fileExt}">
                                       Tu navegador no soporta la reproducción de videos.
                                   </video>`;

        } else if (['mp3', 'wav'].includes(fileExt)) {
            container.innerHTML = `<audio controls>
                                       <source src="${filePath}" type="audio/${fileExt}">
                                       Tu navegador no soporta la reproducción de audio.
                                   </audio>`;

        } else if (fileExt === 'pdf') {
            container.innerHTML = `<iframe src="${filePath}" width="100%" height="600px" style="border:none; border-radius:10px;"></iframe>`;

        } else if (fileExt === 'txt') {
            // Load plain-text files asynchronously
            fetch(filePath)
                .then(response => {
                    if (!response.ok) throw new Error('Error al cargar el archivo');
                    return response.text();
                })
                .then(text => {
                    container.innerHTML = `<pre style="white-space:pre-wrap; overflow-wrap:break-word; max-width:100%;">${text}</pre>`;
                })
                .catch(() => {
                    container.innerHTML = `<p style="color:red;">No se pudo cargar el archivo de texto.</p>`;
                });

        } else {
            container.innerHTML = `<p>El archivo no es compatible para mostrar.</p>`;
        }

        // ── Confirmation dialog for action buttons ────────────────────────

        let confirmMsg = '';

        /**
         * Sets the confirmation message shown when an action button is clicked.
         * Called via onclick on each submit button before form submission.
         * @param {string} message - The message to display in the confirm dialog.
         */
        function setConfirmMsg(message) {
            confirmMsg = message;
        }

        /**
         * Shows a confirm dialog using the message set by setConfirmMsg().
         * Prevents form submission if the user cancels.
         * @param {Event} event - The form submit event.
         * @returns {boolean} True to allow submission, false to cancel.
         */
        function confirmAction(event) {
            const confirmed = confirm(confirmMsg);
            if (!confirmed) event.preventDefault();
            return confirmed;
        }
    </script>

</body>
</html>
