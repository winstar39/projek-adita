<?php
declare(strict_types=1);

function show_review(): void {
    require_role(['admin', 'moderator']);
    $rows = db()->query(
        "SELECT p.*, u.full_name
        FROM projects p
        JOIN users u ON u.id = p.owner_id
        WHERE p.submission_status IN ('pending', 'under_review', 'revision_requested')
        ORDER BY p.created_at ASC"
    )->fetchAll();

    render_page('Moderasi proyek', 'admin/review', ['projects' => $rows]);
}
function handle_project_review(): void {
    $u = require_role(['admin', 'moderator']);
    verify_csrf();
    $id = (int) ($_POST['project_id'] ?? 0);
    $decision = post_string('decision', 30);
    $note = post_string('note', 3000);

    if (!in_array($decision, ['approved', 'revision_requested', 'rejected'], true)) {
        flash('error', 'Keputusan tidak valid.');
        redirect('/?page=review');
    }

    if ($decision !== 'approved' && strlen($note) < 5) {
        flash('error', 'Berikan alasan minimal 5 karakter.');
        redirect('/?page=review');
    }

    $pdo = db();
    $pdo->beginTransaction();

    try {
        $stmt = $pdo->prepare("SELECT owner_id, title FROM projects WHERE id = ? FOR UPDATE");
        $stmt->execute([$id]);
        $p = $stmt->fetch();

        if (!$p) {
            throw new RuntimeException('Proyek tidak ditemukan.');
        }

        $pdo->prepare(
            "UPDATE projects
            SET submission_status = ?, reviewed_by = ?, reviewed_at = NOW(), review_note = ?
            WHERE id = ?"
        )->execute([$decision, $u['id'], $note, $id]);
        notify_user(
            (int) $p['owner_id'],
            'Status pengajuan proyek diperbarui',
            'Pengajuan ' . $p['title'] . ': ' . $decision . '. ' . $note
        );
        audit('project_' . $decision, 'project', $id, $note);
        $pdo->commit();
        flash('success', 'Keputusan moderasi tersimpan.');
    } catch (Throwable $e) {
        $pdo->rollBack();
        flash('error', $e->getMessage());
    }

    redirect('/?page=review');
}
