<?php
declare(strict_types=1);

function show_projects(): void {
    $q = trim((string) ($_GET['q'] ?? ''));
    $program = (int) ($_GET['program'] ?? 0);
    $category = (string) ($_GET['category'] ?? '');
    $sql = "SELECT p.*, u.full_name, sp.name AS program_name,
        (SELECT COUNT(*) FROM project_members pm WHERE pm.project_id = p.id) AS member_count,
        (SELECT COUNT(*) FROM join_requests jr WHERE jr.project_id = p.id AND jr.status = 'pending') AS applicant_count
        FROM projects p
        JOIN users u ON u.id = p.owner_id
        LEFT JOIN study_programs sp ON sp.id = u.study_program_id
        WHERE p.submission_status = 'approved'
            AND p.visibility IN ('campus', 'public')";
    $params = [];

    if ($q !== '') {
        $sql .= " AND (p.title LIKE ? OR p.summary LIKE ? OR p.description LIKE ?)";
        $like = "%{$q}%";
        array_push($params, $like, $like, $like);
    }

    if ($program) {
        $sql .= " AND u.study_program_id = ?";
        $params[] = $program;
    }

    if (in_array($category, ['small', 'medium', 'research', 'creative', 'competition'], true)) {
        $sql .= " AND p.category = ?";
        $params[] = $category;
    }

    $sql .= " ORDER BY p.created_at DESC LIMIT 60";
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $projects = $stmt->fetchAll();
    $programs = db()
        ->query("SELECT id, name FROM study_programs WHERE is_active = 1 ORDER BY name")
        ->fetchAll();
    render_page('Jelajahi Proyek', 'projects/index', [
        'q' => $q,
        'program' => $program,
        'category' => $category,
        'projects' => $projects,
        'programs' => $programs,
    ]);
}

function show_project_create(): void {
    require_login();
    render_page('Ajukan Proyek', 'projects/create');
}

function handle_project_create(): void {
    $u=require_login(); verify_csrf();
    $title = post_string('title', 200);
    $summary = post_string('summary', 500);
    $description = post_string('description', 12000);
    $category = post_string('category', 30);
    $members = max(1, min(50, (int) ($_POST['members_needed'] ?? 1)));

    if (strlen($title) < 5 || strlen($summary) < 10 || strlen($description) < 20 || !in_array($category, ['small', 'medium', 'research', 'creative', 'competition'], true)) {
        flash('error', 'Data proyek belum memenuhi ketentuan.');
        redirect('/?page=project-create');
    }

    $slug = strtolower(trim(
        preg_replace('/[^a-zA-Z0-9]+/', '-', iconv('UTF-8', 'ASCII//TRANSLIT', $title)),
        '-'
    )) . '-' . bin2hex(random_bytes(3));
    $skills = array_values(array_filter(array_map('trim', explode(',', post_string('skills', 500)))));
    $deadline = post_string('deadline', 10) ?: null;
    $stmt = db()->prepare(
        "INSERT INTO projects(
            owner_id, title, slug, category, summary, description, expected_output,
            required_skills, members_needed, duration_weeks, deadline, supervisor_name
        ) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([
        $u['id'],
        $title,
        $slug,
        $category,
        $summary,
        $description,
        post_string('expected_output', 3000),
        json_encode($skills),
        $members,
        (int) ($_POST['duration_weeks'] ?? 0) ?: null,
        $deadline,
        post_string('supervisor_name', 150) ?: null,
    ]);

    $id = (int) db()->lastInsertId();
    audit('project_submitted', 'project', $id);
    flash('success', 'Pengajuan proyek dikirim dan menunggu peninjauan.');
    redirect('/?page=dashboard');
}

function show_project_detail(): void {
    $id = (int) ($_GET['id'] ?? 0);
    $stmt = db()->prepare(
        "SELECT p.*, u.full_name, u.id AS owner_id, sp.name AS program_name
        FROM projects p
        JOIN users u ON u.id = p.owner_id
        LEFT JOIN study_programs sp ON sp.id = u.study_program_id
        WHERE p.id = ? AND p.submission_status = 'approved'"
    );
    $stmt->execute([$id]);
    $project = $stmt->fetch();

    $members = [];
    $skills = [];
    if ($project) {
        $membersStmt = db()->prepare(
            "SELECT u.full_name, pm.member_role
            FROM project_members pm
            JOIN users u ON u.id = pm.user_id
            WHERE pm.project_id = ?"
        );
        $membersStmt->execute([$id]);
        $members = $membersStmt->fetchAll();
        $skills = json_decode($project['required_skills'] ?? '[]', true) ?: [];
    }

    if (!$project) {
        http_response_code(404);
    }

    render_page($project ? $project['title'] : 'Tidak ditemukan', 'projects/detail', [
        'project' => $project,
        'members' => $members,
        'skills' => $skills,
    ]);
}

function handle_join_request(): void {
    $u = require_login();
    verify_csrf();
    $project = (int) ($_POST['project_id'] ?? 0);
    $stmt = db()->prepare(
        "SELECT p.*,
            (SELECT COUNT(*) FROM project_members pm WHERE pm.project_id = p.id) AS total_members
        FROM projects p
        WHERE p.id = ?
            AND p.submission_status = 'approved'
            AND p.status = 'recruiting'"
    );
    $stmt->execute([$project]);
    $p = $stmt->fetch();

    if (!$p || $p['owner_id'] == $u['id'] || (int) $p['total_members'] >= (int) $p['members_needed'] + 1) {
        flash('error', 'Proyek tidak tersedia untuk permintaan bergabung.');
        redirect('/?page=projects');
    }

    try {
        $stmt = db()->prepare(
            "INSERT INTO join_requests(project_id, user_id, motivation, skills, availability)
            VALUES(?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $project,
            $u['id'],
            post_string('motivation', 3000),
            post_string('skills', 1500),
            post_string('availability', 150),
        ]);
        notify_user(
            (int) $p['owner_id'],
            'Permintaan anggota baru',
            $u['full_name'] . ' ingin bergabung dengan ' . $p['title'] . '.'
        );
        flash('success', 'Permintaan bergabung berhasil dikirim.');
    } catch (PDOException $e) {
        flash('error', 'Kamu sudah pernah mengajukan permintaan untuk proyek ini.');
    }

    redirect('/?page=project&id=' . $project);
}
