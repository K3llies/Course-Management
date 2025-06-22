<?php
$servername = "localhost";
$username = "root";
$password = "csit355pass";
$dbname = "KBN_database";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get student ID
$student_id = filter_input(INPUT_GET, 'student_id', FILTER_VALIDATE_INT);
if (!$student_id) {
    die("Valid Student ID is required to access the dashboard.");
}

// Available courses
$available_courses_query = "SELECT * FROM Courses";
$available_courses = $conn->query($available_courses_query);
if (!$available_courses) {
    die("Error retrieving available courses: " . $conn->error);
}

// Student's enrolled courses using prepared statement
$enrolled_courses_query = "
    SELECT Courses.cid, Courses.cname, Courses.credits 
    FROM Enrollment
    INNER JOIN Courses ON Enrollment.cid = Courses.cid
    WHERE Enrollment.sid = ?";
$stmt = $conn->prepare($enrolled_courses_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$enrolled_courses = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Student Dashboard</title>
        <style>
            body { 
                font-family: Arial, sans-serif; margin: 20px; 
            }
            table { 
                width: 100%; border-collapse: collapse; margin: 20px 0; 
            }
            table, th, td { 
                border: 1px solid #ccc; 
            }
            th, td { 
                padding: 10px; text-align: left; 
            }
            .actions { 
                margin: 20px 0; 
            }
            .form-control { 
                margin-bottom: 10px; 
            }
            button { 
                padding: 8px 12px; 
            }
            .logout { 
                text-align: right; 
            }
        </style>
    </head>
    <body>
        <div class="logout">
            <a href="studentLogin.php">Logout</a>
        </div>
    <h1>Student Dashboard</h1>

    <h2>Available Courses</h2>
    <table>
        <thead>
            <tr>
                <th>Course ID</th>
                <th>Course Name</th>
                <th>Credits</th>
                <th>Slots</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($course = $available_courses->fetch_assoc()) { 
                // Calculate the available slots for each course dynamically
                $available_slots_query = "
                    SELECT (25 - COUNT(Enrollment.sid)) AS available_slots
                    FROM Enrollment
                    WHERE Enrollment.cid = ?";
                $stmt = $conn->prepare($available_slots_query);
                $stmt->bind_param("i", $course['cid']);
                $stmt->execute();
                $slot_result = $stmt->get_result()->fetch_assoc();
                $available_slots = $slot_result['available_slots'];
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($course['cid']); ?></td>
                    <td><?php echo htmlspecialchars($course['cname']); ?></td>
                    <td><?php echo htmlspecialchars($course['credits']); ?></td>
                    <td><?php echo $available_slots; ?></td>
                    <td>
                        <?php if ($available_slots > 0) { ?>
                            <form action="enrollment.php" method="POST">
                                <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>">
                                <input type="hidden" name="course_id" value="<?php echo htmlspecialchars($course['cid']); ?>">
                                <button type="submit">Enroll</button>
                            </form>
                        <?php } else { ?>
                            <span>Full</span>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <h2>Your Enrolled Courses</h2>
    <table>
        <thead>
            <tr>
                <th>Course ID</th>
                <th>Course Name</th>
                <th>Credits</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($enrolled = $enrolled_courses->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($enrolled['cid']); ?></td>
                    <td><?php echo htmlspecialchars($enrolled['cname']); ?></td>
                    <td><?php echo htmlspecialchars($enrolled['credits']); ?></td>
                    <td>
                        <form action="dropClasses.php" method="POST">
                            <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>">
                            <input type="hidden" name="course_id" value="<?php echo htmlspecialchars($enrolled['cid']); ?>">
                            <button type="submit">Drop</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <h2>Your GPA</h2>
    <div>
        <?php
        // Query to fetch the grades for the student
        $gpa_query = "
            SELECT grade
            FROM Enrollment
            WHERE sid = ? AND grade IS NOT NULL";

        $stmt = $conn->prepare($gpa_query);

        if ($stmt === false) {
            echo "Error preparing statement: " . htmlspecialchars($conn->error);
        } 
        else {
            // Bind student ID and execute
            $stmt->bind_param("i", $student_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result) {
                $total_weight = 0;
                $grade_count = 0;

                while ($row = $result->fetch_assoc()) {
                    $grade = $row['grade'];

                    // Map numerical grade to GPA weight
                    if ($grade >= 90) {
                        $weight = 4.0; // A
                    } 
                    elseif ($grade >= 80) {
                        $weight = 3.0; // B
                    } 
                    elseif ($grade >= 70) {
                        $weight = 2.0; // C
                    } 
                    elseif ($grade >= 60) {
                        $weight = 1.0; // D
                    } 
                    else {
                        $weight = 0.0; // F
                    }

                $total_weight += $weight;
                $grade_count++;
                }

                if ($grade_count > 0) {
                    // Calculate GPA
                    $gpa = $total_weight / $grade_count;
                    echo "Your GPA: " . number_format($gpa, 2);
                }  
                else {
                    echo "Your GPA: N/A (No grades available)";
                }
            } 
            else {
                echo "Error retrieving grades: " . htmlspecialchars($conn->error);
            }

            $stmt->close();
        }
        ?>
    </div>
    </body>
</html>

<?php $conn->close(); ?>
