<?php
include '../config/database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header('Location: ../login.php');
    exit();
}

$score = $_GET['score'] ?? 0;
$total = $_GET['total'] ?? 0;
$percentage = $_GET['percentage'] ?? 0;
$passed = $percentage >= 60;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Result</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="card" style="text-align: center;">
            <h1><?php echo $passed ? '🎉 Congratulations!' : '😢 Better Luck Next Time!'; ?></h1>
            
            <div style="margin: 2rem 0;">
                <p style="font-size: 1.2rem;">Your Score: <strong><?php echo $score; ?> / <?php echo $total; ?></strong></p>
                <p style="font-size: 1.2rem;">Percentage: <strong><?php echo number_format($percentage, 2); ?>%</strong></p>
                <p style="font-size: 1.2rem;">Status: 
                    <span style="color: <?php echo $passed ? 'green' : 'red'; ?>">
                        <?php echo $passed ? 'PASSED' : 'FAILED'; ?>
                    </span>
                </p>
            </div>
            
            <div>
                <a href="dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                <a href="../index.php" class="btn btn-success">Home</a>
            </div>
        </div>
    </div>
</body>
</html>