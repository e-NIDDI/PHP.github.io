<?php
session_start();
include "config.php";

// Ensure the user is logged in
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION["username"];
$selectedUser = "";
$showChatBox = false;
$showBlogBox = false;

if (isset($_GET["user"])) {
    $selectedUser = $_GET["user"];
    $selectedUser = mysqli_real_escape_string($conn, $selectedUser);
    $showChatBox = true;
} else {
    $showChatBox = false;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat and Blog</title>
    <link href="style.css" rel="stylesheet">
</head>

<body>

    <div class="container">
        <div class="header">
            <h1>My Account</h1>
            <a href="logout.php" class="logout">Logout</a>
            <br><br>
            <button id="createBlogButton">Create a Blog</button>

            <!-- Blog Post Form (Initially Hidden) -->

        </div>
        <div class="account-info">
            <h2>Welcome, <?php echo ucfirst($username); ?>!</h2>



            <!-- User List for Chat -->
            <div class="user-list">
                <h2>Select a User to Chat With:</h2>
                <ul>
                    <?php
                    $sql = "SELECT username FROM users WHERE username != '$username'";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $user = $row["username"];
                            $user = ucfirst($user);
                            echo "<li><a href='chat.php?user=$user'>$user</a></li>";
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>

        <!-- Blog Box -->
        <div class="blog-box" id="blog-box">
            <div class="blog-box-header">
                <h2>Blogs</h2>
            </div>
            <div class="blog-box-body" id="blog-box-body">
                <div id="blogPosts">
                    <!-- Blog posts will be dynamically loaded here -->
                </div>
            </div>
        </div>
        <div class="blog-form" id="blogForm" style=" display:none; ">
            <h3>Write your Blog Post</h3>
            <form id="blogFormSubmit">
                <textarea id="blogMessage" placeholder="Write your blog message..." required></textarea>
                <button type="submit">Post Blog</button>
            </form>
            <button onclick="closeBlogForm()">Cancel</button>
        </div>
    </div>


    <!-- Chat Box -->
    <?php if ($showChatBox): ?>
        <div class="chat-box" id="chat-box">
            <div class="chat-box-header">
                <h2><?php echo ucfirst($selectedUser); ?></h2>
                <button class="close-btn" onclick="closeChat()">✖</button>
            </div>
            <div class="chat-box-body" id="chat-box-body"></div>
            <form class="chat-form" id="chat-form">
                <input type="hidden" id="sender" value="<?php echo $username; ?>">
                <input type="hidden" id="receiver" value="<?php echo $selectedUser; ?>">
                <input type="text" id="message" placeholder="Type your message..." required>
                <button type="submit">Send</button>
            </form>
        </div>
    <?php endif; ?>





    <!-- jQuery CDN -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>
        // Toggle visibility of the blog form
        document.getElementById("createBlogButton").addEventListener("click", function() {
            var blogForm = document.getElementById("blogForm");
            if (blogForm.style.display === "none" || blogForm.style.display === "") {
                blogForm.style.display = "block"; // Show the form
            } else {
                blogForm.style.display = "none"; // Hide the form
            }
        });


        // Fetch blog posts
        function fetchBlogPosts() {
            $.ajax({
                url: 'fetch_blog_posts.php', // PHP file that retrieves blog posts
                type: 'GET', // Use GET to fetch the posts
                success: function(data) {
                    // Populate the blog posts section with the data returned by the PHP script
                    $('#blogPosts').html(data);
                },
                error: function() {
                    alert('Error fetching blog posts.');
                }
            });
        }



        // When the blog form is submitted
        $('#blogFormSubmit').submit(function(e) {
            e.preventDefault();
            var message = $('#blogMessage').val();
            if (message.trim() === '') {
                alert('Please write something before posting.');
                return;
            }

            $.ajax({
                url: 'submit_blog_post.php',
                type: 'POST',
                data: {
                    message: message
                },
                success: function(response) {
                    $('#blogMessage').val('');
                    closeBlogForm();
                    fetchBlogPosts();
                },
                error: function() {
                    alert('An error occurred while submitting the blog post.');
                }
            });
        });

        // Fetch blog posts on page load
        $(document).ready(function() {
          fetchBlogPosts();
            setInterval(fetchBlogPosts, 10000);
        });

        // Fetch chat messages
        function fetchMessages() {
            var sender = $('#sender').val();
            var receiver = $('#receiver').val();

            $.ajax({
                url: 'fetch_messages.php',
                type: 'POST',
                data: {
                    sender: sender,
                    receiver: receiver
                },
                success: function(data) {
                    $('#chat-box-body').html(data);
                    scrollChatToBottom();
                }
            });
        }

        // Scroll chat box to the bottom
        function scrollChatToBottom() {
            var chatBox = $('#chat-box-body');
            chatBox.scrollTop(chatBox.prop("scrollHeight"));
        }

        // Submit a chat message
        $('#chat-form').submit(function(e) {
            e.preventDefault();
            var sender = $('#sender').val();
            var receiver = $('#receiver').val();
            var message = $('#message').val();

            $.ajax({
                url: 'submit_message.php',
                type: 'POST',
                data: {
                    sender: sender,
                    receiver: receiver,
                    message: message
                },
                success: function() {
                    $('#message').val('');
                    fetchMessages();
                }
            });
        });

        // Close the chat box
        function closeChat() {
            document.getElementById("chat-box").style.display = "none";
        }

        // Fetch chat messages on page load
        $(document).ready(function() {
            fetchMessages();
            setInterval(fetchMessages, 3000);
        });
        // Close the Blog Box
        function closeBlog() {
            document.getElementById("blog-box").style.display = "none";
        }
    </script>
</body>

</html>
