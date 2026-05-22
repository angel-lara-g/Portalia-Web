<?php
/**
 * newProject.php
 * --------------
 * New project creation form for the Portalia web application.
 *
 * Allows authenticated users to upload a new project by providing:
 *   - Project name, creation date, category, description, and optional comments
 *   - A file upload (image, video, audio, PDF, or text — max 30 MB)
 *
 * A live file preview is shown before submission using the FileReader API.
 * The form submits to saveProject.php for database storage and file upload.
 *
 * Requires an active session (user must be logged in).
 */

session_start();
require_once 'config.php';

$us   = $_SESSION["user"];
$usID = $_SESSION["userID"];

// Load project categories from the database
$conn   = get_mysqli_connection();
$result = mysqli_query($conn, "SELECT CATEGORY_ID, CATEGORY FROM CATEGORIES");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portalia — Nuevo Proyecto</title>
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

    <!-- ── New Project Form Card ─────────────────────────────────────────── -->
    <div style="display:flex; justify-content:center; padding: calc(var(--header-h) + 2rem) 1.5rem 3rem;">
        <form action="saveProject.php" method="post" enctype="multipart/form-data"
              class="sData" style="max-width:560px; width:100%;"
              onsubmit="return confirm('¿Deseas crear un nuevo proyecto?')">

            <h1>Crear Nuevo Proyecto</h1>

            <!-- Project name -->
            <label style="margin-top:0.8rem;">Nombre del proyecto *</label>
            <input type="text" name="pName" maxlength="50"
                   placeholder="Título de tu proyecto" required>

            <!-- Creation date -->
            <label style="margin-top:0.8rem;">Fecha de elaboración *</label>
            <input type="date" name="pDate" required>

            <!-- Category selector — loaded from DB -->
            <label style="margin-top:0.8rem;">Categoría *</label>
            <select name="pCate" required>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <option value="<?= $row['CATEGORY_ID'] ?>">
                    <?= htmlspecialchars($row['CATEGORY']) ?>
                </option>
                <?php endwhile; ?>
            </select>

            <!-- Description -->
            <label style="margin-top:0.8rem;">Descripción *</label>
            <input type="text" name="pDes" maxlength="100"
                   placeholder="Breve descripción del proyecto" required>

            <!-- Optional comments -->
            <label style="margin-top:0.8rem;">Comentarios</label>
            <input type="text" name="pCom" maxlength="600"
                   placeholder="Comentarios adicionales (opcional)">

            <!-- File upload input -->
            <label style="margin-top:1rem;">Sube tu archivo *</label>
            <input type="file" id="file-input" name="uploadedFile"
                   accept=".txt,.pdf,image/*,audio/*,video/*" required
                   style="margin-bottom:0.8rem;">

            <!-- Live file preview area -->
            <div id="preview"></div>

            <!-- Hidden input to carry the filename to saveProject.php -->
            <input type="hidden" id="file-name" name="fileName">

            <!-- Action buttons -->
            <div class="hor" style="gap:0.6rem; margin-top:1.5rem;">
                <input type="submit" name="continue" value="💾 Guardar proyecto"
                       class="sBtt green bold" style="flex:1; padding:0.75rem; font-size:1rem;">
                <a href="profile.php" class="sBtt ghost bold"
                   style="flex:1; padding:0.75rem; font-size:1rem; text-align:center; text-decoration:none;">
                    ✕ Cancelar
                </a>
            </div>

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

    <script>
        /**
         * Handles live file preview before the project form is submitted.
         *
         * When the user selects a file:
         *   1. Validates the file size (max 30 MB).
         *   2. Stores the filename in the hidden #file-name input.
         *   3. Renders a preview element appropriate for the file type
         *      (img, video, audio, iframe for PDF, or pre for text).
         */

        const fileInput   = document.getElementById('file-input');
        const previewDiv  = document.getElementById('preview');
        const fileNameInput = document.getElementById('file-name');

        // Maximum allowed file size: 30 MB
        const MAX_FILE_SIZE = 30 * 1024 * 1024;

        fileInput.addEventListener('change', (event) => {
            const file = event.target.files[0];
            previewDiv.innerHTML = ''; // Clear any previous preview

            if (!file) return;

            // Reject files exceeding the size limit
            if (file.size > MAX_FILE_SIZE) {
                alert('El archivo es demasiado grande. El tamaño máximo permitido es de 30 MB.');
                fileInput.value = '';
                return;
            }

            // Store the filename for the server
            fileNameInput.value = file.name;

            const reader = new FileReader();
            const type   = file.type;

            reader.onload = () => {
                let element;

                if (type.startsWith('image/')) {
                    element = document.createElement('img');
                    element.src = reader.result;
                    element.style.cssText = 'max-width:100%; height:auto; border-radius:8px;';

                } else if (type.startsWith('video/')) {
                    element = document.createElement('video');
                    element.src = reader.result;
                    element.controls = true;
                    element.style.maxWidth = '100%';

                } else if (type.startsWith('audio/')) {
                    element = document.createElement('audio');
                    element.src = reader.result;
                    element.controls = true;

                } else if (type === 'application/pdf') {
                    element = document.createElement('iframe');
                    element.src = reader.result;
                    element.style.cssText = 'width:100%; height:300px; border:none; border-radius:8px;';

                } else if (type.startsWith('text/')) {
                    element = document.createElement('pre');
                    element.textContent = reader.result;
                    element.style.whiteSpace = 'pre-wrap';

                } else {
                    element = document.createElement('p');
                    element.style.color = 'var(--gray-text)';
                    element.textContent = 'Tipo de archivo no compatible para previsualización.';
                }

                previewDiv.appendChild(element);
            };

            // Use readAsText for plain-text files, readAsDataURL for everything else
            if (type.startsWith('text/')) {
                reader.readAsText(file);
            } else {
                reader.readAsDataURL(file);
            }
        });
    </script>

    <?php mysqli_close($conn); ?>

</body>
</html>
