<?php
session_start();
include 'db.php';

if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch upcoming events
$query = "SELECT * FROM events WHERE event_date >= CURDATE()";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upcoming Events</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f9fc; /* Soft background color */
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center; /* Center horizontally */
        }

        h1 {
            color: #007bff; /* Primary color */
            margin-bottom: 20px;
            font-weight: bold;
            text-align: center;
        }

        table {
            width: 80%; /* Full width */
            border-collapse: collapse; /* Merge borders */
            margin-top: 20px; /* Space above table */
            background: #ffffff; /* Table background */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            border-radius: 8px; /* Rounded corners */
        }

        th, td {
            padding: 12px; /* Padding inside cells */
            text-align: left; /* Left-align text */
            border-bottom: 1px solid #ddd; /* Bottom border */
        }

        th {
            background-color: #007bff; /* Header background */
            color: white; /* Header text color */
            font-weight: bold; /* Bold header */
        }

        tr:hover {
            background-color: #f1f1f1; /* Highlight row on hover */
        }

        button {
            padding: 8px 12px; /* Button size */
            background-color: #007bff; /* Primary button color */
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 14px;
            cursor: pointer;
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
    <h1>Upcoming Events</h1>
    
    <?php
    if ($result->num_rows > 0) {
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Event Name</th>';
        echo '<th>Date</th>';
        echo '<th>Time</th>';
        echo '<th>Location</th>';
        echo '<th>Description</th>';
        echo '<th>Action</th>'; // New column for action buttons
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        while ($event = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($event['event_name']) . '</td>';
            echo '<td>' . htmlspecialchars($event['event_date']) . '</td>';
            echo '<td>' . htmlspecialchars($event['event_time']) . '</td>';
            echo '<td>' . htmlspecialchars($event['event_location']) . '</td>';
            echo '<td>' . htmlspecialchars($event['event_description']) . '</td>';
            
            // Check if the event is not full
            echo '<td>';
            if ($event['registered_count'] < $event['capacity']) {
                echo '<form action="register_event.php" method="POST" style="display:inline;">'; // Inline form
                echo '<input type="hidden" name="event_id" value="' . $event['id'] . '">';
                echo '<button type="submit">Register</button>';
                echo '</form>';
            } else {
                echo "<span style='color: red;'>Registration Full</span>";
            }
            echo '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    } else {
        echo "<p>No upcoming events.</p>";
    }
    ?>

    <a href="dashboard_new.php">Go Back to Dashboard</a>
</body>
</html>
