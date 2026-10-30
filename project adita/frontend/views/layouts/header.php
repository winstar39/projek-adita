<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> · KampusKolab</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header class="topbar">
        <a class="brand" href="/">
            <span class="brand-mark">K</span>Kampus<span>Kolab</span>
        </a>
        <nav>
            <a href="/?page=projects">Jelajahi Proyek</a>
            <?php if ($user): ?>
                <a href="/?page=dashboard">Dashboard</a>
                <a href="/?page=profile">Profil</a>
                <?php if (in_array($user['role'], ['admin', 'moderator'], true)): ?>
                    <a href="/?page=review">Moderasi</a>
                <?php endif; ?>
                <form class="inline" method="post" action="/?action=logout">
                    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                    <button class="link-button">Keluar</button>
                </form>
            <?php else: ?>
                <a href="/?page=login">Masuk</a>
                <a class="nav-cta" href="/?page=register">Daftar</a>
            <?php endif; ?>
        </nav>
    </header>
    <main class="container">
        <?php if (!empty($_SESSION['flash'])): ?>
            <?php $flashMessage = $_SESSION['flash']; unset($_SESSION['flash']); ?>
            <div class="alert <?= e($flashMessage['type']) ?>"><?= e($flashMessage['message']) ?></div>
        <?php endif; ?>
