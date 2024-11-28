<?php
session_start();
include "config.php";

// Ensure the user is logged in
if (!isset($_SESSION["username"])) {
    exit("You are not logged in");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the blog post data from the form
    $creator = $_SESSION["username"]; // The logged-in user's username
    $message = $_POST["message"];

    // Initialize the image upload variables

    // Prepare the SQL statement for inserting the blog post into the database
    $sql =
        "INSERT INTO blog (creator, message, timestamp) VALUES (?, ?, NOW())";

    // Initialize a prepared statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind the parameters to the SQL query
        $stmt->bind_param("ss", $creator, $message); // "sss" means three strings

        // Execute the prepared statement
        if ($stmt->execute()) {
            echo "Post submitted successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

    // Close the database connection
    $conn->close();
}
