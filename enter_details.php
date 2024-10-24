<?php
session_start();
include 'db.php';

if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_SESSION['student_id'];
    $interests = $_POST['interests'];
    $future_goals = $_POST['future_goals'];

    // Update student details
    $query = "UPDATE students SET interests = ?, future_goals = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $interests, future_goals, $student_id);

    if ($stmt->execute()) {
        // Allocate mentor based on department and interests
        $query = "SELECT * FROM mentors 
                  WHERE department = (SELECT department FROM students WHERE id = ?) 
                  AND interests LIKE CONCAT('%', ?, '%') 
                  ORDER BY RAND() LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("is", $student_id, $interests);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $mentor = $result->fetch_assoc();
            $mentor_id = $mentor['id'];

            // Assign mentor to student
            $update_query = "UPDATE students SET mentor_id = ? WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("ii", $mentor_id, $student_id);
            $stmt->execute();

            echo "Mentor allocated: " . $mentor['name'];
        } else {
            echo "No mentors available matching your department and interests.";
        }
    } else {
        echo "Error submitting details.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter Details</title>
</head>
<body>
    <h1>Enter Your Details</h1>
    <form method="POST">
        <label for="interests">Interests:</label>
        <input type="text" name="interests" required>
        <br>
        <label for="future_goals">Future Goals:</label>
        <input type="text" name="future_goals" required>
        <br>
        <button type="submit">Submit</button>
    </form>
</body>
</html>
