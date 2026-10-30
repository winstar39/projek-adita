<div class="form-page">
    <p class="eyebrow">AKUN DAN IDENTITAS</p>
    <h1>Profil saya</h1>
    <form class="panel form-grid" method="post" action="/?action=profile-update">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
        <label class="span-2">
            Nama lengkap
            <input name="full_name" value="<?= e($user['full_name']) ?>" required>
        </label>
        <label>
            NIM
            <input name="nim" value="<?= e($user['nim'] ?? '') ?>">
        </label>
        <label>
            Semester
            <input type="number" min="1" max="20" name="semester" value="<?= e((string) ($user['semester'] ?? '')) ?>">
        </label>
        <label class="span-2">
            Bio
            <textarea name="bio" rows="4"><?= e($user['bio'] ?? '') ?></textarea>
        </label>
        <label>
            Visibilitas profil
            <select name="profile_visibility">
                <option value="public" <?= $user['profile_visibility'] === 'public' ? 'selected' : '' ?>>Publik</option>
                <option value="campus" <?= $user['profile_visibility'] === 'campus' ? 'selected' : '' ?>>Pengguna kampus</option>
                <option value="private" <?= $user['profile_visibility'] === 'private' ? 'selected' : '' ?>>Privat</option>
            </select>
        </label>
        <div class="span-2"><button class="btn primary">Simpan profil</button></div>
    </form>
</div>
