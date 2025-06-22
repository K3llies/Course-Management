<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "csit355pass";
$dbname = "KBN_database";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $pid = filter_input(INPUT_POST, 'pid', FILTER_VALIDATE_INT);

    if (!$pid) {
        // Redirect with an error if PID is invalid
        header("Location: adminLogin.php?error=Invalid PID");
        exit;
    }

    // Check if the PID exists in the database
    $query = "SELECT pname FROM Professor WHERE pid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $pid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $professor = $result->fetch_assoc();
        // Store PID and Professor name in the session
        $_SESSION['pid'] = $pid;
        $_SESSION['pname'] = $professor['pname'];

        // Redirect to the admin page
        header("Location: adminPage.php");
        exit;
    } 
    else {
        // Redirect back to login with an error message
        header("Location: adminLogin.php?error=Invalid PID");
        exit;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Login</title>
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
        a {
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 20px;
        }
        .form-row {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 10px; 
        }
        .form-row label {
            display: flex;
            width: auto; 
            text-align: center; 
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
        .error {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>Admin Login</h1>
    <div class="container">
        <img src="logo.jpg" width="200" height="200" alt="logo">
        <form action="adminLogin.php" method="POST">
            <div class="form-row">
                <h2><label for="pid">Admin ID</label></h2>
                <input id="pid" name="pid" type="number" required>
            </div>
            <div class="form-row">
                <input type="submit" value="Login">
            </div>
        </form>
        <?php if (isset($_GET['error'])): ?>
            <p class="error"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>
        <a href="studentLogin.php">Not an admin? Click Here.</a>
    </div>
</body>
</html>
