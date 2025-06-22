<?php
$servername = "localhost";
$username = "root";
$password = "csit355pass";
$dbname = "KBN_database";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sid = $_POST['sid'];

    // Check if SID exists in the database
    $query = "SELECT * FROM Students WHERE sid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $sid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Redirect to the student page if SID exists
        header("Location: studentPage.php?student_id=$sid");
        exit();
    } 
    else {
        // Redirect to signup page if SID does not exist
        header("Location: studentSignUp.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Student Login</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style type="text/css">
            .container {
                margin-top: 5%;
                margin-bottom: 5%;
                text-align: center;
                font-family: 'Courier New', Courier, monospace;
            }
            img {
                display: inline-block;
                align-items: center;
            }
            h1 {
                text-align: center;
                font-weight: bolder;
                margin-top: 50px;
                font-size: 60px;
            }
            h2 {
                text-align: center;
                font-weight: bold;
                margin-top: 30px;
                margin-bottom: 10px;
            }
            .form-row {
                display: flex;
                flex-direction: column;
                align-items: center;
                margin-bottom: 10px;
            }
            .form-row input[type="number"],
            .form-row select {
                padding: 10px;
                width: auto;
                align-items: center;
            }
            .form-row input[type="submit"] {
                padding: 10px;
                width: 80px;
                height: 40px;
                font-size: 20px;
                align-items: center;
                margin-top: 20px;
                margin-bottom: 20px;
            }
            a {
                display: flex;
                flex-direction: column;
                align-items: center;
                font-size: 20px;
            }
        </style>
    </head>
    <body>
        <h1>Login/Sign Up Page</h1>
        <div class="container">
            <img src="logo.jpg" width="200" height="200" alt="logo">
            <form action="studentLogin.php" method="POST">
                <div class="form-row">
                    <h2><label>Student ID</label></h2>
                    <input name="sid" type="number" required>
                </div>
                <div class="form-row">
                    <input type="submit" value="Login">
                </div>
            </form>
            <a href="adminLogin.php">Not a student? Click Here.</a>
        </div>
    </body>
</html>
