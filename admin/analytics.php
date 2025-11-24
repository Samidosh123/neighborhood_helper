<?php
session_start();
require_once '../includes/db_connect.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../pages/login.php");
    exit();
}

// Summary stats
$total_issues = $conn->query("SELECT COUNT(*) AS count FROM issues")->fetch_assoc()['count'];
$pending_issues = $conn->query("SELECT COUNT(*) AS count FROM issues WHERE status='Pending'")->fetch_assoc()['count'];
$in_progress_issues = $conn->query("SELECT COUNT(*) AS count FROM issues WHERE status='In Progress'")->fetch_assoc()['count'];
$resolved_issues = $conn->query("SELECT COUNT(*) AS count FROM issues WHERE status='Resolved'")->fetch_assoc()['count'];

// Count estates that have reported
$estates_reported = $conn->query("SELECT COUNT(DISTINCT estate_id) AS count FROM issues WHERE estate_id IS NOT NULL")->fetch_assoc()['count'];

// Top 5 issues by title
$top_issues_result = $conn->query("
    SELECT title, COUNT(*) AS count
    FROM issues
    GROUP BY title
    ORDER BY count DESC
    LIMIT 5
");
$top_titles = [];
$top_counts = [];
while($row = $top_issues_result->fetch_assoc()){
    $top_titles[] = $row['title'];
    $top_counts[] = $row['count'];
}

// Issues by status for Pie Chart
$status_result = $conn->query("
    SELECT status, COUNT(*) AS count
    FROM issues
    GROUP BY status
");
$statuses = [];
$status_counts = [];
while($row = $status_result->fetch_assoc()){
    $statuses[] = $row['status'];
    $status_counts[] = $row['count'];
}

// Issues per estate
$estate_result = $conn->query("
    SELECT estate_id, COUNT(*) AS count
    FROM issues
    WHERE estate_id IS NOT NULL
    GROUP BY estate_id
    ORDER BY count DESC
    LIMIT 10
");
$estate_ids = [];
$estate_counts = [];
while($row = $estate_result->fetch_assoc()){
    $estate_ids[] = 'Estate ' . $row['estate_id']; // Replace with join if you have estate names
    $estate_counts[] = $row['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports & Analytics</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; color: #333; margin: 0; }
        .main-wrapper { margin-left: 220px; padding: 20px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .card { background: white; border-radius: 12px; padding: 20px; font-weight: bold; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .chart-container { background: white; padding: 20px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .summary-text { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); font-size: 1.1em; }
        canvas { max-width: 100%; height: 350px !important; }
        .admin-header { font-size: 24px; font-weight: bold; margin-bottom: 20px; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="dashboard.php">🏠 Dashboard</a></li>
            <li><a href="Manage_users.php">👥 Manage Users</a></li>
            <li><a href="estate.php">🏘️ Manage Estates</a></li>
            <li><a href="issues.php">📌 Issues</a></li>
            <li><a href="analytics.php" class="active">📊 Reports</a></li>
            <li><a href="../logout.php">🚪 Logout</a></li>
        </ul>
    </aside>

    <div class="main-wrapper">
        <header class="admin-header">📊 Reports & Analytics</header>

        <!-- Summary Cards -->
        <div class="stats-grid">
            <div class="card">📌 Total Issues<br><span style="font-size:24px;"><?= $total_issues ?></span></div>
            <div class="card">⏳ Pending<br><span style="font-size:24px; color:#f59e0b;"><?= $pending_issues ?></span></div>
            <div class="card">🔧 In Progress<br><span style="font-size:24px; color:#3b82f6;"><?= $in_progress_issues ?></span></div>
            <div class="card">✅ Resolved<br><span style="font-size:24px; color:#10b981;"><?= $resolved_issues ?></span></div>
            <div class="card">🏘️ Estates Reported<br><span style="font-size:24px;"><?= $estates_reported ?></span></div>
        </div>

        <!-- Top Issues -->
        <div class="chart-container">
            <h2>🔥 Top 5 Reported Issues</h2>
            <canvas id="topIssuesChart"></canvas>
        </div>

        <!-- Issues by Status -->
        <div class="chart-container">
            <h2>🟢 Issues by Status</h2>
            <canvas id="statusChart"></canvas>
        </div>

        <!-- Issues per Estate -->
        <div class="chart-container">
            <h2>🏘️ Issues Reported Per Estate</h2>
            <canvas id="estateChart"></canvas>
        </div>

        <!-- Text Summary -->
        <div class="summary-text">
            <h3>📄 Summary (Easy to Understand)</h3>
            <p>
                A total of <strong><?= $total_issues ?></strong> issues have been reported so far.
                The most common issues include
                <strong><?= $top_titles[0] ?? 'N/A' ?></strong> and
                <strong><?= $top_titles[1] ?? 'N/A' ?></strong>.
                Currently, <strong><?= $pending_issues ?></strong> are pending,
                <strong><?= $in_progress_issues ?></strong> are being worked on,
                and <strong><?= $resolved_issues ?></strong> have been resolved.
                Residents from <strong><?= $estates_reported ?></strong> estates have participated in reporting.
            </p>
        </div>
    </div>

    <script>
        // Top Issues Chart
        new Chart(document.getElementById('topIssuesChart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode($top_titles) ?>,
                datasets: [{
                    label: 'Reports',
                    data: <?= json_encode($top_counts) ?>,
                    backgroundColor: ['#2563eb', '#3b82f6', '#60a5fa', '#93c5fd', '#bfdbfe']
                }]
            },
            options: { responsive: true, plugins: { legend: { display: false } } }
        });

        // Status Pie Chart
        new Chart(document.getElementById('statusChart'), {
            type: 'pie',
            data: {
                labels: <?= json_encode($statuses) ?>,
                datasets: [{
                    data: <?= json_encode($status_counts) ?>,
                    backgroundColor: ['#f59e0b', '#3b82f6', '#10b981']
                }]
            },
            options: { responsive: true }
        });

        // Issues per Estate Chart
        new Chart(document.getElementById('estateChart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode($estate_ids) ?>,
                datasets: [{
                    label: 'Reports',
                    data: <?= json_encode($estate_counts) ?>,
                    backgroundColor: '#2563eb'
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } },
                plugins: { legend: { display: false } }
            }
        });
    </script>
</body>
</html>
