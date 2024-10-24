<?php
session_start();
include 'db.php';

if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit;
}

$student_id = $_SESSION['student_id'];

// Fetch student details
$query = "SELECT * FROM students WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

$mentor = null; // Initialize the mentor variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Fetch mentors based on department or interests
    $department = $student['department'];
    $interests = $student['interests'];

    // Check if department matches
    $mentor_query = "SELECT * FROM mentors WHERE department = ? LIMIT 1";
    $mentor_stmt = $conn->prepare($mentor_query);
    $mentor_stmt->bind_param("s", $department);
    $mentor_stmt->execute();
    $mentor_result = $mentor_stmt->get_result();

    if ($mentor_result->num_rows > 0) {
        // Allot mentor from the same department
        $mentor = $mentor_result->fetch_assoc();
    } else {
        // If no mentor in the same department, check interests
        $mentor_query = "SELECT * FROM mentors WHERE interests LIKE ? LIMIT 1";
        $mentor_stmt = $conn->prepare($mentor_query);
        $interests_param = '%' . $interests . '%';  // Wildcard for LIKE
        $mentor_stmt->bind_param("s", $interests_param);
        $mentor_stmt->execute();
        $mentor_result = $mentor_stmt->get_result();

        if ($mentor_result->num_rows > 0) {
            // Allot mentor based on interests
            $mentor = $mentor_result->fetch_assoc();
        } else {
            // No mentors found
            $mentor = null;
        }
    }

    if ($mentor) {
        // Update student's mentor ID
        $update_query = "UPDATE students SET mentor_id = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ii", $mentor['id'], $student_id);
        $update_stmt->execute();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Allot Mentor</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f4f8; /* Soft background color */
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center; /* Center vertically */
            height: 100vh; /* Full viewport height */
        }

        h1 {
            color: #007bff; /* Primary color */
            margin-bottom: 20px;
            font-weight: bold;
            text-align: center;
        }

        h2 {
            color: #28a745; /* Success color */
            text-align: center;
        }

        p {
            font-size: 18px;
            margin: 10px 0;
            padding: 10px;
            background: #ffffff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .mentor-box {
            background: #ffffff;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 300px; /* Set a fixed width for the box */
            text-align: center; /* Center text inside the box */
        }

        form {
            background: #ffffff;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center; /* Center text inside the form */
        }

        button {
            padding: 10px 15px; /* Smaller button size */
            background-color: #007bff; /* Primary button color */
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px; /* Smaller font size */
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin: 5px; /* Add some margin for spacing */
        }

        button:hover {
            background-color: #0056b3; /* Darker on hover */
        }

        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px;
            color: #007bff; /* Link color */
            text-decoration: none;
            font-weight: bold; /* Bold link text */
            border-radius: 5px;
            transition: color 0.3s ease;
        }

        a:hover {
            color: #0056b3; /* Darker link color on hover */
        }
    </style>
</head>
<body>
    <h1>Allot Mentor</h1>
    <form method="POST" action="">
        <button type="submit">Allot Mentor</button>
    </form>

    <?php if ($mentor): ?>
        <div class="mentor-box">
            <h2>Mentor Allocated!</h2>
            <p><strong>Mentor ID:</strong> <?php echo htmlspecialchars($mentor['id']); ?></p>
            <p><strong>Mentor Email:</strong> <?php echo htmlspecialchars($mentor['email']); ?></p>
            <p><strong>Department:</strong> <?php echo htmlspecialchars($mentor['department']); ?></p>
        </div>
    <?php elseif ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
        <h2>No suitable mentor found.</h2>
    <?php endif; ?>

    <a href="dashboard_new.php">Go Back to Dashboard</a>
</body>
</html>
