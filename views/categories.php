<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="hamzeh.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.7/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>   
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.7/js/materialize.min.js"></script>

    <!-- css -->
     <link rel="stylesheet" href="../assets/css/dashboard.css?v=2">


</head>

<body>
    <?php require "../classes/admin.php";
        session_start();
        $admin = new Admin();

        if(!isset($_SESSION['user_id'])) {
            header("Location: ../forbidden.php");
            exit();
        }
    ?>

    <!-- header start  -->
    <nav>
        <div class="nav-wrapper indigo">
            <a href="#" class="brand-logo center">Categories</a>
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

         <div class="col border border-5">
            <section class="main">
                <?php 
                
                    $categories = $admin->getCategories();
                    if(count($categories) > 0) {
                        echo "<table class='res-table' >
                            <tr>
                                <td>name</td>
                                <td>Description</td>
                                <td>Created at</td>
                                <td>Update date</td>
                                <td>Actions</td>
                            </tr>";


                            foreach($categories as $category) {
                                echo "<tr>
                                    <td>{$category['name']}</td>
                                    <td>{$category['description']}</td>
                                    <td>{$category['created_at']}</td>
                                    <td>{$category['updated_at']}</td>
                                    <td><a href='crud.php?id={$category["id"]}&operation=delete&table=categories' class='operation delete'>Delete</a>
                                        <a href='crud.php?id={$category["id"]}&operation=update&table=categories' class='operation update'>Update</a>
                                    </td>
                                </tr>";
                            }
                        echo "</table>";
                    }
                ?>

                    <a href="crud.php?operation=insert&table=categories">
                        <button class="btn btn-success">Add new category</button>
                    </a>

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