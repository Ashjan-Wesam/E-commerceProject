<?php
session_start();
require_once 'classes/User.php';

$userObj = new User();
$errors = [];

if ($userObj->autoLogin()) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email    = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $remember = isset($_POST["remember"]) ? true : false;

    if (empty($email)) {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }

    if (empty($password)) {
        $errors['password'] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors['password'] = "Password must be at least 8 characters.";
    }

    if (empty($errors)) {
        $result = $userObj->login($email, $password, $remember);

        if ($result) {
            if ($_SESSION['role_id'] === 1) {
                header("Location: views/dash.php");
            } 
            elseif($_SESSION['role_id'] === 2){

              header("Location: index.php");

            }
            else {
                header("Location: register.php");
            }
            exit();
        } 
        else {
            $errors['general'] = $result;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="assets/css/login.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
  <div class="login-container">
    <img src="assets/img/LoginImg.JPG" alt="Login Image">
    <h2>Login to your account</h2>
    <p>Enter your email and password</p>

    <?php if (!empty($errors['general'])): ?>
      <div class="error" style="color: red; margin-bottom: 10px;">
        <?php echo $errors['general']; ?>
      </div>
    <?php endif; ?>

    <form method="post" action="" onsubmit="return validateForm()">
      <!-- Email Input -->
      <div class="input-group">
        <i class="fa-solid fa-envelope"></i>
        <input type="email" id="email" name="email" placeholder="name@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
        <small class="error-message" id="email-error"><?= $errors['email'] ?? '' ?></small>
      </div>

      <!-- Password Input -->
      <div class="input-group">
        <i class="fa-solid fa-lock"></i>
        <input type="password" id="password" name="password" placeholder="Password" required>
        <small class="error-message" id="password-error"><?= $errors['password'] ?? '' ?></small>
      </div>

      <!-- Remember Me Checkbox -->
      <div class="remember-me">
        <input type="checkbox" id="remember" name="remember">
        <label for="remember">Remember me</label>
      </div>

      <!-- Submit Button -->
      <button type="submit">
        <i class="fa-solid fa-right-to-bracket"></i> Login
      </button>

      <!-- Links Section -->
      <div class="links">
        <a href="ForgetPassword.html"><i class="fa-solid fa-key"></i> Forgot password?</a>
        <p>Don't have an account?<a href="register.php"> Sign up</a></p>
      </div>
    </form>
  </div>

  <script>
    function validateForm() {
      let isValid = true;

      // Reset error messages
      document.getElementById("email-error").textContent = "";
      document.getElementById("password-error").textContent = "";

      let email = document.getElementById("email").value.trim();
      let password = document.getElementById("password").value.trim();

      // ✅ Email Validation
      if (email === "") {
        document.getElementById("email-error").textContent = "Email is required.";
        isValid = false;
      } else if (!/^\S+@\S+\.\S+$/.test(email)) {
        document.getElementById("email-error").textContent = "Invalid email format.";
        isValid = false;
      }

      // ✅ Password Validation
      if (password === "") {
        document.getElementById("password-error").textContent = "Password is required.";
        isValid = false;
      } else if (password.length < 8) {
        document.getElementById("password-error").textContent = "Password must be at least 8 characters.";
        isValid = false;
      }

      return isValid;
    }
  </script>
</body>
</html>
