<div class="page-heading">
    <div>
        <p class="eyebrow">TEMUKAN KOLABORASI</p>
        <h1>Proyek untuk ide hebatmu.</h1>
        <p class="muted">Temukan tim, kembangkan kemampuan, dan hasilkan karya nyata.</p>
    </div>
    <?php if ($user): ?>
        <a class="btn primary" href="/?page=project-create">+ Ajukan proyek</a>
    <?php endif; ?>
</div>
<form class="filter-bar" method="get">
    <input type="hidden" name="page" value="projects">
    <input name="q" placeholder="Cari judul, skill, atau deskripsi..." value="<?= e($q) ?>">
    <select name="program">
        <option value="">Semua program studi</option>
        <?php foreach ($programs as $programOption): ?>
            <option value="<?= (int) $programOption['id'] ?>" <?= $program === (int) $programOption['id'] ? 'selected' : '' ?>>
                <?= e($programOption['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <select name="category">
        <option value="">Semua kategori</option>
        <?php foreach ([
            'small' => 'Proyek kecil',
            'medium' => 'Proyek medium',
            'research' => 'Penelitian',
            'creative' => 'Kreatif',
            'competition' => 'Kompetisi',
        ] as $key => $value): ?>
            <option value="<?= e($key) ?>" <?= $category === $key ? 'selected' : '' ?>><?= e($value) ?></option>
        <?php endforeach; ?>
    </select>
    <button class="btn dark">Cari</button>
    <a class="btn light" href="/?page=projects">Reset</a>
</form>
<div class="project-grid">
    <?php foreach ($projects as $project): ?>
        <article class="project-card">
            <div class="card-top">
                <span class="pill"><?= e(ucfirst($project['category'])) ?></span>
                <span class="status"><?= e($project['status']) ?></span>
            </div>
            <h3>
                <a href="/?page=project&id=<?= (int) $project['id'] ?>"><?= e($project['title']) ?></a>
            </h3>
            <p><?= e($project['summary']) ?></p>
            <div class="card-meta">
                <span><?= e($project['program_name'] ?? 'Lintas program studi') ?></span>
                <span><?= (int) $project['member_count'] ?> anggota</span>
            </div>
            <div class="card-bottom">
                <span class="muted small">Oleh <?= e($project['full_name']) ?></span>
                <a href="/?page=project&id=<?= (int) $project['id'] ?>">Lihat detail →</a>
            </div>
        </article>
    <?php endforeach; ?>
    <?php if (!$projects): ?>
        <div class="empty-state">Belum ada proyek yang cocok. Coba ubah kata kunci atau filter.</div>
    <?php endif; ?>
</div>
