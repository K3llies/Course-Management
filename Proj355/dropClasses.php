<?php
$servername = "localhost";
$username = "root";
$password = "csit355pass";
$dbname = "KBN_database";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$student_id = $_POST['student_id'];
$course_id = $_POST['course_id'];

// Remove the enrollment record
$query = "DELETE FROM Enrollment WHERE sid = ? AND cid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $student_id, $course_id);

if ($stmt->execute()) {
    // 'slots' column exists
    $check_column_query = "SHOW COLUMNS FROM Courses LIKE 'slots'";
    $result = $conn->query($check_column_query);
    
    if ($result->num_rows == 0) {
        // If 'slots' column does not exist, add it
        $alter_table_query = "ALTER TABLE Courses ADD slots INT DEFAULT 25";
        if ($conn->query($alter_table_query)) {
            echo "Column 'slots' added to Courses table.";
        } 
        else {
            echo "Error adding 'slots' column: " . $conn->error;
        }
    }

    // Increase the available slots for the course
    $stmt = $conn->prepare("UPDATE Courses SET slots = slots + 1 WHERE cid = ?");
    $stmt->bind_param("i", $course_id);
    
    if ($stmt->execute()) {
        // Redirect to the student page with updated information
        header("Location: studentPage.php?student_id=" . urlencode($student_id));
        exit();
    } 
    else {
        echo "Error updating course slots: " . $stmt->error;
    }
} 
else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
