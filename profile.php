<?php
/**
 * profile.php
 * -----------
 * Personal profile page for the authenticated Portalia user.
 *
 * Displays the user's profile data (name, age, social links, etc.)
 * in an editable form, and lists all of their uploaded projects as a card grid.
 *
 * Actions available from this page:
 *   - Edit profile data (submitted to modifyData.php)
 *   - Create a new project (via options.php → newProject.php)
 *   - Open an existing project (via options.php → project.php)
 *   - Log out (via destroy.php)
 *
 * Requires an active session (user must be logged in).
 */

session_start();
require_once 'config.php';

$us   = $_SESSION["user"];
$usID = $_SESSION["userID"];

// ── Fetch profile data ────────────────────────────────────────────────────
$conn  = get_mysqli_connection();
$sql0  = "SELECT FIRST_NAME, MIDDLE_NAME, fLAST_NAME, mLAST_NAME, AGE, EMAIL,
                 FACEBOOK, INSTAGRAM, X, WHATSAPP
          FROM PROFILEDATA WHERE USER_ID = '$usID'";
$result0 = mysqli_query($conn, $sql0);
$row     = mysqli_fetch_assoc($result0);

// ── Fetch continent options for the select dropdown ───────────────────────
$sqlDefault  = "SELECT CONTINENT FROM PROFILEDATA WHERE USER_ID = '$usID'";
$resDefault  = mysqli_query($conn, $sqlDefault);
$rowDefault  = mysqli_fetch_assoc($resDefault);
$defaultContinent = $rowDefault['CONTINENT'] ?? null;

$sqlContinents = "SELECT CONTINENT_ID, CONTINENT FROM CONTINENTS";
$resContinents = mysqli_query($conn, $sqlContinents);

// ── Fetch the user's PROFILE_ID for project lookup ────────────────────────
$sql01  = "SELECT PROFILE_ID FROM PROFILEDATA
           JOIN USERS USING(USER_ID)
           WHERE PROFILEDATA.USER_ID = '$usID'";
$res01  = mysqli_query($conn, $sql01);
$row01  = mysqli_fetch_assoc($res01);
$pID    = $row01['PROFILE_ID'] ?? null;

// ── Fetch the user's projects via PDO ─────────────────────────────────────
$pdo   = get_pdo_connection();
$sqlP  = "SELECT pNAME, URL_FILE FROM Projects
          WHERE PROFILE_ID = '$pID'
          ORDER BY PROJECT_ID DESC";
$stmt  = $pdo->query($sqlP);
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portalia — Mi Perfil</title>
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

        <!-- Top action row -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
            <span class="section-label">Mis Datos</span>
            <div class="hor" style="gap:0.5rem;">
                <form action="destroy.php" method="post"
                      onsubmit="return confirm('¿Deseas cerrar sesión?')">
                    <input type="submit" name="destroy" value="Cerrar sesión" class="sBtt red bold">
                </form>
            </div>
        </div>

        <!-- ── Profile Info Card ─────────────────────────────────────────── -->
        <div class="b">
            <form action="modifyData.php" method="post"
                  onsubmit="return confirm('¿Deseas modificar la información?')">

                <div style="display:flex; justify-content:flex-end; margin-bottom:1rem;">
                    <input type="submit" name="mod" value="✏️ Modificar Información"
                           class="sBtt orange bold">
                </div>

                <!-- Name fields -->
                <div class="w" style="justify-content:flex-start; gap:1rem;">
                    <div style="flex:1; min-width:180px;">
                        <label>Primer nombre</label>
                        <input type="text" name="fName"
                               value="<?= htmlspecialchars($row['FIRST_NAME']) ?>"
                               maxlength="50" required>
                    </div>
                    <div style="flex:1; min-width:180px;">
                        <label>Segundo nombre</label>
                        <input type="text" name="mName"
                               value="<?= htmlspecialchars($row['MIDDLE_NAME'] ?? '') ?>"
                               maxlength="50">
                    </div>
                    <div style="flex:1; min-width:180px;">
                        <label>Apellido paterno</label>
                        <input type="text" name="fLName"
                               value="<?= htmlspecialchars($row['fLAST_NAME']) ?>"
                               maxlength="25" required>
                    </div>
                    <div style="flex:1; min-width:180px;">
                        <label>Apellido materno</label>
                        <input type="text" name="mLName"
                               value="<?= htmlspecialchars($row['mLAST_NAME'] ?? '') ?>"
                               maxlength="25">
                    </div>
                </div>

                <!-- Age, continent, email -->
                <div class="w" style="justify-content:flex-start; gap:1rem; margin-top:1rem;">
                    <div style="flex:0 0 100px;">
                        <label>Edad</label>
                        <input type="number" name="age"
                               value="<?= htmlspecialchars($row['AGE']) ?>"
                               min="16" max="120" required>
                    </div>
                    <div style="flex:1; min-width:180px;">
                        <label>Nacionalidad</label>
                        <select name="continent" required>
                            <?php while ($c = mysqli_fetch_assoc($resContinents)): ?>
                            <option value="<?= $c['CONTINENT_ID'] ?>"
                                <?= ($c['CONTINENT_ID'] == $defaultContinent) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['CONTINENT']) ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div style="flex:2; min-width:220px;">
                        <label>Correo electrónico</label>
                        <input type="email" name="mail"
                               value="<?= htmlspecialchars($row['EMAIL']) ?>"
                               maxlength="100" required>
                    </div>
                </div>

                <!-- Social links -->
                <div class="w" style="justify-content:flex-start; gap:1rem; margin-top:1rem;">
                    <?php
                    // Map of social field names to display labels
                    $socials = [
                        'fb' => ['Facebook',  'FACEBOOK'],
                        'ig' => ['Instagram', 'INSTAGRAM'],
                        'wa' => ['WhatsApp',  'WHATSAPP'],
                        'x'  => ['X',         'X'],
                    ];
                    foreach ($socials as $name => [$label, $col]):
                    ?>
                    <div style="flex:1; min-width:160px;">
                        <label><?= $label ?></label>
                        <input type="text" name="<?= $name ?>"
                               value="<?= htmlspecialchars($row[$col] ?? '') ?>"
                               maxlength="100">
                    </div>
                    <?php endforeach; ?>
                </div>

            </form>
        </div><!-- /.b -->

        <!-- ── Projects Section ──────────────────────────────────────────── -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
            <span class="section-label">Mis Proyectos</span>
            <form action="options.php" method="post">
                <input type="submit" name="createP" value="＋ Crear Nuevo Proyecto"
                       class="sBtt green bold">
            </form>
        </div>

        <!-- Project grid — populated by JavaScript below -->
        <form action="options.php" method="post">
            <div class="PRO" id="contenedor"></div>
        </form>

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
         * Renders the user's own project cards into the #contenedor grid.
         * Each card shows a media preview with the project name overlaid.
         * Clicking the name submits the form to options.php to open the project.
         */

        const archivos   = <?= json_encode($files) ?>;
        const contenedor = document.getElementById('contenedor');

        if (archivos.length === 0) {
            contenedor.innerHTML = '<p style="color:var(--gray-text); padding:1rem;">Aún no has subido proyectos.</p>';
        } else {
            archivos.forEach(({ pNAME, URL_FILE }) => {
                const ext = URL_FILE.split('.').pop().toLowerCase();

                const card = document.createElement('div');
                card.className = 'archivo';

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

    <?php mysqli_close($conn); ?>

</body>
</html>
