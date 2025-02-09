<?php
require 'config/Database.php';
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

class Review {
    public $conn;

    public function __construct() {
        $db = new BDatabase();
        $this->conn = $db->getConnection();
    }

    public function addReview($user_id, $product_id, $rating, $comment) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO reviews (user_id, product_id, rating, comment, created_at) 
                                          VALUES (?, ?, ?, ?, NOW())");
            return $stmt->execute([$user_id, $product_id, $rating, $comment]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getReviewsByProduct($product_id) {
        $stmt = $this->conn->prepare("SELECT users.name, reviews.rating, reviews.comment, reviews.created_at 
                                      FROM reviews 
                                      JOIN users ON reviews.user_id = users.id 
                                      WHERE reviews.product_id = ? ORDER BY reviews.created_at DESC");
        $stmt->execute([$product_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductDetails($product_id) {
        $stmt = $this->conn->prepare("SELECT name, image, description, price FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

$review = new Review();

$product_id = $_GET['product_id'] ?? null;


if (!$product_id) {
    echo "<p class='text-danger text-center'>Error: Product ID is missing.</p>";
    exit();
}


$product = $review->getProductDetails($product_id);


if (!$product) {
    echo "<p class='text-danger text-center'>Error: Product not found.</p>";
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_review'])) {
    $user_id = 1; 
    $rating = $_POST['rating'];
    $comment = htmlspecialchars($_POST['review']);

    if ($review->addReview($user_id, $product_id, $rating, $comment)) {
        header("Location: product-reviews.php?id=$product_id&success=1");
        exit();
    } else {
        echo "<p class='text-danger text-center'>Error submitting review.</p>";
    }
}


$reviews = $review->getReviewsByProduct($product_id);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Product Reviews - Fruitables</title>
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

    <div class="container mt-5">
        
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <img src="asstes/img/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid rounded mb-3" style="max-height: 300px;">
                <h2 class="text-primary"><?php echo htmlspecialchars($product['name']); ?></h2>
                <p class="text-muted"><?php echo htmlspecialchars($product['description']); ?></p>
                <h4 class="text-success">$<?php echo number_format($product['price'], 2); ?></h4>
            </div>
        </div>

        <h3 class="text-center text-primary">Product Reviews</h3>

     
        <div class="row justify-content-center">
            <div class="col-md-8">
                <form action="product-reviews.php?id=<?php echo $product_id; ?>" method="POST" class="p-4 shadow rounded bg-light">
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">

                    <div class="mb-3">
                        <label class="form-label">Your Review:</label>
                        <textarea name="review" class="form-control" rows="4" placeholder="Write your review here..." required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Rating:</label>
                        <select name="rating" class="form-select" required>
                            <option value="5">⭐️⭐️⭐️⭐️⭐️ (Excellent)</option>
                            <option value="4">⭐️⭐️⭐️⭐️ (Good)</option>
                            <option value="3">⭐️⭐️⭐️ (Average)</option>
                            <option value="2">⭐️⭐️ (Poor)</option>
                            <option value="1">⭐️ (Very Bad)</option>
                        </select>
                    </div>

                    <button type="submit" name="submit_review" class="btn btn-primary w-100">Submit Review</button>
                </form>
            </div>
        </div>

        
        <h3 class="mt-5 text-center">User Reviews</h3>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <?php if (!empty($reviews)) : ?>
                    <?php foreach ($reviews as $r) : ?>
                        <div class="p-3 shadow-sm border rounded mb-3">
                            <p><strong><?php echo htmlspecialchars($r['name']); ?></strong> - <?php echo str_repeat("⭐️", $r['rating']); ?></p>
                            <p><?php echo htmlspecialchars($r['comment']); ?></p>
                            <small class="text-muted"><?php echo $r['created_at']; ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p class="text-center">No reviews yet. Be the first to review this product!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

  
    


   <!-- to Do Post review -->


   <!-- End to Do Post review -->





   

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

        <!-- Copyright Start -->
        <div class="container-fluid copyright bg-dark py-4">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        <span class="text-light"><a href="#"><i class="fas fa-copyright text-light me-2"></i>Your Site Name</a>, All right reserved.</span>
                    </div>
                    <div class="col-md-6 my-auto text-center text-md-end text-white">
                        <!--/* This template is free as long as you keep the below author’s credit link/attribution link/backlink. */-->
                        <!--/* If you'd like to use the template without the below author’s credit link/attribution link/backlink, */-->
                        <!--/* you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". */-->
                        Designed By <a class="border-bottom" href="https://htmlcodex.com">HTML Codex</a> Distributed By <a class="border-bottom" href="https://themewagon.com">ThemeWagon</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Copyright End -->



        <!-- Back to Top -->
        <a href="#" class="btn btn-primary border-3 border-primary rounded-circle back-to-top"><i class="fa fa-arrow-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/lib/easing/easing.min.js"></script>
    <script src="assets/lib/waypoints/waypoints.min.js"></script>
    <script src="assets/lib/lightbox/js/lightbox.min.js"></script>
    <script src="assets/lib/owlcarousel/owl.carousel.min.js"></script>
    

    <!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Owl Carousel CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">

<!-- Owl Carousel JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>



    <!-- Template Javascript -->
    <script src="assets/js/main.js"></script>
    <script src="assets/js/cart.js"></script>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
