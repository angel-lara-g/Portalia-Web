<?php
/**
 * othersProfile.php
 * -----------------
 * Public profile view page for the Portalia web application.
 *
 * Displays another user's profile information (name, age, nationality,
 * contact links) and their uploaded projects as a card grid.
 *
 * If the logged-in user is the admin (userID = 1), a "Delete User" button
 * is shown (excluding the admin's own profile).
 *
 * The target user's ID is read from $_SESSION["UID"], set by options.php
 * when a profile button is clicked.
 *
 * Requires an active session (user must be logged in).
 */

session_start();
require_once 'config.php';

$us   = $_SESSION["user"];
$usID = $_SESSION["userID"];
$uid  = $_SESSION["UID"]; // ID of the profile being viewed

// ── Fetch the viewed user's profile data ──────────────────────────────────
$conn = get_mysqli_connection();
$sql0 = "SELECT FIRST_NAME, MIDDLE_NAME, fLAST_NAME, mLAST_NAME,
                AGE, CONTINENT, EMAIL, FACEBOOK, INSTAGRAM, X, WHATSAPP
          FROM PROFILEDATA WHERE USER_ID = '$uid'";
$result0 = mysqli_query($conn, $sql0);
$row     = mysqli_fetch_assoc($result0);
$conti   = $row["CONTINENT"];

// ── Resolve continent name from ID ────────────────────────────────────────
$sqlC   = "SELECT CONTINENT FROM CONTINENTS WHERE CONTINENT_ID = '$conti'";
$resC   = mysqli_query($conn, $sqlC);
$rowC   = mysqli_fetch_assoc($resC);
$continentName = $rowC["CONTINENT"] ?? '—';

// ── Fetch the viewed user's projects via PDO ──────────────────────────────
$pdo   = get_pdo_connection();
$sqlP  = "SELECT pNAME, URL_FILE FROM Projects
          WHERE PROFILE_ID = '$uid'
          ORDER BY PROJECT_ID DESC";
$stmt  = $pdo->query($sqlP);
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Build the user's full name
$fullName = trim(implode(' ', array_filter([
    $row['FIRST_NAME'],
    $row['MIDDLE_NAME'],
    $row['fLAST_NAME'],
    $row['mLAST_NAME'],
])));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portalia — Perfil de usuario</title>
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

        <!-- Section header row -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
            <span class="section-label">Datos</span>

            <?php
            // Show the "Delete User" button only for the admin viewing another user's profile
            if ($usID == 1 && $uid != 1):
            ?>
            <form action="options.php" method="post"
                  onsubmit="return confirm('¿Deseas eliminar este usuario?')">
                <input type="submit" name="deleteU" value="🗑 Eliminar usuario" class="sBtt red bold">
            </form>
            <?php endif; ?>
        </div>

        <!-- ── Profile Info Card ─────────────────────────────────────────── -->
        <div class="b">
            <div style="margin-bottom:0.6rem;">
                <strong>Nombre completo:</strong> <?= htmlspecialchars($fullName) ?>
            </div>
            <div style="margin-bottom:0.6rem;">
                <strong>Edad:</strong> <?= htmlspecialchars($row['AGE']) ?>
            </div>
            <div style="margin-bottom:0.6rem;">
                <strong>Nacionalidad:</strong> <?= htmlspecialchars($continentName) ?>
            </div>
            <div style="margin-bottom:0.6rem;">
                <strong>Correo electrónico:</strong> <?= htmlspecialchars($row['EMAIL']) ?>
            </div>

            <!-- Social links — only rendered if the user has set them -->
            <?php
            $socials = [
                'Facebook'  => $row['FACEBOOK'],
                'Instagram' => $row['INSTAGRAM'],
                'WhatsApp'  => $row['WHATSAPP'],
                'X'         => $row['X'],
            ];
            foreach ($socials as $label => $value):
                if (!empty($value)):
            ?>
            <div style="margin-bottom:0.6rem;">
                <strong><?= $label ?>:</strong> <?= htmlspecialchars($value) ?>
            </div>
            <?php
                endif;
            endforeach;
            ?>
        </div><!-- /.b -->

        <!-- ── Projects Section ──────────────────────────────────────────── -->
        <div style="margin-bottom:1rem;">
            <span class="section-label">Proyectos</span>
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
         * Renders the viewed user's project cards into the #contenedor grid.
         * Each card shows a media preview with the project name overlaid.
         * Clicking the name submits the form to options.php to open the project.
         */

        const archivos   = <?= json_encode($files) ?>;
        const contenedor = document.getElementById('contenedor');

        if (archivos.length === 0) {
            contenedor.innerHTML = '<p style="color:var(--gray-text); padding:1rem;">Este usuario no tiene proyectos aún.</p>';
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
