<?php
include '../config/database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

$questions = $pdo->query("SELECT * FROM questions ORDER BY id DESC")->fetchAll();

// Get message from session
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';

// Clear messages
unset($_SESSION['message']);
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Questions</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
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
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        .btn-sm {
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
        }
        table {
            width: 100%;
            overflow-x: auto;
            display: block;
        }
        @media (min-width: 768px) {
            table {
                display: table;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo">📝 Manage Questions</div>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="add_question.php" class="btn btn-success" style="padding: 0.5rem 1rem;">+ Add New Question</a>
            <a href="../logout.php">Logout</a>
        </div>
    </nav>

    <div class="container">
        <?php if($message): ?>
            <div class="message success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="card">
            <h2>All Questions (<?php echo count($questions); ?> total)</h2>
            
            <?php if(count($questions) == 0): ?>
                <div style="text-align: center; padding: 3rem;">
                    <p>No questions found.</p>
                    <a href="add_question.php" class="btn btn-primary">➕ Add Your First Question</a>
                </div>
            <?php else: ?>
                <div style="overflow-x: auto;">
                     <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Question</th>
                                <th>Option A</th>
                                <th>Option B</th>
                                <th>Option C</th>
                                <th>Option D</th>
                                <th>Correct</th>
                                <th>Marks</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($questions as $q): ?>
                            <tr>
                                <td><?php echo $q['id']; ?></td>
                                <td style="max-width: 300px;"><?php echo htmlspecialchars(substr($q['question_text'], 0, 60)); ?>...</td>
                                <td><?php echo htmlspecialchars(substr($q['option_a'], 0, 30)); ?></td>
                                <td><?php echo htmlspecialchars(substr($q['option_b'], 0, 30)); ?></td>
                                <td><?php echo htmlspecialchars(substr($q['option_c'], 0, 30)); ?></td>
                                <td><?php echo htmlspecialchars(substr($q['option_d'], 0, 30)); ?></td>
                                <td style="text-align: center; font-weight: bold; color: green;"><?php echo $q['correct_option']; ?></td>
                                <td style="text-align: center;"><?php echo $q['marks']; ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="edit_question.php?id=<?php echo $q['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                        <a href="delete_question.php?id=<?php echo $q['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this question?\n\nQuestion: <?php echo addslashes(htmlspecialchars(substr($q['question_text'], 0, 50))); ?>')">Delete</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="card" style="margin-top: 1rem; background: #f7fafc;">
            <h3>📊 Quick Stats</h3>
            <ul style="margin-top: 0.5rem; margin-left: 1.5rem;">
                <li>Total Questions: <strong><?php echo count($questions); ?></strong></li>
                <li>Total Marks Available: <strong><?php 
                    $totalMarks = 0;
                    foreach($questions as $q) {
                        $totalMarks += $q['marks'];
                    }
                    echo $totalMarks;
                ?></strong></li>
            </ul>
        </div>
    </div>
</body>
</html>