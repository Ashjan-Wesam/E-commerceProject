<?php

// require "../classes/SQLHelper.php";
require "../classes/admin.php";

// $admin = new Admin($db);
$admin = new Admin();
// $sql = new SQLHelper();
$totalIncome = $admin->getTotalIncome();
$totalCategories = $admin->getTotalCategories();
$totalOrders = $admin->getTotalOrders();
$totalCustomers = $admin->getTotalCustomers();
$dailyRevenue = $admin->getDailyRevenue();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="hamzeh.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.7/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>   
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.7/js/materialize.min.js"></script>
 </head>
<body class="admin">
    <nav>
        <div class="nav-wrapper indigo">
            <a href="#" class="brand-logo center">Admin Dashboard</a>
            <a href="#" data-activates="slide-out" class="button-collapse show-on-large left">
                <i class="material-icons">menu</i>
            </a>
        </div>
    </nav>

    <ul id="slide-out" class="side-nav">
        <li class="center no-padding">
            <div class="user-info">
                <img src="https://www.w3schools.com/howto/img_avatar.png" alt="Admin Avatar" class="circle responsive-img" />
                <h5>Admin</h5>
            </div>
        </li>
        <li><a href="#dashboard"><i class="material-icons">dashboard</i>Dashboard</a></li>
        <li><a href="product.php"><i class="material-icons">store</i>Products</a></li>  
        <li><a href="orders.php"><i class="material-icons">assignment</i>Orders</a></li>
        <li><a href="users.php"><i class="material-icons">people</i>Customers</a></li>
        <li><a href="categories.php"><i class="material-icons">category</i>Categories</a></li>
        <li><a href="reviews.php"><i class="material-icons">category</i>Reviews</a></li>
        <li><a href="subscriptions.php"><i class="material-icons">category</i>subscription</a></li>
        <li><a href="../logout.php"><i class="material-icons">exit_to_app</i>Logout</a></li>


    </ul>

    <main class="container">
        <div class="row" style="margin-top: 20px;" >
            <div class="col s12 m6 l3" >
                <div class="card teal lighten-2">
                    <div class="card-content white-text center">
                        <i class="material-icons large">attach_money</i>
                        <h5>Total Income</h5>
                        <p>$<?php echo number_format($totalIncome, 2); ?></p>
                    </div>
                </div>
            </div>
            <div class="col s12 m6 l3">
                <div class="card orange lighten-1">
                    <div class="card-content white-text center">
                        <i class="material-icons large">category</i>
                        <h5>Categories</h5>
                        <p><?php echo $totalCategories; ?></p>
                    </div>
                </div>
            </div>
            <div class="col s12 m6 l3">
                <div class="card green lighten-1">
                    <div class="card-content white-text center">
                        <i class="material-icons large">shopping_cart</i>
                        <h5>Orders</h5>
                        <p><?php echo $totalOrders; ?></p>
                    </div>
                </div>
            </div>
            <div class="col s12 m6 l3">
                <div class="card blue lighten-1">
                    <div class="card-content white-text center">
                        <i class="material-icons large">people</i>
                        <h5>Customers</h5>
                        <p><?php echo $totalCustomers; ?></p>
                    </div>
                </div>
            </div>
        </div>

   
        <div class="card">
            <div class="card-content">
                <h5>Daily Revenue</h5>
                <table class="striped responsive-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Orders</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
    if (empty($dailyRevenue)) {
        echo "<tr><td colspan='3' class='center'>No revenue data available.</td></tr>";
    } else {
        foreach ($dailyRevenue as $row) {
            echo "<tr>
                    <td>" . date('Y-m-d', strtotime($row['order_day'])) . "</td>
                    <td>" . $row['total_orders'] . "</td>
                    <td>$" . number_format($row['total_revenue'], 2) . "</td>
                </tr>";
        }
    }
    ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <script>
        $(document).ready(function(){
            $(".button-collapse").sideNav();
        });
    </script>
</body>
</html>
