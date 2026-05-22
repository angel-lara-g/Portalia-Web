<?php
/**
 * options.php
 * -----------
 * Central routing and action handler for the Portalia web application.
 *
 * Acts as a controller that receives POST submissions from all pages
 * and performs the appropriate action based on which button was pressed.
 *
 * Handled actions:
 *   - search:       Redirect to search.php
 *   - favs:         Redirect to favorites.php
 *   - prof:         Redirect to profile.php
 *   - createP:      Redirect to newProject.php
 *   - profile:      Redirect to othersProfile.php
 *   - project:      Load project data into session, redirect to project.php
 *   - deleteP:      Delete a project file and DB record, redirect to profile.php
 *   - deleteU:      Delete a user and all their project files, redirect to home.php
 *   - favsAdd:      Add a project to favorites, redirect to favorites.php
 *   - elimFavValue: Remove a project from favorites, redirect to favorites.php
 *
 * Requires an active session (user must be logged in).
 */

session_start();
require_once 'config.php';

$us   = $_SESSION["user"];
$usID = $_SESSION["userID"];

$conn = get_mysqli_connection();

// ── Navigate to Search ────────────────────────────────────────────────────
if (isset($_POST['search'])) {
    header("Location: search.php");
    exit();
}

// ── Navigate to Favorites ─────────────────────────────────────────────────
if (isset($_POST['favs'])) {
    header("Location: favorites.php");
    exit();
}

// ── Navigate to own Profile ───────────────────────────────────────────────
if (isset($_POST['prof'])) {
    header("Location: profile.php");
    exit();
}

// ── Navigate to New Project form ──────────────────────────────────────────
if (isset($_POST['createP'])) {
    header("Location: newProject.php");
    exit();
}

// ── Navigate to another user's Profile ───────────────────────────────────
if (isset($_POST['profile'])) {
    header("Location: othersProfile.php");
    exit();
}

// ── Open a Project ────────────────────────────────────────────────────────
if (isset($_POST['project'])) {
    $project = $_POST['project'];

    // Fetch project details along with author and profile info
    $sqlProject = "SELECT PROJECT_ID, pNAME, pDATE, pDESCRIPTION, pCOMMENTS,
                          URL_FILE, USERS.USER_NAME AS USER_NAME,
                          PROFILEDATA.USER_ID AS UID,
                          PROFILEDATA.PROFILE_ID AS proID
                   FROM PROJECTS
                   JOIN PROFILEDATA USING(PROFILE_ID)
                   JOIN USERS USING(USER_ID)
                   WHERE PROJECTS.pNAME = '$project'";

    $sqlCategory = "SELECT CATEGORY FROM PROJECTS
                    JOIN CATEGORIES USING(CATEGORY_ID)
                    WHERE PROJECTS.pNAME = '$project'";

    $resProject  = mysqli_query($conn, $sqlProject);
    $resCategory = mysqli_query($conn, $sqlCategory);

    if (mysqli_num_rows($resProject) > 0 && mysqli_num_rows($resCategory) > 0) {
        $row  = mysqli_fetch_assoc($resProject);
        $row2 = mysqli_fetch_assoc($resCategory);

        // Store all project data in session for use by project.php
        $_SESSION["projectID"]          = $row['PROJECT_ID'];
        $_SESSION["projectUser"]        = $row['USER_NAME'];
        $_SESSION["projectName"]        = $row['pNAME'];
        $_SESSION["projectDate"]        = $row['pDATE'];
        $_SESSION["projectCategory"]    = $row2['CATEGORY'];
        $_SESSION["projectDescription"] = $row['pDESCRIPTION'];
        $_SESSION["projectComments"]    = $row['pCOMMENTS'];
        $_SESSION["projectURL"]         = $row['URL_FILE'];
        $_SESSION["UID"]                = $row['UID'];
        $_SESSION["proID"]              = $row['proID'];
    }

    header("Location: project.php");
    exit();
}

// ── Delete a Project ──────────────────────────────────────────────────────
if (isset($_POST['deleteP'])) {
    $proID = $_SESSION["projectID"];

    // Retrieve the project's file path before deleting the DB record
    $stmt = $conn->prepare("SELECT URL_FILE FROM Projects WHERE project_id = ?");
    $stmt->bind_param("i", $proID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $filePath = "Projects/" . $row['URL_FILE'];
        // Remove the physical file from the server if it exists
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
    $stmt->close();

    // Delete the project record from the database
    $sqlDelete = "DELETE FROM PROJECTS WHERE PROJECT_ID = '$proID'";
    if (!$conn->query($sqlDelete)) {
        echo "Error al eliminar: " . $conn->error;
    }

    header("Location: profile.php");
    exit();
}

// ── Delete a User (admin only) ────────────────────────────────────────────
if (isset($_POST['deleteU'])) {
    $userID = $_SESSION["UID"];
    $proID  = $_SESSION["proID"];

    // Delete all project files belonging to this user before removing the DB record
    $stmt = $conn->prepare("SELECT URL_FILE FROM Projects WHERE PROFILE_ID = ?");
    $stmt->bind_param("i", $proID);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $filePath = "Projects/" . $row['URL_FILE'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
    $stmt->close();

    // Deleting the user cascades to PROFILEDATA, PROJECTS, and FAVORITES
    $sqlDeleteUser = "DELETE FROM USERS WHERE USER_ID = '$userID'";
    if (!$conn->query($sqlDeleteUser)) {
        echo "Error al eliminar: " . $conn->error;
    }

    header("Location: home.php");
    exit();
}

// ── Add a Project to Favorites ────────────────────────────────────────────
if (isset($_POST['favsAdd'])) {
    $proID = $_SESSION["projectID"];

    $sqlFav = "INSERT INTO FAVORITES (USER_ID, PROJECT_ID) VALUES ('$usID', '$proID')";
    if (!$conn->query($sqlFav)) {
        echo "Error al insertar en favoritos: " . $conn->error;
    }

    header("Location: favorites.php");
    exit();
}

// ── Remove a Project from Favorites ──────────────────────────────────────
if (isset($_POST['elimFavValue'])) {
    $fav = $_POST['elimFavValue'];

    // Resolve the FAVORITE_ID from the project name
    $sqlFavID = "SELECT FAVORITES.FAVORITE_ID AS favID
                 FROM FAVORITES
                 JOIN PROJECTS USING(PROJECT_ID)
                 WHERE PROJECTS.pNAME = '$fav'";
    $resFavID = mysqli_query($conn, $sqlFavID);

    if (mysqli_num_rows($resFavID) > 0) {
        $rowFav = mysqli_fetch_assoc($resFavID);
        $fID    = $rowFav['favID'];

        $sqlDeleteFav = "DELETE FROM FAVORITES WHERE FAVORITE_ID = '$fID'";
        if (!$conn->query($sqlDeleteFav)) {
            echo "Error al eliminar favorito: " . $conn->error;
        }
    }

    header("Location: favorites.php");
    exit();
}

mysqli_close($conn);
?>
