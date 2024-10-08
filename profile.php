<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$username = $user['username'] ?? 'N/A'; // Default to 'N/A' if not set
$email = $user['email'] ?? 'N/A'; // Default to 'N/A' if not set
$theme = $user['theme'] ?? 'light';
$background_image = $user['background_image'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $theme = htmlspecialchars($_POST['theme']);
    
    // Handle file upload for background image
    if (isset($_FILES['background_image']) && $_FILES['background_image']['error'] == 0) {
        $background_image = 'uploads/' . time() . '_' . $_FILES['background_image']['name'];
        move_uploaded_file($_FILES['background_image']['tmp_name'], $background_image);
    }

    // Update profile information
    if (isset($_POST['update_profile'])) {
        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, theme = ?, background_image = ? WHERE id = ?");
        $stmt->execute([$username, $email, $theme, $background_image, $user_id]);
        $success = "Profile updated successfully.";
    }

    // Change password
    if (isset($_POST['change_password'])) {
        $current_password = htmlspecialchars($_POST['current_password']);
        $new_password = htmlspecialchars($_POST['new_password']);
        $confirm_password = htmlspecialchars($_POST['confirm_password']);

        if (password_verify($current_password, $user['password'])) {
            if ($new_password == $confirm_password) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $user_id]);
                $success = "Password changed successfully.";
            } else {
                $error = "New passwords do not match.";
            }
        } else {
            $error = "Current password is incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="<?php echo htmlspecialchars($theme); ?>" style="background-image: url('<?php echo htmlspecialchars($background_image); ?>');">
    <div class="container mt-5">
        <h1 class="text-center mb-4">User Profile</h1>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" name="username" class="form-control" id="username" value="<?php echo htmlspecialchars($username); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" class="form-control" id="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div class="form-group">
                <label for="theme">Theme:</label>
                <select name="theme" id="theme" class="form-control">
                    <option value="light" <?php if ($theme == 'light') echo 'selected'; ?>>Light</option>
                    <option value="dark" <?php if ($theme == 'dark') echo 'selected'; ?>>Dark</option>
                </select>
            </div>
            <div class="form-group">
                <label for="background_image">Background Image:</label>
                <input type="file" name="background_image" class="form-control-file" id="background_image">
            </div>
            <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
        </form>

        <hr>

        <h3>Change Password</h3>
        <form method="POST" action="">
            <div class="form-group">
                <label for="current_password">Current Password:</label>
                <input type="password" name="current_password" class="form-control" id="current_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password:</label>
                <input type="password" name="new_password" class="form-control" id="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" name="confirm_password" class="form-control" id="confirm_password" required>
            </div>
            <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
        </form>
    </div>
</body>
</html>
