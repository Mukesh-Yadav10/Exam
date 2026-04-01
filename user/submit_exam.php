<?php
include '../config/database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header('Location: ../login.php');
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $score = 0;
    $questions = $pdo->query("SELECT * FROM questions")->fetchAll();
    
    foreach($questions as $question) {
        $userAnswer = $_POST['answer_' . $question['id']] ?? '';
        if($userAnswer == $question['correct_option']) {
            $score += $question['marks'];
        }
    }
    
    $totalQuestions = count($questions);
    $percentage = ($score / $totalQuestions) * 100;
    
    $stmt = $pdo->prepare("INSERT INTO results (user_id, score, total_questions, percentage) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $score, $totalQuestions, $percentage]);
    
    header("Location: result.php?score=$score&total=$totalQuestions&percentage=$percentage");
    exit();
}
?>