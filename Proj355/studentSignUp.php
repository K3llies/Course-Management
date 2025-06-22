<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "csit355pass";
$dbname = "KBN_database";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sid = $_POST['sid'];
    $sname = $_POST['sname'];
    $age = $_POST['age'];

    // Check if SID exists in Students or Professor tables
    $query = "SELECT sid FROM Students WHERE sid = ? UNION SELECT pid FROM Professor WHERE pid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $sid, $sid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // SID is already taken by either a student or professor
        echo "Error: This SID is already taken. Please try again.";
    } else {
        // Insert new student into the database
        $query = "INSERT INTO Students (sid, sname, age) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isi", $sid, $sname, $age);

        if ($stmt->execute()) {
            echo "Signup successful! Redirecting to student dashboard...";
            header("refresh:3;url=studentPage.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Student Signup</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            form { max-width: 300px; margin: auto; }
            input { display: block; margin: 10px 0; padding: 8px; width: 100%; }
            button { padding: 10px; width: 100%; }
        </style>
    </head>
    <body>
        <h1>Student Signup</h1>
        <form action="studentSignUp.php" method="POST">
            <label for="sid">Student ID:</label>
            <input type="number" id="sid" name="sid" required>

            <label for="sname">Name:</label>
            <input type="text" id="sname" name="sname" required>

            <label for="age">Age:</label>
            <input type="number" id="age" name="age" required>

            <button type="submit">Sign Up</button>
        </form>
    </body>
</html>
