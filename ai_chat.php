<?php
session_start();
include 'ai_helper.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$apiKey = 'your_openai_api_key'; // Replace with your actual API key
$aiHelper = new AIHelper($apiKey);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['chat_message'])) {
    $message = htmlspecialchars($_POST['chat_message']);
    // Simulate an AI chat response for demonstration purposes
    $response = "AI Response to: " . $message;

    $_SESSION['chat_history'][] = ['user' => $message, 'ai' => $response];
}

$chatHistory = $_SESSION['chat_history'] ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AI Chat</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">AI Chat</h1>
        <div class="chat-box mb-4">
            <?php foreach ($chatHistory as $chat): ?>
                <div class="chat-message">
                    <strong>User:</strong> <?php echo htmlspecialchars($chat['user']); ?><br>
                    <strong>AI:</strong> <?php echo htmlspecialchars($chat['ai']); ?>
                </div>
                <hr>
            <?php endforeach; ?>
        </div>
        <form method="POST" action="">
            <div class="form-group">
                <input type="text" name="chat_message" class="form-control" placeholder="Type your message..." required>
            </div>
            <button type="submit" class="btn btn-primary">Send</button>
        </form>
        <form method="POST" action="logout.php" class="text-center mt-3">
            <input type="submit" value="Logout" class="btn btn-secondary">
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
