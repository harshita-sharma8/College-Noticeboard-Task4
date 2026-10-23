<?php
require_once '../config/Database.php';

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = 'student'; // Default role set to student

    if (!empty($username) && !empty($email) && !empty($password)) {
        // Secure password hashing
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $database = new Database();
            $db_name = $database->getConnection();

            // Prepared Statement to prevent SQL Injection
            $query = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)";
            $stmt = $db_name->prepare($query);
            
            $params = [
                'username' => $username,
                'email'    => $email,
                'password' => $hashed_password,
                'role'     => $role
            ];

            if ($stmt->execute($params)) {
                $message = "Student registration successful! You can now <a href='login.php'>Login here</a>.";
            }
        } catch (PDOException $e) {
            $error = "Registration failed: Username or Email already exists.";
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Registration - College Notice Board</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width: 450px;">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h3 class="card-title text-center mb-4 text-primary">Student Register</h3>
            
            <?php if($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="register.php">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Register as Student</button>
                <p class="mt-3 text-center mb-0">Already registered? <a href="login.php">Login here</a></p>
            </form>
        </div>
    </div>
</div>
</body>
</html>