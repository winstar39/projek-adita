<div class="page-heading">
    <div>
        <p class="eyebrow">DASHBOARD</p>
        <h1>Halo, <?= e(explode(' ', $user['full_name'])[0]) ?> 👋</h1>
        <p class="muted">Pantau aktivitas dan lanjutkan kolaborasimu.</p>
    </div>
    <a class="btn primary" href="/?page=project-create">+ Buat proyek</a>
</div>
<div class="stats-grid">
    <div class="stat-card"><span>Proyek dibuat</span><strong><?= count($projects) ?></strong></div>
    <div class="stat-card"><span>Permintaan anggota</span><strong><?= count($requests) ?></strong></div>
    <div class="stat-card"><span>Notifikasi terbaru</span><strong><?= count($notifications) ?></strong></div>
</div>
<div class="two-col">
    <section class="panel">
        <div class="section-head">
            <h2>Proyek saya</h2>
            <a href="/?page=projects">Jelajahi semua →</a>
        </div>
        <?php foreach ($projects as $project): ?>
            <div class="list-row">
                <div>
                    <strong><?= e($project['title']) ?></strong>
                    <p class="small muted"><?= e($project['submission_status']) ?> · <?= e($project['status']) ?></p>
                </div>
                <?php if ($project['submission_status'] === 'approved'): ?>
                    <a href="/?page=project&id=<?= (int) $project['id'] ?>">Detail</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <?php if (!$projects): ?>
            <p class="muted">Kamu belum membuat proyek.</p>
        <?php endif; ?>
    </section>
    <section class="panel">
        <h2>Notifikasi</h2>
        <?php foreach ($notifications as $notification): ?>
            <div class="list-row">
                <div>
                    <strong><?= e($notification['title']) ?></strong>
                    <p class="small muted"><?= e($notification['message']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (!$notifications): ?>
            <p class="muted">Belum ada notifikasi.</p>
        <?php endif; ?>
    </section>
</div>
<?php if ($requests): ?>
    <section class="panel section-gap">
        <h2>Permintaan bergabung ke proyekmu</h2>
        <?php foreach ($requests as $request): ?>
            <div class="list-row">
                <div>
                    <strong><?= e($request['full_name']) ?></strong>
                    <p class="small muted"><?= e($request['title']) ?> · <?= e($request['motivation']) ?></p>
                </div>
                <div class="action-row">
                    <form method="post" action="/?action=join-review">
                        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="request_id" value="<?= (int) $request['id'] ?>">
                        <input type="hidden" name="decision" value="accepted">
                        <button class="btn tiny primary">Terima</button>
                    </form>
                    <form method="post" action="/?action=join-review">
                        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="request_id" value="<?= (int) $request['id'] ?>">
                        <input type="hidden" name="decision" value="rejected">
                        <button class="btn tiny light">Tolak</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </section>
<?php endif; ?>
