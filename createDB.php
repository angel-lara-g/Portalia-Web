<?php
/**
 * createDB.php
 * ------------
 * Database initialization script for the Portalia web application.
 *
 * Included by index.php on every page load.
 * Creates the database and all required tables if they do not exist yet,
 * then seeds initial data (admin user, continents, categories, sample projects).
 *
 * Safe to include multiple times — all operations use IF NOT EXISTS checks.
 */

require_once 'config.php';

// Open connection without selecting a database (needed to create it first)
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create the database if it does not already exist
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if (mysqli_query($conn, $sql) !== TRUE) {
    die("Error creating database: " . mysqli_error($conn));
}

// Select the newly created (or existing) database
mysqli_select_db($conn, DB_NAME);

// Check whether the USERS table already exists to avoid re-seeding
$tableCheck = mysqli_query($conn, "SHOW TABLES LIKE 'USERS'");

if (mysqli_num_rows($tableCheck) === 0) {

    /**
     * createTable()
     * Helper function that executes a CREATE TABLE statement
     * and echoes an error message on failure.
     *
     * @param mysqli $conn  Active database connection.
     * @param string $sql   SQL CREATE TABLE statement to execute.
     * @return bool         True on success, false on failure.
     */
    function createTable(mysqli $conn, string $sql): bool {
        if (!mysqli_query($conn, $sql)) {
            echo "Error creating table: " . mysqli_error($conn);
            return false;
        }
        return true;
    }

    // ── Table: USERS ──────────────────────────────────────────────────────
    createTable($conn, "CREATE TABLE USERS (
        USER_ID   INT AUTO_INCREMENT,
        USER_NAME VARCHAR(50)  NOT NULL,
        PASSW     VARCHAR(100) NOT NULL,
        PRIMARY KEY (USER_ID)
    )");

    // ── Table: CONTINENTS ─────────────────────────────────────────────────
    createTable($conn, "CREATE TABLE CONTINENTS (
        CONTINENT_ID INT AUTO_INCREMENT,
        CONTINENT    VARCHAR(50) NOT NULL,
        PRIMARY KEY (CONTINENT_ID)
    )");

    // ── Table: CATEGORIES ─────────────────────────────────────────────────
    createTable($conn, "CREATE TABLE CATEGORIES (
        CATEGORY_ID INT AUTO_INCREMENT,
        CATEGORY    VARCHAR(50) NOT NULL,
        PRIMARY KEY (CATEGORY_ID)
    )");

    // ── Table: PROFILEDATA ────────────────────────────────────────────────
    // Linked to USERS and CONTINENTS via foreign keys
    createTable($conn, "CREATE TABLE PROFILEDATA (
        PROFILE_ID       INT AUTO_INCREMENT,
        USER_ID          INT          NOT NULL,
        FIRST_NAME       VARCHAR(50)  NOT NULL,
        MIDDLE_NAME      VARCHAR(50),
        fLAST_NAME       VARCHAR(25)  NOT NULL,
        mLAST_NAME       VARCHAR(25),
        AGE              INT UNSIGNED NOT NULL,
        CONTINENT        INT          NOT NULL,
        EMAIL            VARCHAR(100) NOT NULL,
        FACEBOOK         VARCHAR(100),
        INSTAGRAM        VARCHAR(100),
        X                VARCHAR(100),
        WHATSAPP         VARCHAR(100),
        PRIMARY KEY (PROFILE_ID),
        FOREIGN KEY (USER_ID)    REFERENCES USERS(USER_ID)      ON UPDATE CASCADE ON DELETE CASCADE,
        FOREIGN KEY (CONTINENT)  REFERENCES CONTINENTS(CONTINENT_ID) ON UPDATE CASCADE ON DELETE RESTRICT
    )");

    // ── Table: PROJECTS ───────────────────────────────────────────────────
    // Linked to PROFILEDATA and CATEGORIES via foreign keys
    createTable($conn, "CREATE TABLE PROJECTS (
        PROJECT_ID   INT AUTO_INCREMENT,
        PROFILE_ID   INT          NOT NULL,
        pNAME        VARCHAR(50)  NOT NULL,
        pDATE        DATE,
        CATEGORY_ID  INT          NOT NULL,
        pDESCRIPTION VARCHAR(100) NOT NULL,
        pCOMMENTS    VARCHAR(600),
        URL_FILE     VARCHAR(100) NOT NULL,
        PRIMARY KEY (PROJECT_ID),
        FOREIGN KEY (PROFILE_ID)  REFERENCES PROFILEDATA(PROFILE_ID) ON UPDATE CASCADE ON DELETE CASCADE,
        FOREIGN KEY (CATEGORY_ID) REFERENCES CATEGORIES(CATEGORY_ID)  ON UPDATE CASCADE ON DELETE RESTRICT
    )");

    // ── Table: FAVORITES ──────────────────────────────────────────────────
    // Stores user-project relationships for the favorites feature
    createTable($conn, "CREATE TABLE FAVORITES (
        FAVORITE_ID INT AUTO_INCREMENT,
        USER_ID     INT NOT NULL,
        PROJECT_ID  INT NOT NULL,
        PRIMARY KEY (FAVORITE_ID),
        FOREIGN KEY (USER_ID)    REFERENCES USERS(USER_ID)    ON UPDATE CASCADE ON DELETE CASCADE,
        FOREIGN KEY (PROJECT_ID) REFERENCES PROJECTS(PROJECT_ID) ON UPDATE CASCADE ON DELETE CASCADE
    )");

    // ── Seed: default admin user ──────────────────────────────────────────
    mysqli_query($conn, 'INSERT INTO USERS (USER_NAME, PASSW) VALUES ("ADMIN", "SOYADMIN123")');

    // ── Seed: continents ──────────────────────────────────────────────────
    mysqli_query($conn, 'INSERT INTO CONTINENTS (CONTINENT) VALUES
        ("África"), ("América"), ("Antártida"), ("Asia"), ("Europa"), ("Oceanía")');

    // ── Seed: project categories ──────────────────────────────────────────
    mysqli_query($conn, 'INSERT INTO CATEGORIES (CATEGORY) VALUES
        ("Tecnología e Innovación"), ("Educación y Capacitación"),
        ("Medio Ambiente y Sostenibilidad"), ("Salud y Bienestar"),
        ("Arte y Cultura"), ("Negocios y Emprendimiento"),
        ("Ciencias e Investigación"), ("Infraestructura y Construcción"), ("Personal")');

    // ── Seed: admin profile data ──────────────────────────────────────────
    mysqli_query($conn, 'INSERT INTO PROFILEDATA
        (USER_ID, FIRST_NAME, MIDDLE_NAME, fLAST_NAME, mLAST_NAME, AGE, CONTINENT, EMAIL, FACEBOOK, INSTAGRAM, X, WHATSAPP)
        VALUES (1, "José", "Ángel", "Lara", "Gómez", 21, 2, "aintgel1987@gmail.com", "Ángel Lara", "aint_lg", NULL, "3325930049")');

    // ── Seed: sample projects ─────────────────────────────────────────────
    mysqli_query($conn, 'INSERT INTO PROJECTS (PROFILE_ID, pNAME, pDATE, CATEGORY_ID, pDESCRIPTION, pCOMMENTS, URL_FILE)
        VALUES
        (1, "Prueba",  "2024-11-24", 6, "Prueba",  NULL, "pru.png"),
        (1, "Prueba2", "2024-11-24", 5, "Prueba2", NULL, "liston.jpg"),
        (1, "Prueba3", "2024-11-24", 5, "Prueba3", NULL, "mousepad.jpg")');
}

mysqli_close($conn);
?>
