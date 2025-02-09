<?php
session_start();
require_once 'classes/User.php';
$user = new User();
$user->logout();
session_unset();
session_destroy();
header("Location: login.php");
exit();
?>