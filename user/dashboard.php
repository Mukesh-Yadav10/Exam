<?php
include '../config/database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header('Location: ../login.php');
    exit();
}

// Get user's previous results
$stmt = $pdo->prepare("SELECT * FROM results WHERE user_id = ? ORDER BY exam_date DESC");
$stmt->execute([$_SESSION['user_id']]);
$results = $stmt->fetchAll();

// Check if exam already taken
$hasTakenExam = $pdo->prepare("SELECT COUNT(*) FROM results WHERE user_id = ?");
$hasTakenExam->execute([$_SESSION['user_id']]);
$hasTakenExam = $hasTakenExam->fetchColumn() > 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">📝 Welcome, <?php echo $_SESSION['username']; ?></div>
        <div class="nav-links">
            <a href="../index.php">Home</a>
            <a href="../logout.php">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="card" style="text-align: center;">
            <h1>Online Examination</h1>
            
            <?php if(!$hasTakenExam): ?>
                <p style="margin: 1rem 0;">Ready to test your knowledge? Click below to start the exam.</p>
                <a href="take_exam.php" class="btn btn-success" style="font-size: 1.2rem;">Start Exam 🚀</a>
            <?php else: ?>
                <div class="alert alert-success">
                    <p>✅ You have already completed the exam. Check your results below!</p>
                </div>
            <?php endif; ?>
        </div>

        <?php if($hasTakenExam): ?>
        <div class="card">
            <h2>Your Previous Results</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Score</th>
                        <th>Total Questions</th>
                        <th>Percentage</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($results as $result): ?>
                    <tr>
                        <td><?php echo $result['exam_date']; ?></td>
                        <td><?php echo $result['score']; ?></td>
                        <td><?php echo $result['total_questions']; ?></td>
                        <td><?php echo $result['percentage']; ?>%</td>
                        <td>
                            <?php if($result['percentage'] >= 60): ?>
                                <span style="color: green;">✅ Passed</span>
                            <?php else: ?>
                                <span style="color: red;">❌ Failed</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>