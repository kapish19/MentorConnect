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

// Check if mentor is allocated
$mentor = null;
if (!empty($student['mentor_id'])) {
    $mentor_query = "SELECT * FROM mentors WHERE id = ?";
    $mentor_stmt = $conn->prepare($mentor_query);
    $mentor_stmt->bind_param("i", $student['mentor_id']);
    $mentor_stmt->execute();
    $mentor_result = $mentor_stmt->get_result();
    $mentor = $mentor_result->fetch_assoc();
}

// Initialize message variables
$message = '';
$message_type = ''; // To track success or failure

// Update interests and future goals if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_interests'])) {
    $new_interests = $_POST['new_interests'] ?? '';
    $future_goals = $_POST['future_goals'] ?? '';

    // Update student interests and future goals
    $update_query = "UPDATE students SET interests = ?, future_goals = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("ssi", $new_interests, $future_goals, $student_id);
    $update_stmt->execute();

    if ($update_stmt->affected_rows > 0) {
        // Refresh student details after update
        $student['interests'] = $new_interests;
        $student['future_goals'] = $future_goals;
        $message = "Interests and future goals updated successfully!";
        $message_type = "success"; // Set type to success
    } else {
        $message = "Failed to update. Please try again later.";
        $message_type = "error"; // Set type to error
    }
}
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
        }

        .container {
            display: flex;
            max-width: 1200px;
            width: 300%;
            height: 100vh; /* Full height of the viewport */
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: auto; /* Allow scrolling if content overflows */
        }

        .actions {
            flex: 0 0 200px;
            padding: 20px;
            background-color: #e9ecef;
            border-right: 1px solid #ccc;
            display: flex;
            flex-direction: column; /* Align buttons vertically */
            justify-content: flex-start; /* Align to the top */
        }

        .actions a {
            display: block;
            margin: 15px 0;
            padding: 15px 20px; /* Increase padding for a bigger button */
            text-align: center;
            font-size: 18px; /* Increase font size */
            color: #ffffff;
            background-color: #007bff;
            border-radius: 8px; /* Increase border radius for a smoother look */
            text-decoration: none;
            transition: background-color 0.3s ease;
            width: 100%; /* Ensure the buttons take full width */
            box-sizing: border-box; /* Avoid any padding causing overflow */
        }

        .actions a:hover {
            background-color: #0056b3;
        }
        .details {
            flex: 2;
            padding: 20px;
            overflow-y: auto; /* Allow scrolling if content overflows */
        }

        h1, h2 {
            color: #333;
            text-align: left;
            margin-bottom: 10px;
        }

        p {
            font-size: 16px;
            margin: 10px 0;
            padding: 10px;
            background: #f4f4f4;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        form {
            background: #ffffff;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 16px;
        
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #218838;
        }

        .message {
            margin-top: 10px;
            padding: 10px;
            border-radius: 4px;
            display: flex;
            align-items: center;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .message i {
            margin-right: 10px;
        }

        a.link {
            display: block;
            margin: 10px 0;
            color: #007bff;
            text-decoration: none;
        }

        a.link:hover {
            text-decoration: underline;
        }
        .actions img {
            display: block; /* Ensure it's a block element */
            margin: 0 auto 20px; /* Center the logo and add margin below */
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="actions">
            <!-- University Logo -->
            <img src="https://upload.wikimedia.org/wikipedia/en/thumb/e/e9/Netaji_Subhas_University_of_Technology.svg/1920px-Netaji_Subhas_University_of_Technology.svg.png" alt="University Logo" style="max-width: 150px; margin-bottom: 20px;">

            <h3><i class="fas fa-cogs"></i> Available Actions:</h3>
            <a href="allot_mentor.php"><i class="fas fa-user-plus"></i> Allot Mentor</a>
            <a href="feedback.php"><i class="fas fa-comments"></i> Submit Feedback</a>
            <a href="events.php"><i class="fas fa-calendar-alt"></i> Upcoming Mentorship Events</a>
            <a href="?logout=true"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <div class="details">
            <h1><i class="fas fa-user-graduate"></i> Welcome to Your Dashboard</h1>

            <h2>Your Details:</h2>
            <p><i class="fas fa-user"></i> Name: <?php echo htmlspecialchars($student['name']); ?></p>
            <p><i class="fas fa-envelope"></i> Student ID: <?php echo htmlspecialchars($student['id']); ?></p>
            <p><i class="fas fa-building"></i> Department: <?php echo htmlspecialchars($student['department']); ?></p>

            <?php if (!empty($student['mentor_id'])): ?>
                <h2>Your Allocated Mentor:</h2>
                <p><i class="fas fa-user-tie"></i> Mentor Name: <?php echo htmlspecialchars($mentor['name']); ?></p>
                <p><i class="fas fa-envelope"></i> Mentor Email: <?php echo htmlspecialchars($mentor['email']); ?></p>
                <p><i class="fas fa-building"></i> Mentor Department: <?php echo htmlspecialchars($mentor['department']); ?></p>
                <a class="link" href="mailto:<?php echo htmlspecialchars($mentor['email']); ?>?subject=Question%20for%20My%20Mentor"><i class="fas fa-question-circle"></i> Ask Doubt to Mentor</a>
            <?php else: ?>
                <h2>No Mentor Allocated Yet</h2>
            <?php endif; ?>

            <h2>Your Interests and Future Goals:</h2>
            <p><i class="fas fa-lightbulb"></i> Technical Interests: <?php echo htmlspecialchars($student['interests']); ?></p>
            <p><i class="fas fa-rocket"></i> Future Goals: <?php echo htmlspecialchars($student['future_goals']); ?></p>

            <h2>Update Your Interests and Future Goals:</h2>
            <form method="POST" action="">
                <label for="new_interests"><i class="fas fa-pencil-alt"></i> Update Technical Interests:</label>
                <input type="text" name="new_interests" value="<?php echo htmlspecialchars($student['interests']); ?>" required>
                
                <label for="future_goals"><i class="fas fa-target"></i> Future Goals:</label>
                <input type="text" name="future_goals" value="<?php echo htmlspecialchars($student['future_goals']); ?>" required>
                
                <button type="submit" name="update_interests"><i class="fas fa-save"></i> Update</button>
            </form>

            <!-- Display message here -->
            <?php if ($message): ?>
                <div class="message <?php echo $message_type; ?>">
                    <i class="<?php echo $message_type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle'; ?>"></i>
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>


</body>
</html>
