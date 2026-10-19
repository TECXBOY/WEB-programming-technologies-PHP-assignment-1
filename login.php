<?php
require_once __DIR__ . '/includes/auth.php';
redirect_if_logged_in();

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if (!csrf_valid()) {
        $error = 'Your session expired. Please try again.';
    } else {
        $stmt = db()->prepare('SELECT id, full_name, password_hash FROM users WHERE email = :email');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        // One generic message for "no such user" and "wrong password",
        // so the form never reveals which emails are registered.
        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']   = (int) $user['id'];
            $_SESSION['user_name'] = $user['full_name'];

            if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
                db()->prepare('UPDATE users SET password_hash = :h WHERE id = :id')
                    ->execute([':h' => password_hash($password, PASSWORD_DEFAULT), ':id' => $user['id']]);
            }
            db()->prepare("UPDATE users SET last_login = datetime('now') WHERE id = :id")
                ->execute([':id' => $user['id']]);

            header('Location: dashboard.php');
            exit;
        }
        $error = 'Incorrect email or password.';
    }
}

$pageTitle = 'Log in';
require __DIR__ . '/includes/header.php';
?>
<section class="card">
    <h1>Welcome back</h1>

    <?php if ($error): ?>
        <div class="alert alert-error" role="alert"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" action="login.php" novalidate>
        <?= csrf_field() ?>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= e($email) ?>" required autocomplete="email">

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required autocomplete="current-password">

        <button type="submit" class="btn btn-block">Log in</button>
    </form>
    <p class="muted">New here? <a href="register.php">Create an account</a></p>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
