<?php
include 'config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Examination System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">📝 Online Exam System</div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <?php if(isset($_SESSION['user_id'])): ?>
                <?php if($_SESSION['role'] == 'admin'): ?>
                    <a href="admin/dashboard.php">Admin Dashboard</a>
                <?php else: ?>
                    <a href="user/dashboard.php">User Dashboard</a>
                <?php endif; ?>
                <a href="logout.php">Logout (<?php echo $_SESSION['username']; ?>)</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container">
        <div class="card" style="text-align: center;">
            <h1>Welcome to Online Examination System</h1>
            <p style="margin: 1rem 0;">Test your knowledge with our online exams. Get instant results and track your progress!</p>
            <?php if(!isset($_SESSION['user_id'])): ?>
                <a href="register.php" class="btn btn-primary">Get Started</a>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2>Features</h2>
            <ul style="margin-top: 1rem; list-style: none;">
                <li>✅ User Registration & Login</li>
                <li>✅ Multiple Choice Questions</li>
                <li>✅ Timer for Exam</li>
                <li>✅ Instant Results</li>
                <li>✅ Admin Panel for Question Management</li>
            </ul>
        </div>
    </div>
</body>
</html>