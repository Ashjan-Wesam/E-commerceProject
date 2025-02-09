<?php
session_start();
require_once 'config/config.php';
require_once 'classes/User.php';


$db = new Database(); 
$pdo = $db->conn;

$message = ''; 
$message_type = ''; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userObj = new User();
$userData = $userObj->getUserById($_SESSION['user_id']);

// جلب بيانات الاشتراكات للمستخدم
$query_subscription = "SELECT subscriptions.*, packages.name 
                       FROM subscriptions 
                       JOIN packages ON subscriptions.package_id = packages.id 
                       WHERE subscriptions.user_id = ?";
$stmt_subscription = $pdo->prepare($query_subscription);
$stmt_subscription->execute([$_SESSION['user_id']]);
$subscriptions = $stmt_subscription->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];

        if (empty($name) || empty($email) || empty($phone) || empty($address)) {
            $message = 'Please fill in all the fields.';
            $message_type = 'error';
        } elseif (!preg_match('/^07[89]\d{7}$/', $phone)) {
            $message = 'Please enter a valid Jordanian phone number (starts with 078, 077, 079).';
            $message_type = 'error';
        } else {
            $userObj->updateUser($_SESSION['user_id'], $name, $email, $phone, $address);
            $message = 'Profile updated successfully.';
            $message_type = 'success';
        }
    }
    
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $message = 'Please fill in all the fields.';
            $message_type = 'error';
        } elseif ($new_password !== $confirm_password) {
            $message = 'New password and confirmation do not match.';
            $message_type = 'error';
        } else {
            $message = $userObj->changePassword($_SESSION['user_id'], $current_password, $new_password, $confirm_password);
            $message_type = $message === 'Password changed successfully' ? 'success' : 'error';
        }
    }

    header("Location: profile.php");
    exit();
}

// $purchases = $userObj->getPurchaseHistory($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .profile-container { max-width: 100%; margin: 20px auto; padding: 20px; }
        .btn-custom { background-color: #81c408; color: white; }
        .navbar-custom { background-color: #81c408; }
        .navbar-brand { color: white; }
        .navbar-nav .nav-link { color: white; }
        .container { padding: 0 15px; }
        .message { padding: 10px; border-radius: 5px; margin-bottom: 20px; font-weight: bold; }
        .message.success { background-color: #28a745; color: white; }
        .message.error { background-color: #dc3545; color: white; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Fruitables</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Profile Section -->
    <div class="container mt-5">
        <div class="profile-container bg-light p-4 rounded shadow">
            
            <?php if (isset($message)): ?>
                <div class="message <?= $message_type ?? '' ?>" id="message">
                    <?= $message ?>
                </div>
            <?php endif; ?>

            <h3>User Profile</h3>
            <form method="POST" class="mt-3" id="profile-form">
                <div class="row">
                    <div class="col-md-6">
                        <label>Name</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($userData['name']) ?>" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($userData['email']) ?>" class="form-control" required>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label>Phone</label>
                        <input type="text" name="phone" id="phone" placeholder="+962" value="<?= htmlspecialchars($userData['phone']) ?>" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label>Address</label>
                        <input type="text" name="address" value="<?= htmlspecialchars($userData['address']) ?>" class="form-control" required>
                    </div>
                </div>
                <button type="submit" name="update_profile" class="btn btn-custom mt-3 w-100" id="update-btn" disabled>Update Profile</button>
            </form>

            <form method="POST" class="mt-5">
                <h4>Change Password</h4>
                <div class="row">
                    <div class="col-md-4">
                        <label>Current Password</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label>New Password</label>
                        <input type="password" name="new_password" class="form-control" required id="new-password">
                    </div>
                    <div class="col-md-4">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" required id="confirm-password">
                    </div>
                </div>
                <button type="submit" name="change_password" class="btn btn-custom mt-3 w-100" id="change-password-btn">Change Password</button>
            </form>

            <!-- Subscription History Section -->
            <h4 class="mt-4">Subscription History</h4>
            <ul class="list-group">
                <?php if (!empty($subscriptions)): ?>
                    <?php foreach ($subscriptions as $subscription): ?>
                        <li class="list-group-item">
                        <strong>Package Name:</strong> <?= htmlspecialchars($subscription['name']) ?><br>
                        <strong>Start Date:</strong> <?= htmlspecialchars($subscription['start_date']) ?><br>
                            <strong>End Date:</strong> <?= htmlspecialchars($subscription['end_date']) ?><br>
                            <strong>Status:</strong> <?= htmlspecialchars($subscription['status']) ?><br>
                            <strong>Created At:</strong> <?= htmlspecialchars($subscription['created_at']) ?><br>
                            <strong>Updated At:</strong> <?= htmlspecialchars($subscription['updated_at']) ?><br>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="list-group-item">No subscription history available.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // إخفاء رسالة النجاح بعد 5 ثواني
        window.onload = function() {
            var successMessage = document.getElementById('message');
            if (successMessage && successMessage.classList.contains('success')) {
                setTimeout(function() {
                    successMessage.style.display = 'none';
                }, 5000); // إخفاء بعد 5 ثواني
            }
        }

        // التحقق من رقم الهاتف الأردني
        document.getElementById('phone').addEventListener('input', function() {
            var phone = this.value;
            var phoneRegex = /^9627[89]\d{7}$/;

            if (phoneRegex.test(phone)) {
                document.getElementById('update-btn').disabled = false;
            } else {
                document.getElementById('update-btn').disabled = true;
            }
        });

        // التحقق من تطابق كلمة المرور الجديدة مع التأكيد
        document.getElementById('confirm-password').addEventListener('input', function() {
            var newPassword = document.getElementById('new-password').value;
            var confirmPassword = this.value;

            if (newPassword !== confirmPassword) {
                document.getElementById('change-password-btn').disabled = true;
            } else {
                document.getElementById('change-password-btn').disabled = false;
            }
        });

        // تفعيل زر التحديث عند تعديل أي حقل
        var form = document.getElementById('profile-form');
        form.addEventListener('input', function() {
            document.getElementById('update-btn').disabled = false;
        });
    </script>
</body>
</html>
