<?php 
require_once __DIR__ . '/../config/config.php';

class User {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->conn;  // استخدام الاتصال مباشرة من المتغير $conn
    }

    /**
     * استرجاع بيانات المستخدم بواسطة ID
     */
    public function getUserById($userId) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * تسجيل مستخدم جديد
     */
    public function register($name, $email, $password, $phone, $address) {
        if (empty($name) || empty($email) || empty($password) || empty($phone) || empty($address)) {
            return "Please fill in all fields.";
        }

        // التحقق من رقم الهاتف الأردني
        if (!preg_match('/^(078|077|079)[0-9]{7}$/', $phone)) {
            return "Phone number must start with 078, 077, or 079 and contain exactly 10 digits.";
        }

        // التحقق من قوة كلمة المرور
        if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
            return "Password must be at least 8 characters long and include an uppercase letter, a lowercase letter, a number, and a special character.";
        }

        // التحقق مما إذا كان البريد الإلكتروني موجودًا بالفعل
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            return "This email is already registered.";
        }

        // الدور الافتراضي للمستخدم
        $role_id = 2;

        // تشفير كلمة المرور
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $created_at = date('Y-m-d H:i:s');

        // إدخال المستخدم في قاعدة البيانات
        $stmt = $this->conn->prepare("INSERT INTO users (name, email, password, phone, address, role_id, created_at, updated_at) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        return $stmt->execute([$name, $email, $passwordHash, $phone, $address, $role_id, $created_at, $created_at]);
    }

    /**
     * تسجيل الدخول
     */
    public function login($email, $password, $remember = false) {
        if (empty($email) || empty($password)) {
            return "Please fill in all fields.";
        }

        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['name'];
            $_SESSION['email'] = $user['email'];

            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $stmt = $this->conn->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                $stmt->execute([$token, $user['id']]);
                setcookie("remember_token", $token, time() + (30 * 24 * 60 * 60), "/", "", false, true);
            }

            return true;
        } else {
            return "Invalid email or password.";
        }
    }

    /**
     * تسجيل الدخول التلقائي عبر `Remember Me`
     */
    public function autoLogin() {
        if (isset($_COOKIE['remember_token'])) {
            $token = $_COOKIE['remember_token'];
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE remember_token = ?");
            $stmt->execute([$token]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['fullname'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                return true;
            }
        }
        return false;
    }

    /**
     * تحديث بيانات المستخدم
     */
    public function updateUser($userId, $name, $email, $phone, $address) {
        $stmt = $this->conn->prepare("UPDATE users SET name = ?, email = ?, phone = ?, address = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$name, $email, $phone, $address, $userId]);
    }

    /**
     * تسجيل الخروج وحذف `Remember Me`
     */
    public function logout() {
        session_start();
        session_destroy();

        if (isset($_COOKIE['remember_token'])) {
            setcookie("remember_token", "", time() - 3600, "/", "", false, true);
        }

        return true;
    }
}
?>
