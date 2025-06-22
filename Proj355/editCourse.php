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
    # Get cid, dname, credits info
    if ($cid && $cname && $credits) {
        $stmt = $conn->prepare("UPDATE Courses SET cname = ?, credits = ? WHERE cid = ?");
        $stmt->bind_param("sii", $cname, $credits, $cid);
        $stmt->execute();
        header("Location: adminPage.php?success=Course updated");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Edit Course</title>
    </head>
    <body>
        <h1>Edit a Course</h1>
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
            <button type="submit">Update Course</button>
        </form>
        <a href="adminPage.php">Back to Admin Page</a>
    </body>
</html>
