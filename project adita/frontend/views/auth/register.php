<section class="auth-wrap">
    <div class="auth-card">
        <p class="eyebrow">MULAI BERKOLABORASI</p>
        <h1>Buat akun</h1>
        <p class="muted">Gunakan email aktif. Verifikasi email kampus perlu diaktifkan sebelum produksi.</p>
        <form method="post" action="/?action=register">
            <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
            <label>
                Nama lengkap
                <input name="full_name" maxlength="150" required>
            </label>
            <label>
                Email
                <input type="email" name="email" maxlength="190" required>
            </label>
            <label>
                Program studi
                <select name="study_program_id">
                    <option value="">Pilih program studi</option>
                    <?php foreach ($programs as $program): ?>
                        <option value="<?= (int) $program['id'] ?>"><?= e($program['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                Password
                <input type="password" name="password" minlength="10" required>
                <small>Minimal 10 karakter.</small>
            </label>
            <label>
                Konfirmasi password
                <input type="password" name="password_confirmation" minlength="10" required>
            </label>
            <button class="btn primary full">Buat akun</button>
        </form>
        <p class="small center">Sudah punya akun? <a href="/?page=login">Masuk</a></p>
    </div>
</section>
