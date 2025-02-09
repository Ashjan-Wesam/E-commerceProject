<?php
class Product {
    private $conn;
    public function __construct() {
        $this->conn = new mysqli("localhost", "root", "", "e-commerce");
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }
  
    public function getProducts() {
        $current_time = date("Y-m-d H:i:s");
        $sql = "SELECT products.*, 
                categories.name AS category,
                CASE 
                    WHEN discount_price IS NOT NULL AND discount_start <= ? AND discount_end >= ? 
                    THEN discount_price 
                    ELSE price 
                END AS final_price 
                FROM products 
                LEFT JOIN categories ON products.category_id = categories.id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $current_time, $current_time);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getProductById($id) {
        $sql = "SELECT * FROM products WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function addProduct($name, $description, $price, $category_id, $image, $discount_price = null, $discount_start = null, $discount_end = null) {
        $imagePath = "../assets/images/" . basename($image["name"]);
        move_uploaded_file($image["tmp_name"], $imagePath);

        $sql = "INSERT INTO products (name, description, price, image, category_id, discount_price, discount_start, discount_end, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssdsdsss", $name, $description, $price, $imagePath, $category_id, $discount_price, $discount_start, $discount_end);
        return $stmt->execute();
    }

    public function updateProduct($id, $name, $description, $price, $category_id, $image, $discount_price = null, $discount_start = null, $discount_end = null) {
        if ($image["name"]) {
            $imagePath = "../assets/images/" . basename($image["name"]);
            move_uploaded_file($image["tmp_name"], $imagePath);
            $sql = "UPDATE products SET name=?, description=?, price=?, image=?, category_id=?, discount_price=?, discount_start=?, discount_end=?, updated_at=NOW() WHERE id=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssdsssssi", $name, $description, $price, $imagePath, $category_id, $discount_price, $discount_start, $discount_end, $id);
        } else {
            $sql = "UPDATE products SET name=?, description=?, price=?, category_id=?, discount_price=?, discount_start=?, discount_end=?, updated_at=NOW() WHERE id=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssdsdsssi", $name, $description, $price, $category_id, $discount_price, $discount_start, $discount_end, $id);
        }
        return $stmt->execute();
    }

    public function deleteProduct($id) {
        $sql = "DELETE FROM products WHERE id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getCategories() {
        $sql = "SELECT * FROM categories";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->get_result();
    }

    
}

session_start();

if(!isset($_SESSION['user_id'])) {
    header("Location: ../forbidden.php");
    exit();
}
$productObj = new Product();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["add"])) {
        if ($productObj->addProduct($_POST["name"], $_POST["description"], $_POST["price"], $_POST["category_id"], $_FILES["image"], $_POST["discount_price"], $_POST["discount_start"], $_POST["discount_end"])) {
            $message = "Product added successfully!";
        } else {
            $message = "Failed to add product.";
        }
    } elseif (isset($_POST["edit"])) {
        if ($productObj->updateProduct($_POST["id"], $_POST["name"], $_POST["description"], $_POST["price"], $_POST["category_id"], $_FILES["image"], $_POST["discount_price"], $_POST["discount_start"], $_POST["discount_end"])) {
            $message = "Product updated successfully!";
        } else {
            $message = "Failed to update product.";
        }
    } elseif (isset($_POST["delete"])) {
        if ($productObj->deleteProduct($_POST["id"])) {
            $message = "Product deleted successfully!";
        } else {
            $message = "Failed to delete product.";
        }
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["action"]) && $_GET["action"] == "getProduct" && isset($_GET["id"])) {
    $product = $productObj->getProductById($_GET["id"]);
    header('Content-Type: application/json');
    echo json_encode($product);
    exit;
}

$products = $productObj->getProducts();
?>

