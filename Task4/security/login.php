<?php
session_start();
require_once '../config/Database.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $database = new Database();
        $db_name = $database->getConnection();

        // Prepared Statement to prevent SQL Injection
        $query = "SELECT * FROM users WHERE username = :username OR email = :email LIMIT 1";
        $stmt = $db_name->prepare($query); // Yahan aapka variable $db_name use kar liya hai

        // It passes the value of both username and email
        $stmt->execute([
            'username' => $username,
            'email'    => $username  // because in sinle input box both username and email exist 
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify hashed password securely
        if ($user && password_verify($password, $user['password'])) {
            // Regenerate session ID to prevent Session Fixation attacks
            session_regenerate_id(true);
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect according to role
            if ($user['role'] === 'admin') {
                header("Location: ../admin_dashboard.php");
            } else {
                header("Location: ../index.php"); // Student dashboard / Notice view
            }
            exit();
        } else {
            $error = "Invalid Username/Email or Password.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - College Notice Board</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width: 420px;">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h3 class="card-title text-center mb-4 text-success">Portal Login</h3>
            
            <?php if($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="mb-3">
                    <label class="form-label">Username or Email</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success w-100">Login</button>
                <p class="mt-3 text-center mb-0">New Student? <a href="register.php">Register here</a></p>
            </form>
        </div>
    </div>
</div>
</body>
</html>