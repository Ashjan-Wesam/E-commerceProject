<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="hamzeh.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.7/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>   
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.7/js/materialize.min.js"></script>
    <!-- css -->
     <link rel="stylesheet" href="../assets/css/dashboard.css">

    <!-- js -->
     <script src="../assets/js/reviews.js" defer></script>

</head>

<body>
    <?php require "../classes/admin.php";
        $admin = new Admin();
        session_start();
    ?>

    <!-- header start  -->
    <nav>
        <div class="nav-wrapper indigo">
            <a href="#" class="brand-logo center">Reviews</a>
            <a href="#" data-activates="slide-out" class="button-collapse show-on-large left">
                <i class="material-icons">menu</i>
            </a>
        </div>
    </nav>

    <ul id="slide-out" class="side-nav">
        <li class="center no-padding">
            <div class="user-info">
                <img src="https://www.w3schools.com/howto/img_avatar.png" alt="Admin Avatar" class="circle responsive-img" />
                <h5><?php echo $_SESSION['fullname'] ?></h5>
            </div>
        </li>
        <li><a href="dash.php"><i class="material-icons">dashboard</i>Dashboard</a></li>
        <li><a href="product.php"><i class="material-icons">store</i>Products</a></li>  
        <li><a href="orders.php"><i class="material-icons">assignment</i>Orders</a></li>
        <li><a href="users.php"><i class="material-icons">people</i>Customers</a></li>
        <li><a href="categories.php"><i class="material-icons">category</i>Categories</a></li>
        <li><a href="reviews.php"><i class="material-icons">category</i>Reviews</a></li>
        <li><a href="subscriptions.php"><i class="material-icons">category</i>subscription</a></li>
        <li><a href="../logout.php"><i class="material-icons">exit_to_app</i>Logout</a></li>


    </ul>
    <!-- end of header -->

    <div class="row">

        <!-- search area -->
         <div class="col border border-5">
            <section class="main">
            <div class="d-flex">
                <!-- <form action="" class="m-auto mt-3 review-search d-flex" id="search-form">
                    <label for="search" class="form-label" hidden>search </label>
                    <input type="text" name="search" id="search" placeholder="search for a product" class="form-control me-2">
                    <button type="submit" class="btn" id="search-btn">Search</button>
                </form> -->
        <!-- End of search  -->
            </div>
                <?php 

                    $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
                    $reviews = $admin->getReviews($searchTerm);

                    if(count($reviews) > 0) {
                        echo "<table class='res-table'>
                            <tr>
                                <td>Product name</td>
                                <td>Comment</td>
                                <td>rating</td>
                                <td>User name</td>
                                <td>Email</td>
                                <td>Actions</td>
                            </tr>";


                            foreach($reviews as $review) {
                                echo "<tr>
                                    <td>{$review['product_name']}</td>
                                    <td>{$review['comment']}</td>
                                    <td>{$review['rating']}</td>
                                    <td>{$review['name']}</td>
                                    <td>{$review['email']}</td>
                                    <td><a href='crud.php?id={$review["id"]}&operation=delete&table=reviews' class='operation delete'>delete</a></td>
                                </tr>";
                            }
                        echo "</table>";
                    }
                ?>
            </section>
         </div>
    </div>

    <script>
        $(document).ready(function(){
            $(".button-collapse").sideNav();
        });
    </script>
    
</body>
</html>