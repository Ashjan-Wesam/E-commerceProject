<?php 
        
        if(isset($_POST['pay'])) {
            
            session_start();
            require_once 'config/Database.php';
            $db = new BDatabase();
            $conn = $db->getConnection();
    
            $insert = $conn->prepare("INSERT INTO orders 
            (total_amount, status, order_date, delivery_date, created_at, updated_at, user_id)
            VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 3 DAY), NOW(), NOW(), ?)");
    
            $insert->execute([$_SESSION['total'], "pending" , $_SESSION['user_id'] ]);

            $_SESSION['cart'] = [];
            $_SESSION['total'] = 0;
            header("Location: Thankyou.php");
            exit();
        }
    
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Payment</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f4f4f4; }
        .container { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 380px; }
        h2 { text-align: center; font-size: 20px; font-weight: bold; }
        label { display: block; margin: 8px 0 3px; font-size: 14px; font-weight: bold; color: #333; }
        input { width: calc(100% - 10px); padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 14px; }
        .form-group { display: flex; justify-content: space-between; }
        .form-group input { width: 48%; }
        button { width: 100%; padding: 12px; background:#81C408; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; margin-top: 10px; }
        button:hover { background: #81C408; }
        .error { color: red; text-align: center; }
        .card-logos { display: flex; justify-content: flex-end; margin-bottom: 10px; }
        .card-logos img { height: 20px; margin-left: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Confirm Your Purchase</h2>
        <div class="card-logos">
            <img src="https://upload.wikimedia.org/wikipedia/commons/0/04/Mastercard-logo.png" alt="Mastercard">
            <img src="https://upload.wikimedia.org/wikipedia/commons/0/04/Visa.svg" alt="Visa">
        </div>
        <p id="errorMessage" class="error" style="display: none;"></p>
        <form id="paymentForm" method="POST" action=""  onsubmit="return vaidate()">
            <label>Name on Card</label>
            <input type="text" name="name" id="name" required>
            <div class="form-group">
                <div>
                    <label>Expiry</label>
                    <input type="text" name="expiry" id="expiry" placeholder="MM/YY" required>
                </div>
                <div>
                    <label>CVV</label>
                    <input type="text" name="cvv" id="cvv" maxlength="3" required>
                </div>
            </div>
            <label>Card Number</label>
            <input type="text" name="cardNumber" id="cardNumber" maxlength="19" placeholder="1111 2222 3333 4444" required>
            <button type="submit" name="pay">Complete Payment</button>
        </form>
    </div>

    <script>
        // document.getElementById('paymentForm').addEventListener('submit', function(event) {
        //     event.preventDefault();

        //     const name = document.getElementById('name').value.trim();
        //     const cardNumber = document.getElementById('cardNumber').value.replace(/\s+/g, '');
        //     const expiry = document.getElementById('expiry').value.trim();
        //     const cvv = document.getElementById('cvv').value.trim();

        //     const errorMessage = document.getElementById('errorMessage');

        //     if (!name || !cardNumber || !expiry || !cvv) {
        //         errorMessage.textContent = "All fields are required.";
        //         errorMessage.style.display = 'block';
        //         return;
        //     }

        //     if (!/^\d{16}$/.test(cardNumber)) {
        //         errorMessage.textContent = "Invalid card number.";
        //         errorMessage.style.display = 'block';
        //         return;
        //     }

        //     if (!/^\d{3}$/.test(cvv)) {
        //         errorMessage.textContent = "Invalid CVV.";
        //         errorMessage.style.display = 'block';
        //         return;
        //     }

          
        //     this.submit();
        // });

        function vaidate() {
            const name = document.getElementById('name').value.trim();
            const cardNumber = document.getElementById('cardNumber').value.replace(/\s+/g, '');
            const expiry = document.getElementById('expiry').value.trim();
            const cvv = document.getElementById('cvv').value.trim();

            const errorMessage = document.getElementById('errorMessage');

            if (!name || !cardNumber || !expiry || !cvv) {
                errorMessage.textContent = "All fields are required.";
                errorMessage.style.display = 'block';
                return false;
            }

            if (!/^\d{16}$/.test(cardNumber)) {
                errorMessage.textContent = "Invalid card number.";
                errorMessage.style.display = 'block';
                return false;
            }

            if (!/^\d{3}$/.test(cvv)) {
                errorMessage.textContent = "Invalid CVV.";
                errorMessage.style.display = 'block';
                return false;
            }

            return true;
        }
    </script>


</body>
</html>