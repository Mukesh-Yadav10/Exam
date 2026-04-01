<?php
include '../config/database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

$id = isset($_GET['id']) ? $_GET['id'] : 0;

if($id > 0) {
    try {
        // First check if question exists
        $stmt = $pdo->prepare("SELECT * FROM questions WHERE id = ?");
        $stmt->execute([$id]);
        $question = $stmt->fetch();
        
        if($question) {
            // Delete the question
            $stmt = $pdo->prepare("DELETE FROM questions WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['message'] = "Question deleted successfully!";
        } else {
            $_SESSION['error'] = "Question not found!";
        }
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error deleting question: " . $e->getMessage();
    }
}

// Redirect back to manage questions page
header('Location: manage_questions.php');
exit();
?>