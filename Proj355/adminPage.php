<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['pid'])) {
    header("Location: adminLogin.php?error=Please login first");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "csit355pass";
$dbname = "KBN_database";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$pid = $_SESSION['pid'];

// Fetch courses and enrolled students
$courses = $conn->query("SELECT cid, cname, credits FROM Courses");

// Fetch students enrolled in courses
$enrollment = $conn->query("SELECT Courses.cname, Students.sname 
                            FROM Enrollment 
                            JOIN Courses ON Enrollment.cid = Courses.cid 
                            JOIN Students ON Enrollment.sid = Students.sid 
                            ORDER BY Courses.cname, Students.sname");

// Fetch courses taught by this professor
$teaching = $conn->query("SELECT Courses.cname, Courses.credits 
                          FROM Teaching 
                          JOIN Courses ON Teaching.cid = Courses.cid 
                          WHERE Teaching.pid = $pid");
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin Page</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            body {
                font-family: Arial, sans-serif;
            }
            .container {
                max-width: 900px;
                margin: auto;
                padding: 20px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }
            table, th, td {
                border: 1px solid #ddd;
            }
            th, td {
                padding: 8px;
                text-align: left;
            }
            th {
                background-color: #f2f2f2;
            }
            .actions {
                margin-bottom: 20px;
            }
            .logout {
                text-align: right;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="logout">
                <a href="adminLogin.php">Logout</a>
            </div>
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['pname']); ?></h1>
        
            <h2>Manage Courses</h2>
            <div class="actions">
                <a href="addCourse.php">Add Course</a> |
                <a href="editCourse.php">Edit Course</a> |
                <a href="deleteCourse.php">Delete Course</a>
            </div>

            <h2>Available Courses</h2>
            <table>
                <thead>
                    <tr>
                        <th>Course ID</th>
                        <th>Course Name</th>
                        <th>Credits</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($course = $courses->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($course['cid']); ?></td>
                            <td><?php echo htmlspecialchars($course['cname']); ?></td>
                            <td><?php echo htmlspecialchars($course['credits']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <h2>Courses You Teach</h2>
            <table>
                <thead>
                    <tr>
                        <th>Course Name</th>
                        <th>Credits</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $teaching->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['cname']); ?></td>
                            <td><?php echo htmlspecialchars($row['credits']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <h2>Students Enrolled in Courses</h2>
            <?php if ($enrollment->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Course Name</th>
                        <th>Student Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $enrollment->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['cname']); ?></td>
                            <td><?php echo htmlspecialchars($row['sname']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p>No students are enrolled in any courses.</p>
            <?php endif; ?>
        </div>
    </body>
</html>
<?php
$conn->close();
?>
