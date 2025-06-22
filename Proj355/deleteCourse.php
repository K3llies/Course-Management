<?php
session_start();
if (!isset($_SESSION['pid'])) {
    header("Location: adminLogin.php?error=Please login first");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "csit355pass";
$dbname = "KBN_database";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $cid = filter_input(INPUT_POST, 'cid', FILTER_VALIDATE_INT);

    if ($cid) {
        // First, remove any teaching assignments for this course
        $stmt = $conn->prepare("DELETE FROM Teaching WHERE cid = ?");
        $stmt->bind_param("i", $cid);
        $stmt->execute();

        // Then, remove any enrollments for this course
        $stmt = $conn->prepare("DELETE FROM Enrollment WHERE cid = ?");
        $stmt->bind_param("i", $cid);
        $stmt->execute();

        // Finally, delete the course from the Courses table
        $stmt = $conn->prepare("DELETE FROM Courses WHERE cid = ?");
        $stmt->bind_param("i", $cid);
        $stmt->execute();

        header("Location: adminPage.php?success=Course deleted successfully");
        exit;
    }  
    else {
        header("Location: adminPage.php?error=Invalid course ID");
        exit;
    }
}

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Delete Course</title>
    </head>
    <body>
        <h1>Delete a Course</h1>
        <form method="POST">
            <label for="cid">Course ID to delete:</label>
            <input type="number" name="cid" id="cid" required>
            <br>
            <button type="submit">Delete Course</button>
        </form>
        <a href="adminPage.php">Back to Admin Page</a>
    </body>
</html>
