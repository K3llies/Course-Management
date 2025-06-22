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
    $cname = filter_input(INPUT_POST, 'cname', FILTER_SANITIZE_STRING);
    $credits = filter_input(INPUT_POST, 'credits', FILTER_VALIDATE_INT);
    $pid = $_SESSION['pid'];

    if ($cid && $cname && $credits) {
        // Insert course into Courses table
        $stmt = $conn->prepare("INSERT INTO Courses (cid, cname, credits) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $cid, $cname, $credits);
        $stmt->execute();

        // Assign professor to teach the new course
        $stmt = $conn->prepare("INSERT INTO Teaching (pid, cid) VALUES (?, ?)");
        $stmt->bind_param("ii", $pid, $cid);
        $stmt->execute();

        header("Location: adminPage.php?success=Course added and assigned to you");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Add Course</title>
    </head>
    <body>
        <h1>Add a New Course</h1>
        <form method="POST">
            <label for="cid">Course ID:</label>
            <input type="number" name="cid" id="cid" required>
            <br>
            <label for="cname">Course Name:</label>
            <input type="text" name="cname" id="cname" required>
            <br>
            <label for="credits">Credits:</label>
            <input type="number" name="credits" id="credits" required>
            <br>
            <button type="submit">Add Course</button>
        </form>
        <a href="adminPage.php">Back to Admin Page</a>
    </body>
</html>
