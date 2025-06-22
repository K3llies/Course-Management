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

$student_id = filter_input(INPUT_POST, 'student_id', FILTER_VALIDATE_INT);
$course_id = filter_input(INPUT_POST, 'course_id', FILTER_VALIDATE_INT);

if (!$student_id || !$course_id) {
    die("Invalid input: Both student ID and course ID are required.");
}

// Check if the course exists and calculate available slots dynamically
$query = "
    SELECT Courses.cid, 
           (25 - COUNT(Enrollment.sid)) AS available_slots
    FROM Courses
    LEFT JOIN Enrollment ON Courses.cid = Enrollment.cid
    WHERE Courses.cid = ?
    GROUP BY Courses.cid
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();

if (!$course) {
    die("Error: Course not found.");
}

if ($course['available_slots'] <= 0) {
    die("Error: This course is already full.");
}

// Check if the student is already enrolled in the course
$query = "SELECT * FROM Enrollment WHERE sid = ? AND cid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $student_id, $course_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    die("Error: You are already enrolled in this course.");
}

// Enroll the student
$query = "INSERT INTO Enrollment (sid, cid) VALUES (?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $student_id, $course_id);

if ($stmt->execute()) {
    // Redirect back to student page after successful enrollment
    header("Location: studentPage.php?student_id=" . urlencode($student_id));
    exit();
} 
else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
