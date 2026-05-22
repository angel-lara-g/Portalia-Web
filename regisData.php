<?php
/**
 * regisData.php
 * -------------
 * Profile data entry form for newly registered Portalia users.
 *
 * Shown immediately after a successful registration (exists.php).
 * Collects the user's personal and contact information, which is then
 * submitted to saveData.php for storage in the database.
 *
 * Requires an active session with a valid userID.
 */

session_start();
require_once 'config.php';

$us   = $_SESSION["user"];
$usID = $_SESSION["userID"];

// Load continent options from the database
$conn   = get_mysqli_connection();
$result = mysqli_query($conn, "SELECT CONTINENT_ID, CONTINENT FROM CONTINENTS");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portalia — Ingresa tus datos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- ── Header ───────────────────────────────────────────────────────── -->
    <div class="header">
        <div class="home">
            <img src="Images/portalia.png" alt="Portalia logo">
        </div>
    </div>

    <!-- ── Registration Form Card ────────────────────────────────────────── -->
    <div class="start" style="align-items:flex-start; padding-top:calc(var(--header-h) + 2rem);">
        <form action="saveData.php" method="post" class="sData"
              onsubmit="return confirm('¿Deseas ingresar los datos?')">

            <h1>Completa tu perfil</h1>
            <p style="color:var(--gray-text); font-size:0.88rem; margin-bottom:1.2rem;">
                Ingresa tus datos para comenzar a usar Portalia.
            </p>

            <!-- Name fields -->
            <label>Primer nombre *</label>
            <input type="text" name="fName" maxlength="50" placeholder="Tu primer nombre" required>

            <label style="margin-top:0.8rem;">Segundo nombre</label>
            <input type="text" name="mName" maxlength="50" placeholder="Tu segundo nombre (opcional)">

            <label style="margin-top:0.8rem;">Apellido paterno *</label>
            <input type="text" name="fLName" maxlength="25" placeholder="Tu apellido paterno" required>

            <label style="margin-top:0.8rem;">Apellido materno</label>
            <input type="text" name="mLName" maxlength="25" placeholder="Tu apellido materno (opcional)">

            <!-- Age and nationality -->
            <label style="margin-top:0.8rem;">Edad *</label>
            <input type="number" name="age" min="16" max="120" placeholder="Tu edad" required>

            <label style="margin-top:0.8rem;">Nacionalidad *</label>
            <select name="continent" required>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <option value="<?= $row['CONTINENT_ID'] ?>">
                    <?= htmlspecialchars($row['CONTINENT']) ?>
                </option>
                <?php endwhile; ?>
            </select>

            <!-- Contact info -->
            <label style="margin-top:0.8rem;">Correo electrónico *</label>
            <input type="email" name="mail" maxlength="100" placeholder="tu@correo.com" required>

            <!-- Optional social links -->
            <p style="font-weight:600; margin-top:1rem; margin-bottom:0.4rem;">Redes sociales (opcionales)</p>

            <?php
            // Social network fields — label and input name pairs
            $socials = [
                'Facebook'  => 'fb',
                'Instagram' => 'ig',
                'WhatsApp'  => 'wa',
                'X'         => 'x',
            ];
            foreach ($socials as $label => $name):
            ?>
            <label style="margin-top:0.6rem;"><?= $label ?></label>
            <input type="text" name="<?= $name ?>" maxlength="100"
                   placeholder="Tu usuario de <?= $label ?>">
            <?php endforeach; ?>

            <input type="submit" name="continue" value="Guardar y continuar"
                   class="sBtt green bold"
                   style="width:100%; padding:0.75rem; font-size:1rem; margin-top:1.5rem;">

        </form>
    </div>

    <?php mysqli_close($conn); ?>

</body>
</html>
