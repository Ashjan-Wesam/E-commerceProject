<?php
include_once 'config/Database.php';
include_once 'config/config.php';
include_once 'classes/User.php';      
include_once 'Product.php';
include_once 'category.php';
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

function addToCart($productId) { 
    $_SESSION['cart'][$productId] = ($_SESSION['cart'][$productId] ?? 0) + 1;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $productId = $_POST['product_id'];
    addToCart($productId);
}

$cartCount = array_sum($_SESSION['cart']);

$db = new BDatabase();
$conn = $db->getConnection(); 

$userLoggedIn = false;
$userName = "Guest";
$userEmail = "";
$userAddress = "";

if (isset($_SESSION['user_id'])) {
    $userLoggedIn = true;

    $userObj = new User();
    $userData = $userObj->getUserById($_SESSION['user_id']);

    if ($userData) {
        $userName = $userData['name'];
        $userEmail = $userData['email'];
        $userAddress = $userData['address'];
    }
}






$db = new BDatabase();
$conn = $db->getConnection();

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    $stmt = $conn->prepare("SELECT p.*, c.name AS category_name 
                            FROM products p 
                            JOIN categories c ON p.category_id = c.id 
                            WHERE p.id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        die("Product not found!");
    }

    $review_stmt = $conn->prepare("SELECT r.*, u.name FROM reviews r INNER JOIN users u ON r.user_id = u.id WHERE product_id = ? ORDER BY created_at DESC");
    $review_stmt->execute([$product_id]);
    $reviews = $review_stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    die("No product selected!");
}
?>



<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <title>Fruitables - Vegetable Website Template</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <meta content="" name="keywords">
        <meta content="" name="description">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@600;800&display=swap" rel="stylesheet"> 
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
        <div id="spinner" class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50  d-flex align-items-center justify-content-center">
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
                        <a href="index.php" class="nav-item nav-link ">Home</a>
                        <a href="shop2.php" class="nav-item nav-link">Shop</a>
                        <a href="packages.php" class="nav-item nav-link">Package</a>
            
                        <a href="contact.php" class="nav-item nav-link">Contact</a>
                    </div>
                    <div class="d-flex m-3 me-0">
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

        <div class="d-flex border border-5 justify-content-center align-items-center ">
            
            <!-- Single Product Start -->
            <div class="container-fluid py-5 mt-5">
                <div class="container py-5">
                    <div class="row g-4 ">
                        <div class="col-lg-8 col-xl-9">
                            <div class="row g-4">
                               
    <div class="container-fluid py-5 mt-5">
        <div class="container py-5">
            <div class="row g-4 mb-5">
                <div class="col-lg-8 col-xl-9">
                <div class="row g-4">
        <div class="col-lg-6">
            <div class="border rounded">
                <img src="<?php echo htmlspecialchars($product['image']); ?>" class="img-fluid rounded" alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
        </div>
        <div class="col-lg-6">
            <h4 class="fw-bold mb-3"><?php echo htmlspecialchars($product['name']); ?></h4>
            <p class="mb-3">Category: <?php echo htmlspecialchars($product['category_name']); ?></p>
            <h5 class="fw-bold mb-3">$<?php echo htmlspecialchars($product['price']); ?></h5>
            <p class="mb-4"><?php echo htmlspecialchars($product['description']); ?></p>
    
            <form action="add_to_cart.php" method="POST">
                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                <input type="hidden" name="name" value="<?php echo htmlspecialchars($product['name']); ?>">
                <input type="hidden" name="price" value="<?php echo htmlspecialchars($product['price']); ?>">
                <input type="hidden" name="image" value="<?php echo htmlspecialchars($product['image']); ?>">
    
                <button type="submit" class="btn border border-secondary rounded-pill px-4 py-2 mb-4 text-primary add-to-casrt">
                    <i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart
                </button>
            </form>
        </div>
    </div>
    
    
                    <h4 class="mb-4 fw-bold">Reviews</h4>
                    <?php if ($reviews): ?>
                        <?php foreach ($reviews as $review): ?>
                            <div class="d-flex">
                                <!-- <img src="img/avatar.jpg" class="img-fluid rounded-circle p-3" style="width: 80px; height: 80px;" alt=""> -->
                                <div>
                                    <p class="mb-2 text-muted" style="font-size: 14px;"><?php echo $review['created_at']; ?></p>
                                    <h5><?php echo htmlspecialchars($review['name']); ?></h5>
                                    <p><?php echo htmlspecialchars($review['comment']); ?></p>
                                    <p>Rating: <?php echo str_repeat("⭐", $review['rating']); ?></p>
                                    <hr>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No reviews yet.</p>
                    <?php endif; ?>
    
                    <form action="submit_review.php" method="POST">
                        <h4 class="mb-4 fw-bold">Leave a Review</h4>
                        <input type="text" value="<?php echo $_SESSION['user_id']; ?>" name="user_id" hidden>
                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                        <div class="mb-3">
                            <textarea name="review" class="form-control" rows="4" placeholder="Your Review *" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Rating:</label>
                            <select name="rating" class="form-select" required>
                                <option value="5">⭐️⭐️⭐️⭐️⭐️</option>
                                <option value="4">⭐️⭐️⭐️⭐️</option>
                                <option value="3">⭐️⭐️⭐️</option>
                                <option value="2">⭐️⭐️</option>
                                <option value="1">⭐️</option>
                            </select>
                        </div>
                       <button type="submit" class="btn btn-primary">Submit Review</button>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
    
                              
                            </div>
                        </div> 
                    </div>
            </div>
        </div>   
    </div>            
        <!-- Single Product End -->

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