<?php
require 'config/Database.php'; 


if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    $db = new BDatabase();
    $conn = $db->getConnection();

    $product_id = trim($_POST['product_id']);
    $user_id = trim($_POST['user_id']); 
    $comment = trim(htmlspecialchars($_POST['review'])); 
    $rating = intval($_POST['rating']); 

    try {

        $stmt = $conn->prepare("INSERT INTO reviews (user_id, product_id, rating, comment, created_at) 
                                VALUES (?, ?, ?, ?, NOW())");
        
        if ($stmt->execute([$user_id, $product_id, $rating, $comment])) {
            header("Location: product-detail.php?id=" . $product_id . "&success=1");
            exit();
        } else {
            echo "Error submitting review.";
        }
    } catch (PDOException $e) {
        echo "Database Error: " . $e->getMessage();
    }
}
?>

