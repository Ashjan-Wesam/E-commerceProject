<?php
// config/config.php

class Database {
    private $host = 'localhost';      // اسم السيرفر
    private $dbname = 'e-commerce';   // اسم قاعدة البيانات
    private $user = 'root';           // اسم المستخدم
    private $pass = '';               // كلمة المرور
    public $conn;

    public function __construct() {
        try {
            // إنشاء اتصال باستخدام PDO
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8",
                $this->user,
                $this->pass
            );
            // تعيين وضع الأخطاء ليكون الاستثناء (Exception Mode)
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // في حال حدوث خطأ أثناء الاتصال يتم عرض رسالة الخطأ وإيقاف التنفيذ
            die("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
        }
    }
}
?>
