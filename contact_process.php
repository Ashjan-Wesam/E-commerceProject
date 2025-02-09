<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
<<<<<<< HEAD
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $message = trim($_POST["message"]);

    if (!empty($name) && !empty($email) && !empty($message)) {
=======

    $message = trim($_POST["message"]);

    if (!empty($message)) {
>>>>>>> de50e4f1d107fd7e7d9137b413efa3fccf8eb121
        echo json_encode(["status" => "success", "message" => "Your message has been sent successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Please fill in all fields."]);
    }
<<<<<<< HEAD
=======

>>>>>>> de50e4f1d107fd7e7d9137b413efa3fccf8eb121
}
?>

