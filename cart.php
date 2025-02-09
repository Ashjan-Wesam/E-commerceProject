<?php
session_start();

include_once 'config/Database.php';  
include_once 'config/config.php';
include_once 'classes/User.php';      
include_once 'Product.php'; // تم تعديل المسار
include_once 'category.php'; // تم تعديل المسار

// التحقق من سلة التسوق
$_SESSION['cart'] = $_SESSION['cart'] ?? [];

function addToCart($productId) {
    if (!isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] = 1;
    } else {
        $_SESSION['cart'][$productId]++;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    if ($productId) {
        addToCart($productId);
    } else {
        echo "Invalid product ID.";
    }
}

// حساب عدد العناصر في السلة
$cartCount = !empty($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;

// إنشاء الاتصال بقاعدة البيانات
$db = new BDatabase();
$conn = $db->getConnection(); 

$userLoggedIn = false;
$userName = "Guest";
$userEmail = "";
$userAddress = "";

if (isset($_SESSION['user_id'])) {
    $userId = filter_var($_SESSION['user_id'], FILTER_VALIDATE_INT);
    if ($userId) {
        $userLoggedIn = true;
        session_regenerate_id(true); // تأمين الجلسة

        // إنشاء كائن المستخدم
        $userObj = new User($conn);
        $userData = $userObj->getUserById($userId);

        if ($userData) {
            $userName = htmlspecialchars($userData['name']);
            $userEmail = htmlspecialchars($userData['email']);
            $userAddress = htmlspecialchars($userData['address']);
        }
    }
}

$cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];

$cartCount = array_reduce($cart, function($total, $item) {
    return $total + $item['quantity'];
}, 0);

$total_amount = 0;
foreach ($cart as $productId => $item) {
    $total_amount += $item['price'] * $item['quantity'];
}

// عرض المجموع الكلي
echo "Total Amount: $" . number_format($total_amount, 2);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Fruitables - Vegetable Website Template</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">
     <!-- Google Web Fonts -->
     <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@600;800&display=swap" rel="stylesheet"> 

        <!-- Icon Font Stylesheet -->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

        <!-- Libraries Stylesheet -->
        <link href="assets/lib/lightbox/css/lightbox.min.css" rel="stylesheet">
        <link href="assets/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">


        <!-- Customized Bootstrap Stylesheet -->
        <link href="assets/css/bootstrap.min.css" rel="stylesheet">

        <!-- Template Stylesheet -->
        <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" role="status"></div>
    </div>
    <!-- Spinner End -->

    <!-- Navbar Start -->
    <div class="container-fluid fixed-top">
        <div class="container topbar bg-primary d-none d-lg-block">
            <div class="d-flex justify-content-between">
                <div class="top-info ps-2">
                    <small class="me-3">
                        <i class="fas fa-map-marker-alt me-2 text-secondary"></i> 
                        <a href="#" class="text-white"><?= $userLoggedIn ? htmlspecialchars($userAddress) : "123 Street, New York" ?></a>
                    </small>
                    <small class="me-3">
                        <i class="fas fa-envelope me-2 text-secondary"></i>
                        <a href="#" class="text-white"><?= $userLoggedIn ? htmlspecialchars($userEmail) : "Email@Example.com" ?></a>
                    </small>
                </div>
                <div class="top-link pe-2">
                    <a href="#" class="text-white"><small class="text-white mx-2">Privacy Policy</small>/</a>
                    <a href="#" class="text-white"><small class="text-white mx-2">Terms of Use</small>/</a>
                    <a href="#" class="text-white"><small class="text-white ms-2">Sales and Refunds</small></a>
                </div>
            </div>
        </div>
        <div class="container px-0">
            <nav class="navbar navbar-light bg-white navbar-expand-xl">
                <a href="index.php" class="navbar-brand"><h1 class="text-primary display-6">Fruitables</h1></a>
                <button class="navbar-toggler py-2 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars text-primary"></span>
                </button>
                <div class="collapse navbar-collapse bg-white" id="navbarCollapse">
                    <div class="navbar-nav mx-auto">
                        <a href="index.php" class="nav-item nav-link active">Home</a>
                        <a href="shop2.php" class="nav-item nav-link">Shop</a>
                        <a href="packages.php" class="nav-item nav-link">Package</a>
            
                        <a href="contact.html" class="nav-item nav-link">Contact</a>
                    </div>
                    <div class="d-flex m-3 me-0">
                        <button class="btn-search btn border border-secondary btn-md-square rounded-circle bg-white me-4" data-bs-toggle="modal" data-bs-target="#searchModal"><i class="fas fa-search text-primary"></i></button>
                        <a href="cart.php" class="position-relative me-4 my-auto">
    <i class="fa fa-shopping-bag fa-2x"></i>
    <span id="cart-count" class="position-absolute bg-secondary rounded-circle d-flex align-items-center justify-content-center text-dark px-1"
          style="top: -5px; left: 15px; height: 20px; min-width: 20px;">
        0
    </span>
