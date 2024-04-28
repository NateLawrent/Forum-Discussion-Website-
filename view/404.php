<?php
require_once("common.php");

$session = new Session();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Huy's Forum Discussion Community</title>
    <link rel="stylesheet" href="/style/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    
</head>
<body>
    <header>
        <img src="/images/huylogo.png" alt="Logo" class="logo">
        <h1>Huy's Forum Discussion Community</h1>
        <!-- Login/Logout Section -->

    </header>

    <nav class="nav-center">
        <a href="index.php">Home</a>
        <a href="questions.php">Questions</a>
        <a href="tags.php">Tags</a>
        <a href="users.php">Users</a>
        <?php if($session->get("user")): ?>
            <a href="askquestion.php">Ask Question</a>
        <?php endif ?>
        <a href="contactus.php">Contact Us</a>
        <?php if($session->get("isAdmin")): ?>
            <a href="admin.php">Admin</a>
        <?php endif ?>
        <?php if($session->get("user")): ?>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="signin.php">Login</a>
        <?php endif ?>
    </nav>

    <?php
        if ($session->getFlash('signup-success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <p><?=$session->getFlash('signup-success')?></p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
    <?php endif; ?>
    <?php
        if ($session->getFlash('signin-success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <p><?=$session->getFlash('signin-success')?></p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
    <?php endif; ?>
    <?php
        if ($session->getFlash('signout-success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <p><?=$session->getFlash('signout-success')?></p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
    <?php endif; ?>
    <div class="container">
        <section class="main-content">
            <h2>Page Not Found</h2>
        </section>

        <aside class="sidebar">
            <h2>About Us</h2>
            <p>Welcome to Huy's Coursework Forum Discussion - Your Ultimate Coursework Companion!</p>
            <p>At Huy's Coursework Forum Discussion, we understand the challenges that university students face when navigating through their coursework. Balancing lectures, assignments, and exams can be overwhelming, and that's why we've created a dedicated space for collaborative learning and assistance.</p>
            <h2>Our Mission</h2>
            <p>Our mission is to foster a supportive community where students can come together to discuss, share, and seek help on a wide range of university coursework. Whether you're tackling complex assignments, delving into challenging topics, or just seeking advice on study techniques, Huy's Forum is the place to connect with peers who understand the journey.</p>
        </aside>
    </div>

    <footer>
        <p>© Huy's Forum Discussion Community. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function login() {
            // Placeholder for actual login logic
            var username = document.querySelector('#login-form input[type="text"]').value;
            document.getElementById('login-form').style.display = 'none';
            document.getElementById('logout-section').style.display = 'block';
            document.getElementById('user-name').textContent = username; // Display the username
        }

        function logout() {
            // Placeholder for actual logout logic
            document.getElementById('login-form').style.display = 'block';
            document.getElementById('logout-section').style.display = 'none';
        }
    </script>

</body>
</html>
