<?php
include '../config/database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
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
        $stmt = $pdo->prepare("INSERT INTO questions (question_text, option_a, option_b, option_c, option_d, correct_option, marks) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$question_text, $option_a, $option_b, $option_c, $option_d, $correct_option, $marks]);
        $success = "Question added successfully!";
        
        // Clear form after successful submission
        $_POST = array();
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
    <title>Add New Question</title>
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
        .correct-option {
            margin-top: 1rem;
        }
        .correct-option select {
            width: 200px;
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
        <div class="logo">📝 Add New Question</div>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="manage_questions.php">Manage Questions</a>
            <a href="../logout.php">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="form-container">
            <div class="card">
                <h2>➕ Add New Question</h2>
                
                <?php if($success): ?>
                    <div class="message success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if($error): ?>
                    <div class="message error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" onsubmit="return validateForm()">
                    <div class="form-group">
                        <label>Question Text *</label>
                        <textarea name="question_text" rows="4" required placeholder="Enter your question here..."><?php echo isset($_POST['question_text']) ? htmlspecialchars($_POST['question_text']) : ''; ?></textarea>
                    </div>
                    
                    <div class="options-grid">
                        <div class="form-group">
                            <label>Option A *</label>
                            <input type="text" name="option_a" required placeholder="Enter option A" value="<?php echo isset($_POST['option_a']) ? htmlspecialchars($_POST['option_a']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Option B *</label>
                            <input type="text" name="option_b" required placeholder="Enter option B" value="<?php echo isset($_POST['option_b']) ? htmlspecialchars($_POST['option_b']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Option C *</label>
                            <input type="text" name="option_c" required placeholder="Enter option C" value="<?php echo isset($_POST['option_c']) ? htmlspecialchars($_POST['option_c']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Option D *</label>
                            <input type="text" name="option_d" required placeholder="Enter option D" value="<?php echo isset($_POST['option_d']) ? htmlspecialchars($_POST['option_d']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="correct-option">
                        <div class="form-group">
                            <label>Correct Option *</label>
                            <select name="correct_option" required>
                                <option value="">Select Correct Answer</option>
                                <option value="A" <?php echo (isset($_POST['correct_option']) && $_POST['correct_option'] == 'A') ? 'selected' : ''; ?>>A - Option A</option>
                                <option value="B" <?php echo (isset($_POST['correct_option']) && $_POST['correct_option'] == 'B') ? 'selected' : ''; ?>>B - Option B</option>
                                <option value="C" <?php echo (isset($_POST['correct_option']) && $_POST['correct_option'] == 'C') ? 'selected' : ''; ?>>C - Option C</option>
                                <option value="D" <?php echo (isset($_POST['correct_option']) && $_POST['correct_option'] == 'D') ? 'selected' : ''; ?>>D - Option D</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Marks</label>
                        <input type="number" name="marks" value="<?php echo isset($_POST['marks']) ? $_POST['marks'] : '1'; ?>" min="1" max="10" required>
                        <small>Points for this question (1-10)</small>
                    </div>
                    
                    <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                        <button type="submit" class="btn btn-primary">💾 Save Question</button>
                        <button type="reset" class="btn" style="background: #718096; color: white;" onclick="resetForm()">🔄 Reset Form</button>
                        <a href="manage_questions.php" class="btn" style="background: #718096; color: white; text-decoration: none;">⬅️ Back to List</a>
                    </div>
                </form>
            </div>
            
            <div class="card" style="margin-top: 1rem;">
                <h3>📝 Tips for Writing Good Questions:</h3>
                <ul style="margin-top: 0.5rem; margin-left: 1.5rem;">
                    <li>Keep question text clear and concise</li>
                    <li>Make sure only ONE option is correct</li>
                    <li>Distractors (wrong options) should be plausible</li>
                    <li>Avoid "All of the above" or "None of the above"</li>
                    <li>Use proper grammar and spelling</li>
                </ul>
            </div>
        </div>
    </div>
    
    <script>
        function validateForm() {
            let question = document.querySelector('textarea[name="question_text"]').value.trim();
            let optionA = document.querySelector('input[name="option_a"]').value.trim();
            let optionB = document.querySelector('input[name="option_b"]').value.trim();
            let optionC = document.querySelector('input[name="option_c"]').value.trim();
            let optionD = document.querySelector('input[name="option_d"]').value.trim();
            let correct = document.querySelector('select[name="correct_option"]').value;
            
            if(question === '') {
                alert('Please enter the question');
                return false;
            }
            if(optionA === '' || optionB === '' || optionC === '' || optionD === '') {
                alert('Please fill all options');
                return false;
            }
            if(correct === '') {
                alert('Please select the correct option');
                return false;
            }
            return true;
        }
        
        function resetForm() {
            document.querySelector('textarea[name="question_text"]').value = '';
            document.querySelectorAll('input[type="text"]').forEach(input => input.value = '');
            document.querySelector('select[name="correct_option"]').value = '';
            document.querySelector('input[name="marks"]').value = '1';
        }
    </script>
</body>
</html>