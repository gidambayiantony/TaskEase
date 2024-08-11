<?php
session_start();
include 'db.php';
include 'ai_helper.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$api_key = getenv('OPENAI_API_KEY');// Replace with your actual API key
$aiHelper = new AIHelper($apiKey);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['parse_nlp'])) {
    $nlpTask = htmlspecialchars($_POST['nlp_task']);
    $parsedTask = $aiHelper->parseNaturalLanguageTask($nlpTask);

    $todo = htmlspecialchars($parsedTask['title']);
    $priority = htmlspecialchars($parsedTask['priority']);
    $due_date = htmlspecialchars($parsedTask['due_date']);
    $description = htmlspecialchars($parsedTask['description']);
    $category = htmlspecialchars($parsedTask['category']);
    $subtasks = htmlspecialchars(implode(', ', $parsedTask['subtasks']));

    $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, priority, due_date, completed, description, category, subtasks) VALUES (?, ?, ?, ?, 0, ?, ?, ?)");
    $stmt->execute([$user_id, $todo, $priority, $due_date, $description, $category, $subtasks]);

    header('Location: index.php');
    exit;
}
?>
