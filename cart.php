<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .cart-item {
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }

        .cart-item img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            margin-right: 10px;
        }

        .cart-item .details {
            flex-grow: 1;
        }

        .cart-item .details h5 {
            margin: 0;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2>Your Shopping Cart</h2>
    <div id="cart-items">
        <!-- المنتجات ستظهر هنا -->
    </div>

    <div class="d-flex justify-content-between mt-4">
        <button id="clear-cart" class="btn btn-danger">Clear Cart</button>
        <button id="checkout" class="btn btn-success">Checkout</button>
    </div>
</div>

<!-- Script -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        loadCart();
    });

    function loadCart() {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        displayCartItems(cart);
    }

    function displayCartItems(cart) {
        let cartItemsHtml = '';
        if (cart.length > 0) {
            cart.forEach(item => {
                cartItemsHtml += `
                    <div class="cart-item d-flex align-items-center">
                        <img src="${item.image}" alt="${item.name}">
                        <div class="details">
                            <h5>${item.name}</h5>
                            <p>$${item.price} x ${item.quantity}</p>
                        </div>
                        <button class="btn btn-danger btn-sm remove-item" data-id="${item.id}">Remove</button>
                    </div>
                `;
            });
        } else {
            cartItemsHtml = `<p>Your cart is empty</p>`;
        }
        document.getElementById('cart-items').innerHTML = cartItemsHtml;
    }

    $(document).on('click', '.remove-item', function() {
        const productId = $(this).data('id');
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        cart = cart.filter(item => item.id != productId);
        localStorage.setItem('cart', JSON.stringify(cart));
        loadCart();
    });

    $('#clear-cart').click(function() {
        localStorage.removeItem('cart');
        loadCart();
    });
</script>

</body>
</html>
