<?php
session_start();
include('config.php');
if (!isset($_SESSION['username'])) {
    exit("You are not logged in");
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {


    $sender = $_POST['sender'];
    $receiver = $_POST['receiver'];
    $message = $_POST['message'];

    $sql = "INSERT INTO chat_messages (sender, receiver, message) VALUES ('$sender', '$receiver', '$message')";
    $conn->query($sql);
    $conn->close();
}