<!DOCTYPE html>
<html>
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.7/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>   
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.7/js/materialize.min.js"></script>
    <link rel="stylesheet" href="hamzeh.css">

    <title>Products</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h2 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f8f8f8;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        img {
            width: 50px;
            height: auto;
        }
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        input[type="text"], textarea, input[type="number"], select, input[type="file"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            width: 100%;
            background-color: #4CAF50;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .actions form {
            display: inline-block;
            margin: 0;
        }
        .actions input[type="submit"] {
            background-color: #f44336;
            padding: 8px 12px;
            font-size: 14px;
        }
        .actions input[type="submit"]:hover {
            background-color: #d32f2f;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            width: 400px;
        }
        .close {
            float: right;
            cursor: pointer;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<nav>
        <div class="nav-wrapper indigo">
            <a href="#" class="brand-logo center">Products</a>
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
    <h2>Product List</h2>
    <?php if (isset($message)): ?>
        <p><?= $message ?></p>
    <?php endif; ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Original Price</th>
            <th>Discount percentage</th>
            <th>Discount Period</th>
            <th>Image</th>
            <th>Category</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $products->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['name'] ?></td>
            <td><?= $row['description'] ?></td>
            <td>$<?= $row['price'] ?></td>
            <td><?= ($row['discount_price']) ? "%".$row['discount_price'] : "No Discount" ?></td>
            <td><?= ($row['discount_price']) ? "From: " . $row['discount_start'] . "<br> To: " . $row['discount_end'] : "N/A" ?></td>
            <td><img src="<?= $row['image'] ?>" alt="Product Image" style="width: 50px; height: auto;"></td>
            <td><?= $row['category'] ?></td>
            <td class="actions">
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <input type="submit" name="delete" value="Delete">
                </form>
                <button onclick="openEditModal(<?= $row['id'] ?>)">Edit</button>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h2>Add Product</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Product Name" required>
        <textarea name="description" placeholder="Description" required></textarea>
        <input type="number" step="0.01" name="price" placeholder="Price" required>
        <input type="file" name="image" required>
        <select name="category_id" required>
            <?php 
            $categories = $productObj->getCategories(); 
            while ($category = $categories->fetch_assoc()) {
                echo "<option value='" . $category['id'] ."'>" . $category['name'] . "</option>";
            }
            ?>
        </select>
        <h3>Discount (Optional)</h3>
        <input type="number" step="0.01" name="discount_price" placeholder="Discount Price" oninput="calculateDiscount()">
        <input type="datetime-local" name="discount_start">
        <input type="datetime-local" name="discount_end">
        <input type="submit" name="add" value="Add Product">
    </form>

    <!-- Edit Product Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit Product</h2>
            <form id="editForm" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" id="editId">
                <input type="text" name="name" id="editName" placeholder="Product Name" required>
                <textarea name="description" id="editDescription" placeholder="Description" required></textarea>
                <input type="number" step="0.01" name="price" id="editPrice" placeholder="Price" required oninput="calculateDiscount()">
                <div>
                    <img id="editImagePreview" src="" alt="Current Image" style="width: 50px; height: auto;">
                    <input type="file" name="image" id="editImage">
                </div>
                <select name="category_id" id="editCategoryId" required>
                    <?php 

                    ?>
                    <option value="1">Category 1</option>
                    <option value="2">Category 2</option>
                </select>
                <h3>Discount (Optional)</h3>
                <input type="number" step="0.01" name="discount_price" id="editDiscountPrice" placeholder="Discount Price" oninput="calculateDiscount()">
                <input type="datetime-local" name="discount_start" id="editDiscountStart">
                <input type="datetime-local" name="discount_end" id="editDiscountEnd">
                <input type="submit" name="edit" value="Update Product">
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id) {
            fetch(`?action=getProduct&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('editId').value = data.id;
                    document.getElementById('editName').value = data.name;
                    document.getElementById('editDescription').value = data.description;
                    document.getElementById('editPrice').value = data.price;
                    document.getElementById('editCategoryId').value = data.category_id;
                    document.getElementById('editDiscountPrice').value = data.discount_price || "";
                    document.getElementById('editDiscountStart').value = data.discount_start || "";
                    document.getElementById('editDiscountEnd').value = data.discount_end || "";
                    document.getElementById('editImagePreview').src = data.image;
                    document.getElementById('editModal').style.display = 'flex';
                });
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function calculateDiscount() {
            const price = parseFloat(document.getElementById('editPrice').value);
            const discount = parseFloat(document.getElementById('editDiscountPrice').value) || 0;
            const discountedPrice = price - discount;
            document.getElementById('editDiscountPrice').value = discountedPrice.toFixed(2);
        }
    </script>

    <script>
        $(document).ready(function(){
            $(".button-collapse").sideNav();
        });
    </script>
</body>
</html>