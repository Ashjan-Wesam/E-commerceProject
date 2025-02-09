<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $message = trim($_POST["message"]);

    if (!empty($message)) {
        echo json_encode(["status" => "success", "message" => "Your message has been sent successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Please fill in all fields."]);
    }

}
?>

