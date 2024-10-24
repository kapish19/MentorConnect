<?php
session_start();
include 'db.php';

if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit;
}

$student_id = $_SESSION['student_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_id = $_POST['event_id'];

    // Check if the student has already registered for the event
    $check_query = "SELECT * FROM registrations WHERE student_id = ? AND event_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ii", $student_id, $event_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        $message = "<h2>You have already registered for this event!</h2>";
    } else {
        // Insert registration into the registrations table
        $query = "INSERT INTO registrations (student_id, event_id) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $student_id, $event_id);
        $stmt->execute();

        // Increment registered_count in events table
        $update_query = "UPDATE events SET registered_count = registered_count + 1 WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("i", $event_id);
        $update_stmt->execute();

        if ($stmt->affected_rows > 0) {
            $message = "<h2>Registered successfully! 🎉</h2>";
        } else {
            $message = "<h2>Failed to register. Please try again later.</h2>";
        }
    }

    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registration Status</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f7f9fc;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                height: 100vh; /* Full viewport height */
                margin: 0;
                padding: 20px;
            }
            h2 {
                font-size: 36px; /* Larger font size */
                color: #007bff; /* Primary color */
                margin: 20px 0;
                text-align: center;
            }
            .message {
                text-align: center; /* Center the text */
                padding: 20px;
                border-radius: 8px;
                background-color: #ffffff; /* White background */
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            }
            a {
                margin-top: 20px;
                padding: 10px;
                color: #007bff; /* Link color */
                text-decoration: none;
                font-weight: bold;
                border-radius: 5px;
                transition: color 0.3s ease;
            }
            a:hover {
                color: #0056b3; /* Darker link color on hover */
            }
        </style>
    </head>
    <body>';

    echo '<div class="message">' . $message . '</div>';
    echo '<a href="events.php">Go Back to Events</a>';

    echo '</body>
    </html>';
    exit;
}
?>
