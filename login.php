<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        // Handle Login
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Check if student exists
        $query = "SELECT * FROM students WHERE username = ? AND password = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $student = $result->fetch_assoc();
            $_SESSION['student_id'] = $student['id'];
            header("Location: dashboard_new.php");
            exit;
        } else {
            echo "<p class='error'>Invalid credentials!</p>";
        }
    } elseif (isset($_POST['signup'])) {
        // Handle Signup
        $name = $_POST['name'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $department = $_POST['department'];

        // Insert new student
        $query = "INSERT INTO students (name, username, password, department) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssss", $name, $username, $password, $department);

        if ($stmt->execute()) {
            $_SESSION['student_id'] = $stmt->insert_id;
            header("Location: dashboard_new.php");
            exit;
        } else {
            echo "<p class='error'>Error creating account!</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MentorConnect - Login / Signup</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            text-align: center;
        }

        .header-box {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: 007bff; /* Dark Blue */
            padding: 20px;
            border-radius: 10px;
            margin: 0 auto 40px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 60%;
            max-width: 400px; 
            text-align: center;
            
        }

        .header-box img {
            max-width: 80px;
            margin-right:30px;
            
        }

        .header-box h1 {
            color: #fff;
            margin: 0;
            font-size: 32px;
            font-weight: bold;
        }

        form {
            background: #fff;
            padding: 20px;
            margin: 20px auto;
            max-width: 400px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        input[type="text"],
        input[type="password"],
        select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff; /* Green */
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0D47A1;
        }

        .error {
            color: red;
        }

        label {
            text-align: left;
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

    </style>
</head>
<body>

    <!-- Header Box with Logo and Title -->
    <div class="header-box">
        <img src="https://res.cloudinary.com/dr7iepp6t/image/upload/v1686335119/venatus/other%20assets/nsut_feed7b.png" alt="University Logo">
        <h1>MentorConnect</h1>
    </div>

    <h2>Login</h2>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>

    <h2>Sign Up</h2>
    <form method="POST">
        <input type="text" name="name" placeholder="Full Name" required>
        
        <input type="text" name="username" placeholder="Username" required>

        <input type="password" name="password" placeholder="Password" required>

        <!-- Department Dropdown -->
        <label for="department">Department:</label>
        <select name="department" required>
            <option value="Computer Science">Computer Science</option>
            <option value="Information Technology">Information Technology</option>
            <option value="Electronics and Communication Engineering">Electronics and Communication Engineering</option>
            <option value="Electrical Engineering">Electrical Engineering</option>
            <option value="Instrumentation and Control Engineering">Instrumentation and Control Engineering</option>
            <option value="Mechanical Engineering">Mechanical Engineering</option>
            <option value="Biotechnology">Biotechnology</option>
            <option value="Civil Engineering">Civil Engineering</option>

        </select>

        <button type="submit" name="signup">Sign Up</button>
    </form>

</body>
</html>
