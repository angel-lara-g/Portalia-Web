<?php
/**
 * modifyData.php
 * --------------
 * Handles profile data updates for authenticated Portalia users.
 *
 * Receives POST data from profile.php and updates the user's
 * personal information in the PROFILEDATA table.
 *
 * Required fields: fName, fLName, age, continent, mail.
 * Optional fields: mName, mLName, fb, ig, wa, x.
 *
 * Required fields are updated together in a single query.
 * Optional fields are updated individually only if provided.
 *
 * On success, redirects back to profile.php.
 */

session_start();
require_once 'config.php';

$us   = $_SESSION["user"];
$usID = $_SESSION["userID"];

$conn = get_mysqli_connection();

// ── Update required profile fields in a single query ─────────────────────
if (
    isset($_POST['fName'], $_POST['fLName'], $_POST['age'],
          $_POST['continent'], $_POST['mail'])
) {
    $fName     = $_POST['fName'];
    $fLName    = $_POST['fLName'];
    $age       = $_POST['age'];
    $continent = $_POST['continent'];
    $mail      = $_POST['mail'];

    $sql = "UPDATE PROFILEDATA
            SET FIRST_NAME = '$fName',
                fLAST_NAME = '$fLName',
                AGE        = '$age',
                CONTINENT  = '$continent',
                EMAIL      = '$mail'
            WHERE USER_ID  = '$usID'";

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

// Redirect back to the profile page after saving
header("Location: profile.php");
exit();
?>
