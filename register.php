<?php
require_once 'classes/User.php';

$name = $email = $password = $confirm_password = $phone = $address = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm-password']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    // ✅ Validate phone number (Jordan)
    if (!preg_match("/^(078|077|079)[0-9]{7}$/", $phone)) {
        $errors['phone'] = "Phone number must be in a valid format (078, 077, 079 + 7 digits).";
    }

    // ✅ Validate password (at least 8 characters, numbers, letters, and symbols)
    if (strlen($password) < 8 || 
        !preg_match("/[A-Z]/", $password) || 
        !preg_match("/[a-z]/", $password) || 
        !preg_match("/[0-9]/", $password) || 
        !preg_match("/[\W]/", $password)) {
        $errors['password'] = "Password must be at least 8 characters long and include a number, uppercase letter, lowercase letter, and symbol.";
    }

    // ✅ Check if passwords match
    if ($password !== $confirm_password) {
        $errors['confirm-password'] = "Passwords do not match.";
    }

    // ✅ If no errors, register the user
    if (empty($errors)) {
        $user = new User();
        $result = $user->register($name, $email, $password, $phone, $address);

        if ($result === true) {
            header("Location: login.php?success=registered");
            exit();
        } else {
            $errors['general'] = $result; // General registration error
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 5px;
            display: block;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="assets/img/LoginImg.JPG" alt="Login Image">
        <h2>Create a new account</h2>
        <p>Fill in the details below</p>

        <?php if (!empty($errors['general'])) echo "<p style='color:red;'>{$errors['general']}</p>"; ?>

        <form method="POST" action="">
            <div class="input-group">
                <i class="fa-solid fa-user"></i>
                <input type="text" name="name" placeholder="Full Name" value="<?= htmlspecialchars($name) ?>" required>
                <small class="error-message"><?= $errors['name'] ?? "" ?></small>
            </div>
            
            <div class="input-group">
                <i class="fa-solid fa-envelope"></i>
                <input type="email" name="email" placeholder="Email Address" value="<?= htmlspecialchars($email) ?>" required>
                <small class="error-message"><?= $errors['email'] ?? "" ?></small>
            </div>

            <div class="input-group">
                <i class="fa-solid fa-phone"></i>
                <input type="text" name="phone" placeholder="Phone Number" value="<?= htmlspecialchars($phone) ?>" required>
                <small class="error-message"><?= $errors['phone'] ?? "" ?></small>
            </div>

            <div class="input-group">
                <i class="fa-solid fa-map-marker-alt"></i>
                <input type="text" name="address" placeholder="Address" value="<?= htmlspecialchars($address) ?>" required>
                <small class="error-message"><?= $errors['address'] ?? "" ?></small>
            </div>

            <div class="input-group">
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
                <small class="error-message"><?= $errors['password'] ?? "" ?></small>
            </div>

            <div class="input-group">
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="confirm-password" placeholder="Confirm Password" required>
                <small class="error-message"><?= $errors['confirm-password'] ?? "" ?></small>
            </div>

            <button type="submit"><i class="fa-solid fa-user-plus"></i> Register</button>
            <div class="links">
                <p>Already have an account? <a href="login.php">Login</a></p>
            </div>
        </form>
    </div>
</body>
</html>
