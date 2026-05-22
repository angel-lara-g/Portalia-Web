<?php
/**
 * saveProject.php
 * ---------------
 * Handles new project file upload and database insertion for Portalia.
 *
 * Receives POST data and a file upload from newProject.php.
 * Validates the file size (max 30 MB), moves it to the Projects/ directory,
 * and inserts the project record into the PROJECTS table.
 *
 * Optional comments field is updated in a separate query if provided.
 *
 * On success, redirects to profile.php.
 */

session_start();
require_once 'config.php';

$us   = $_SESSION["user"];
$usID = $_SESSION["userID"];

$conn = get_mysqli_connection();

// Maximum allowed file size: 30 MB
const MAX_FILE_SIZE = 30 * 1024 * 1024;

// ── Validate required fields and process the file upload ──────────────────
if (isset($_POST['pName'], $_POST['pDate'], $_POST['pCate'], $_POST['pDes'])) {

    $pName    = $_POST['pName'];
    $pDate    = $_POST['pDate'];
    $pCate    = $_POST['pCate'];
    $pDes     = $_POST['pDes'];
    $fileName = $_POST['fileName'] ?? '';
    $file     = $_FILES['uploadedFile'] ?? null;

    if (!$file) {
        die("No se recibió ningún archivo.");
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        die("Error al procesar el archivo.");
    }

    if ($file['size'] > MAX_FILE_SIZE) {
        die("Error: El archivo supera el tamaño máximo permitido de 30 MB.");
    }

    // Move the uploaded file to the Projects/ directory
    $uploadDir = 'Projects/';
    $filePath  = $uploadDir . basename($file['name']);

    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        die("Error al subir el archivo.");
    }

    // ── Resolve the user's PROFILE_ID ─────────────────────────────────────
    $sqlProfile = "SELECT PROFILE_ID FROM PROFILEDATA
                   JOIN USERS USING(USER_ID)
                   WHERE PROFILEDATA.USER_ID = '$usID'";
    $resProfile = mysqli_query($conn, $sqlProfile);

    if (mysqli_num_rows($resProfile) === 0) {
        die("No se encontró el perfil del usuario.");
    }

    $rowProfile = mysqli_fetch_assoc($resProfile);
    $pID        = $rowProfile['PROFILE_ID'];

    // ── Insert the new project record ─────────────────────────────────────
    $sqlInsert = "INSERT INTO PROJECTS
                    (PROFILE_ID, pNAME, pDATE, CATEGORY_ID, pDESCRIPTION, URL_FILE)
                  VALUES ('$pID', '$pName', '$pDate', '$pCate', '$pDes', '$fileName')";

    if (!$conn->query($sqlInsert)) {
        echo "Error al guardar: " . $conn->error;
    }

} else {
    echo "Por favor llena todos los campos requeridos.";
}

// ── Update optional comments field if provided ────────────────────────────
if (!empty($_POST['pCom'])) {
    $pCom = $_POST['pCom'];

    // Retrieve the ID of the most recently inserted project
    $sqlLastID = "SELECT PROJECT_ID FROM PROJECTS ORDER BY PROJECT_ID DESC LIMIT 1";
    $resLastID = mysqli_query($conn, $sqlLastID);

    if (mysqli_num_rows($resLastID) > 0) {
        $rowLastID  = mysqli_fetch_assoc($resLastID);
        $lastProjID = $rowLastID['PROJECT_ID'];

        $sqlComment = "UPDATE PROJECTS SET pCOMMENTS = '$pCom' WHERE PROJECT_ID = '$lastProjID'";
        if (!$conn->query($sqlComment)) {
            echo "Error al guardar comentarios: " . $conn->error;
        }
    }
}

$conn->close();

// Redirect to profile page after saving
header("Location: profile.php");
exit();
?>
