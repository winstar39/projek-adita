<section class="auth-wrap">
    <div class="auth-card">
        <p class="eyebrow">SELAMAT DATANG KEMBALI</p>
        <h1>Masuk ke KampusKolab</h1>
        <p class="muted">Lanjutkan proyek dan kolaborasimu.</p>
        <form method="post" action="/?action=login">
            <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
            <label>
                Email kampus
                <input type="email" name="email" required autocomplete="username">
            </label>
            <label>
                Password
                <input type="password" name="password" required autocomplete="current-password">
            </label>
            <button class="btn primary full">Masuk</button>
        </form>
        <p class="small center">Belum punya akun? <a href="/?page=register">Daftar sekarang</a></p>
    </div>
</section>
