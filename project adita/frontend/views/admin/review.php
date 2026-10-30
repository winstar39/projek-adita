<div class="page-heading">
    <div>
        <p class="eyebrow">PUSAT MODERASI</p>
        <h1>Peninjauan proyek</h1>
        <p class="muted">Tinjau proposal sebelum tampil di halaman eksplorasi.</p>
    </div>
</div>
<?php foreach ($projects as $project): ?>
    <section class="panel review-card">
        <div class="section-head">
            <div>
                <span class="pill"><?= e($project['submission_status']) ?></span>
                <h2><?= e($project['title']) ?></h2>
                <p class="muted small">Pengaju: <?= e($project['full_name']) ?></p>
            </div>
        </div>
        <p><?= e($project['summary']) ?></p>
        <details>
            <summary>Lihat deskripsi lengkap</summary>
            <p class="preline"><?= e($project['description']) ?></p>
        </details>
        <form method="post" action="/?action=project-review" class="review-form">
            <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="project_id" value="<?= (int) $project['id'] ?>">
            <label>
                Catatan / alasan
                <textarea name="note" rows="2" placeholder="Wajib diisi untuk penolakan atau revisi"></textarea>
            </label>
            <div class="action-row">
                <button class="btn primary" name="decision" value="approved">Setujui</button>
                <button class="btn light" name="decision" value="revision_requested">Minta revisi</button>
                <button class="btn danger" name="decision" value="rejected">Tolak</button>
            </div>
        </form>
    </section>
<?php endforeach; ?>
<?php if (!$projects): ?>
    <div class="empty-state">Tidak ada pengajuan yang menunggu peninjauan.</div>
<?php endif; ?>
