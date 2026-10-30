<?php
declare(strict_types=1);

function show_dashboard(): void {
    $u = require_login();

    $mine = db()->prepare("SELECT * FROM projects WHERE owner_id = ? ORDER BY created_at DESC LIMIT 8");
    $mine->execute([$u['id']]);
    $projects = $mine->fetchAll();

    $joins = db()->prepare(
        "SELECT jr.*, p.title, p.id AS project_id, u.full_name
        FROM join_requests jr
        JOIN projects p ON p.id = jr.project_id
        JOIN users u ON u.id = jr.user_id
        WHERE p.owner_id = ? AND jr.status = 'pending'
        ORDER BY jr.created_at DESC"
    );
    $joins->execute([$u['id']]);
    $requests = $joins->fetchAll();

    $notif = db()->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
    $notif->execute([$u['id']]);
    $notifications = $notif->fetchAll();

    render_page('Dashboard', 'dashboard/index', [
        'user' => $u,
        'projects' => $projects,
        'requests' => $requests,
        'notifications' => $notifications,
    ]);
}

function handle_join_review(): void {
    $u = require_login();
    verify_csrf();
    $id = (int) ($_POST['request_id'] ?? 0);
    $decision = post_string('decision', 20);

    if (!in_array($decision, ['accepted', 'rejected'], true)) {
        flash('error', 'Keputusan tidak valid.');
        redirect('/?page=dashboard');
    }

    $pdo = db();
    $pdo->beginTransaction();

    try {
        $stmt = $pdo->prepare(
            "SELECT jr.*, p.owner_id, p.id AS pid, p.title, p.members_needed,
                (SELECT COUNT(*) FROM project_members pm WHERE pm.project_id = p.id) AS total_members
            FROM join_requests jr
            JOIN projects p ON p.id = jr.project_id
            WHERE jr.id = ?
            FOR UPDATE"
        );
        $stmt->execute([$id]);
        $r = $stmt->fetch();

        if (!$r || (int) $r['owner_id'] !== (int) $u['id'] || $r['status'] !== 'pending') {
            throw new RuntimeException('Permintaan tidak dapat diproses.');
        }

        if ($decision === 'accepted') {
            if ((int) $r['total_members'] >= (int) $r['members_needed'] + 1) {
                throw new RuntimeException('Kuota anggota telah penuh.');
            }

            $pdo->prepare("INSERT IGNORE INTO project_members(project_id, user_id) VALUES(?, ?)")
                ->execute([$r['pid'], $r['user_id']]);
        }

        $pdo->prepare("UPDATE join_requests SET status=?, reviewed_by=?, reviewed_at=NOW() WHERE id=?")
            ->execute([$decision, $u['id'], $id]);
        notify_user(
            (int) $r['user_id'],
            'Permintaan bergabung diperbarui',
            'Permintaanmu untuk ' . $r['title'] . ' ' . $decision . '.'
        );
        $pdo->commit();
        flash('success', 'Permintaan berhasil diproses.');
    } catch (Throwable $e) {
        $pdo->rollBack();
        flash('error', $e->getMessage());
    }

    redirect('/?page=dashboard');
}

function show_profile(): void {
    $user = require_login();
    render_page('Profil Saya', 'dashboard/profile', ['user' => $user]);
}
function handle_profile_update(): void {
    $u = require_login();
    verify_csrf();

    $stmt = db()->prepare(
        "UPDATE users
        SET full_name = ?, nim = ?, semester = ?, bio = ?, profile_visibility = ?
        WHERE id = ?"
    );
    $visibility = post_string('profile_visibility', 20);
    if (!in_array($visibility, ['public', 'campus', 'private'], true)) {
        $visibility = 'campus';
    }

    $semester = (int) ($_POST['semester'] ?? 0);
    $stmt->execute([
        post_string('full_name', 150),
        post_string('nim', 40) ?: null,
        $semester ?: null,
        post_string('bio', 2000),
        $visibility,
        $u['id'],
    ]);

    flash('success', 'Profil berhasil diperbarui.');
    redirect('/?page=profile');
}
