<?php
declare(strict_types=1);

require_once __DIR__ . '/../backend/core/bootstrap.php';
require_once __DIR__ . '/../backend/controllers/AuthController.php';
require_once __DIR__ . '/../backend/controllers/ProjectController.php';
require_once __DIR__ . '/../backend/controllers/DashboardController.php';
require_once __DIR__ . '/../backend/controllers/AdminController.php';

$actions = [
    'login' => 'handle_login',
    'register' => 'handle_register',
    'logout' => 'handle_logout',
    'project-create' => 'handle_project_create',
    'join' => 'handle_join_request',
    'join-review' => 'handle_join_review',
    'profile-update' => 'handle_profile_update',
    'project-review' => 'handle_project_review',
];

$action = $_GET['action'] ?? null;
if ($action && isset($actions[$action])) {
    $actions[$action]();
    exit;
}

$pages = [
    'login' => 'show_login',
    'register' => 'show_register',
    'projects' => 'show_projects',
    'project' => 'show_project_detail',
    'project-create' => 'show_project_create',
    'dashboard' => 'show_dashboard',
    'profile' => 'show_profile',
    'review' => 'show_review',
];

$page = $_GET['page'] ?? 'home';
if (isset($pages[$page])) {
    $pages[$page]();
    exit;
}

render_page('Kolaborasi dimulai di sini', 'home');
