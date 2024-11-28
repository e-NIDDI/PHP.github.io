<?php
// Assuming you have the database connection already set up (config.php)
include "config.php";

// Query to fetch all blog posts in ascending order of timestamp (earliest first)
$sql =
    "SELECT id, creator, message, timestamp FROM blog ORDER BY timestamp DESC;";
$result = $conn->query($sql);

// Check if there are any results
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Output each blog post as HTML
        echo '<div class="blog-post">';
        echo "<strong>" .
            ucfirst(htmlspecialchars($row["creator"])) .
            "</strong>"; // Creator's name
        echo "<p>" . nl2br(htmlspecialchars($row["message"])) . "</p>"; // Message content with line breaks
        echo "<small>" . htmlspecialchars($row["timestamp"]) . "</small>"; // Timestamp
        echo "</div>";
    }
} else {
    echo "No blog posts found.";
}

$conn->close(); // Close the connection
?>
