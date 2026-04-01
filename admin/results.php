<?php
include '../config/database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

// Get filter parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Build query
$query = "SELECT r.*, u.username, u.email 
          FROM results r 
          JOIN users u ON r.user_id = u.id 
          WHERE u.role = 'user'";

if($search) {
    $query .= " AND (u.username LIKE :search OR u.email LIKE :search)";
}
if($status == 'pass') {
    $query .= " AND r.percentage >= 60";
} elseif($status == 'fail') {
    $query .= " AND r.percentage < 60";
}

$query .= " ORDER BY r.exam_date DESC";

$stmt = $pdo->prepare($query);
if($search) {
    $stmt->bindValue(':search', "%$search%");
}
$stmt->execute();
$results = $stmt->fetchAll();

// Get statistics
$stats_query = "SELECT 
                    COUNT(*) as total_exams,
                    AVG(percentage) as avg_percentage,
                    SUM(CASE WHEN percentage >= 60 THEN 1 ELSE 0 END) as passed,
                    SUM(CASE WHEN percentage < 60 THEN 1 ELSE 0 END) as failed,
                    MAX(percentage) as highest,
                    MIN(percentage) as lowest
                FROM results r
                JOIN users u ON r.user_id = u.id
                WHERE u.role = 'user'";
$stats = $pdo->query($stats_query)->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Results - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .stat-card h3 {
            font-size: 0.875rem;
            color: #666;
            margin-bottom: 0.5rem;
        }
        .stat-card .number {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .stat-card.passed .number { color: #48bb78; }
        .stat-card.failed .number { color: #e53e3e; }
        .stat-card.avg .number { color: #667eea; }
        .filter-bar {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            align-items: center;
        }
        .filter-bar input, .filter-bar select {
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .filter-bar input {
            flex: 1;
            min-width: 200px;
        }
        .badge {
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: bold;
        }
        .badge-pass {
            background: #c6f6d5;
            color: #22543d;
        }
        .badge-fail {
            background: #fed7d7;
            color: #742a2a;
        }
        .export-buttons {
            display: flex;
            gap: 0.5rem;
            margin-left: auto;
        }
        .btn-sm {
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
        }
        @media print {
            .navbar, .filter-bar, .export-buttons, .no-print {
                display: none;
            }
            .stats-grid {
                break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo">📊 All Exam Results</div>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="manage_questions.php">Manage Questions</a>
            <a href="../logout.php">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <h2>📈 Exam Statistics</h2>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Exams Taken</h3>
                    <div class="number"><?php echo $stats['total_exams'] ?? 0; ?></div>
                </div>
                <div class="stat-card avg">
                    <h3>Average Score</h3>
                    <div class="number"><?php echo round($stats['avg_percentage'] ?? 0, 1); ?>%</div>
                </div>
                <div class="stat-card passed">
                    <h3>Passed</h3>
                    <div class="number"><?php echo $stats['passed'] ?? 0; ?></div>
                </div>
                <div class="stat-card failed">
                    <h3>Failed</h3>
                    <div class="number"><?php echo $stats['failed'] ?? 0; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Highest Score</h3>
                    <div class="number"><?php echo round($stats['highest'] ?? 0, 1); ?>%</div>
                </div>
                <div class="stat-card">
                    <h3>Lowest Score</h3>
                    <div class="number"><?php echo round($stats['lowest'] ?? 0, 1); ?>%</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="filter-bar">
                <form method="GET" style="display: flex; gap: 1rem; flex: 1; flex-wrap: wrap;">
                    <input type="text" name="search" placeholder="Search by username or email..." 
                           value="<?php echo htmlspecialchars($search); ?>">
                    <select name="status">
                        <option value="">All Results</option>
                        <option value="pass" <?php echo $status == 'pass' ? 'selected' : ''; ?>>Passed Only</option>
                        <option value="fail" <?php echo $status == 'fail' ? 'selected' : ''; ?>>Failed Only</option>
                    </select>
                    <button type="submit" class="btn btn-primary">🔍 Filter</button>
                    <?php if($search || $status): ?>
                        <a href="results.php" class="btn" style="background: #718096; color: white;">Clear</a>
                    <?php endif; ?>
                </form>
                <div class="export-buttons no-print">
                    <button onclick="exportToCSV()" class="btn btn-success btn-sm">📥 Export CSV</button>
                    <button onclick="window.print()" class="btn btn-primary btn-sm">🖨️ Print</button>
                </div>
            </div>

            <?php if(count($results) == 0): ?>
                <div style="text-align: center; padding: 3rem;">
                    <p>No exam results found.</p>
                </div>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Score</th>
                                <th>Total Questions</th>
                                <th>Percentage</th>
                                <th>Status</th>
                                <th>Exam Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($results as $result): ?>
                            <tr>
                                <td><?php echo $result['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($result['username']); ?></strong></td>
                                <td><?php echo htmlspecialchars($result['email']); ?></td>
                                <td><?php echo $result['score']; ?> / <?php echo $result['total_questions']; ?></td>
                                <td><?php echo $result['total_questions']; ?></td>
                                <td><?php echo round($result['percentage'], 2); ?>%</td>
                                <td>
                                    <?php if($result['percentage'] >= 60): ?>
                                        <span class="badge badge-pass">✅ Passed</span>
                                    <?php else: ?>
                                        <span class="badge badge-fail">❌ Failed</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d M Y, h:i A', strtotime($result['exam_date'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div style="margin-top: 1rem; padding: 1rem; background: #f7fafc; border-radius: 5px;">
                    <strong>📊 Summary:</strong> 
                    Showing <?php echo count($results); ?> results | 
                    Pass Rate: <?php 
                        $passCount = 0;
                        foreach($results as $r) {
                            if($r['percentage'] >= 60) $passCount++;
                        }
                        $passRate = count($results) > 0 ? ($passCount / count($results)) * 100 : 0;
                        echo round($passRate, 1) . '%';
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function exportToCSV() {
            let table = document.querySelector('table');
            let rows = table.querySelectorAll('tr');
            let csv = [];
            
            for(let row of rows) {
                let cells = row.querySelectorAll('th, td');
                let rowData = [];
                for(let cell of cells) {
                    let text = cell.innerText.replace(/,/g, ';');
                    rowData.push('"' + text + '"');
                }
                csv.push(rowData.join(','));
            }
            
            let blob = new Blob([csv.join('\n')], { type: 'text/csv' });
            let url = URL.createObjectURL(blob);
            let a = document.createElement('a');
            a.href = url;
            a.download = 'exam_results_' + new Date().toISOString().slice(0,19) + '.csv';
            a.click();
            URL.revokeObjectURL(url);
        }
    </script>
</body>
</html>