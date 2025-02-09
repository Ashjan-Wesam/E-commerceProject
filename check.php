<?php 
    session_start();
    require_once 'config/Database.php';
    $db = new BDatabase();
    $conn = $db->getConnection();

    
    if(isset($_POST["order"])) {

        if($_POST['payment_method'] == "paypal") {
            header("Location: payment.php");
            exit();
        }

        else {
            
            $insert = $conn->prepare("INSERT INTO orders 
            (total_amount, status, order_date, delivery_date, created_at, updated_at, user_id)
            VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 3 DAY), NOW(), NOW(), ?)");
    
            $insert->execute([$_SESSION['total'], "pending" , $_SESSION['user_id'] ]);

            $_SESSION['cart'] = [];
            $_SESSION['total'] = 0;
            header("Location: Thankyou.php");
            exit();

        }
    }
?>