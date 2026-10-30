<?php if (!$project): ?>
    <div class="empty-state">Proyek tidak ditemukan.</div>
<?php else: ?>
    <a class="back-link" href="/?page=projects">← Kembali ke proyek</a>
    <section class="detail-hero">
        <span class="pill"><?= e($project['category']) ?></span>
        <h1><?= e($project['title']) ?></h1>
        <p class="lead"><?= e($project['summary']) ?></p>
        <div class="card-meta">
            <span>Oleh <?= e($project['full_name']) ?></span>
            <span><?= e($project['program_name'] ?? 'Lintas prodi') ?></span>
            <span><?= count($members) ?> anggota</span>
        </div>
    </section>
    <div class="detail-layout">
        <article class="panel">
            <h2>Tentang proyek</h2>
            <p class="preline"><?= e($project['description']) ?></p>
            <?php if ($project['expected_output']): ?>
                <h3>Luaran</h3>
                <p><?= e($project['expected_output']) ?></p>
            <?php endif; ?>
            <h3>Keahlian yang dibutuhkan</h3>
            <div class="tag-list">
                <?php foreach ($skills as $skill): ?>
                    <span class="tag"><?= e($skill) ?></span>
                <?php endforeach; ?>
            </div>
            <h3>Anggota tim</h3>
            <?php foreach ($members as $member): ?>
                <div class="member-row">
                    <span class="avatar"><?= e(strtoupper(substr($member['full_name'], 0, 1))) ?></span>
                    <?= e($member['full_name']) ?>
                    <span class="muted small"><?= e($member['member_role']) ?></span>
                </div>
            <?php endforeach; ?>
        </article>
        <aside class="panel join-panel">
            <h3>Ingin bergabung?</h3>
            <p class="muted small">Kirim motivasi dan keahlianmu kepada pemimpin proyek.</p>
            <?php if ($user && (int) $user['id'] !== (int) $project['owner_id']): ?>
                <form method="post" action="/?action=join">
                    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="project_id" value="<?= (int) $project['id'] ?>">
                    <label>
                        Motivasi
                        <textarea name="motivation" rows="4" required></textarea>
                    </label>
                    <label>
                        Keahlian relevan
                        <textarea name="skills" rows="2"></textarea>
                    </label>
                    <label>
                        Ketersediaan
                        <input name="availability" placeholder="Contoh: 6 jam/minggu">
                    </label>
                    <button class="btn primary full">Ajukan bergabung</button>
                </form>
            <?php elseif (!$user): ?>
                <a class="btn primary full" href="/?page=login">Login untuk bergabung</a>
            <?php else: ?>
                <p class="small muted">Kamu adalah pemimpin proyek ini.</p>
            <?php endif; ?>
            <hr>
            <p class="small muted">
                Status: <?= e($project['status']) ?><br>
                Anggota dibutuhkan: <?= (int) $project['members_needed'] ?><br>
                Tenggat: <?= e($project['deadline'] ?? 'Belum ditentukan') ?>
            </p>
        </aside>
    </div>
<?php endif; ?>
