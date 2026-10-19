<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$stmt = db()->prepare('SELECT full_name, email, created_at, last_login FROM users WHERE id = :id');
$stmt->execute([':id' => $_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) { // account deleted while the session was alive
    header('Location: logout.php');
    exit;
}

$hour = (int) date('G');
$greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
$firstName = explode(' ', trim($user['full_name']))[0];
$memberDays = max(0, (int) floor((time() - strtotime($user['created_at'] . ' UTC')) / 86400));
$memberCount = (int) db()->query('SELECT COUNT(*) FROM users')->fetchColumn();

$pageTitle = 'Dashboard';
require __DIR__ . '/includes/header.php';
?>
<section class="dash-intro">
    <h1><?= e($greeting) ?>, <?= e($firstName) ?>.</h1>
    <p class="muted">Here is a quick look at your account.</p>
</section>

<section class="stats">
    <article class="stat">
        <span class="stat-label">Member for</span>
        <span class="stat-value"><?= $memberDays ?> <?= $memberDays === 1 ? 'day' : 'days' ?></span>
    </article>
    <article class="stat">
        <span class="stat-label">Last login</span>
        <span class="stat-value small"><?= $user['last_login'] ? e(date('j M Y, H:i', strtotime($user['last_login'] . ' UTC'))) : 'First visit' ?></span>
    </article>
    <article class="stat">
        <span class="stat-label">Community size</span>
        <span class="stat-value"><?= $memberCount ?> <?= $memberCount === 1 ? 'member' : 'members' ?></span>
    </article>
</section>

<section class="card wide">
    <h2>Your profile</h2>
    <dl class="profile">
        <dt>Full name</dt><dd><?= e($user['full_name']) ?></dd>
        <dt>Email</dt><dd><?= e($user['email']) ?></dd>
        <dt>Joined</dt><dd><?= e(date('j F Y', strtotime($user['created_at'] . ' UTC'))) ?></dd>
    </dl>
    <a class="btn btn-outline" href="logout.php">Log out</a>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
