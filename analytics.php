<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch completed tasks
$completed_tasks = $pdo->prepare("SELECT COUNT(*) as count FROM tasks WHERE user_id = ? AND completed = 1");
$completed_tasks->execute([$user_id]);
$completed_tasks_count = $completed_tasks->fetch()['count'];

// Fetch overdue tasks
$overdue_tasks = $pdo->prepare("SELECT COUNT(*) as count FROM tasks WHERE user_id = ? AND due_date < NOW() AND completed = 0");
$overdue_tasks->execute([$user_id]);
$overdue_tasks_count = $overdue_tasks->fetch()['count'];

// Fetch total tasks
$total_tasks = $pdo->prepare("SELECT COUNT(*) as count FROM tasks WHERE user_id = ?");
$total_tasks->execute([$user_id]);
$total_tasks_count = $total_tasks->fetch()['count'];

// Calculate task completion rate
$completion_rate = $total_tasks_count > 0 ? ($completed_tasks_count / $total_tasks_count) * 100 : 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Analytics Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Analytics Dashboard</h1>
        <div class="row">
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Completed Tasks</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $completed_tasks_count; ?></h5>
                        <p class="card-text">Total completed tasks.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-header">Overdue Tasks</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $overdue_tasks_count; ?></h5>
                        <p class="card-text">Total overdue tasks.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-info mb-3">
                    <div class="card-header">Task Completion Rate</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo round($completion_rate, 2); ?>%</h5>
                        <p class="card-text">Percentage of tasks completed.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6">
                <canvas id="tasksChart"></canvas>
            </div>
            <div class="col-md-6">
                <canvas id="completionRateChart"></canvas>
            </div>
        </div>
        <div class="text-center mt-3">
            <a href="index.php" class="btn btn-primary">Back to TaskEase</a>
        </div>
    </div>

    <script>
        const ctx1 = document.getElementById('tasksChart').getContext('2d');
        const tasksChart = new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: ['Completed Tasks', 'Overdue Tasks'],
                datasets: [{
                    label: 'Number of Tasks',
                    data: [<?php echo $completed_tasks_count; ?>, <?php echo $overdue_tasks_count; ?>],
                    backgroundColor: ['rgba(75, 192, 192, 0.2)', 'rgba(255, 99, 132, 0.2)'],
                    borderColor: ['rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)'],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        const ctx2 = document.getElementById('completionRateChart').getContext('2d');
        const completionRateChart = new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'Incomplete'],
                datasets: [{
                    label: 'Task Completion Rate',
                    data: [<?php echo $completed_tasks_count; ?>, <?php echo $total_tasks_count - $completed_tasks_count; ?>],
                    backgroundColor: ['rgba(75, 192, 192, 0.2)', 'rgba(255, 205, 86, 0.2)'],
                    borderColor: ['rgba(75, 192, 192, 1)', 'rgba(255, 205, 86, 1)'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw + ' (' + (tooltipItem.raw / <?php echo $total_tasks_count; ?> * 100).toFixed(2) + '%)';
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
