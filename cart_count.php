<?php
// جلب السلة من الـ cookies
$cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];

// حساب العدد الإجمالي
$cartCount = array_reduce($cart, function($total, $item) {
    // return $total + $item['quantity'];
    return $total + 1;
}, 0);

// طباعة العدد
echo $cartCount;
?>
