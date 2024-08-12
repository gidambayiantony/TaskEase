<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Ensure uploads directory exists
if (!file_exists('uploads')) {
    mkdir('uploads', 0777, true);
}

// Fetch tasks including shared tasks
$todos = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ? OR id IN (SELECT task_id FROM task_shares WHERE user_id = ?)");
$todos->execute([$user_id, $user_id]);
$todos = $todos->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add'])) {
        $todo = htmlspecialchars($_POST['todo']);
        $priority = htmlspecialchars($_POST['priority']);
        $due_date = htmlspecialchars($_POST['due_date']);
        $description = htmlspecialchars($_POST['description']);
        $category = htmlspecialchars($_POST['category']);
        $subtasks = htmlspecialchars($_POST['subtasks']);
        $tags = htmlspecialchars($_POST['tags']);
        $attachment = '';

        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
            $attachment = 'uploads/' . time() . '_' . $_FILES['attachment']['name'];
            move_uploaded_file($_FILES['attachment']['tmp_name'], $attachment);
        }

        $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, priority, due_date, description, category, subtasks, tags, attachment) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $todo, $priority, $due_date, $description, $category, $subtasks, $tags, $attachment]);
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $user_id]);
    } elseif (isset($_POST['complete'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("UPDATE tasks SET completed = 1 WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $user_id]);
    } elseif (isset($_POST['clear_completed'])) {
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE completed = 1 AND user_id = ?");
        $stmt->execute([$user_id]);
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $new_todo = htmlspecialchars($_POST['new_todo']);
        $priority = htmlspecialchars($_POST['priority']);
        $due_date = htmlspecialchars($_POST['due_date']);
        $description = htmlspecialchars($_POST['description']);
        $category = htmlspecialchars($_POST['category']);
        $subtasks = htmlspecialchars($_POST['subtasks']);
        $tags = htmlspecialchars($_POST['tags']);
        $attachment = $_POST['existing_attachment']; // Retain existing attachment if not updated

        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
            $attachment = 'uploads/' . time() . '_' . $_FILES['attachment']['name'];
            move_uploaded_file($_FILES['attachment']['tmp_name'], $attachment);
        }

        $stmt = $pdo->prepare("UPDATE tasks SET title = ?, priority = ?, due_date = ?, description = ?, category = ?, subtasks = ?, tags = ?, attachment = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$new_todo, $priority, $due_date, $description, $category, $subtasks, $tags, $attachment, $id, $user_id]);
    } elseif (isset($_POST['share'])) {
        $task_id = $_POST['task_id'];
        $share_email = $_POST['share_email'];

        // Find the user by email
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$share_email]);
        $user = $stmt->fetch();

        if ($user) {
            $share_user_id = $user['id'];

            // Share the task with the user
            $stmt = $pdo->prepare("INSERT INTO task_shares (task_id, user_id) VALUES (?, ?)");
            $stmt->execute([$task_id, $share_user_id]);

            echo "Task shared successfully!";
        } else {
            echo "User with email $share_email not found.";
        }
    } elseif (isset($_POST['set_theme'])) {
        $theme = $_POST['theme'];
        $_SESSION['theme'] = $theme;

        if (isset($_FILES['background_image']) && $_FILES['background_image']['error'] == 0) {
            $background_image = 'uploads/' . time() . '_' . $_FILES['background_image']['name'];
            move_uploaded_file($_FILES['background_image']['tmp_name'], $background_image);
            $_SESSION['background_image'] = $background_image;
        }
    }
    header('Location: index.php');
    exit;
}

$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? '';
$filter_priority = $_GET['filter_priority'] ?? '';
$filter_category = $_GET['filter_category'] ?? '';
$filter_tags = $_GET['filter_tags'] ?? '';

