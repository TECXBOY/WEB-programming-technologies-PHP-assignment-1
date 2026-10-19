<?php
require_once __DIR__ . '/includes/auth.php';
redirect_if_logged_in();

$errors = [];
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['full_name'] ?? '');
    $email    = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if (!csrf_valid()) {
        $errors[] = 'Your session expired. Please try again.';
    }
    if ($name === '' || mb_strlen($name) > 80) {
        $errors[] = 'Enter your full name (80 characters at most).';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'That email address does not look right.';
    }
    if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
        $errors[] = 'Use a password of at least 8 characters with letters and numbers.';
    }
    if ($password !== $confirm) {
        $errors[] = 'The two passwords do not match.';
    }

    if (!$errors) {
        $check = db()->prepare('SELECT id FROM users WHERE email = :email');
        $check->execute([':email' => $email]);
        if ($check->fetch()) {
            $errors[] = 'An account with that email already exists.';
        } else {
            $insert = db()->prepare(
                'INSERT INTO users (full_name, email, password_hash) VALUES (:n, :e, :h)'
            );
            $insert->execute([
                ':n' => $name,
                ':e' => $email,
                ':h' => password_hash($password, PASSWORD_DEFAULT),
            ]);
            $_SESSION['flash'] = ['type' => 'success', 'text' => 'Account created. You can log in now.'];
            header('Location: login.php');
            exit;
        }
    }
}

$pageTitle = 'Sign up';
require __DIR__ . '/includes/header.php';
?>
<section class="card">
    <h1>Create your account</h1>

    <?php if ($errors): ?>
        <div class="alert alert-error" role="alert">
            <ul>
                <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="register.php" novalidate>
        <?= csrf_field() ?>
        <label for="full_name">Full name</label>
        <input type="text" id="full_name" name="full_name" value="<?= e($name) ?>" maxlength="80" required autocomplete="name">

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= e($email) ?>" required autocomplete="email">

        <label for="password">Password</label>
        <input type="password" id="password" name="password" minlength="8" required autocomplete="new-password">
        <small>At least 8 characters, with letters and numbers.</small>

        <label for="confirm">Confirm password</label>
        <input type="password" id="confirm" name="confirm" required autocomplete="new-password">

        <button type="submit" class="btn btn-block">Sign up</button>
    </form>
    <p class="muted">Already registered? <a href="login.php">Log in</a></p>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
