<?php
session_start();
include 'db.php';

if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_SESSION['student_id'];
    $mentor_id = $_POST['mentor_id'];
    $mentor_department = $_POST['mentor_department'];
    $feedback_text = $_POST['feedback'];
    $rating = $_POST['rating'];

    // Prepare the SQL statement to insert feedback
    $query = "INSERT INTO feedback (student_id, mentor_id, mentor_department, feedback_text, rating) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    // Check if the statement preparation was successful
    if ($stmt === false) {
        die('MySQL prepare error: ' . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("iisss", $student_id, $mentor_id, $mentor_department, $feedback_text, $rating);

    // Execute the statement
    if ($stmt->execute()) {
        echo "<h2>Feedback submitted successfully!</h2>";
    } else {
        echo "<h2>Error: " . $stmt->error . "</h2>"; // Display the error message
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>

<a href="dashboard_new.php">Go Back to Dashboard</a>
