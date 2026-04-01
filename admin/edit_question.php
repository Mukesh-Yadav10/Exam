<?php
include '../config/database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

$id = isset($_GET['id']) ? $_GET['id'] : 0;

// Get question details
$stmt = $pdo->prepare("SELECT * FROM questions WHERE id = ?");
$stmt->execute([$id]);
$question = $stmt->fetch();

if(!$question) {
    header('Location: manage_questions.php');
    exit();
}

$success = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $question_text = $_POST['question_text'];
    $option_a = $_POST['option_a'];
    $option_b = $_POST['option_b'];
    $option_c = $_POST['option_c'];
    $option_d = $_POST['option_d'];
    $correct_option = $_POST['correct_option'];
    $marks = $_POST['marks'];
    
    try {
        $stmt = $pdo->prepare("UPDATE questions SET question_text = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_option = ?, marks = ? WHERE id = ?");
        $stmt->execute([$question_text, $option_a, $option_b, $option_c, $option_d, $correct_option, $marks, $id]);
        $success = "Question updated successfully!";
        
        // Refresh question data
        $stmt = $pdo->prepare("SELECT * FROM questions WHERE id = ?");
        $stmt->execute([$id]);
        $question = $stmt->fetch();
    } catch(PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Question</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .form-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .options-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        .message {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
        .success {
            background: #c6f6d5;
            color: #22543d;
            border: 1px solid #9ae6b4;
        }
        .error {
            background: #fed7d7;
            color: #742a2a;
            border: 1px solid #feb2b2;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo">📝 Edit Question #<?php echo $id; ?></div>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="manage_questions.php">Manage Questions</a>
            <a href="../logout.php">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="form-container">
            <div class="card">
                <h2>✏️ Edit Question</h2>
                
                <?php if($success): ?>
                    <div class="message success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if($error): ?>
                    <div class="message error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label>Question Text *</label>
                        <textarea name="question_text" rows="4" required><?php echo htmlspecialchars($question['question_text']); ?></textarea>
                    </div>
                    
                    <div class="options-grid">
                        <div class="form-group">
                            <label>Option A *</label>
                            <input type="text" name="option_a" required value="<?php echo htmlspecialchars($question['option_a']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Option B *</label>
                            <input type="text" name="option_b" required value="<?php echo htmlspecialchars($question['option_b']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Option C *</label>
                            <input type="text" name="option_c" required value="<?php echo htmlspecialchars($question['option_c']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Option D *</label>
                            <input type="text" name="option_d" required value="<?php echo htmlspecialchars($question['option_d']); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Correct Option *</label>
                        <select name="correct_option" required>
                            <option value="">Select Correct Answer</option>
                            <option value="A" <?php echo ($question['correct_option'] == 'A') ? 'selected' : ''; ?>>A - Option A</option>
                            <option value="B" <?php echo ($question['correct_option'] == 'B') ? 'selected' : ''; ?>>B - Option B</option>
                            <option value="C" <?php echo ($question['correct_option'] == 'C') ? 'selected' : ''; ?>>C - Option C</option>
                            <option value="D" <?php echo ($question['correct_option'] == 'D') ? 'selected' : ''; ?>>D - Option D</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Marks</label>
                        <input type="number" name="marks" value="<?php echo $question['marks']; ?>" min="1" max="10" required>
                    </div>
                    
                    <div style="display: flex; gap: 1rem;">
                        <button type="submit" class="btn btn-primary">💾 Update Question</button>
                        <a href="manage_questions.php" class="btn" style="background: #718096; color: white; text-decoration: none;">⬅️ Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>