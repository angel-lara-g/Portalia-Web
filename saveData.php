<?php
/**
 * saveData.php
 * ------------
 * Handles profile data storage for newly registered Portalia users.
 *
 * Receives POST data from regisData.php and inserts the user's
 * personal information into the PROFILEDATA table.
 *
 * Required fields: fName, fLName, age, continent, mail.
 * Optional fields: mName, mLName, fb, ig, wa, x.
 *
 * Each optional field is updated in a separate query only if provided,
 * preserving NULL values for fields the user left blank.
 *
 * On success, redirects to home.php.
 */

session_start();
require_once 'config.php';

$us   = $_SESSION["user"];
$usID = $_SESSION["userID"];

$conn = get_mysqli_connection();

// ── Insert required profile fields ────────────────────────────────────────
if (
    isset($_POST['fName'], $_POST['fLName'], $_POST['age'],
          $_POST['continent'], $_POST['mail'])
) {
    $fName     = $_POST['fName'];
    $fLName    = $_POST['fLName'];
    $age       = $_POST['age'];
    $continent = $_POST['continent'];
    $mail      = $_POST['mail'];

    $sql = "INSERT INTO PROFILEDATA (USER_ID, FIRST_NAME, fLAST_NAME, AGE, CONTINENT, EMAIL)
            VALUES ('$usID', '$fName', '$fLName', '$age', '$continent', '$mail')";

    if (!$conn->query($sql)) {
        echo "Error al guardar: " . $conn->error;
    }
} else {
    echo "Por favor llena todos los campos requeridos.";
}

// ── Update optional fields (only if submitted and non-empty) ──────────────

/**
 * updateField()
 * Helper that runs an UPDATE on PROFILEDATA for a single column.
 *
 * @param mysqli $conn    Active database connection.
 * @param string $column  Column name to update.
 * @param string $value   Value to set.
 * @param int    $usID    ID of the user whose profile is being updated.
 */
function updateField(mysqli $conn, string $column, string $value, int $usID): void {
    $sql = "UPDATE PROFILEDATA SET $column = '$value' WHERE USER_ID = '$usID'";
    if (!$conn->query($sql)) {
        echo "Error al guardar $column: " . $conn->error;
    }
}

// Map of POST keys to their corresponding database column names
$optionalFields = [
    'mName'  => 'MIDDLE_NAME',
    'mLName' => 'mLAST_NAME',
    'fb'     => 'FACEBOOK',
    'ig'     => 'INSTAGRAM',
    'wa'     => 'WHATSAPP',
    'x'      => 'X',
];

foreach ($optionalFields as $postKey => $column) {
    if (!empty($_POST[$postKey])) {
        updateField($conn, $column, $_POST[$postKey], $usID);
    }
}

$conn->close();

// Redirect to home page after saving
header("Location: home.php");
exit();
?>
