<?php
include '../config/database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header('Location: ../login.php');
    exit();
}

// Check if already taken exam
$stmt = $pdo->prepare("SELECT COUNT(*) FROM results WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
if($stmt->fetchColumn() > 0) {
    header('Location: dashboard.php?error=already_taken');
    exit();
}

$questions = $pdo->query("SELECT * FROM questions")->fetchAll();
$totalQuestions = count($questions);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Exam</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script>
        let timeLeft = 1800; // 30 minutes in seconds
        let timerInterval;
        
        function startTimer() {
            timerInterval = setInterval(function() {
                timeLeft--;
                let minutes = Math.floor(timeLeft / 60);
                let seconds = timeLeft % 60;
                document.getElementById('timer').innerHTML = 
                    `⏱️ Time Left: ${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                
                if(timeLeft <= 0) {
                    clearInterval(timerInterval);
                    document.getElementById('examForm').submit();
                }
            }, 1000);
        }
        
        window.onload = startTimer;
    </script>
</head>
<body>
    <div class="timer" id="timer">⏱️ Time Left: 30:00</div>
    
    <div class="container">
        <div class="card">
            <h2>Online Examination</h2>
            <p>Total Questions: <?php echo $totalQuestions; ?> | Time: 30 Minutes</p>
        </div>
        
        <form id="examForm" method="POST" action="submit_exam.php">
            <?php foreach($questions as $index => $q): ?>
            <div class="question-card">
                <h3>Question <?php echo $index + 1; ?>: <?php echo $q['question_text']; ?></h3>
                <div class="options">
                    <label class="option">
                        <input type="radio" name="answer_<?php echo $q['id']; ?>" value="A" required> A) <?php echo $q['option_a']; ?>
                    </label>
                    <label class="option">
                        <input type="radio" name="answer_<?php echo $q['id']; ?>" value="B"> B) <?php echo $q['option_b']; ?>
                    </label>
                    <label class="option">
                        <input type="radio" name="answer_<?php echo $q['id']; ?>" value="C"> C) <?php echo $q['option_c']; ?>
                    </label>
                    <label class="option">
                        <input type="radio" name="answer_<?php echo $q['id']; ?>" value="D"> D) <?php echo $q['option_d']; ?>
                    </label>
                </div>
            </div>
            <?php endforeach; ?>
            
            <div style="text-align: center; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary" style="font-size: 1.2rem;">Submit Exam ✅</button>
            </div>
        </form>
    </div>
</body>
</html>