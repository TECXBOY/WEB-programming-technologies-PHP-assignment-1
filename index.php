<?php
$pageTitle = 'Home';
require __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <h1>Learn to code. Build something that is yours.</h1>
    <p>Django Girls Sierra Leone runs free, beginner-friendly workshops that take women from
       "I have never written code" to "I just launched my own site." Create an account to
       reach your member dashboard.</p>
    <div class="hero-actions">
        <?php if (is_logged_in()): ?>
            <a class="btn" href="dashboard.php">Go to dashboard</a>
        <?php else: ?>
            <a class="btn" href="register.php">Create an account</a>
            <a class="btn btn-outline" href="login.php">Log in</a>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