</a>



                        <div class="dropdown my-auto">
                            <?php if ($userLoggedIn): ?>
                                <button class="btn btn-primary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <?= htmlspecialchars($userName) ?>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="userDropdown">
                                    <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i> Profile</a></li>
                                    <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                                </ul>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-primary"><i class="fas fa-sign-in-alt me-2"></i> Login</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- Navbar End -->


    <!-- Single Page Header Start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Cart</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Cart</li>
        </ol>
    </div>
    <!-- Single Page Header End -->

        
<!-- Cart Page Start -->
<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Products</th>
                        <th scope="col">Name</th>
                        <th scope="col">Price</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Total</th>
                        <th scope="col">Handle</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($cart)) : ?>
                        <?php foreach ($cart as $productId => $quantity) :
                            $product = $productObj->getProductById($productId);
                            if (!$product) continue;

                            $productTotal = $product['price'] * $quantity;
                            $total_amount += $productTotal;
                        ?>
                            <tr>
                                <th scope="row">
                                    <div class="d-flex align-items-center">
                                        <img src="<?= htmlspecialchars($product['image']) ?>" class="img-fluid me-5 rounded-circle" style="width: 80px; height: 80px;" alt="">
                                    </div>
                                </th>
                                <td>
                                    <p class="mb-0 mt-4"><?= htmlspecialchars($product['name']) ?></p>
                                </td>
                                <td>
                                    <p class="mb-0 mt-4">$<?= number_format($product['price'], 2) ?></p>
                                </td>
                                <td>
                                    <div class="input-group quantity mt-4" style="width: 100px;">
                                        <form method="POST" action="update_cart.php">
                                            <input type="hidden" name="product_id" value="<?= $productId ?>">
                                            <input type="hidden" name="action" value="decrease">
                                            <button type="submit" class="btn btn-sm btn-minus rounded-circle bg-light border">
                                                <i class="fa fa-minus"></i>
                                            </button>
                                        </form>

                                        <input type="text" class="form-control form-control-sm text-center border-0" value="<?= $quantity ?>" readonly>

                                        <form method="POST" action="update_cart.php">
                                            <input type="hidden" name="product_id" value="<?= $productId ?>">
                                            <input type="hidden" name="action" value="increase">
                                            <button type="submit" class="btn btn-sm btn-plus rounded-circle bg-light border">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                <td>
                                    <p class="mb-0 mt-4">$<?= number_format($productTotal, 2) ?></p>
                                </td>
                                <td>
                                    <form method="POST" action="update_cart.php">
                                        <input type="hidden" name="product_id" value="<?= $productId ?>">
                                        <input type="hidden" name="action" value="remove">
                                        <button type="submit" class="btn btn-md rounded-circle bg-light border mt-4">
                                            <i class="fa fa-times text-danger"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="6" class="text-center">Your cart is empty</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="row g-4 justify-content-end">
            <div class="col-sm-8 col-md-7 col-lg-6 col-xl-4">
                <div class="bg-light rounded">
                    <div class="p-4">
                        <h1 class="display-6 mb-4">Cart <span class="fw-normal">Total</span></h1>
                        <div class="d-flex justify-content-between mb-4">
                            <h5 class="mb-0 me-4">Subtotal:</h5>
                            <p class="mb-0">$<?= number_format($total_amount, 2) ?></p>
                        </div>
                        <div class="d-flex justify-content-between">
                            <h5 class="mb-0 me-4">Shipping</h5>
                            <p class="mb-0">$3.00</p>
                        </div>
                        <p class="mb-0 text-end">Shipping to your location.</p>
                    </div>
                    <div class="py-4 mb-4 border-top border-bottom d-flex justify-content-between">
                        <h5 class="mb-0 ps-4 me-4">Total</h5>
                        <p class="mb-0 pe-4">$<?= number_format($total_amount + 3, 2) ?></p>
                    </div>
                    <button class="btn border-secondary rounded-pill px-4 py-3 text-primary text-uppercase mb-4 ms-4" type="button">Proceed to Checkout</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Cart Page End -->


    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-white-50 footer pt-5 mt-5">
            <div class="container py-5">
                <div class="pb-4 mb-4" style="border-bottom: 1px solid rgba(226, 175, 24, 0.5) ;">
                    <div class="row g-4">
                        <div class="col-lg-3">
                            <a href="#">
                                <h1 class="text-primary mb-0">Fruitables</h1>
                                <p class="text-secondary mb-0">Fresh products</p>
                            </a>
                        </div>
                        <div class="col-lg-6">
                            <div class="position-relative mx-auto">
                                <input class="form-control border-0 w-100 py-3 px-4 rounded-pill" type="number" placeholder="Your Email">
                                <button type="submit" class="btn btn-primary border-0 border-secondary py-3 px-4 position-absolute rounded-pill text-white" style="top: 0; right: 0;">Subscribe Now</button>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="d-flex justify-content-end pt-3">
                                <a class="btn  btn-outline-secondary me-2 btn-md-square rounded-circle" href=""><i class="fab fa-twitter"></i></a>
                                <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" href=""><i class="fab fa-facebook-f"></i></a>
                                <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" href=""><i class="fab fa-youtube"></i></a>
                                <a class="btn btn-outline-secondary btn-md-square rounded-circle" href=""><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-5">
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-item">
                            <h4 class="text-light mb-3">Why People Like us!</h4>
                            <p class="mb-4">typesetting, remaining essentially unchanged. It was 
                                popularised in the 1960s with the like Aldus PageMaker including of Lorem Ipsum.</p>
                            <a href="" class="btn border-secondary py-2 px-4 rounded-pill text-primary">Read More</a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="d-flex flex-column text-start footer-item">
                            <h4 class="text-light mb-3">Shop Info</h4>
                            <a class="btn-link" href="">About Us</a>
                            <a class="btn-link" href="">Contact Us</a>
                            <a class="btn-link" href="">Privacy Policy</a>
                            <a class="btn-link" href="">Terms & Condition</a>
                            <a class="btn-link" href="">Return Policy</a>
                            <a class="btn-link" href="">FAQs & Help</a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="d-flex flex-column text-start footer-item">
                            <h4 class="text-light mb-3">Account</h4>
                            <a class="btn-link" href="">My Account</a>
                            <a class="btn-link" href="">Shop details</a>
                            <a class="btn-link" href="">Shopping Cart</a>
                            <a class="btn-link" href="">Wishlist</a>
                            <a class="btn-link" href="">Order History</a>
                            <a class="btn-link" href="">International Orders</a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-item">
                            <h4 class="text-light mb-3">Contact</h4>
                            <p>Address: 1429 Netus Rd, NY 48247</p>
                            <p>Email: Example@gmail.com</p>
                            <p>Phone: +0123 4567 8910</p>
                            <p>Payment Accepted</p>
                            <img src="img/payment.png" class="img-fluid" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <!-- Footer End -->


    

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
        let subtotal = 0;

        if (cart.length > 0) {
            cart.forEach(item => {
                const total = item.price * item.quantity;
                cartItemsHtml += `
                    <tr data-id="${item.id}">
                        <th scope="row">
                            <div class="d-flex align-items-center">
                                <img src="${item.image}" class="img-fluid me-5 rounded-circle" style="width: 80px; height: 80px;" alt="${item.name}">
                            </div>
                        </th>
                        <td><p class="mb-0 mt-4">${item.name}</p></td>
                        <td><p class="mb-0 mt-4">$${item.price}</p></td>
                        <td>
                            <div class="input-group quantity mt-4" style="width: 100px;">
                                <div class="input-group-btn">
                                    <button class="btn btn-sm btn-minus rounded-circle bg-light border decrease-quantity">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                                <input type="text" class="form-control form-control-sm text-center border-0" value="${item.quantity}">
                                <div class="input-group-btn">
                                    <button class="btn btn-sm btn-plus rounded-circle bg-light border increase-quantity">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </td>
                        <td><p class="mb-0 mt-4">$${total}</p></td>
                        <td>
                            <button class="btn btn-md rounded-circle bg-light border mt-4 remove-item">
                                <i class="fa fa-times text-danger"></i>
                            </button>
                        </td>
                    </tr>
                `;
                subtotal += total;
            });
        } else {
            cartItemsHtml = `<tr><td colspan="6"><p>Your cart is empty</p></td></tr>`;
        }

        document.getElementById('cart-items').innerHTML = cartItemsHtml;
        updateTotal(subtotal);
    }

    function updateTotal(subtotal) {
        const shippingCost = 3; // Fixed shipping cost
        const total = subtotal + shippingCost;
        document.getElementById('subtotal').innerText = `$${subtotal.toFixed(2)}`;
        document.getElementById('total').innerText = `$${total.toFixed(2)}`;
    }

    $(document).on('click', '.remove-item', function() {
        const productId = $(this).closest('tr').data('id');
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        cart = cart.filter(item => item.id != productId);
        localStorage.setItem('cart', JSON.stringify(cart));
        loadCart();
    });

    $(document).on('click', '.decrease-quantity', function() {
        const productId = $(this).closest('tr').data('id');
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        cart = cart.map(item => {
            if (item.id == productId && item.quantity > 1) {
                item.quantity -= 1;
            }
            return item;
        });
        localStorage.setItem('cart', JSON.stringify(cart));
        loadCart();
    });

    $(document).on('click', '.increase-quantity', function() {
        const productId = $(this).closest('tr').data('id');
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        cart = cart.map(item => {
            if (item.id == productId) {
                item.quantity += 1;
            }
            return item;
        });
        localStorage.setItem('cart', JSON.stringify(cart));
        loadCart();
    });

    $('#proceed-checkout').click(function() {
        alert("Proceeding to checkout...");
    });
</script>

    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/lib/easing/easing.min.js"></script>
    <script src="assets/lib/waypoints/waypoints.min.js"></script>
    <script src="assets/lib/lightbox/js/lightbox.min.js"></script>
    <script src="assets/lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="assets/js/main.js"></script>
    <script src="assets/js/cart.js"></script>
</body>
</html>