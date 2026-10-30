<div class="form-page">
    <p class="eyebrow">BAGIKAN IDEMU</p>
    <h1>Ajukan proyek baru</h1>
    <p class="muted">Pengajuan akan ditinjau moderator/admin sebelum diterbitkan.</p>
    <form class="panel form-grid" method="post" action="/?action=project-create">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
        <label class="span-2">
            Judul proyek
            <input name="title" maxlength="200" required>
        </label>
        <label>
            Kategori
            <select name="category">
                <option value="small">Proyek kecil</option>
                <option value="medium">Proyek medium</option>
                <option value="research">Penelitian</option>
                <option value="creative">Kreatif</option>
                <option value="competition">Kompetisi</option>
            </select>
        </label>
        <label>
            Jumlah anggota dibutuhkan
            <input type="number" name="members_needed" min="1" max="50" value="2" required>
        </label>
        <label class="span-2">
            Ringkasan
            <textarea name="summary" maxlength="500" rows="2" required></textarea>
        </label>
        <label class="span-2">
            Deskripsi dan permasalahan
            <textarea name="description" rows="5" required></textarea>
        </label>
        <label>
            Keahlian dibutuhkan <small>Pisahkan dengan koma</small>
            <input name="skills" placeholder="PHP, UI/UX, Database">
        </label>
        <label>
            Tenggat waktu
            <input type="date" name="deadline" min="<?= date('Y-m-d') ?>">
        </label>
        <label>
            Durasi (minggu)
            <input type="number" name="duration_weeks" min="1" max="104">
        </label>
        <label>
            Dosen pembimbing (opsional)
            <input name="supervisor_name">
        </label>
        <label class="span-2">
            Luaran yang diharapkan
            <textarea name="expected_output" rows="3"></textarea>
        </label>
        <div class="span-2"><button class="btn primary">Kirim untuk ditinjau</button></div>
    </form>
</div>
