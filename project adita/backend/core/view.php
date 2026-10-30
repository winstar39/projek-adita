<?php
declare(strict_types=1);

function render_view(string $view, array $data = []): void {
    if (!preg_match('/\A[a-zA-Z0-9_-]+(?:\/[a-zA-Z0-9_-]+)*\z/', $view)) {
        throw new InvalidArgumentException('Nama view tidak valid.');
    }

    $viewFile = __DIR__ . '/../../frontend/views/' . $view . '.php';
    if (!is_file($viewFile)) {
        throw new RuntimeException('View tidak ditemukan: ' . $view);
    }

    extract($data, EXTR_SKIP);
    require $viewFile;
}

function render_page(string $title, string $view, array $data = []): void {
    $data['user'] = $data['user'] ?? current_user();

    render_view('layouts/header', [
        'title' => $title,
        'user' => current_user(),
    ]);
    render_view($view, $data);
    render_view('layouts/footer');
}
