<?php
include '../config/database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

// Get statistics
$totalQuestions = $pdo->query("SELECT COUNT(*) FROM questions")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$totalExams = $pdo->query("SELECT COUNT(*) FROM results")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">📝 Admin Panel</div>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="manage_questions.php">Manage Questions</a>
            <a href="results.php">All Results</a>
            <a href="../index.php">Home</a>
            <a href="../logout.php">Logout</a>
        </div>
    </nav>

    <div class="container">
        <h1>Admin Dashboard</h1>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-top: 2rem;">
            <div class="card" style="text-align: center;">
                <h3>📋 Total Questions</h3>
                <p style="font-size: 2rem; font-weight: bold;"><?php echo $totalQuestions; ?></p>
                <a href="manage_questions.php" class="btn btn-primary">Manage</a>
            </div>
            
            <div class="card" style="text-align: center;">
                <h3>👥 Total Users</h3>
                <p style="font-size: 2rem; font-weight: bold;"><?php echo $totalUsers; ?></p>
            </div>
            
            <div class="card" style="text-align: center;">
                <h3>📊 Exams Taken</h3>
                <p style="font-size: 2rem; font-weight: bold;"><?php echo $totalExams; ?></p>
                <a href="results.php" class="btn btn-primary">View Results</a>
            </div>
        </div>
    </div>
</body>
</html>