$filtered_todos = array_filter($todos, function($todo) use ($search, $filter_priority, $filter_category, $filter_tags) {
    return stripos($todo['title'], $search) !== false && 
           ($filter_priority ? $todo['priority'] == $filter_priority : true) &&
           ($filter_category ? $todo['category'] == $filter_category : true) &&
           ($filter_tags ? stripos($todo['tags'], $filter_tags) !== false : true);
});

if ($sort == 'due_date_asc') {
    usort($filtered_todos, function($a, $b) {
        return strtotime($a['due_date']) <=> strtotime($b['due_date']);
    });
} elseif ($sort == 'due_date_desc') {
    usort($filtered_todos, function($a, $b) {
        return strtotime($b['due_date']) <=> strtotime($a['due_date']);
    });
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TaskEase</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container mt-5">
        <button class="btn btn-dark toggle-mode mb-3">Toggle Dark Mode</button>
        <h1 class="text-center mb-4">TaskEase</h1>

        <div class="text-center mb-4">
            <a href="profile.php" class="btn btn-secondary">Profile</a>
            <a href="analytics.php" class="btn btn-secondary">Analytics Dashboard</a>
            <form method="POST" action="logout.php" class="d-inline">
                <input type="submit" value="Logout" class="btn btn-primary">
            </form>
        </div>

        <form class="form-inline mb-4">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" class="form-control mr-2" placeholder="Search todos">
            <select name="sort" class="form-control mr-2">
                <option value="">Sort by due date</option>
                <option value="due_date_asc" <?php if ($sort == 'due_date_asc') echo 'selected'; ?>>Ascending</option>
                <option value="due_date_desc" <?php if ($sort == 'due_date_desc') echo 'selected'; ?>>Descending</option>
            </select>
            <select name="filter_priority" class="form-control mr-2">
                <option value="">Filter by priority</option>
                <option value="Low" <?php if ($filter_priority == 'Low') echo 'selected'; ?>>Low</option>
                <option value="Medium" <?php if ($filter_priority == 'Medium') echo 'selected'; ?>>Medium</option>
                <option value="High" <?php if ($filter_priority == 'High') echo 'selected'; ?>>High</option>
            </select>
            <select name="filter_category" class="form-control mr-2">
                <option value="">Filter by category</option>
                <option value="Work" <?php if ($filter_category == 'Work') echo 'selected'; ?>>Work</option>
                <option value="Personal" <?php if ($filter_category == 'Personal') echo 'selected'; ?>>Personal</option>
            </select>
            <input type="text" name="filter_tags" value="<?php echo htmlspecialchars($filter_tags); ?>" class="form-control mr-2" placeholder="Filter by tags">
            <button type="submit" class="btn btn-primary">Apply</button>
        </form>

        <form method="POST" action="" enctype="multipart/form-data" class="mb-4">
            <label for="theme">Choose Theme:</label>
            <select name="theme" id="theme" class="form-control mr-2">
                <option value="light">Light</option>
                <option value="dark">Dark</option>
            </select>
            <label for="background_image">Upload Background Image:</label>
            <input type="file" name="background_image" id="background_image" class="form-control-file">
            <button type="submit" name="apply_settings" class="btn btn-primary">Apply Settings</button>
        </form>

        <ul class="list-group">
            <?php foreach ($filtered_todos as $todo): ?>
                <li class="list-group-item <?php echo $todo['completed'] ? 'completed' : ''; ?>">
                    <strong><?php echo htmlspecialchars($todo['title']); ?></strong> - <?php echo htmlspecialchars($todo['due_date']); ?>
                    <span class="badge badge-info"><?php echo htmlspecialchars($todo['priority']); ?></span>
                    <span class="badge badge-secondary"><?php echo htmlspecialchars($todo['category']); ?></span>
                    <span class="badge badge-primary"><?php echo htmlspecialchars($todo['tags']); ?></span>
                    <?php if ($todo['attachment']): ?>
                        <a href="<?php echo htmlspecialchars($todo['attachment']); ?>" download class="badge badge-light">Download Attachment</a>
                    <?php endif; ?>
                    <div class="float-right">
                        <form method="POST" class="d-inline-block" action="">
                            <input type="hidden" name="id" value="<?php echo $todo['id']; ?>">
                            <button type="submit" name="complete" class="btn btn-sm btn-success">Complete</button>
                        </form>
                        <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editModal-<?php echo $todo['id']; ?>">Edit</button>
                        <form method="POST" class="d-inline-block" action="">
                            <input type="hidden" name="id" value="<?php echo $todo['id']; ?>">
                            <button type="submit" name="delete" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                        
                        <!-- Share Task Form -->
                        <form method="POST" class="d-inline-block" action="">
                            <input type="hidden" name="task_id" value="<?php echo $todo['id']; ?>">
                            <input type="email" name="share_email" class="form-control-sm mr-2" placeholder="User Email" required>
                            <button type="submit" name="share" class="btn btn-sm btn-info">Share</button>
                        </form>
                    </div>
                </li>

                <!-- Edit Modal -->
                <div class="modal fade" id="editModal-<?php echo $todo['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel-<?php echo $todo['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel-<?php echo $todo['id']; ?>">Edit Todo</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="<?php echo $todo['id']; ?>">
                                    <input type="text" name="new_todo" value="<?php echo htmlspecialchars($todo['title']); ?>" class="form-control mb-2" placeholder="Todo">
                                    <select name="priority" class="form-control mb-2">
                                        <option value="Low" <?php if ($todo['priority'] == 'Low') echo 'selected'; ?>>Low</option>
                                        <option value="Medium" <?php if ($todo['priority'] == 'Medium') echo 'selected'; ?>>Medium</option>
                                        <option value="High" <?php if ($todo['priority'] == 'High') echo 'selected'; ?>>High</option>
                                    </select>
                                    <input type="date" name="due_date" value="<?php echo htmlspecialchars($todo['due_date']); ?>" class="form-control mb-2">
                                    <input type="text" name="description" value="<?php echo htmlspecialchars($todo['description']); ?>" class="form-control mb-2" placeholder="Description">
                                    <input type="text" name="category" value="<?php echo htmlspecialchars($todo['category']); ?>" class="form-control mb-2" placeholder="Category">
                                    <input type="text" name="subtasks" value="<?php echo htmlspecialchars($todo['subtasks']); ?>" class="form-control mb-2" placeholder="Subtasks (comma-separated)">
                                    <input type="text" name="tags" value="<?php echo htmlspecialchars($todo['tags']); ?>" class="form-control mb-2" placeholder="Tags (comma-separated)">
                                    <input type="file" name="attachment" class="form-control mb-2" placeholder="Attach a file">
                                    <input type="hidden" name="existing_attachment" value="<?php echo htmlspecialchars($todo['attachment']); ?>">
                                    <button type="submit" name="edit" class="btn btn-primary">Update Task</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </ul>

        <!-- Add Modal -->
        <button class="btn btn-primary mt-3" data-toggle="modal" data-target="#addModal">Add Task</button>
        <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addModalLabel">Add Todo</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="" enctype="multipart/form-data">
                            <input type="text" name="todo" class="form-control mb-2" placeholder="Todo">
                            <select name="priority" class="form-control mb-2">
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                            </select>
                            <input type="date" name="due_date" class="form-control mb-2">
                            <input type="text" name="description" class="form-control mb-2" placeholder="Description">
                            <input type="text" name="category" class="form-control mb-2" placeholder="Category">
                            <input type="text" name="subtasks" class="form-control mb-2" placeholder="Subtasks (comma-separated)">
                            <input type="text" name="tags" class="form-control mb-2" placeholder="Tags (comma-separated)">
                            <input type="file" name="attachment" class="form-control mb-2" placeholder="Attach a file">
                            <button type="submit" name="add" class="btn btn-primary">Add Task</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
