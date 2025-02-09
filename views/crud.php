<?php 
    
    require_once '../classes/admin.php';
    require_once '../classes/SQLHelper.php';

    $admin = new Admin();
    $sql = new SQLHelper();

    $operation = $_GET['operation'];
    $table = $_GET['table'];

    if($operation == 'delete') {
        $id = $_GET['id'];
        $admin->delteRecord($table, $id);
    }

    if($operation == 'update' && $table == 'categories') {
        $id = $_GET['id'];

        $data = $sql->getData($table, $id)[0]; /* [0] to ensure that the first element is returned */
        echo '<link rel="stylesheet" type="text/css" href="../assets/css/form.css">';
        echo '<div class="flex">
        
            <form action="" method="POST" class="form" onsubmit="return validateForm()">
            <input name="id" type="text" value="' . $data["id"] . '" hidden>
            <input id="name" name="name" type="text" value="' . $data["name"] . '">
            <br>
            <input id="description" name="description" type="text" value="' . $data["description"] . '" >
            <br>
            <input name="created_at" type="text" value="' . $data["created_at"] . '" disabled>
            <input name="updated_at" type="text" value="' . $data["updated_at"] . '" disabled>
            <button type="submit" name="update">Update</button>
            </form>
            <div class="error">';
                // for the back end validation
                if(isset($_POST["update"])) {
                $result = $admin->updateCategory($_POST['id'], $_POST['name'], $_POST['description'] );

                if (!$result) {
                    echo '<p>Error: can\'t have empty fields</p>';
                }
                }
            
            echo '</div>
        </div>
        <script>
            
            function validateForm() {
            let name = document.getElementById("name").value;
            let description = document.getElementById("description").value;
            if (name == "" || description == "") {
                alert("can\'t have empty fields");
                return false;
            }
            return true;
            }
        </script>';

    }

    if($operation == 'update' && $table == 'orders') {
        $id = $_GET['id'];

        $data = $sql->getData($table, $id)[0];
        
        echo '<link rel="stylesheet" type="text/css" href="../assets/css/form.css">';
        echo '<div class="flex">
                <form action="" method="POST" class="form">
                    <input name="id" type="text" value="' . $data["id"] . '" hidden >
                    <input name="total" type="text" value="' . $data["total_amount"]  . '" disabled>
                    <select name="status" value="' . $data["status"]  . '">
                        <option value="pending">pending </option>
                        <option value="completed">completed </option>
                        <option value="shipped">shipped </option>
                        <option value="canceled">canceled </option>
                    </select>
                    <input name="order_date" type="text" value="' . $data["order_date"]  . '" disabled>
                    <input name="delivery_date" type="text" value="' . $data["delivery_date"]  . '" disabled>
                    <input name="created_at" type="text" value="' . $data["created_at"]  . '" disabled>
                    <input name="updated_at" type="text" value="' . $data["updated_at"]  . '" disabled>
                    <input name="user_id" type="text" value="' . $data["user_id"]  . '" hidden>
                    <button type="submit" name="update">Update</button>

                </form>
        
             </div>';

             if(isset($_POST["update"])) {
                $admin->updateStatus($_POST["id"], $_POST["status"]);
             }

    }

    if($operation == 'insert') {
        echo '<link rel="stylesheet" type="text/css" href="../assets/css/form.css">';

        echo '<div class="flex">
                <form action="" method="POST" class="form">
                    <input type="text" name="name" placeholder="category name">
                    <input type="text" name="description" placeholder="description ">
                    <button type="submit" name="insert" style="display: block; margin: 0 auto; padding: 0.9vh 3vh;">Add</button>
                </form>
                <div class="error">' ;

                    if(isset($_POST["insert"])) {

                        $result = $admin->insertCategory($_POST["name"], $_POST["description"]);

                        if(!$result) {
                            echo '<p>Error: can\'t have empty fields</p>';
                        }
                    }
                
                '</div>
            </div>';
    }

    if($operation == 'update' && $table == 'subscriptions' ) {
        $id = $_GET['id'];
        $data = $admin->getSub()[0];
        echo '<link rel="stylesheet" type="text/css" href="../assets/css/form.css">';

        echo '<div class="flex">
        <form action="" method="POST" class="form">
            <input name="username" type="text" value="' . $data["user_name"] . '" disabled >
            <input name="id" type="text" value="' . $data["id"] . '" hidden >
            <input name="userid" type="text" value="' . $data["user_id"] . '" hidden >
            <input name="packageid" type="text" value="' . $data["package_id"] . '" hidden > 
            <input name="start_date" type="text" value="' . $data["start_date"] . '" disabled > 
            <input name="end_date" type="text" value="' . $data["end_date"] . '" disabled > 
            <select name="status" value="' . $data["status"]  . '">
                <option value="active">active </option>
                <option value="cancelled">cancelled </option>
                <option value="expired">expired </option>
            </select>
            <input name="created_at" type="text" value="' . $data["created_at"]  . '" disabled>
            <input name="updated_at" type="text" value="' . $data["updated_at"]  . '" disabled>
            <button type="submit" name="update">Update</button>

        </form>

     </div>';

     if(isset($_POST["update"])) {

        $admin->updateSub($_POST["id"], $_POST["status"]);

     }
    }

    if($operation == "delete" && $table == "users") {
        $id = $_GET['id'];
        $admin->delteRecord($table, $id);
    }

    if($operation == "update" && $table == "users") {
        $data = $sql->getData($table, $_GET['id'])[0];
        echo '<link rel="stylesheet" type="text/css" href="../assets/css/form.css">';
        echo '<div class="flex">
            <form action="" method="POST" class="form">
                <input name="id" type="text" value="' . $data["id"] . '" hidden >
                <input name="name" type="text" value="' . $data["name"] . '"  >
                <input name="email" type="text" value="' . $data["email"] . '"  > 
                <input name="phone" type="text" value="' . $data["phone"] . '"  > 
                <input name="address" type="text" value="' . $data["address"] . '"  > 
                <input name="created_at" type="text" value="' . $data["created_at"] . '" disabled > 
                <input name="updated_at" type="text" value="' . $data["updated_at"] . '" disabled > 
                <button type="submit" name="update">Update</button>
            </form>
            <div class="error">';
            if(isset($_POST['update'])) {
                $result = $admin->updateUser($_POST['name'], $_POST['email'], $_POST['phone'], $_POST['address'], $_POST['id']);

                if(!$result) {
                    echo '<p>Error: can\'t have empty fields</p>';
                }
            }

            echo '</div>
        </div>';

    }
?>