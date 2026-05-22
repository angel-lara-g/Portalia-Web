<?php
/**
 * exists.php
 * ----------
 * Handles user authentication and registration for Portalia.
 *
 * Receives POST data from loginP.php and performs one of two actions:
 *   - Login  (isset $_POST['exist']): validates credentials against the DB.
 *   - Register (isset $_POST['new']): checks username availability and creates account.
 *
 * On success, redirects to home.php (login) or regisData.php (register).
 * On failure, displays an error message with a back link.
 */

// Start session and load DB configuration
session_start();
require_once 'config.php';

// Store submitted credentials in session variables
if (isset($_POST['user']) && isset($_POST['pass'])) {
    $_SESSION["user"] = $_POST['user'];
    $_SESSION["pass"] = $_POST['pass'];
}

$us  = $_SESSION["user"];
$psw = $_SESSION["pass"];

// Open database connection
$conn = get_mysqli_connection();

// Query: check if the username already exists in the database
$sql    = "SELECT User_ID, User_Name, Passw FROM USERS WHERE User_Name = '$us'";
$result = mysqli_query($conn, $sql);

// ── LOGIN flow ────────────────────────────────────────────────────────────
if (isset($_POST['exist'])) {
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // Validate both username and password match
        if ($row["User_Name"] === $us && $row["Passw"] === $psw) {
            $_SESSION["userID"] = $row['User_ID'];
            mysqli_close($conn);
            header("Location: home.php");
            exit();
        }
    }

    // Credentials do not match — destroy session and show error
    mysqli_close($conn);
    session_destroy();
    echo 'Usuario o contraseña incorrectos. <a href="index.php">Regresar</a>';
    exit();
}

// ── REGISTER flow ─────────────────────────────────────────────────────────
if (isset($_POST['new'])) {
    if (mysqli_num_rows($result) > 0) {
        // Username is already taken
        mysqli_close($conn);
        session_destroy();
        echo 'El nombre de usuario ingresado ya existe. <a href="index.php">Regresar</a>';
        exit();
    }

    // Username is available — insert new user
    $sql2 = "INSERT INTO USERS (User_Name, Passw) VALUES ('$us', '$psw')";

    if (mysqli_query($conn, $sql2)) {
        // Retrieve the new user's ID and store it in the session
        $sql3    = "SELECT User_ID FROM USERS WHERE User_Name = '$us'";
        $result2 = mysqli_query($conn, $sql3);
        $row2    = mysqli_fetch_assoc($result2);

        $_SESSION["userID"] = $row2['User_ID'];
        mysqli_close($conn);

        // Redirect to profile data entry form
        header("Location: regisData.php");
        exit();
    } else {
        echo "Error al registrar: " . mysqli_error($conn);
        mysqli_close($conn);
    }
}
?>
