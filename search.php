<?php
/**
 * search.php
 * ----------
 * Project search page for the Portalia web application.
 *
 * Allows authenticated users to search for projects by:
 *   - Creation date
 *   - Category (loaded dynamically from the database)
 *   - Project name
 *
 * Each search method has its own form that submits to searchResult.php.
 * Requires an active session (user must be logged in).
 */

session_start();
require_once 'config.php';

$us   = $_SESSION["user"];
$usID = $_SESSION["userID"];

// Open connection to load category options
$conn = get_mysqli_connection();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portalia — Buscar</title>
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

    <!-- ── Search Panel ──────────────────────────────────────────────────── -->
    <div class="search">
        <div class="sContent">
            <h1>Selecciona un método de búsqueda</h1>

            <div class="w">

                <!-- Search by creation date -->
                <form action="searchResult.php" method="post">
                    <div class="sOp">
                        <h3>Por FECHA DE ELABORACIÓN</h3>
                        <input type="date" name="sDate" required>
                        <input type="submit" name="subDate" value="Buscar" class="sBtt blue bold">
                    </div>
                </form>

                <!-- Search by category (options loaded from DB) -->
                <form action="searchResult.php" method="post">
                    <div class="sOp">
                        <h3>Por CATEGORÍA</h3>
                        <select name="sCat" required>
                            <?php
                            // Load all available categories from the database
                            $result = mysqli_query($conn, "SELECT CATEGORY_ID, CATEGORY FROM CATEGORIES");
                            while ($row = mysqli_fetch_assoc($result)):
                            ?>
                            <option value="<?= $row['CATEGORY_ID'] ?>">
                                <?= htmlspecialchars($row['CATEGORY']) ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                        <input type="submit" name="subCat" value="Buscar" class="sBtt blue bold">
                    </div>
                </form>

                <!-- Search by project name -->
                <form action="searchResult.php" method="post">
                    <div class="sOp">
                        <h3>Por NOMBRE</h3>
                        <input type="text" name="sName" placeholder="Nombre del proyecto" required>
                        <input type="submit" name="subName" value="Buscar" class="sBtt blue bold">
                    </div>
                </form>

            </div><!-- /.w -->
        </div><!-- /.sContent -->
    </div><!-- /.search -->

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

    <?php mysqli_close($conn); ?>

</body>
</html>
