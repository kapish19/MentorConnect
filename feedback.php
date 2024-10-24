<?php
session_start();
include 'db.php';

if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit;
}

$student_id = $_SESSION['student_id'];

// Fetch mentor ID for the logged-in student
$query = "SELECT mentor_id FROM students WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

$mentor_id = $student['mentor_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $feedback_text = $_POST['feedback_text'] ?? '';
    $rating = $_POST['rating'] ?? '';

    // Check if a mentor is allocated
    if (!empty($mentor_id)) {
        // Insert feedback into the database
        $insert_query = "INSERT INTO feedback (student_id, mentor_id, feedback_text, rating) VALUES (?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("iisi", $student_id, $mentor_id, $feedback_text, $rating);
        
        if ($insert_stmt->execute()) {
            $message = "Feedback submitted successfully!";
        } else {
            $message = "Failed to submit feedback. Please try again later.";
        }
    } else {
        $message = "You need to be allocated a mentor before submitting feedback.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit Feedback</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f9fc; /* Soft background color */
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

        p {
            font-size: 18px;
            margin: 10px 0;
            padding: 10px;
            background: #ffffff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            font-weight: bold; /* Bold text */
            text-align: center;
        }

        form {
            background: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 300px; /* Fixed width for the form */
            text-align: center; /* Center text inside the form */
        }

        label {
            font-weight: bold; /* Bold labels */
            display: block; /* Block display for labels */
            margin-bottom: 5px; /* Space between label and input */
        }

        textarea {
            width: 100%; /* Full width */
            padding: 10px; /* Padding for textarea */
            border: 1px solid #ccc; /* Border color */
            border-radius: 5px; /* Rounded corners */
            margin-bottom: 10px; /* Space below textarea */
            resize: none; /* Disable resizing */
        }

        select {
            width: 100%; /* Full width */
            padding: 10px; /* Padding for select */
            border: 1px solid #ccc; /* Border color */
            border-radius: 5px; /* Rounded corners */
            margin-bottom: 20px; /* Space below select */
        }

        button {
            padding: 10px 15px; /* Button size */
            background-color: #007bff; /* Primary button color */
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            font-weight: bold; /* Bold button text */
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3; /* Darker on hover */
        }

        a {
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
    <h1>Submit Feedback for Your Mentor</h1>

    <?php if (isset($message)): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="feedback_text">Feedback:</label>
        <textarea name="feedback_text" rows="4" required></textarea>
        
        <label for="rating">Rating:</label>
        <select name="rating" required>
            <option value="" disabled selected>Select your rating</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
        </select>

        <button type="submit">Submit Feedback</button>
    </form>

    <a href="dashboard_new.php">Back to Dashboard</a>
</body>
</html>
